<?php


namespace Hahadu\ThinkCrawling\Base;


use Hahadu\Helper\HttpHelper;
use Hahadu\Helper\StringHelper;
use Hahadu\ThinkCrawling\Traits\FilesTrait;
use Http\Client\Response;
use QL\QueryList;
use think\cache\driver\Redis;

class Base
{
    use FilesTrait;
    protected const DOT = '.';
    protected $QueryList;
    /***
     * @var \Redis
     */
    protected $redis;
    protected const PAGE_CACHE = 'CRAWLING_PAGE_CACHE';
    protected const FAIL_PAGE_URL = 'CRAWLING_FAIL_PAGE_URL';
    protected const FILE_DATA_CACHE = 'CRAWLING_URL_FILE_DATA';
    protected const DOWNLOAD_URI_FILE_NAME = 'DOWNLOAD_URI_FILE_NAME';


    public function __construct()
    {
        $this->QueryList = new QueryList();
        $this->redis = new Redis();
    }

    /****
     * @param $url
     * @param int $page_cache_timeout
     * @return Response|NULL
     */
    protected function getHtml($url,$page_cache_timeout = 36000){
        $html = $this->redis->get(self::PAGE_CACHE.$url);
        if(!$html){
            $request = HttpHelper::request('get',$url);
            $responseCode = (int)$request->getResponseCode();
            if($responseCode==200){
                $html = $request->getBody();
                $transType = $this->getTransType($html);
                $html = StringHelper::$transType($html);
                $this->redis->set(self::PAGE_CACHE.$url,$html,$page_cache_timeout);
                $this->redis->zRem(self::FAIL_PAGE_URL,$url);
            }else{
                $this->redis->zAdd(self::FAIL_PAGE_URL,$responseCode,$url);
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
        $fileData = $this->redis->get(self::FILE_DATA_CACHE.$url);
        if(null==$fileData){

            $fileData = file_get_contents($url);
            $this->redis->set(self::FILE_DATA_CACHE.$url,$fileData);
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