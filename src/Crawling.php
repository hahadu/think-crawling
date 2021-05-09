<?php


namespace Hahadu\ThinkCrawling;


use Hahadu\ThinkCrawling\Core\Base;
use think\swoole\Websocket;

class Crawling extends Base
{
    /*****
     * @var Websocket
     */
    protected $websocket = false;

    protected $host = '';

    /****
     * @param string $host
     */
    public function setHost($host=''){
        $this->host = $host;
    }

    /****
     * @param Websocket $websocket
     */
    public function setWebsocket($websocket){
        $this->websocket = $websocket;
    }

    /******
     * @return bool
     */
    public function isSocket(){
        return (strtolower(php_sapi_name())=='cli' && ($this->websocket instanceof Websocket));
    }

}