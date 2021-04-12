<?php


namespace Hahadu\ThinkCrawling\Traits;


use Hahadu\ThinkCrawling\InterFaces\ConfigureGetInterface;

trait ConfigureTrait
{
    /****
     * @var ConfigureGetInterface
     */
    protected $configure;


    /*****
     * 获取页面缓存键名
     * @param string $url
     * @return string
     */
    protected function get_page_cache($url = ''): string
    {
        return $this->configure->get_page_cache($url);
    }

    /****
     * 获取失败页面连接缓存键名
     * @return string
     */
    protected function get_fail_page_url(): string
    {
        return $this->configure->get_fail_page_url();
    }

    /*****
     * 获取缓存文件键名
     * @param string $url
     * @return string
     */
    protected function get_file_data_cache($url = ''): string
    {
        return $this->configure->get_file_data_cache($url);
    }

    /****
     * 获取远程文件下载本地名称
     * @param string $uri
     * @return string
     */
    protected function get_download_uri_file_name($uri = ''): string
    {
        return $this->configure->get_download_uri_file_name($uri);
    }
}