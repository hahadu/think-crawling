<?php


namespace Hahadu\ThinkCrawling\Interfaces;
use Hahadu\Collect\Collection;

interface CrawlingCategoryInterface
{
    /*****
     * @param string|null $href
     * @return Collection
     */
    public function CategoryQueryList(string $href = null):Collection;

}