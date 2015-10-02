<?php


namespace Eva\YinxingApiVer1\Controllers;

use Eva\YinxingApiVer1\Models;
use GuzzleHttp\Client;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $client = new Client();
        $response = $client->get('http://affiliate-api.dmm.com/',
            [
                'query' =>
                    array_merge([
                        'api_id' => '',
                        'affiliate_id' => '',
                        'operation' => 'ItemList',
                        'version' => '2.0',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'site' => 'DMM.co.jp',
                        'service' => 'digital',
                        'keyword' => ''
                    ], [
                        'api_id' => $this->config->movieApi->dmm->apiId,
                        'affiliate_id' => $this->config->movieApi->dmm->affiliateId,
                    ])
            ],
            [
                'connect_timeout' => 2
            ]
        );
        return $this->response->setJsonContent(simplexml_load_string($response->getBody()));
    }
}
