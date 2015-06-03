<?php
class LPager{
    public static function getPageStr($total, $size, $page){
        //分页
        $pages = new CPagination($total);
        $pages->pageSize = $size;
        $pages->currentPage = $page-1;

        $pageStr = new MLinkPager();
        $pageStr->pages = $pages;
        $pageStr->header = "";
        $pageStr->firstPageLabel="第一页";
        $pageStr->prevPageLabel ="上一页";
        $pageStr->nextPageLabel ="下一页";
        $pageStr->lastPageLabel= "最后一页";
        $pageStr = $pageStr->run();
        return $pageStr;
    }
}