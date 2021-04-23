<?php


namespace Hahadu\ThinkCrawling\Interfaces;
use Hahadu\Collect\Collection;

interface CrawlingCategoryInterface
{
    /*****
     * @param string $href
     * @return Collection
     */
    public function CategoryQueryList(string $href):Collection;

}