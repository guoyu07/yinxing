# -*- coding: utf-8 -*-
import os
import re
import urllib
import urlparse
from scrapy import Request
from scrapy.conf import settings

from scrapy.spiders import Rule
from scrapy.spiders import CrawlSpider
from scrapy.linkextractors import LinkExtractor
import time
from yinxing_dmm.items import YinxingDmmItem


class DmmSpider(CrawlSpider):
    name = "dmm"
    allowed_domains = ["www.dmm.co.jp", "affiliate-api.dmm.com"]
    start_urls = [
        # enter by genre
        "http://www.dmm.co.jp/digital/videoa/-/genre/=/display=syllabary/sort=ranking/",
        "http://www.dmm.co.jp/digital/videoa/-/maker/",
        "http://www.dmm.co.jp/digital/videoa/-/series/=/keyword=a/sort=ruby/",
        "http://www.dmm.co.jp/digital/videoa/-/actress/=/keyword=a/",
    ]

    rules = (
        # Enter pages
        Rule(LinkExtractor(allow='digital/videoa/-/actress/=/keyword=\w+/(page=\d+/)*$', ), follow=True, ),
        Rule(LinkExtractor(allow='digital/videoa/-/maker/=/keyword=\w+/(page=\d+/)*$', ), follow=True, ),
        Rule(LinkExtractor(allow='digital/videoa/-/series/=/keyword=\w+/sort=ruby/(page=\d+/)*$', ), follow=True, ),
        # List pages
        Rule(LinkExtractor(
            allow='digital/videoa/-/list/=/article=(actress|series|maker|keyword)/id=\d+/(page=\d+/)*$', ),
             follow=True, ),
        Rule(LinkExtractor(allow='detail/=/cid=(\w+)/$', ),
             follow=True,
             callback='download_movie_detail'),
    )

    def download_movie_detail(self, response):
        dmm_id = response.url.split('/')[-2].replace('cid=', '')
        filepath = self.get_dl_html_file_path(dmm_id)
        if os.path.exists(filepath):
            self.logger.debug("Html %s already exists, skipped" % filepath)
            return [YinxingDmmItem()]

        self.logger.debug("Download html %s" % response.url)
        with open(filepath, 'wb') as f:
            f.write(response.body)
        return [YinxingDmmItem()]

    def get_detail_xml_url(self, dmm_id):
        params = {
            'api_id': settings['YINXING_DMM_APP_ID'],
            'affiliate_id': settings['YINXING_DMM_AFFILIATE_ID'],
            'operation': 'ItemList',
            'version': '2.0',
            'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
            'site': 'DMM.co.jp',
            'service': 'digital',
            'offset': '0',
            'hits': '100',
            'keyword': dmm_id
        }
        return urlparse.urlunparse(('http', 'affiliate-api.dmm.com', '/', '', urllib.urlencode(params), ''))

    def get_dl_html_file_path(self, dmm_id):
        return './dl/html_all/%s.html' % self.dmm_id_to_yinxing_id(dmm_id)

    def get_dl_xml_file_path(self, dmm_id):
        return './dl/xml_all/%s.xml' % self.dmm_id_to_yinxing_id(dmm_id)

    def dmm_id_to_yinxing_id(self, dmm_id):
        return dmm_id

    def download_detail_xml(self, response):
        self.logger.debug("Download xml %s" % response.url)
        with open(self.get_dl_xml_file_path(response.meta['dmm_id']), 'wb') as f:
            f.write(response.body)
        return [YinxingDmmItem()]

    def parse_movie_id_from_list(self, response):
        pattern = re.compile(r'detail\/=\/cid=(\w+)')
        for link in response.xpath('//*[@id="list"]/li[1]/div/p[2]/a'):
            url = link.xpath('@href').extract()[0]
            match = pattern.search(url)
            # self.logger.debug("Found detail url %s" % url)
            if not match:
                continue
            dmm_id = match.group(1)
            self.logger.debug("Found dmm id %s" % dmm_id)
            url = self.get_detail_xml_url(dmm_id)
            self.logger.debug("Detail url : %s" % url)
            if os.path.exists(self.get_dl_xml_file_path(dmm_id)):
                self.logger.info("File %s already exists, download skipped" % self.get_dl_xml_file_path(dmm_id))
                continue
            yield Request(url, callback=self.download_detail_xml, meta={'dmm_id': dmm_id})
