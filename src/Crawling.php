<?php


namespace Hahadu\ThinkCrawling;


use Hahadu\ThinkCrawling\Core\Base;

class Crawling extends Base
{
    protected $host = '';

    /****
     * @param string $host
     */
    public function setHost($host=''){
        $this->host = $host;
    }

}