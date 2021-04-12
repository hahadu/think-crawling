<?php


namespace Hahadu\ThinkCrawling\Base;


use Hahadu\Helper\HttpHelper;
use Hahadu\Helper\StringHelper;
use Hahadu\ThinkCrawling\Traits\ConfigureTrait;
use Hahadu\ThinkCrawling\Traits\FilesTrait;
use Http\Client\Response;
use QL\QueryList;
use think\cache\driver\Redis;

use Hahadu\ThinkCrawling\Configure\Configure;


class Base
{
    use FilesTrait;
    use ConfigureTrait;

    protected $QueryList;
    /***
     * @var \Redis
     */
    protected $redis;

    /****
     * Base constructor.
     * @param Configure $configure
     */
    public function __construct(Configure $configure)
    {
        $this->configure = $configure;
        $this->QueryList = new QueryList();
        $this->redis = new Redis();
    }

    /****
     * @param $url
     * @param int $page_cache_timeout
     * @return Response|NULL
     */
    protected function getHtml($url,$page_cache_timeout = 36000){
        $html = $this->redis->get($this->get_page_cache($url));
        if(!$html){
            $request = HttpHelper::request('get',$url);
            $responseCode = (int)$request->getResponseCode();
            if($responseCode==200){
                $html = $request->getBody();
                $transType = $this->getTransType($html);
                $html = StringHelper::$transType($html);
                $this->redis->set($this->get_page_cache($url),$html,$page_cache_timeout);
                $this->redis->zRem($this->get_fail_page_url(),$url);
            }else{
                $this->redis->zAdd($this->get_fail_page_url(),$responseCode,$url);
            }
            usleep(10000);
        }else{
            usleep(1000);
        }

        return  $html;
    }

    /****
     * @param QueryList $html
     * @param array $rules
     * @return mixed
     */
    protected function getQueryDataInfo(QueryList $html,array $rules){
        return $html->rules($rules)->queryData();

    }

    /****
     * 缓存远程文件数据
     * @param $url
     * @return false|mixed|string
     */
    private function urlFileData($url){
        $fileData = $this->redis->get($this->configure->get_file_data_cache($url));
        if(null==$fileData){

            $fileData = file_get_contents($url);
            $this->redis->set($this->get_file_data_cache($url),$fileData);
        }
        return $fileData;
    }

    protected function getTransType($html){
        $charset = 'utf-8';
        $_charset = $this->QueryList->html($html)->find('meta')->attrs('*')->filter(function ($item){
            if(isset($item['charset'])) return $item;
        });
        foreach ($_charset as $item) {

            $charset = isset($item['charset'])?strtolower($item['charset']):'utf8';
        }

        switch ($charset){

            case 'gbk':
            case 'gb2312':
                $transType = 'trans_gbk';
                break;
            case 'utf8':
            case 'utf-8':
            default:
                $transType = 'trans_utf8';
                break;
        }
        return $transType;
    }

}