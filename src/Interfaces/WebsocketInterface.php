<?php


namespace Hahadu\ThinkCrawling\Interfaces;


use think\swoole\Websocket;

interface WebsocketInterface
{
    /****
     * @param Websocket $websocket
     */
    public function setWebsocket(Websocket $websocket);

}