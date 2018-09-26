<?php

// was Mobile_PageBar in pagination.class.php

class Hustler_Mobile_Pagebar implements iHustler_Pagination {

    private $rs;                            //the result set to paginate (since we don't really load all this data we'll just have a number for size)
    private $pageSize;                      //number of records to display
    private $pageNumber;                    //the page to be displayed
    private $rowNumber;                     //the current row of data which must be less than the pageSize in keeping with the specified size
    private $offSet;
    private $siteType;                     // 'members' or 'tour'

    private $url;
    private $queryString;
    
    private $specialLinks;                   // for special tour sites only. To build different style links
    private $maxLink;                        // for special tour sites only. To show max number, linking to join page
    private $maxPages;                       // for use with $maxLink
    private $maxShowMore;                     // for use with $maxLink... show this many pages than maxPages but link to maxLink
    
    private $ajax;                          // ajax version will have some data elements instead of href in the links



    function __construct($data_size = NULL, $pageSize, $pageNum = 1, $siteType, $ajax = NULL) {
//        $pageNum = 1; // default page number
        $this->siteType = $siteType;
                
        // set # of records
        $this->setRs($data_size);
        
        $this->setPageSize($pageSize);
        $this->assignPageNumber($pageNum);

        $this->setOffSet(($this->getPageNumber() - 1) * ($this->getPageSize()));
        
        $this->url = getUrlSansPage(); // put method in class?
        defined('FIXED_QUERY_STRING') ? $this->queryString = FIXED_QUERY_STRING : $this->queryString = '';
        
        $this->ajax = $ajax;

        $this->specialLinks = NULL;
        $this->maxLink = NULL;
        $this->maxPages = NULL;
        $this->maxShowMore = NULL;
    }

    private function makeLink($page) {
        
        if ($this->specialLinks != NULL) {
//echo "\n we have special link array:" . print_r($this->specialLinks, TRUE) . '<BR>';
            // replace special string with the page number
            // also do the anchor label if it exists
            $label = '';
            $tmp = $this->specialLinks;
            foreach ($tmp as $k=>$v) {
                if ($v == 'PAGE_NUM') {
                    $tmp[$k] = $page;
                }
                if (substr($v,0,1) == '#') {
                    $label = $v;
                    unset($tmp[$k]);
                }
            }
//echo "\n made link for page $page :" . implode('/',$tmp) . '/' . '<BR>' . print_r($tmp, TRUE) . '<BR>';
            return implode('/',$tmp) . '/' . $this->queryString . $label;
            
        }
        if ($this->ajax) {
            return '" data-page="' . $page . '"';
        }
        return $this->url . $page . '/' . $this->queryString;
    }
    
    public function setMaxLink($link) {
        $this->maxLink = $link;
    }
    
    public function setMaxPage($actual_total_records) {
        $this->maxPages = ceil($actual_total_records / (float) $this->getPageSize());
    }
    
    public function setMaxShowMore($num) {
        $this->maxShowMore = $num;
    }

    public function setSpecialLinks ($arr) {
//echo "\n\nmaking special links\n" . print_r($arr,TRUE);
        $this->specialLinks = $arr;
    }

    /*
     *  makePagedNav($num)
     *  makes the page numbers and links for the links on the navigation bar
     *  $numPages = number of pages to show.  This may not be accurate due to integer rounding
     */

    public function makePagedNav($numPages, $no_bar = FALSE) {
        $pages_shown = $numPages; // how many pages to show in nav bar.
        if ($this->rs === NULL) {
            // going to make an empty one.
            return <<<HERE
   <div class="pagination_container" data-steps="{$pages_shown}">
            <a href=""><div class="prev_arrow"></div></a>
            <div class="page_numbers">
                <div class="center_links">
                </div>     
            </div><!--end page numbers--> 
            <a href=""><div class="next_arrow"></div></a>
    </div>
HERE;
        }
        if (FALSE === $no_bar || is_null($no_bar)) {
            $bar = ' | ';
        } else {
            $bar = ' ';
        }
        $currentPage = $this->getPageNumber();

        $str = "";

        // show the "First" tab?
        $show_first = FALSE;
        $more = 0;
        if (!$this->isFirstPage() && !$this->isTour()) {
            if (($currentPage >= $pages_shown) || 
		    // below is a special case for portrait view [First] [2] 3 [4] [Last]  ...
                    ($currentPage == 3 && $pages_shown <= 5 && $this->fetchNumberPages() > 4) 
                ) {
                $str = $str . '<a href="' . $this->makeLink(1) . '"'. ' title="Start" class="pagenum">First</a>';
                $show_first = TRUE;
                $more = 0;
            } else {
                $more = 1; // since we don't show "First" in this scenario, we will add one more page to click to!
            }
        }
        elseif (!$this->isFirstPage() && $this->isTour() && $currentPage < $pages_shown) {
                $more = 1; // since we don't show "First" in this scenario, it will be short one button and we will add one more page to click to
        }

        $next_style = '';
        $prev_style = '';

        // determine which arrow imaging to use: enabled or disabled
        
        if ($this->isFirstPage()) {
            $prev_style = 'style="background-position:center top;"';
        }
        if ($this->isLastPage()) {
            $next_style = 'style="background-position:center top;"';
        }
        
        //write statement that handles the previous and next phases
        //if it is not the first page then write previous to the screen
        $arrow_prev = '<div class="prev_arrow" ' . $prev_style . '></div>' . PHP_EOL;
        if (!$this->isFirstPage()) {
            $previousPage = $currentPage - 1;
            $arrow_prev = '<a href="' . $this->makeLink($previousPage) . '"><div class="prev_arrow" ' . $prev_style . '></div></a>'. PHP_EOL;
        }

        // preparation for the for loop to print page numbers
        if ($this->fetchNumberPages() - $currentPage < $pages_shown) {
            $previous_pages_add = $pages_shown - ($this->fetchNumberPages() - $currentPage);
            $future_pages_add = $pages_shown - $previous_pages_add;
        } else if ($currentPage < $pages_shown) {
            $future_pages_add = $pages_shown - $currentPage;
            $previous_pages_add = $pages_shown - $future_pages_add;
        } else {
            $future_pages_add = floor((int) ($pages_shown / 2));
            if (1 == 1) {
                // assure us we will get a more future pages than past pages
                $z = $pages_shown - $future_pages_add;
                if ($z - $future_pages_add >= 1) {
                    $future_pages_add = $future_pages_add + ($z - $future_pages_add);
                    if (! $this->isTour())  {
                            $future_pages_add++;
                    }
                } else {
                    // some hokiness because of "First" and "Last" link if $z is odd or 0
                    if (! $this->isTour())  {
                        if ($z % 2 == 0) {
                            $future_pages_add++;
                        }
                    }
                }
            }
            $previous_pages_add = $pages_shown - $future_pages_add;
        }
        
        $future_pages_add = $future_pages_add + $more; // the special scenarios when we're in first few pages...

        // see if they don't add up to $pages_shown, because floor had rounded down...
        if ($previous_pages_add + $future_pages_add < $pages_shown) {
            $future_pages_add++;
        }
        if ($previous_pages_add + $future_pages_add < $pages_shown) {
            $previous_pages_add++;
        }

        if ($currentPage == 1) {
            $future_pages_add++;
        }

        // show the "Last" tab?
        $show_last = FALSE;
        if (!$this->isLastPage() && !$this->isTour()) {
            //echo "num_pages: " .$this->fetchNumberPages() . " current_page: " . $currentPage . " pages shown: " . $pages_shown . PHP_EOL . "<BR>";
            //print "current page ( $currentPage ) + future page ( $future_pages_add ) = " . ($currentPage + $future_pages_add); echo " vs " . $this->fetchNumberPages();
            if ($this->fetchNumberPages() >  $pages_shown) {
                $show_last = TRUE;
                if ($future_pages_add > 1) {
                    $future_pages_add--;
                } elseif ($previous_pages_add > 1) {
                    $previous_pages_add--;
                }
                $more = 0;
                if ($currentPage + $future_pages_add >= $this->fetchNumberPages()) {
                    $show_last = FALSE;
                    $more = 1;
                }
            } else {
                $more = 1;
            }
        }
        $previous_pages_add = $previous_pages_add + $more;  // special scenario if there is no LAST button, we add more previous pages.

        if ($show_first == TRUE) {
               if ($future_pages_add > 1) {
                    $future_pages_add--;
                } elseif ($previous_pages_add > 1) {
                    $previous_pages_add--;
                }
        }
        
        $this->specialLinks != NULL ? $oth_class = "selected-pagenum" : $oth_class = "";
        for ($i = $currentPage - $previous_pages_add; $i <= $currentPage + $future_pages_add; $i++) {
            // OLD ONE
            //for ($i = $currentPage - $pages_shown; $i <= $currentPage + $pages_shown; $i++) {
            //if i is less than one then continue to next iteration		
            if ($i < 1) {
                continue;
            }

            if ($i > $this->fetchNumberPages()) {
                break;
            }

            if ($i == $currentPage) {
                $str .= '<a name="current" style="text-decoration:none;" class="pagenum ' . $oth_class . '">' . $i . '</a>' . PHP_EOL;
            } else {
                $str .= '<a href="' . $this->makeLink($i) . '" class="pagenum">' . $i. '</a>' . PHP_EOL;
            }
            ($i == $currentPage + $pages_shown || $i == $this->fetchNumberPages()) ? $str .= " " : $str .= $bar;              //determine if to print bars or not
        }//end for

        $arrow_next = '<div class="next_arrow" ' . $next_style . '></div>' . PHP_EOL;
        if (!$this->isLastPage()) {
            $nextPage = $currentPage + 1;
            $arrow_next = '<a href="' . $this->makeLink($nextPage) .  '"><div class="next_arrow" ' . $next_style . '></div></a>' . PHP_EOL;
        }

        if ($show_last == TRUE && $this->maxLink == NULL) {
            $str = $str . "<a href=\"" . $this->makeLink($this->fetchNumberPages()) . 
                    "\" title=\"Last\" class=\"pagenum\">Last</a>" . PHP_EOL;
        }
        
        $links = $str;
        
        $max_link = '';
        if ($this->maxLink != NULL && 
            $this->maxPages > $numPages) {
            //echo "fetchnumber: " . $this->maxPages  . "   numpages=" . $numPages;
            // NOTE: when using maxLink, $this->fetchNumberPages() isn't really workable for showing actual 
            // max pages from the content query. Use $this->maxPages instead for real maximum pages
            if ($this->maxShowMore != NULL) {
                for ($x=1; $x <= $this->maxShowMore; $x++) {
                    $n = $numPages + $x;
                    $n = (string) $n;
                    $max_link .= '<a href="' . $this->maxLink . '" class="pagenum">' . $n . '</a>' . PHP_EOL;
                }
            }
            $next_link = $this->makeLink($currentPage + 1);
            if ($currentPage + 1 > $numPages) {
                $next_link = $this->maxLink;
            }

            $max_link .= ' ... ' . 
                    '<a href="' . $this->maxLink . '" class="pagenum">' . $this->maxPages . '</a>' . PHP_EOL . 
                    '<a href="' . $next_link . '" class="pagenum"> Next </a>';
        }

        return 
        '<div class="pagination_container" data-steps="' . $pages_shown . '">
                ' . $arrow_prev . '
                <div class="page_numbers">
                    <div class="center_links">
                    ' . $links . $max_link . '
                    </div>     
                </div><!--end page numbers--> 
                ' . $arrow_next . '
      </div>';
        
    }
    
    
    // go to page to start from
    public function setPage($pageNum) {
        $this->assignPageNumber($pageNum);
        $this->setRowNumber(0);
        $this->setOffSet(($this->getPageNumber() - 1) * ($this->getPageSize()));
    }

    //implement getters and setters
    public function setOffSet($offSet) {
        $this->offSet = $offSet;
    }

    public function getOffSet() {
        return $this->offSet;
    }

    public function getRs() {
        return $this->rs;
    }

    public function setRs($obj) {
        $this->rs = $obj;
    }

    public function getPageSize() {
        return $this->pageSize;
    }

    public function setPageSize($pages) {
        $this->pageSize = $pages;
    }

    //accessor and mutator for page numbers
    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function setPageNumber($number) {
        $this->pageNumber = $number;
    }

    //fetches the row number
    public function getRowNumber() {
        return $this->rowNumber;
    }

    public function setRowNumber($number) {
        $this->rowNumber = $number;
    }

    public function fetchNumberPages() {
        if (!$this->getRs()) {
            return false;
        }

        $pages = ceil($this->getRs() / (float) $this->getPageSize());
        return $pages;
    }

    //sets the current page being viewed to the value of the parameter
    public function assignPageNumber($page) {
        if (($page <= 0) || ($page > $this->fetchNumberPages()) || ($page == "")) {
            $this->setPageNumber(1);
        } else {
            $this->setPageNumber($page);
        }
        //upon assigning the current page, move the cursor in the result set to (page number minus one) multiply by the page size
        //example  (2 - 1) * 10
    }

    public function fetchPagedRow() {
        // unused
    }

    public function isFirstPage() {
        return ($this->getPageNumber() <= 1);
    }

    public function isLastPage() {
        return ($this->getPageNumber() >= $this->fetchNumberPages());
    }
    
    private function isTour() {
        if ($this->siteType == 'tour') {
            return TRUE;
        }
        return FALSE;
    }

}

?>