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
    use FilesTrait;
    use ConfigureTrait;

    protected $QueryList;
    /***
     * @var \Redis
     */
    protected $redis;

    protected $guzzle;

    /****
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

    /****
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

    /****
     * @param QueryList $html
     * @param array $rules
     * @return mixed
     */
    protected function getQueryDataInfo(QueryList $html, array $rules)
    {
        return $html->rules($rules)->queryData();

    }

    /*****
     * html to QueryList object
     * @param string $html
     * @return QueryList
     */
    protected function buildHtml($html){
        return $this->QueryList->html($html);
    }

    /****
     * 缓存远程文件数据
     * @param $url
     * @return false|mixed|string
     */
    private function urlFileData($url)
    {
        $fileData = $this->redis->get($this->configure->get_file_data_cache($url));
        if (null == $fileData) {

            $fileData = file_get_contents($url);
            $this->redis->set($this->get_file_data_cache($url), $fileData);
        }
        return $fileData;
    }

    protected function getTransType($html)
    {
        $charset = 'utf-8';
        $_charset = $this->QueryList->html($html)->find('meta')->attrs('*')->filter(function ($item) {
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

}