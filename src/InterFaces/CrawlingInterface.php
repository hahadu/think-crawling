<?php


namespace Hahadu\ThinkCrawling\InterFaces;


interface CrawlingInterface
{

    /****
     * 采集页面列表
     * @param int $start
     * @param int $end
     * @return Collection
     */
    public function pageList($start=0,$end=-1): Collection;

    /****
     * 采集单页面内容
     * @param string $url
     * @return array
     */
    public function pageQueryInfo(string $url): array;

}