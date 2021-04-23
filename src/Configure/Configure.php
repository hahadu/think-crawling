<?php


namespace Hahadu\ThinkCrawling\Configure;

use Hahadu\ThinkCrawling\Constants\Constants;
use Hahadu\ThinkCrawling\Interfaces\ConfigureGetInterface;
use Hahadu\ThinkCrawling\Interfaces\ConfigureSetInterface;

class Configure implements ConfigureGetInterface,ConfigureSetInterface
{
    /****
     * @var string 页面缓存名称
     */
    private $pageCache = Constants::PAGE_CACHE;
    /****
     * @var string 失败页面连接
     */
    private $failPageUrl = Constants::FAIL_PAGE_URL;

    /****
     * @var string 文件数据缓存
     */
    private $fileDataCache = Constants::FILE_DATA_CACHE;

    /****
     * @var string 下载文件名
     */
    private $downloadUriFileName = Constants::DOWNLOAD_URI_FILE_NAME;

    /*****
     * 设置页面缓存键名
     * @param string $pageCache
     */
    public function set_page_cache(string $pageCache)
    {
        $this->pageCache = $pageCache;
    }

    /****
     * 设置失败页面连接缓存键名
     * @param string $failPageUrl
     */
    public function set_fail_page_url(string $failPageUrl)
    {
        $this->failPageUrl = $failPageUrl;
    }

    /*****
     * 设置缓存文件键名
     * @param string $fileDataCache
     */
    public function set_file_data_cache(string $fileDataCache)
    {
        $this->fileDataCache = $fileDataCache;
    }

    /****
     * 设置远程文件下载本地名称
     * @param string $downloadUriFileName
     */
    public function set_download_uri_file_name(string $downloadUriFileName)
    {
        $this->downloadUriFileName = $downloadUriFileName;
    }

    /*****
     * 获取页面缓存键名
     * @param string $url
     * @return string
     */
    public function get_page_cache($url = ''):string
    {
        return $this->pageCache . $url;
    }

    /****
     * 获取失败页面连接缓存键名
     * @return string
     */
    public function get_fail_page_url():string
    {
        return $this->failPageUrl;
    }

    /*****
     * 获取缓存文件键名
     * @param string $url
     * @return string
     */
    public function get_file_data_cache($url = ''):string
    {
        return $this->fileDataCache . $url;
    }

    /****
     * 获取远程文件下载本地名称
     * @param string $uri
     * @return string
     */
    public function get_download_uri_file_name($uri = ''):string
    {
        return $this->downloadUriFileName . $uri;
    }


}