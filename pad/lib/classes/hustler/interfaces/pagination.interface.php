<?php
// pagination interface

interface iHustler_Pagination {

    public function setPage($number);

    public function setOffSet($offSet);

    public function getOffSet();

    public function getRs();

    public function setRs($obj);

    public function getPageSize();

    public function setPageSize($pages);

    //accessor and mutator for page numbers
    public function getPageNumber();

    public function setPageNumber($number);

    //fetches the row number
    public function getRowNumber();

    public function setRowNumber($number);

    public function fetchNumberPages();

    //sets the current page being viewed to the value of the parameter
    public function assignPageNumber($page);

    public function fetchPagedRow();

    public function isFirstPage();

    public function isLastPage();
}

?>