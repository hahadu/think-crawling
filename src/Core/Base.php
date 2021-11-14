<?php


namespace Hahadu\ThinkCrawling\Core;
use Hahadu\Helper\StringHelper;
use Hahadu\ThinkCrawling\Traits\ConfigureTrait;
use Hahadu\ThinkCrawling\Traits\FilesTrait;
use QL\QueryList;
use think\cache\driver\Redis;
use GuzzleHttp\Client as Guzzle;
use Hahadu\ThinkCrawling\Configure\Configure;

abstract class Base
{
    use FilesTrait,ConfigureTrait;

    protected $QueryList;
    /***
     * @var \Redis
     */
    protected $redis;

    protected $guzzle;

    protected $host = '';

    /****
     * @param string $host
     */
    public function setHost($host=''){
        $this->host = $host;
    }


    /**
     * Base constructor.
     * @param Configure $configure
     */
    public function __construct(Configure $configure, QueryList $queryList, Redis $redis)
    {
        $this->configure = $configure;
        $this->QueryList = $queryList;
        $this->redis = $redis;
        $this->guzzle = new Guzzle();
    }

    /**
     * 获取文本内容
     * 优先获取缓存内容，
     * 如果缓存过期或者不存在，
     * 则重新拉取远程页面进行缓存
     * @param string $url
     * @param int $page_cache_timeout
     * @return bool|mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getHtml($url, $page_cache_timeout = 36000)
    {
        $html = $this->redis->get($this->get_page_cache($url));
        if (!$html) {
            $request = $this->guzzle->get($url,[
                'allow_redirects' => false
            ]);
            $responseCode = (int)$request->getStatusCode();
            if ($responseCode == 200) {
                $html = $request->getBody()->getContents();
                $transType = $this->getTransType($html);
                $html = StringHelper::$transType($html);
                $this->redis->set($this->get_page_cache($url), $html, $page_cache_timeout);
                $this->redis->zRem($this->get_fail_page_url(), $url);
            } else {
                $this->redis->zAdd($this->get_fail_page_url(), $responseCode, $url);
            }
            usleep(10000);
        } else {
            usleep(1000);
        }

        return $html;
    }

    /**
     * 根据设置查询条件，获取文档信息
     * @param QueryList $html
     * @param array $rules
     * @return mixed
     */
    protected function getQueryDataInfo(QueryList $html, array $rules)
    {
        return $html->rules($rules)->queryData();

    }

    /**
     * 打包文档信息为QL对象
     * html to QueryList object
     * @param string $html
     * @return QueryList
     */
    protected function buildHtml($html):QueryList
    {
        return $this->QueryList->html($html);
    }

    /**
     * 缓存远程文件数据
     * @param $url
     * @return false|mixed|string
     */
    private function urlFileData($url)
    {
        $fileData = $this->redis->get($this->configure->get_file_data_cache($url));
        if (null == $fileData) {

            try{
                $fileData = file_get_contents($url);
                $this->redis->set($this->get_file_data_cache($url), $fileData);
            }catch (\Exception $e){
                $fileData = null;
            }
        }
        return $fileData;
    }

    /**
     * 获取编码
     * @param $html
     * @return string
     */
    protected function getTransType($html):string
    {
        $charset = 'utf-8';
        $_charset = $this->buildHtml($html)->find('meta')->attrs('*')->filter(function ($item) {
            if (isset($item['charset'])) return $item;
        });
        foreach ($_charset as $item) {

            $charset = isset($item['charset']) ? strtolower($item['charset']) : 'utf8';
        }

        switch ($charset) {

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
    /**
     * 随机时间createTime
     * @param $create_time
     * @return int
     */
    protected function setTime():int
    {

        $create_time = date('Y') . '-' . rand(1, 12) . '-' . rand(1, 30);
        $time = rand(1, 24) . ':' . rand(0, 59) . ':' . rand(0, 59);
        $time = $create_time . ' ' . $time;
        $time = strtotime($time);
        $year = (date('m', $time) < date('m')) ? date('Y') : date('Y') - 1;
        return strtotime($year . date('-m-d H:i:s', $time));

    }

    /**
     * @return bool
     */
    protected function isCLI(): bool
    {
        return isCLI();
    }


}