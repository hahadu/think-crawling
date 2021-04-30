<?php


namespace Hahadu\ThinkCrawling\Interfaces;


interface PageListInterface
{
    /****
     * @param int $min
     * @param int $max
     * @return mixed
     */
    public function createPageList(int $min,int $max);

}