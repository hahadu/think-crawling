<?php


namespace Hahadu\ThinkCrawling\Interfaces;


interface ConfigureGetInterface
{

    /*****
     * 获取页面缓存键名
     * @param string $url
     * @return string
     */
    public function get_page_cache($url = ''): string;

    /****
     * 获取失败页面连接缓存键名
     * @return string
     */
    public function get_fail_page_url(): string;

    /*****
     * 获取缓存文件键名
     * @param string $url
     * @return string
     */
    public function get_file_data_cache($url = ''): string;

    /****
     * 获取远程文件下载本地名称缓存键名
     * @param string $uri
     * @return string
     */
    public function get_download_uri_file_name($uri = ''): string;


    }