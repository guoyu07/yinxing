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
        # "http://www.dmm.co.jp/digital/videoa/-/detail/=/cid=rki00344/"
        "http://www.dmm.co.jp/digital/videoa/-/actress/=/keyword=a/"
        # "http://www.dmm.co.jp/digital/videoa/-/list/=/sort=date/"
    ]

    rules = (
        # Actress List Page
        Rule(LinkExtractor(allow=('digital/videoa/-/actress/=/keyword=\w+/(page=\d+/)*',), ), follow=True),
        # Movie List page filtered by actress
        Rule(LinkExtractor(allow='digital/videoa/-/list/=/article=actress/id=\d+/sort=ranking/(page=\d+/)*', ),
             follow=True,
             callback='parse_movie_id_from_list'),
    )

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

    def get_dl_file_path(self, dmm_id):
        return './dl/%s.xml' % self.dmm_id_to_yinxing_id(dmm_id)

    def dmm_id_to_yinxing_id(self, dmm_id):
        return dmm_id

    def download_detail_xml(self, response):
        self.logger.debug("Download xml %s" % response.url)
        with open(self.get_dl_file_path(response.meta['dmm_id']), 'wb') as f:
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
            if os.path.exists(self.get_dl_file_path(dmm_id)):
                self.logger.info("File %s already exists, download skipped" % self.get_dl_file_path(dmm_id))
                continue
            yield Request(url, callback=self.download_detail_xml, meta={'dmm_id': dmm_id})
