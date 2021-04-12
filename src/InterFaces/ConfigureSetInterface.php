<?php


namespace Hahadu\ThinkCrawling\InterFaces;


interface ConfigureSetInterface
{
    /*****
     * 设置页面缓存键名
     * @param string $pageCache
     */
    public function set_page_cache(string $pageCache);

    /****
     * 设置失败页面连接缓存键名
     * @param string $failPageUrl
     */
    public function set_fail_page_url(string $failPageUrl);

    /*****
     * 设置缓存文件键名
     * @param string $fileDataCache
     */
    public function set_file_data_cache(string $fileDataCache);

    /****
     * 设置远程文件下载本地名称键名
     * @param string $downloadUriFileName
     */
    public function set_download_uri_file_name(string $downloadUriFileName);

}