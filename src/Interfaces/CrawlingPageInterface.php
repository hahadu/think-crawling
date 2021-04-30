<?php


namespace Hahadu\ThinkCrawling\Interfaces;
use Hahadu\Collect\Collection;

interface CrawlingPageInterface
{

    /****
     * 采集页面列表
     * @param int $start
     * @param int $end
     * @return Collection
     */
    public function pageQueryList($start=0,$end=-1): Collection;

    /****
     * 采集单页面内容
     * @param string $href
     * @return Collection
     */
    public function pageQueryInfo(string $href): Collection;

}