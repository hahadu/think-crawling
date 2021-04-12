<?php


namespace Hahadu\ThinkCrawling\Traits;


use Hahadu\Helper\FilesHelper;
use Hahadu\Helper\StringHelper;
use Hahadu\ThinkCrawling\Constants\Constants;
use think\facade\Filesystem;

trait FilesTrait
{

    private function getUrlFileName($url){
        $file_name = $this->redis->get($this->get_download_uri_file_name($url));
        if(null==$file_name){
            $file_ext = FilesHelper::get_file_ext($url);
            $file_name = md5(time().pathinfo($url,PATHINFO_BASENAME).StringHelper::create_rand_string(6)).Constants::DOT.$file_ext;
            $this->redis->set($this->get_download_uri_file_name($url),$file_name);
        }
        return $file_name;
    }

    /****
     * 保存远程文件到本地
     * @param $url
     * @return string
     */
    protected function saveUrlFile($url){
        $file_format = FilesHelper::file_format($url);
        $path = 'files'.DIRECTORY_SEPARATOR.$file_format['file_type'].DIRECTORY_SEPARATOR;
        $filesystem = Filesystem::disk('public');
        $_path = $filesystem->path($path);
        FilesHelper::mkdir($_path);

        $fileData = $this->urlFileData($url);

        $filename = $_path. $this->getUrlFileName($url);
        file_put_contents($filename,$fileData);

        $_filename = FilesHelper::get_file_info($filename);
        return $filesystem->getConfig()->get('url').DIRECTORY_SEPARATOR.$path.$_filename->getBasename();
    }

}