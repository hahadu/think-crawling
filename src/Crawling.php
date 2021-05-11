<?php


namespace Hahadu\ThinkCrawling;


use Hahadu\Helper\JsonHelper;
use Hahadu\ThinkCrawling\Core\Base;
use think\swoole\Websocket;

abstract class Crawling extends Base
{
    /*****
     * @var Websocket
     */
    protected $websocket = false;

    /****
     * @param Websocket $websocket
     */
    public function setWebsocket($websocket){
        $this->websocket = $websocket;
    }

    /*****
     * @return bool
     */
    public function isSocket(): bool
    {
        return ($this->isCLI() && ($this->websocket instanceof Websocket));
    }

    /*****
     * 推送数据
     * @param $message
     * @param string $value
     * @param int $code
     * @return bool
     */
    protected function wsPush($message, $value = '', $code = 1)
    {

        if($this->isSocket()){
            $type = ($code == 1) ? 'success' : 'error';
            $_data = wrap_msg_array($code, $message, [
                'fd' => $this->websocket->getSender(),
                'type' => $type,
                "value" => $value
            ]);
            $value = JsonHelper::json_encode($_data);
            usleep(150000);
            return $this->websocket->push($value);
        }
    }


}