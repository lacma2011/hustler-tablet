// hmCon: Hustler Mobile pad control

// tracks page state

// gets ui input

// be able to read the hash tag, get content
// 
// be able to write a new hash tag when getting input/updates

// observer/observerable pattern

var clearPage = 0; // determine whether to clear page, on pages with multiple content types being received from ajax. ex iPad results page
var clearPageCount = 0; // counter for clearPage
var loadPage = 0; // // determines whether to execute anything after content is loaded in a search results page
var loadPageCount = 0; // counter for when search page is done loading content

var hmCon = {

    ajax: hmajax,
    query: {
        page: 'home',
        pageNum: 1,
        site: 'hustler',
        order: 0,
        details: null
    },
    
    htmlWidgets: null,
    staticData: null,

    queryStringCode: null,
    
    clearQuery: function() {
        this.query.page = 'home';
        this.query.pageNum = 1;
        this.query.site = 'hustler';
        this.query.order = 0;
        this.query.details = {
//            // home was used when we could show more pages of content on homepage carousel
//            home: {
//                videos: 1, // page num
//                dvds: 1 // page num
//            },
            catSelect: {
                id: 0,
                name: '',
		fullName: ''
            },
            dvdSelect: {
                id: 0,
                name: '', // seo name, only needed for URLs
                fullName: ''
            },
            siteSelect: {
                id: 0
            },
            modSelect: {
                id: 0,
                name: '', // seo name, only needed for URLs
                fullName: ''
            },
            text: {
                str: '',
                options: ''
            }
        }
    },
    
    hash: {
        
        getQuery : function (myObj) {
            var hash = window.location.hash.substr(1).split('/');
            var site;
            if (hash[0] == undefined) { //  || hash[0] == ''
                // hash is empty
                return false;
            }

            myObj.clearQuery();
            switch (hash[0]) {
                case 'model': 
                    myObj.query.page = hash[0];
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.site = 'hustler';
                        myObj.query.order = 0;
                        myObj.query.pageNum = 1;
                    } else {

                        myObj.query.site = hash[1];
                        myObj.query.order = hash[2];
                        myObj.query.pageNum = hash[3];
                    }
                    break;
                case 'cat': 
                    myObj.query.page = hash[0];
                    myObj.query.site = hash[1];
                    break;
                case 'video':
                    myObj.query.page = hash[0];
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.site = 'hustler';
                        myObj.query.order = 0;
                        myObj.query.pageNum = 1;
                        myObj.query.details.catSelect.id = 0;
                    } else {

                        myObj.query.site = hash[1];
                        myObj.query.details.catSelect.id = hash[2];
                        myObj.query.order = hash[3];
                        myObj.query.pageNum = hash[4];
                    }                    
                    break;

                case 'dvd':
                    myObj.query.page = hash[0];
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.site = 'hustler';
                        myObj.query.order = 0;
                        myObj.query.pageNum = 1;
                        myObj.query.details.catSelect.id = 0;
                    } else {
                        myObj.query.site = hash[1];
                        myObj.query.details.catSelect.id = hash[2];
                        myObj.query.order = hash[3];
                        myObj.query.pageNum = hash[4];
                    }
                    break;

                case 'mag':
                    myObj.query.page = hash[0];
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.site = 'hustler';
                        myObj.query.order = 0;
                        myObj.query.pageNum = 1;
                    } else {

                        myObj.query.site = hash[1];
                        myObj.query.order = hash[2];
                        myObj.query.pageNum = hash[3];
                    }
                    break;

                case 'photo':
                    myObj.query.page = hash[0];
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.site = 'hustler';
                        myObj.query.order = 0;
                        myObj.query.pageNum = 1;
                        myObj.query.details.catSelect.id = 0;
                    } else {

                        myObj.query.site = hash[1];
                        myObj.query.details.catSelect.id = hash[2];
                        myObj.query.order = hash[3];
                        myObj.query.pageNum = hash[4];

                    }
                    break;

                case 'photodetail':
                    myObj.query.page = hash[0];
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.site = 'hustler';
                        myObj.query.order = 0;
                        myObj.query.pageNum = 1;
                    } else {

                        myObj.query.site = hash[1];
                        myObj.query.order = hash[2];
                        myObj.query.scene_id = hash[3];
                        myObj.query.code = hash[4];    
                        myObj.query.pageNum = hash[5];
                    }
                    break;
                    
                // search pages
                case 'search':
                  
                    myObj.query.page = hash[0];
                    myObj.query.site = hash[1];
                    
                    myObj.query.details.text.str = hash[2];
                    myObj.query.details.text.whichTypes = hash[3];
                    myObj.query.details.text.category_id = hash[4];
                    
                    if (hash[5] == undefined || hash[5] == '') {
                        myObj.query.order = 0;
                        myObj.query.pageNum = '1,1,1,1';
                    } else {
                        myObj.query.order = hash[5];
                        myObj.query.pageNum = hash[6];
                    }
                    break;
                case 'c': // categories
                case 'm': // models
                case 'd': // dvd
                
                    myObj.query.page = hash[0];
                    myObj.query.site = hash[1];
                    switch (hash[0]) {
                        case 'c':
                            myObj.query.details.catSelect.id = hash[2];
                            myObj.query.details.catSelect.name = hash[3];
                            break;
                        case 'm':
                            myObj.query.details.modSelect.id = hash[2];
                            myObj.query.details.modSelect.name = hash[3];
                            break;
                        case 'd':
                            myObj.query.details.dvdSelect.id = hash[2];
                            myObj.query.details.dvdSelect.name = hash[3];
                            break;
                    }
                    if (hash[4] == undefined || hash[4] == '') {
                        myObj.query.order = 0;
                        myObj.query.pageNum = '1,1,1';
                    } else {
                        myObj.query.order = hash[4];
                        myObj.query.pageNum = hash[5];
                    }
                    break;
                case 'fav': // favorites (not much options... yet?)
                    myObj.query.page = hash[0];
                    //myObj.query.page = 'hustler';
                    myObj.query.details.fav = true;
                    if (hash[1] == undefined || hash[1] == '') {
                        myObj.query.order = 0;
                        myObj.query.pageNum = '1,1,1,1';
                        myObj.query.details.catSelect.id = 0;
                    } else {
                        myObj.query.details.catSelect.id = hash[1];
                        myObj.query.order = hash[2];
                        myObj.query.pageNum = hash[3];
                    }
                    break;
                case 'site':
                case 'join':
                    myObj.query.page = hash[0];
                    break;
                default: // home page
                    if (hash[0] !== undefined && hash[0] !== '') myObj.query.site = hash[0];
                    if (hash[1] !== undefined) myObj.query.details.catSelect.id = hash[1];
                    if (hash[2] !== undefined) myObj.query.details.catSelect.name = hash[2];
                    break;
            }

        },

        setThis: function (x) {
            window.location.hash = x;
        },
        
        set: function (query) {

if (query.site == undefined || query.site == '') {
//BUG: somehow the site and order are cleared!
    query.site = 'hustler';
    query.order = 0;
}
            var hash = '';
            switch (query.page) {
                case 'model':
                    hash = 'model' + '/' + query.site + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'video':
                    hash = 'video' + '/' + query.site + '/' + query.details.catSelect.id + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'dvd':
                    hash = 'dvd' + '/' + query.site + '/' + query.details.catSelect.id + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'mag':
                    hash = 'mag' + '/' + query.site + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'photo':
                    hash = 'photo' + '/' + query.site + '/' + query.details.catSelect.id + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'photo_listing_detail':
                 
                    hash = 'photodetail' + '/' + query.site + '/' + query.order + '/' + query.filter + '/' + query.code + '/' + query.pageNum;
                    break;

//TODO: SEARCH PAGES
                // search pages
                case 'c':
                    hash = query.page + '/' + query.site + '/' + query.details.catSelect.id + '/' + hmCon.staticData.categories[query.details.catSelect.id].name + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'm':
                    hash = query.page + '/' + query.site + '/' + query.details.modSelect.id + '/' + query.details.modSelect.name + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'd':
                    hash = query.page + '/' + query.site + '/' + query.details.dvdSelect.id + '/' + query.details.dvdSelect.name + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'search':
                  
                    hash = query.page + '/' + query.site + '/' + encodeURI(query.details.text.str) + '/' + query.details.text.whichTypes + '/' + query.details.text.category_id + '/' + query.order + '/' + query.pageNum;
                    break;
                case 'fav':
                    hash = query.page + '/' + query.details.catSelect.id + '/' + query.order + '/' + query.pageNum;
                    break
                case 'site':
                    hash = query.page;
                    break
                case 'cat':
                    hash = query.page + '/' + query.site;
                    break
                case 'join':
                    hash = query.page;
                    break
                case 'home':
                    hash = query.site;
                    if (query.details.catSelect.id != undefined && query.details.catSelect.id != 0) {
                        hash += '/' + query.details.catSelect.id + '/' + query.details.catSelect.name;
                    }
                    break;
            }
            window.location.hash = hash + '/';
        }
    },
    
    jobs : {
        // remembers the last api call for each content type
        // useful for doing API call
        // TODO: maybe use run() in change()/change2() ?
        
        // Important! run(): use of ajax object for getting content must match how it is used in hmCon.change() for all pages & content types

        counter: 0,
        query: {},
        con: null, // the larger controller

        clear: function () {
            this.counter++;
            this.query = {};
            this.con = hmCon;
            return this;
        },
        getJob: function() {
            return this.counter;
        },
        add: function(query) {
//jobs.add(page, this.query.pageNum, this.query.site, type, this.query.order, this.query.details.catSelect.id);
            this.query = query;
        },
        run: function(jobId, type, selector, missingImgs, ajaxObj, successCallback) {
            if (jobId !== this.counter) {
                // this job expired
console.log('job no longer for current page! mine:' + jobId + ' vs ' + this.counter + ' (user changed page?)');
                return false;
            }
            var query = this.query;
//console.log('run the query for just the type asked... ' + type);console.log(this.query);
            var successOptions = {
                //some data for success
                page: query.page,
                type: query.type,
                selector: selector,
                type: type,
                jobId: jobId,
                missingImgs : missingImgs
            };
            switch (query.page) {
                case 'model':
                case 'video':
                case 'dvd':
                case 'mag':
                case 'photo':
                    //ajaxObj.clearScenes(); // we won't use scenesLoaded with job results anyway
                    ajaxObj.setSite(query.site);
                    ajaxObj.setType(type);
                    ajaxObj.setCode('retry'); // actually does not do anything, just easy to notice
                    ajaxObj.setOrder(query.order);
                    // set further filters if available (category)... models, magazines should NOT do this
                    if (query.page != 'model' && query.page != 'magazine' && query.catId != 0) {
                        ajaxObj.setCategory(query.details.catSelect.id);
                    }
                    ajaxObj.get(
                        query.pageNum, 
                        false,
                        [
                            // success callback functions
                            successCallback,
                        ],
                        successOptions
                    );
                    break;

                case 'd':
                case 'c':
                case 'm':
                case 'fav':
                    ajaxObj.setSite(query.site);
                    ajaxObj.setOrder(query.order);
                    ajaxObj.setCode('retry'); // actually does not do anything, just easy to notice
                    if (query.page == 'fav' && query.details.catSelect.id != 0) { // for faves, filter by category
                        ajaxObj.setCategory(query.details.catSelect.id);
                    }
                    switch (query.page) {
                        case 'd':
                            ajaxObj.getDvd(query.details.dvdSelect.id, 1, successCallback, successOptions);
                            return true;
                            // 'd' page is done!

                        case 'c': // CATEGORIES
                            ajaxObj.setCategory(query.details.catSelect.id);
                            break;
                        case 'm': // MODELS
                            ajaxObj.setModel(query.details.modSelect.id);
                            break;
                        case 'fav': // FAVORITES
                            ajaxObj.setSite('hustler'); // ...or use hustler??
                            ajaxObj.setFav();
                            break;
                    }
                    ajaxObj.setType(type);
                    ajaxObj.get(
                        this.con.getPageNum(query, type), 
                        false,
                        [
                            // success callback functions
                            successCallback,
                        ],
                        successOptions
                    );
                    break;
                case 'search':
                    ajaxObj.setSearchString(query.details.text.str);
                    //ajaxObj.setSite(query.site);
                    var code = '';
                    switch (type) {
                        case 'photos':
                            code = 'searchPhotos';
                            break;
                        case 'models':
                            code = 'searchModels';
                            break;
                        case 'dvds':
                            code = 'searchDvds';
                            break;
                        case 'videos':
                            code = 'searchVideos';
                            break;
                        default:
                    }
                    ajaxObj.setCode(code); // actually does not do anything, just easy to notice
                    ajaxObj.setType(type);
                    ajaxObj.get(
                        this.con.getPageNum(query, type), 
                        false,
                        [
                            // success callback functions
                            successCallback,
                        ],
                        successOptions
                    );
                    break;
                case 'home':
                    ajaxObj.setOrder(query.order);
                    ajaxObj.setType(type);
                    if (type == 'mags') {
                        ajaxObj.setCode('homeretry'); // actually does not do anything, just easy to notice
                    } else {
                        ajaxObj.setCode('retry'); // actually does not do anything, just easy to notice
                    }
                    if (query.site != undefined) {
                        ajaxObj.setSite(this.query.site);
                    }
                    if (query.details.catSelect.id != 0) {
                        ajaxObj.setCategory(query.details.catSelect.id);
                    }
                    // run ajax object!!!!
                    ajaxObj.get(
                        query.pageNum,
                        false,
                        [
                            // success callback functions
                            successCallback,
                        ],
                        successOptions
                    );
                    break;
                default:
console.log('this page "' + query.page + '" (w/ content type "' + type + '") does not support an API reload');
                    break;
            }
            return true;
        }
    },

    init: function (urlAjax) {
        this.ajax.setUrl(urlAjax);
        this.clearQuery();
        
        // get querystrings, if passed
        if ( !Array.prototype.forEach ) { // IE8- support
            Array.prototype.forEach = function(fn, scope) {
                for(var i = 0, len = this.length; i < len; ++i) {
                    fn.call(scope, this[i], i, this);
                }
            }
        }
        var result = {}, keyValuePairs = location.search.slice(1).split('&');
        keyValuePairs.forEach(function(keyValuePair) {
             keyValuePair = keyValuePair.split('=');
             result[keyValuePair[0]] = keyValuePair[1] || '';
        });
        this.queryStringCode = result;
        //console.log('query string:'); console.log(this.queryStringCode);

        // define some html widgets
        this.htmlWidgets = {
            'pagination': function(pagebarId, current, max) {
                var disableL = ' ';
                if (current == 1) {
                    disableL = 'ui-disabled ';
                }
                var disableR = ' ';
                if (current == max) {
                    disableR = 'ui-disabled ';
                }
                return ' \
                    <!-- Pagination --> \
                    <form action="/" method="get" id="' + pagebarId + '"> \
                        <div data-role="fieldcontain" class="pagination"> \
                            <a href="#" data-role="button" data-inline="true" data-icon="arrow-l" data-iconpos="left" data-theme="a" data-corners="false" class="' + disableL + 'prev">Prev</a> \
                            <label for="slider-pagination" class="ui-hidden-accessible">Pagination:</label> \
                            <input type="range" name="slider-pagination" value="' + current + '" min="1" max="' + max + '" data-show-value="false" data-popup-enabled="true" data-theme="a" data-track-theme="a" /> \
                            <div data-role="controlgroup" data-type="horizontal" data-corners="false"> \
                                <input type="submit" value="Go" data-inline="true" data-theme="a"/> \
                                <a href="#" data-role="button" data-inline="true" data-icon="arrow-r" data-iconpos="right" data-theme="a" class="' + disableR + 'next">Next</a> \
                            </div> \
                        </div> \
                    </form> \
                </div>';
            },

            'disclaimer': $('.sub-footer').clone(), // assume that disclaimer is on the original index page source code
                                                    // otherwise, use disclaimer_hardcode below...
            'disclaimer_hardcode': '<div class="sub-footer"> \
                    <p><a href="http://www.hustler.com/2257.html" target="_blank" class="ui-link">18 U.S.C 2257 Record-Keeping Requirements Compliance Statement</a></p> \
                    <p>All models appearing on this site are 18 or older. © 2013 LFP Internet Group, LLC, All Rights Reserved</p> \
                    <p>LFP Internet Group, LLC, 8484 Wilshire Blvd. #900, Beverly Hills, CA 90211 USA</p> \
                    <p>Vendo is our authorized reseller</p> \
                    <nav class="sub-footer-nav" role="navigation"> \
                        <a href="http://www.hustler.com/privacy.html" target="_blank">Privacy Policy</a> | \
                        <a href="http://hustler.com/rsaci.html" target="_blank">Parental Blocking</a> | \
                        <a href="http://www.lfpcareers.com/" target="_blank">Employment</a> | \
                        <a href="mailto:vhurley@lfp.com">Media Inquiries</a> | \
                        <a href="http://www.hustler.com/model" target="_blank">Models Wanted</a> | \
                        <a href="http://hustlercash.com/" target="_blank">Affiliate Program</a> \
                    </nav> \
                </div>',
            'joinButton' : '<a href="' + joinPad + '" class="member-join ui-btn ui-shadow ui-btn-corner-all ui-btn-icon-right ui-last-child ui-btn-up-a" data-role="button" data-icon="plus" data-iconpos="right" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a"><span class="ui-btn-inner"><span class="ui-btn-text">Join</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a>',
            
            'dvdSlider' : '<!-- DVD / Video toggle switch OFF --><form action="#" method="get"><div data-role="fieldcontain" class="flip-switch"><label for="dvds">Switch to DVDs:</label> \n\
                <select name="dvds" id="dvds" data-role="slider" data-inline="true" data-track-theme="a" data-theme="a"> \n\
                <option value="off">Videos</option><option value="on">DVDs</option></select></div></form>',
            
            'videoSlider' : '<!-- DVD / Video toggle switch ON --><form action="#" method="get"><div data-role="fieldcontain" class="flip-switch"><label for="dvds">Switch to Videos:</label> \n\
                <select name="dvds" id="dvds" data-role="slider" data-inline="true" data-track-theme="a" data-theme="a"> \n\
                <option value="off">Videos</option><option value="on">DVDs</option></select></div></form>',

            'tourSplash': '<div class="content-guts content-tour"> \n\
				<div class="custom-slider"> \n\
					<div class="slide-content"> \n\
						<h1>Porn, Porn, and More Porn on the Go, <em>Instantly.</em></h1> \n\
						<p>Bookmark Hustler Web-App to take Porn on-the-go with your full account membership in your pocket or on your tablet.</p> \n\
						<a href="#" id="splash-watch" data-role="button" data-inline="true" data-theme="a" data-icon="arrow-b" data-iconpos="right" data-corners="false" data-shadow="true" data-iconshadow="true" data-wrapperels="span" class="ui-btn ui-btn-up-a ui-shadow ui-btn-inline ui-btn-icon-right"><span class="ui-btn-inner"><span class="ui-btn-text">Watch Now!</span><span class="ui-icon ui-icon-arrow-b ui-icon-shadow">&nbsp;</span></span></a> \n\
					</div> \n\
					<div class="slide-image"> \n\
						<img src="images/tour-assets/ipad.jpg" alt="Huslter iPad App"> \n\
					</div> \n\
				</div> \n\
				<div class="ui-grid-a"> \n\
					<div class="tour-block-hd ui-block-a"> \n\
						<h3>Porn with a <em>New</em> Experience in <span class="hd-icon">HD</span></h3> \n\
						<p>Enjoy your porn in a whole new way. Simplified so you just get what you need when you want it. With all new features to explore.</p> \n\
						<img src="images/tour-assets/ipad-hd.png" alt="Huslter iPad App"> \n\
						<a href="#"  id="splash-explore" class="explore-btn ui-link">Explore Now!</a> \n\
					</div> \n\
					<div class="tour-block-sites ui-block-b"> \n\
						<h3>Enjoy Barely Legal and 20+ Sites All-in-One Site!</h3> \n\
						<p>Enjoy all your favorite websites in one place. With easy navigation, seamlessly transfer from one site to the other to find what your looking for.</p> \n\
						<img src="images/tour-assets/sites-ui.png" alt="Huslter iPad App"> \n\
						<a href="#" id="splash-site" class="explore-btn ui-link">Go to Sites Page!</a> \n\
					</div> \n\
				</div> \n\
				<div class="ui-grid-a"> \n\
					<div class="tour-block-mag ui-block-a"> \n\
						<h3>The Magazine that Started it All</h3> \n\
						<p>Hustler Magazine, Barely Legal and Taboo magazines are all available online for your viewing and downloading pleasure.</p> \n\
						<img src="images/tour-assets/mag-ui.png" alt="Huslter iPad App"> \n\
						<a href="#" id="splash-mag" class="explore-btn ui-link">View Magazines!</a> \n\
					</div> \n\
					<div class="tour-block-search ui-block-b"> \n\
						<h3>A One-Stop Search for Everything</h3> \n\
						<p>It\'s never been easier. Find your favorite on-demand movies, dvds, photos, categories, models - Everything all from one place!</p> \n\
						<img src="images/tour-assets/search-ui.png" alt="Hustler iPad App"> \n\
						<a href="#" id="splash-search" class="explore-btn ui-link" data-rel="popup" data-position-to="window" data-transition="slidedown">Search Everything!</a> \n\
					</div> \n\
				</div> \n\
			</div>'
        }
        
        //load some static data

        // categories
        this.staticData = {
            'categories' : [],
            'sites' : hustlerSites
        };

        var tmp = hustlerCategories;
        for(var i in tmp) {
            var arr = i.split(':');
            var tmp2 = arr[1].toString();
            var tmp3 = arr[0].toString();
            var tmp4 = { 'name': tmp2, 'fullName': tmp[i] };
            this.staticData.categories[tmp3] = tmp4;
        }
        
        
        this.staticData.getCatFull= function (id) {
            //globals: hustlerCategories
            var ret = null;
            $.each(hustlerCategories, function(i, val) {
                var tmp = i.split(':');
                if (parseFloat(tmp[0]) == id) {
                    ret = val;
                }
            });
            return ret;
        };
        
        this.staticData.getSiteName = function (siteCode) {
            //globals: hustlerSites
            var ret = null;
            $.each(hmCon.staticData.sites, function(i, val) {
                if (i == siteCode) {
                    ret = val;
                }
            });
            return ret;
        };
    },

    change: function(skipFade) {
        if (skipFade === undefined || skipFade !== true) {
            //$('#content').css('opacity', '0.5');
	    $.mobile.showPageLoadingMsg();
            $('#content').fadeTo(250, .04,  function() {
                //$("html, body").animate({ scrollTop: 0 }, "slow"); // scroll to top
                hmCon.change2();
            });// fade out content page, speed in milliseconds. Opacity .2            
        } else {
            this.change2();
        }
    },

    change2: function () {
        var msg = "any message for loading screen?";
        var pageLoaded = function (msg) {
	    $.mobile.silentScroll(0); //jquery mobile way to scroll to top
            $.mobile.hidePageLoadingMsg();
            //$('#content').css('opacity', '1');
            $('#content').fadeTo(250, 1,  $.noop());//  fade out content page, speed in milliseconds. Opacity 1
        };
        var page = this.query.page;
        if (page === undefined) page = 'home';
        
        // do site logo
        $('.sub-logo').css('background-image', 'url("images/logos/' + hmCon.query.site + '.png")');



        // COOKIE -- determines if we will do a popup. Also has query so we know whether we are just doing a page change
        var prevSearchCookie = 'hPrevSearch';
        switch (page) {
            case 'c':
            case 'm':
            case 'search':
                var oldQuery = null;
                var newQuery = clone(this.query);
                $.setCookie('searchResults', 2); // this is for collapsing the results of 2 content
                if ($.getCookie(prevSearchCookie) && JSON.parse($.getCookie(prevSearchCookie)) != null) {
                    oldQuery = JSON.parse($.getCookie(prevSearchCookie)).query;
                    if (oldQuery === undefined || oldQuery.length == 0) oldQuery = null;
//console.log('SEARCH cookie: previous one...');
//console.log(oldQuery);
                }
                // set cookie for next page/url change...
                $.setCookie(prevSearchCookie, JSON.stringify({
                    query: newQuery
                }));
//console.log('SEARCH cookie: set new one...');
//console.log(JSON.parse($.getCookie(prevSearchCookie)));
                break;

            case 'fav': // fav page much the same as above SEARCH pages, but has more content areas!
                var oldQuery = null;
                var newQuery = clone(this.query);
                $.setCookie('searchResults', 4); // this is for collapsing the results of 2 content
                if ($.getCookie(prevSearchCookie) && JSON.parse($.getCookie(prevSearchCookie)) != null) {
                    oldQuery = JSON.parse($.getCookie(prevSearchCookie)).query;
                    if (oldQuery === undefined || oldQuery.length == 0) oldQuery = null;
//console.log('SEARCH cookie: previous one...');
//console.log(oldQuery);
                }
                // set cookie for next page/url change...
                $.setCookie(prevSearchCookie, JSON.stringify({
                    query: newQuery
                }));
//console.log('SEARCH cookie: set new one...');
//console.log(JSON.parse($.getCookie(prevSearchCookie)));
                break;

            case 'home':
                break;
            case 'd': // dvd search results page will only show videos
            default:
                $.setCookie('prevPage', 'not search');
                // clear search cookie because we changed to a non-search page
                $.setCookie(prevSearchCookie, null); // remove cookie
//console.log('SEARCH cookie CLEARED');
                break;
        }
        // End COOKIE (popup, saved query)


        // DETERMINING PAGE NUMBER CHANGES (and how what API calls to do, if not all content)...
        // compare previous search pages only so we can see if it's just a page change
        // oldQuery, newQuery -- these are determined in the POPUP/cookie section above
        var searchContent;
        switch (page) {
            case 'c':
            case 'm':
            case 'search':
                // 1. by default, search for all content...
                searchContent = '*';  // * = all content
                clearPage = 0;
                clearPageCount = 4;  // important to clear page in initPage() only once
                // 2. ...but now compare queries to see if it's just a page change
                
                var oldPages = [-1,-1,-1,-1];
                if (page == 'c' || page == 'm') {
                    // until we do magazines...
                    clearPageCount = 3;
                    var oldPages = [-1,-1,-1];
                }
                if (oldQuery != null && oldQuery.length != 0 && oldQuery.pageNum.indexOf(',') > -1) {
                    oldPages = oldQuery.pageNum.split(',');
                }
                var newPages = newQuery.pageNum.split(',');
                if (JSON.stringify(oldQuery) != JSON.stringify(newQuery)) {
//console.log('oldQuery');
//console.log(oldQuery);
//console.log('newQuery');
//console.log(newQuery);
//console.log('oldPages');
//console.log(oldPages);
//console.log('newPages');
//console.log(newPages);
                    if (oldQuery != null) oldQuery.pageNum = null;
                    newQuery.pageNum = null;

                    if (JSON.stringify(oldQuery) == JSON.stringify(newQuery)) {
                        // 3. If it's just a page change, check which pages changed, and adjust searchContent accordingly
                        if (oldPages.length == newPages.length) {
                            searchContent = '';
                            clearPageCount = 0;
                            if (oldPages[0] != newPages[0]) {
                                searchContent += 'v'; // videos
                                clearPageCount++;
                            }
                            if (oldPages[1] != newPages[1]) {
                                searchContent += 'd'; // dvds
                                clearPageCount++;
                            }
                            if (oldPages[2] != newPages[2]) {
                                searchContent += 'p'; // photos
                                clearPageCount++;
                            }
                            if (oldPages[3] != newPages[3]) {
                                searchContent += 'm'; // models
                                clearPageCount++;
                            }
                       }
                    }
                }
//console.log('a - clearPageCount:' + clearPageCount + '   searchContent:' + searchContent);
                break;

            case 'fav': // fav page much the same as above SEARCH pages, but has more content areas!
                // 1. by default, search for all content...
                searchContent = '*';  // * = all content
                clearPage = 0;
                clearPageCount = 5;  // important to clear page in initPage() only once
                // 2. ...but now compare queries to see if it's just a page change
                
                var oldPages = [-1,-1,-1,-1,-1];
                if (oldQuery != null && oldQuery.length != 0 && oldQuery.pageNum.indexOf(',') > -1) {
                    oldPages = oldQuery.pageNum.split(',');
                }
                var newPages = newQuery.pageNum.split(',');
                if (JSON.stringify(oldQuery) != JSON.stringify(newQuery)) {
//console.log('oldQuery');
//console.log(oldQuery);
//console.log('newQuery');
//console.log(newQuery);
//console.log('oldPages');
//console.log(oldPages);
//console.log('newPages');
//console.log(newPages);
                    if (oldQuery != null) oldQuery.pageNum = null;
                    newQuery.pageNum = null;

                    if (JSON.stringify(oldQuery) == JSON.stringify(newQuery)) {
                        // 3. If it's just a page change, check which pages changed, and adjust searchContent accordingly
                        if (oldPages.length == newPages.length) {
                            searchContent = '';
                            clearPageCount = 0;
                            if (oldPages[0] != newPages[0]) {
                                searchContent += 'v'; // videos
                                clearPageCount++;
                            }
                            if (oldPages[1] != newPages[1]) {
                                searchContent += 'd'; // dvds
                                clearPageCount++;
                            }
                            if (oldPages[2] != newPages[2]) {
                                searchContent += 'p'; // photos
                                clearPageCount++;
                            }
                            if (oldPages[3] != newPages[3]) {
                                searchContent += 'm'; // photos
                                clearPageCount++;
                            }
                            if (oldPages[4] != newPages[4]) {
                                searchContent += 'z'; // mags
                                clearPageCount++;
                            }
                       }
                    }
                }
//console.log('b - clearPageCount:' + clearPageCount + '   searchContent:' + searchContent);
                break;

            case 'home':
                clearPageCount = 5; // DVDs, Videos, Photos, Models, Magazines
                clearPage = 0;
                break;
            case 'd': // dvd search results page will only show videos
            default:
                clearPageCount = 1; // DVDs only
                clearPage = 0;
                break;
        }
        // END page numbers

        // before doing ajax query, clear its query parameters...
        // maybe have ajax object have a method to reset query?
        this.ajax.query.filters = [];  
        this.ajax.query.code = null; // maybe have ajax object be able to reset query?
        // type, site, order will get changed later...

        // clear ajax jobs list, starting a new round with a new job ID (which can be used later)
        var jobs = this.jobs;
        var jobId = jobs.clear().getJob();
        jobs.add(this.query); // add to jobs list, which may be useful later for an API retry
        
        // now do the ajax calls
        switch (page) {
            case 'model':
            case 'video':
            case 'dvd':
            case 'mag':
            case 'photo':
                var type = page + 's';
                loadPageCount = 0;
                loadPage = 1; // load one ajax content only (Models, Videos, DVDs, Magazines or Photos)
                var pageInitFn = function(currentPage, totalPages) {
                    hmCon.initPage(page, currentPage, totalPages);
                };
                this.ajax.clearScenes();
                this.ajax.setSite(this.query.site);
                this.ajax.setType(type);
                this.ajax.setOrder(this.query.order);
                // set further filters if available (category)... models, magazines should NOT do this
                if (page != 'model' && page != 'magazine' && this.query.details.catSelect.id != 0) {
                    this.ajax.setCategory(this.query.details.catSelect.id);
                }

                this.ajax.get(
                    this.query.pageNum, 
                    false,
                    [
                        // success callback functions
                        successCallback,
                    ],
                    {
                        //some data for success
                        page: page,
                        type: type,
                        jobId: jobId,
                        pageInitFn: pageInitFn, // run at the start to clear page
                        pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                    }
                );
                break;

             case 'photodetail':  // i.e. the photos belonging to each photoset
             
                var type = 'photodetail';
                loadPageCount = 0;
                loadPage = 1; // load one ajax content only (Models, Videos, DVDs or Photos)
                var pageInitFn = function() {
                    hmCon.initPage(page);
                };

                this.ajax.clearScenes();
                this.ajax.setType(type);
                this.ajax.setSite(this.query.site);
                this.ajax.setOrder(this.query.order);
                this.ajax.setCode('listing=detail');
                this.ajax.setPhotosetSceneId(this.query.scene_id);
                this.ajax.get(
                    this.query.pageNum, 
                    false,
                    [
                        // success callback functions
                        successCallback,
                    ],
                    {
                        //some data for success
                        page: page,
                        type: type,
                        pageInitFn: pageInitFn, // run at the start to clear page
                        pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                    }
                );
                break;
                
            // search/multi-content pages
            case 'd':
            case 'c':
            case 'm':
            case 'fav':
                //this.ajax.clearScenes(); // TODO: we need a way to clear only data of content we asked for
                this.ajax.setSite(this.query.site);
                this.ajax.setOrder(this.query.order);
                this.ajax.setCode('a');
                if (page == 'fav' && this.query.details.catSelect.id != 0) { // for faves, filter by category
                    this.ajax.setCategory(this.query.details.catSelect.id);
                }

                if (page == 'd') {
                    this.ajax.clearScenes();
                    // DVD has a few content...
                    var type = 'videos';
                    var callback = [
                        successSearch // we actually get videos for this call!
                    ];
                    var pageInitFn = function(currentPage, totalPages, contentId) {
                        hmCon.initPage(page, currentPage, totalPages, contentId);
                    };
                    var successOptions = {
                        page: page,
                        type: type,
                        jobId: jobId,
                        pageInitFn: pageInitFn, // run this to clear screen
                        pageLoadedFn: pageLoaded // run this after content is loaded
                    };
                    this.ajax.getDvd(this.query.details.dvdSelect.id, 1, callback, successOptions);
                    break;
                }
//console.log('page "' + page + '" has gotten this far...');
                switch (page) {
                    case 'c':
                        // CATEGORIES
//console.log('SEARCH page: getting all category content...');
                        this.ajax.setCategory(this.query.details.catSelect.id);
                        break;
                    case 'm':
                        // CATEGORIES
//console.log('SEARCH page: getting all model content...');
                        this.ajax.setModel(this.query.details.modSelect.id);
                        break;
                    case 'fav':
                        // FAVORITES
//console.log('SEARCH page: getting all fav content...');
                        //this.ajax.setSite(null); // clear site, not used
                        this.ajax.setSite('hustler'); // ...or use hustler??
                        this.ajax.setFav();
                        break;
                }
                if (page == 'm') {
                    var fnClearScreen = function (currentPage, totalPages, contentId) {
                        hmCon.fnClearSearchScreen('m', currentPage, totalPages, contentId);
                    };
                } else {
                    var fnClearScreen = this.fnClearSearchScreenClosure(page);
                }
                loadPage = clearPageCount;
                loadPageCount = 0;
                var apiDelay = 0;
                if(searchContent == '*' || searchContent.indexOf('v') > -1) {
                    if (searchContent.indexOf('v') > -1) {
                        fnClearScreen = function () {
                            // clear just the VIDEO results area
                            $('.content-guts .video-search').empty();
                        }
                    }
                    var type = 'videos';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                page: page,
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            }
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                }
                if(searchContent == '*' || searchContent.indexOf('d') > -1) {
                    if (searchContent.indexOf('d') > -1) {
                        fnClearScreen = function () {
                            // clear just the DVD results area
                            $('.content-guts .dvd-search').empty();
                        }
                    }
                    var type = 'dvds';
                    this.ajax.setType(type);
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                page: page,
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            }
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                }
                if(searchContent == '*' || searchContent.indexOf('p') > -1) {
                    if (searchContent.indexOf('p') > -1) {
                        fnClearScreen = function () {
                            // clear just the PHOTO results area
                            $('.content-guts .photo-search').empty();
                        }
                    }
                    var type = 'photos';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                page: page,
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            }
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                }
                if (page == 'fav') {
                    // fav page gets a little different... because it has 5 content
                    if(searchContent == '*' || searchContent.indexOf('m') > -1) {
                        if (searchContent.indexOf('m') > -1) {
                            fnClearScreen = function () {
                                // clear just the model results area
                                $('.content-guts .model-search').empty();
                            }
                        }
                        var type = 'models';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                        setTimeout(
                            this.ajaxCaller(
                                this.getPageNum(this.query, type),
                                [successSearch],
                                {
                                    page: page,
                                    type: type,
                                    jobId: jobId,
                                    pageInitFn: fnClearScreen, // run this to clear screen
                                    pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                                }
                            ),
                            apiDelay * multiAjaxInterval
                        );
                        apiDelay++;
                    }
                    if(searchContent == '*' || searchContent.indexOf('z') > -1) {
                        if (searchContent.indexOf('z') > -1) {
                            fnClearScreen = function () {
                                // clear just the mag results area
                                $('.content-guts .mag-search').empty();
                            }
                        }
                        var type = 'mags';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                        setTimeout(
                            this.ajaxCaller(
                                this.getPageNum(this.query, type),
                                [successSearch],
                                {
                                    page: page,
                                    type: type,
                                    jobId: jobId,
                                    pageInitFn: fnClearScreen, // run this to clear screen
                                    pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                                }
                            ),
                            apiDelay * multiAjaxInterval
                        );
                        apiDelay++;
                    }
                }
                break;
            case 'search':
                //console.log("sc at 790: " + searchContent);
                //console.log('SEARCH page: getting all model content...');
                this.ajax.setSearchString(this.query.details.text.str);
                typesBitfield = this.query.details.text.whichTypes;
                
                var pageChange = false;
                if (searchContent !== '*' && clearPageCount > 0) {
                    // it's been determined, earlier/above, that this is just a page change
                    pageChange = true;
                }

// if this is a new search, not just a page change, then re-figure searchContent, clearPageCount
if (pageChange == false) {
                //console.log("sc at 802: " + searchContent);
                if ( typesBitfield == 15 ) {
                    searchContent = '*'; // videos
                    clearPageCount = 4;
                }
                else {
                    clearPageCount = 0;
                    if ( typesBitfield & 1 ) {
                        searchContent += 'v'; // videos
                        clearPageCount++;
                    }
                    if ( typesBitfield & 2 ) {
                        searchContent += 'd'; // dvds
                        clearPageCount++;
                    }
                    
                    if ( typesBitfield & 4 ) {
                        searchContent += 'm'; // models
                        clearPageCount++;
                    }
                    if ( typesBitfield & 8 ) {
                        searchContent += 'p'; // photos
                        clearPageCount++;
                    }
                }
}

                var fnClearScreen = this.fnClearSearchScreenClosure(page);
                var apiDelay = 0;
                loadPage = clearPageCount;
                loadPageCount = 0;
                this.ajax.setSearchCategory(this.query.details.text.category_id);
                if(searchContent == '*' || searchContent.indexOf('v') > -1) {
                    if (pageChange) {
                        fnClearScreen = function () {
                            // clear just the VIDEO results area
                            $('.content-guts .video-search').empty();
                        }
                    }
                    var type = 'videos';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            },
                            "searchVideos"
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                }
                if ( searchContent == '*' || searchContent.indexOf('d') > -1 ) {
                    if (pageChange) {
                        fnClearScreen = function () {
                            // clear just the dvd results area
                            $('.content-guts .dvd-search').empty();
                        }
                    }
                    var type = 'dvds';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            },
                            "searchDvds"
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                
                } 
                
                if ( searchContent == '*' || searchContent.indexOf('p') > -1 ) {
                    if (pageChange) {
                        fnClearScreen = function () {
                            // clear just the photo results area
                            $('.content-guts .photo-search').empty();
                        }
                    }
                    var type = 'photos';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            },
                            "searchPhotos"
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                
                } 
                if ( searchContent == '*' || searchContent.indexOf('m') > -1 ) {
                    if (pageChange) {
                        fnClearScreen = function () {
                            // clear just the model results area
                            $('.content-guts .model-search').empty();
                        }
                    }
                    var type = 'models';
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval);
                    setTimeout(
                        this.ajaxCaller(
                            this.getPageNum(this.query, type),
                            [successSearch],
                            {
                                type: type,
                                jobId: jobId,
                                pageInitFn: fnClearScreen, // run this to clear screen
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            },
                            "searchModels"
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                }
                break;
            // sites
            case 'site':
                this.initPage(page);
                this.printSites();
                pageLoaded();
                break;
            case 'site_OLD':
                // this one gets a list of sites sorted by latest update then shows a page with the latest scene for each site
                this.initPage(page);
                var options = {};
                this.ajax.getSitesList(options, successSites);
                break;
            // categories list
            case 'cat':
                this.initPage(page);
                this.printCategories();
                pageLoaded();
                break;
            // join
            case 'join':
                window.location = joinPad;
                break;
            // home
            case 'home':
            default:
                if (this.tourSplash() == true) {
                    // TOUR splash page
                    loadPageCount = 0;
                    loadPage = 0;
                    // load page
                    var pageInitFn = this.fnClearSearchScreenClosure(page);
                    pageInitFn();
                    // bind clicks
                    $('#splash-watch').on('vclick', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        location.reload();
                    });
                    $('#splash-explore').on('vclick', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        location.reload();
                    });
                    $('#splash-search').on('vclick', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        $('.search-btn').trigger("click");
                    });
                    $('#splash-site').on('vclick', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        $('.apple-navbar-ui a[data-icon="site"]').trigger("vclick");
                    });
                    $('#splash-mag').on('vclick', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        $('.apple-navbar-ui a[data-icon="mag"]').trigger("vclick");
                    });
                    // finish loading
                    var pageLoadedFn = this.fnSearchScreenLoaded(pageLoaded);
                    pageLoadedFn();
                    break;
                }
                if (this.query.pageNum == 1) {
                    // clear contents
                    this.ajax.clearScenes();
                    // back to beginning: Videos, DVDs. etc
                    $('.video-carousel .touchcarousel-container').css('left', '0');
                    $('.dvd-carousel .touchcarousel-container').css('left', '0');
                    $('.photo-carousel .touchcarousel-container').css('left', '0');
                    $('.model-carousel .touchcarousel-container').css('left', '0');
                    $('.mag-carousel .touchcarousel-container').css('left', '0');
                }
//console.log('changing home content...');
                if (this.query.site != undefined) {
                    this.ajax.setSite(this.query.site);
                }
                if (this.query.details.catSelect.id != 0) {
                    this.ajax.setCategory(this.query.details.catSelect.id);
                }
                loadPageCount = 0;
                loadPage = 4; // five ajax content: Videos, DVDs, Photos, Models, Magazines
                var pageInitFn = this.fnClearSearchScreenClosure(page);

                // do an ajax call for each type of content...
                var homeTypes = ['videos', 'dvds', 'photos', 'models', 'mags'];
                var apiDelay = 0;
                for (var t in homeTypes) {
                    var type = homeTypes[t];
                    var code = undefined;
                    if (type == 'mags') {
                        code = 'home';
                    }
//console.log('fire ' + type + ' at ' + apiDelay * multiAjaxInterval + ' code ' + code);
                    setTimeout(
                        this.ajaxCaller(
                            this.query.pageNum,
                            [successCallback],
                            {
                                page: page,
                                type: type,
                                jobId: jobId,
                                pageInitFn: pageInitFn, // run at the start to clear page
                                pageLoadedFn: this.fnSearchScreenLoaded(pageLoaded) // run this after content is loaded
                            },
                            code
                        ),
                        apiDelay * multiAjaxInterval
                    );
                    apiDelay++;
                }
                break;                
        }

        // change dropdowns for category or order
        if (page == 'home') {
            this.updateUiSort('category');
            $('#sort').off("change");
            $('#sort').on("change", {}, function(e) {
                hmCon.changeCat($('#sort').val(), $('#sort option:selected').text()); // home page isn't ORDER, it's categories!
                e.stopImmediatePropagation();
                e.stopPropagation();
            });
        } else if (page == 'mag') {
            this.updateUiSort('mag');
            $('#sort').off("change");
            $('#sort').on("change", {}, function(e) {
                hmCon.changeSite($('#sort').val(), $('#sort option:selected').text()); // home page isn't ORDER, it's categories!
                e.stopImmediatePropagation();
                e.stopPropagation();
            });
        } else {
            if (page == 'model' || page == 'm') {
                this.updateUiSort('order');
            } else {
                // deluxe dropdown
                this.updateUiSort('deluxe');
            }
            $('#sort').off("change");
            $('#sort').on("change", {}, function(e) {
                hmCon.changeOrder($('#sort').val()); // home page isn't ORDER, it's categories!
                e.stopImmediatePropagation();
                e.stopPropagation();
            });
        }
        
        this.updateUiHeader(page);

    },
    
    ajaxCaller: function(pageNum, callbacks, data, code) {
        // callbacks - array (or not) to be executed after ajax call, upon success (or not, it will check for errors)
        // data - some data to be used by success callback
        var myCon = this;
        return function() {
            if (code !== undefined) {
                myCon.ajax.setCode(code);
            }
            myCon.ajax.setType(data.type);
            myCon.ajax.get(pageNum, false, callbacks, data);
        };
    },
    
    // change methods must be activated by the observerables


    getHash: function () {
        this.hash.getQuery(this);
    },
    
    changePageNum: function (pageNum) {
        this.query.pageNum = pageNum;
//console.log('pageNum change: ' + pageNum);
//console.log(this.query);
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    },
    
    changeSearchPageNum: function (page, conType, pageNum) {
        var pages = this.query.pageNum.split(',');
//console.log('pages BEFORE');
//console.log(pages);
        switch (page + '-' + conType) {
            // maybe some pages use different formats??
            case 'c-videos': // category results VIDS
            case 'd-videos': // dvd results VIDS
            case 'm-videos': // model results VIDS
            case 'fav-videos': // fave results VIDS
            case 'search-videos': // search results VIDS
                pages[0] = pageNum;
                break;
            case 'c-dvds': // category results DVDs
            case 'd-dvds': // dvd results DVDs
            case 'm-dvds': // model results DVDs
            case 'fav-dvds': // fave results DVDs
            case 'search-dvds': // search results DVDs
                pages[1] = pageNum;
                break;
            case 'c-photos': // category results PHOTOSETS
            case 'd-photos': // dvd results PHOTOSETS
            case 'm-photos': // model results PHOTOSETS
            case 'fav-photos': // fave results PHOTOSETS
            case 'search-photos': // search results PHOTOSETS
                pages[2] = pageNum;
                break;
            case 'fav-models': // fave results MODELS
            case 'search-models': // search results MODELS
                pages[3] = pageNum;
                break;
            case 'fav-mags': // fave results MAGAZINES
                pages[4] = pageNum;
                break;
            default:
//console.log('I dont understand how to change page for page "' + page + '" and content type "' + conType + '" ');
        }
        this.query.pageNum = pages.join(',');
//console.log('pages AFTER');
//console.log(pages);
//console.log(this.query.pageNum)
//console.log('pageNum change: ' + this.query.pageNum);
//console.log(this.query);
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    },

    changePage: function (page) {
	var multiPageNum = false;
	if (page == 'fav') {
		multiPageNum = true;
		this.query.pageNum  = '1,1,1,1,1';
	}
	if (page == 'search') {
	   //console.log(' in page = search');
		multiPageNum = true;
		this.query.pageNum  = '1,1,1,1';
	}
	//console.log('page: ' + page + '  multiple content:' + multiPageNum);

        if (1 == 1) { // clear the filters (categories)...
            var keepCat = false;
//            // check if page was changed from Video:Videos to Video:DVDs
//            if ((this.query.page == 'video' || this.query.page == 'dvd') && 
//                    (page == 'video' || page == 'dvd')) {
//                    keepCat = true;
//            }
//            // check if we came from the home page (and not going to home page again)
//            if (this.query.page == 'home' && page != 'home') {
//                keepCat = true;
//            }
            // check if we are going to video, dvd, or photo pages
            if (page == 'video' || page == 'dvd' || page == 'photo') {
                keepCat = true;
            }
            // check if we are going to a category page
            if (page == 'c') {
                keepCat = true;
            }

            if (keepCat === true) {
                // keep filters
            } else {
//console.log('clearing category filter');
                this.query.details.catSelect.id = 0;
                this.query.details.catSelect.name = '';
                this.query.details.catSelect.fullName = '';
            }
        }


        this.query.page = page;
        this.resetPages(multiPageNum);
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    },
    
    changePageListingDetail: function (page, id, scene_name) {
//console.log('page: ' + page);
        this.query.page = page;
        this.resetPages(false);
        this.query.filter = id;
        this.query. code = 'listing=detail';
        this.query.scene_name = scene_name;
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    }, 
            
    changeSite: function (site) {
//console.log('site: ' + site);
//console.log(this.query);
        this.query.site = site;
        this.resetPages();
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    },

    changeCat: function (cat, value) {
//console.log('cat: ' + cat + ' value:' + value);
        var ele = cat.split(':');
        this.query.details.catSelect.id = ele[0];
        this.query.details.catSelect.name = ele[1];
        this.query.details.catSelect.fullName = value;
        this.resetPages();
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    },

    changeOrder: function (order) {
        // Exception! check if it's a deluxe "order" dropdown with category selected!
        if (order.indexOf(":") !== -1) {
            // it's a category!
            this.changeCat(order, hustlerCategories[order]);
            return false;
        }
//console.log('order: ' + order);            
        this.query.order = order;
        this.resetPages();
        this.hash.set(this.query);
        //this.change();  // no need to do .change() since we have onhashchange() event
    },
    
    resetPages: function(multi) {
        // reset pages to 1
        if (multi === false) {
            this.query.pageNum = 1;
            return true;
        }
//console.log('let us reset page(s): ' + this.query.pageNum);
        if (this.query.pageNum.toString().indexOf(',') > -1) {
            // multi page format. So reset all to 1.
            var pages = this.query.pageNum.split(',');
//console.log(pages);
            for (var x = 0; x < pages.length; x++) {
                pages[x] = 1;
            }
            this.query.pageNum = pages.join(',');
//console.log('reset multi pages format to ' + this.query.pageNum);
        }  else {
            // regular page format. Just one number.
            this.query.pageNum = 1;
        }
    },

    search: function(type, value, options, sort) {
        // types:
        // search (text): value - string to search
        // m (model): value - model ID
        // d (dvd): value - group ID
        // c (categories): value - catID
        var site = this.query.site;
        // clear query first
        this.clearQuery();
        this.query.page = type;
        this.query.order = 0; // for now just sort by latest ?
        this.query.site = site;
        
        switch (this.query.page) {
            case 'search':

                this.query.details.text.str = value;
               
                var typesToSearch = 0; // using bitfield to contain the possible combinations of videos/dvds/models/photos that user chooses
                if (options.videos == 1) {
                    typesToSearch ^= 1;
                }
                if (options.dvds == 1) {
                    typesToSearch ^= 2;
                }
                if (options.models == 1) {
                     typesToSearch ^= 4;
                }
                if (options.photos == 1) {
                     typesToSearch ^= 8;
                }
                this.query.details.text.whichTypes = typesToSearch;
                //alert(this.query.details.text.whichTypes);
                this.query.details.text.category_id = options.category_id;
                break;
            case 'c':
                this.query.details.catSelect.id = value;
//                this.query.details.catSelect.name = value;
//                this.query.details.catSelect.fullName = value;
                break;
            case 'd':
                this.query.details.dvdSelect.id = value;
                this.query.details.dvdSelect.name = options.nameSeo;
                break;
            case 'm':
                this.query.details.modSelect.id = value;
                this.query.details.modSelect.name = options.nameSeo;
                break;
            default:
//console.log('NO GOOD SEARCH PARAMETERS!');
                break;
        }
        // page numbers have a strange format...
        if (this.query.page == "search")
            this.query.pageNum = '1,1,1,1';
        else
            this.query.pageNum = '1,1,1';
        this.hash.set(this.query);
    },
    
    fav: function(order) {
        // there isn't really much to do here actually
        
        // clear query first
        this.clearQuery();
        this.query.page = 'fav';
        this.query.order = order;
//console.log();
        //this.query.site = 'hustler'; // no site for now
        // page numbers have a strange format...
        this.query.pageNum = '1,1,1,1';
        this.hash.set(this.query);
    },
    
    goHome: function() {
        // home page of currently selected site, clear everything else
        var site = this.query.site;
        this.clearQuery();
        this.query.site = site;
        this.changePage('home');
    },
    
    initPage: function (page, currentPage, totalPages, contentId) {
        // optional: currentPage, totalPages, contentId (for more details of a DVD, model)
        var content;
        //var siteName = this.staticData.getSiteName(this.query.site);

//console.log('initialize page: ' + page);
        if (page === undefined) { 
            //console.log('ERROR!!!! TODO: initPage() ran without a page?');
            return false;
        }

        var pages = function() {
            if (currentPage !== undefined && totalPages !== undefined) {
                return '<span> (page ' + currentPage + ' of ' + totalPages + ')';
            }
            return '';
        }

        var titles = function(myObj) {
            var q = myObj.query;
            
            // model and DVD pages -- m/   &   d/
            // results page for Model and DVD will get a title later if the fullName is not populated
            if (q.details.modSelect.fullName != '') {
                return q.details.modSelect.fullName
            }
            if (q.details.dvdSelect.fullName != '') {
                return q.details.dvdSelect.fullName
            }

            var txt = '';

            switch (q.page) {
                case 'search':
                    txt += 'Search Results';
                    break;
                case 'c':
                    return myObj.staticData.getCatFull(q.details.catSelect.id);
                    break;
                case 'fav':
                    txt += 'Your Favorites';
                    break;
            }
            
            if (q.details.catSelect.id != 0)
                switch (page) {
                    case 'video':
                    case 'dvd':
                    case 'photo':
                    case 'search':
                    case 'c':
                    case 'fav':
                        txt += ' - ' + myObj.staticData.getCatFull(q.details.catSelect.id);
                        break;
                    default:
                }
            if (q.page == 'search') {
                txt += ' - ' + q.details.text.str;
            }
            return txt;
        }
        
        var order = function (myObj) {
            if (myObj.query.page == 'd') { // dvd title does not need this
                return '';
            } else if (myObj.query.page == 'home') {
                var order = myObj.staticData.getCatFull(myObj.query.details.catSelect.id);
                if (order == 'ALL') return '';
            } else {
                var order = hustlerOrder[myObj.query.order];
                if (order == 'latest updates') order = 'most recent';
            }
            return '<span style="text-transform:capitalize;color:white;"> (' + order + ')</span>';
        }

        
        switch (page) {
            case 'model':
                content = '\
    <div class="content-guts all-models"> \
        <h3>Models' + titles(this) + order(this) + pages() + '</h3> \
    </div>';
                break;
            case 'video':
                content = '\
    <div class="content-guts all-videos"> \
        <h3>Videos' + titles(this) + order(this) + pages() + '</h3> \n\
    </div>';
                break;
            case 'dvd':
                content = '\
    <div class="content-guts all-dvds"> \
        <h3>DVDs' + titles(this) + order(this) + pages() + '</h3> \n\
    </div>';
                break;
            case 'mag':
                content = '\
    <div class="content-guts all-mags"> \
        <h3>Magazines' + titles(this) + pages() + '</h3> \n\
    </div>';
                break;
           case 'photo':
                content = '\
    <div class="content-guts all-photos"> \
        <h3>Photos' + titles(this) + order(this) + pages() + '</h3> \
    </div>';
                break;                
          case 'photodetail':
                content = '\
    <div class="content-guts all-individ_photos"></div>';
                break;                                
                
            // search results pages
            case 'search': // text
            case 'd': // dvd
            case 'm': // model
            case 'c': // category
            case 'fav': // favorites
                content = ' \n\
    <div class="content-guts search"> \n\
        <h3 class="search-title">' + titles(this) + order(this) + '</h3> \n\
            <div class="model-search search-results ui-collapsible ui-collapsible-inset ui-collapsible-themed-content" data-role="collapsible" data-corners="false" data-theme="a" data-content-theme="a" data-collapsed="true"> \n\
            </div> \n\
            <div class="dvd-search search-results ui-collapsible ui-collapsible-inset ui-collapsible-themed-content" data-role="collapsible" data-corners="false" data-theme="a" data-content-theme="a" data-collapsed="true"> \n\
            </div> \n\
            <div class="photo-search search-results ui-collapsible ui-collapsible-inset ui-collapsible-themed-content" data-role="collapsible" data-corners="false" data-theme="a" data-content-theme="a" data-collapsed="true"> \n\
            </div> \n\
            <div class="mag-search search-results ui-collapsible ui-collapsible-inset ui-collapsible-themed-content" data-role="collapsible" data-corners="false" data-theme="a" data-content-theme="a" data-collapsed="true"> \n\
            </div> \n\
            <div class="video-search search-results ui-collapsible ui-collapsible-inset ui-collapsible-themed-content" data-role="collapsible" data-corners="false" data-theme="a" data-content-theme="a" data-collapsed="true"> \n\
            </div> \n\
    </div> \n';
                break;
            case 'site':
                content = '\
    <div class="content-guts all-sites"> \
        <h3>All Sites</h3> \
    </div> ';
                break;
            case 'cat':
                content = '\
    <div class="content-guts"> \
        <h3>Categories</h3> \n\
    </div> ';
                break;
            case 'home':
            default:
                if (this.tourSplash() == true) {
                    content = this.htmlWidgets.tourSplash;
                    $.setCookie('splashExpire', true, splashExpire); // this cookie will disable for 24 hours (splashExpire)
                } else {
                    content = '\
    <div class="content-guts all-videos"> \
        <h3><span class="title-genre" style="color:white;">Latest</span> Videos' + titles(this) + ' ' + order(this) + '</h3> \
            <div id="video-carousel" class="touchcarousel light-skin"> \
            </div> \
    </div> \
    <div class="content-guts all-dvds"> \
        <h3><span class="title-genre" style="color:white;">Latest</span> DVDs' + titles(this) + ' ' + order(this) + '</h3> \
        <div id="dvd-carousel" class="touchcarousel light-skin">    \
        </div> \
    </div> \
    <div class="content-guts all-photos"> \
        <h3><span class="title-genre" style="color:white;">Latest</span> Photos' + titles(this) + ' ' + order(this) + '</h3> \
        <div id="photo-carousel" class="touchcarousel light-skin">    \
        </div> \
    </div> \
    <div class="content-guts all-models"> \
        <h3><span class="title-genre" style="color:white;">Latest</span> Models' + titles(this) + '</h3> \
        <div id="model-carousel" class="touchcarousel light-skin">    \
        </div> \
    </div> \
    <div class="content-guts all-mags"> \
        <h3><span class="title-genre" style="color:white;">Latest</span> Magazines' + titles(this) + '</h3> \
        <div id="mag-carousel" class="touchcarousel light-skin">    \
        </div> \
    </div>';
                }
                break;
        }

        $('#content').empty().append(content).append(this.htmlWidgets.disclaimer);

        // use API to get more info for Model or DVD or...??? (maybe we can put selection details in this page)
        if (page == 'm') {
            var successOptions = {};
            hmajax.getDetailModel(contentId, successOptions, function(json) {
                var clip = this.myobj.parseJSON(json);
                var titleName = clip['info']['contentDetails']['model']['performer_name'];
                $('.search-title').html(titleName + $('.search-title').html());
            });
        } else if (page == 'd') {
            var successOptions = {};
            hmajax.getDetailDvd(contentId, successOptions, function(json) {
                var clip = this.myobj.parseJSON(json);
                var titleName = clip['info']['contentDetails']['dvd']['group_name'];
                //fave button
                //bindFave('dvd', clips['info']['contentDetails']['dvd'], groupId, this.myobj.url);
                $('.search-title').html(titleName + ' DVD ' + $('.search-title').html());
            });            
        }
        this.updateUiFooter(page);
//        $('.pagination').slider('refresh');
        
    },
    
    getPageNum: function (query, type) {
        // when using the multi-page format... for getting correct page number for a content
        var pageNums = query.pageNum.split(',');
        switch (type) {
            case 'videos':
                return  pageNums[0];
                break;
            case 'dvds':
                return pageNums[1];
                break;
            case 'photos':
                return pageNums[2];
                break;
            case 'models': // only 'fav' page supports this content type
                return pageNums[3];
                break;
            case 'mags': // only 'fav' page supports this content type
                return  pageNums[4];
                break;
            default:
                break;
        }
        return 1;
    },

    titlePage: function (title) {
        switch (this.query.page) {
            // search results pages
            case 'search': // text
            case 'd': // dvd
            case 'm': // model
            case 'c': // category
            case 'fav': // favorites
                $('.content-guts.search h3').html(title);
                break;
            default:
                break;
        }
    },
    
    updateUiSort : function (choice) {
        if (choice == 'deluxe') {
            this.updateUiSortDeluxe();
            return false;
        }
        var arr;
        switch (choice) {
            case 'category':
                arr = hustlerCategories;
                break;
            case 'mag':
                arr = hustlerMagazines;
                break;
            default:
            case 'order':
                arr = hustlerOrder;
                break;
        }
	$('#sort').remove();
        if ($.siteType() != 'members') {
            $('.member-join').remove(); // remove the join button
        }
        $('.submenu .ui-controlgroup-controls div.ui-select:eq(1)').remove();        
        $('.submenu .ui-controlgroup-controls').append('<select name="sort" id="sort"></select>');
        $("#sort").empty();
        $.each(arr, function(i, val) {
            var txt = '<option value="' + i + '">' + val + '</option>';
            $("#sort").append(txt);
        });
        $('#sort').selectmenu();
        if ($.siteType() != 'members') {
            $('.submenu .ui-controlgroup-controls').append(this.htmlWidgets.joinButton); // add back the join button
        }
        if (choice == 'category') {
            // set dropdown value to selected category
            $('#sort').val(this.query.details.catSelect.id + ':' + this.query.details.catSelect.name);
        } else if (choice == 'mag') {
            // set dropdown value to selected category
            $('#sort').val(this.query.site);
        } else {
            // set dropdown value to selected order
            $('#sort').val(this.query.order);
        }
        
        //$('#sort').selectmenu('refresh');
    },
    
    updateUiSortDeluxe : function () {
	$('#sort').remove();
	$('.member-join').remove(); // remove the join button
        $('.submenu .ui-controlgroup-controls div.ui-select:eq(1)').remove();        
        $('.submenu .ui-controlgroup-controls').append('<select name="sort" id="sort"></select>');
        $("#sort").empty();

        // order
        var sort = hustlerOrder;
        $("#sort").append('<optgroup label="SHOWING BY: ' + sort[this.query.order].toUpperCase() + '">');
        $.each(sort, function(i, val) {
            var txt = '<option value="' + i + '">' + val + '</option>';
            $("#sort").append(txt);
        });        
        $("#sort").append('</optgroup>');

        // categories
        var txt = '';
        if (this.query.details.catSelect.id != 0) {
            // set dropdown value to selected category
//console.log(hmCon.staticData);
//console.log(hmCon.htmlWidgets);
            txt = this.staticData.getCatFull(this.query.details.catSelect.id);
            //$('#sort').val(this.query.details.catSelect.id + ':' + this.query.details.catSelect.name)
        } else {
            txt = 'ALL';
        }
//console.log(txt);

        $("#sort").append('<optgroup label="SELECTION : *' + txt.toUpperCase() + '*">');
        $.each(hustlerCategories, function(i, val) {
            var txt = '<option value="' + i + '">' + val + '</option>';
            $("#sort").append(txt);
        });        
        $("#sort").append('</optgroup>');


        $('#sort').selectmenu();
        if ($.siteType() != 'members') {
            $('.submenu .ui-controlgroup-controls').append(this.htmlWidgets.joinButton); // add back the join button
        }
        
//        $('#sort').children().each(function() {
//            var groups = 0;
//            console.log(this);console.log($(this));
//            if ($(this).context == 'optgroup') {
//                groups++;
//            } else {
//                if (groups == 2) {
//                    
//                    // we are in second optgroup, aka Categories
//                    $(this).off('select');
//                    $(this).on('select', {}, function(e) {
//                        e.stopImmediatePropagation();
//                        e.stopPropagation();
//                        e.preventDefault();
//console.log('category!!!');
//                    });
//                }
//            }            
//        });
        //console.log('sort is at ' + this.query.order);
        $('#sort').val(this.query.order);
        //$('#sort').selectmenu('refresh');
    },
    
    updateUiFooter: function (page) {
        var footer = page;

        // some exceptions below
        switch (page) {
            case 'dvd':
                footer = 'video';
                break;
            case 'search':
                footer = 'search';
                break;
            case 'c':
                footer = 'cat';
                break;
            case 'd':
                footer = 'video';
                break;
            case 'm':
                footer = 'model';
                break;
            case 'photodetail':
                footer = 'photo';
                break;
            case 'home':
                //if (this.query.site != 'hustler') footer = 'site';
                break;
            default:
                break;

        }
//console.log('update footer!');
        $('div[data-role="footer"] li a').removeClass('ui-btn-active ui-state-persist'); // clear all of special style
        $('div[data-role="footer"] li a[data-icon="' + footer + '"]').addClass('ui-btn-active ui-state-persist'); // chosen one
    },
    
    updateUiHeader : function (page) {
        // disable dropdowns depending on page

        // sites dropdown
        switch (page) {
            case 'mag':
            case 'fav':
            case 'site':
            case 'm':           
                var opacity = '.2';
                $('#sites').attr('disabled',true);
                break;
            default:
                var opacity = '1';
                $('#sites').removeAttr('disabled');
        }
        $('.ui-bar .ui-controlgroup-controls label:eq(0)').css('opacity', opacity);
        $('.ui-bar .ui-controlgroup-controls div:eq(0)').css('opacity', opacity);

        // sort dropdown
        switch (page) {
            case 'site':
                var opacity = '.2';
                $('#sort').attr('disabled',true);
                break;
            default:
                var opacity = '1';
                $('#sort').removeAttr('disabled');
        }
        $('.ui-bar .ui-controlgroup-controls label:eq(1)').css('opacity', opacity);
        $('.ui-bar .ui-controlgroup-controls div:eq(2)').css('opacity', opacity);
    },
    
    tourSplash : function() {
        return false;  // override, we won't use this anymore
        if ($.siteType() == 'members') return false;
        if ($.getCookie('splashExpire') == 'true') return false;
        return true;
    },
    
    printCategories : function () {
        var categories = clone(hustlerCategories);
        delete categories['0:none'];
        
        $.each(categories, function(i, val) {
            var tmp = i.split(':');
            var id = tmp[0];
            var nameShort = tmp[1];
            var nameLong = val;
            //var link = '#' + query.site + '/' + id + '/' + nameShort + '/';
            
            var d=document.createElement('div');
            $(d).addClass('content-block')
                .addClass('cat-block')
                .html('<a href="#" class="ui-link"><img src="images/cat-assets/' + nameShort + 
                    '.jpg" alt="' + nameLong + '"><span class="cat-title">' + nameLong + '</span></a>')
                .appendTo($(".content-guts"))
                .click({ i: i, val: val, id: id}, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (1 == 0) {
                        // go to home page
                        //var site = hmCon.query.site;
                        //hmCon.clearQuery();
                        hmCon.query.page = 'home';
                        //hmCon.query.site = site;
                        hmCon.changeCat(e.data.i, e.data.val);
                    } else {
                        hmCon.search('c', e.data.id);
                    }
                });
        });
        
    },
    
    printSites : function () {
        var html = '\n\
                    <div class="content-block site-block hu" data-site="hustler">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/hustler.png" alt="Hustler"></a>\n\
                                    <p>Hustler - #1 in the Adult Entertainment</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-hu.jpg" alt="Hustler"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block bl" data-site="barely-legal">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/barelylegal.png" alt="Barely Legal"></a>\n\
                                    <p>Barel Legal - The Youngest and Tightest Girls on the Web</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-bl.jpg" alt="Barely Legal"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block tb" data-site="hustlers-taboo">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/taboo.png" alt="Taboo"></a>\n\
                                    <p>Hustler’s Taboo - The Kinkiest Site on the Web!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-tb.jpg" alt="Taboo"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block ah" data-site="anal-hookers">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/analhookers.png" alt="Anal Hookers"></a>\n\
                                    <p>Hustler’s Anal Hookers - Cum thru the backdoor!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-ah.jpg" alt="Anal Hookers"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block vca" data-site="vcaxxx">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/vcaclassics.png" alt="VCA Classics"></a>\n\
                                    <p>VCA Classics - The Classics Never Die!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-vca.jpg" alt="VCA Classics"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block af" data-site="asian-fever">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/asianfever.png" alt="Asian Fever"></a>\n\
                                    <p>Asian Fever - Orient girls have never been hotter than this!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-af.jpg" alt="Asian Fever"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block bb" data-site="busty-beauties">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/bustybeauties.png" alt="Busty Beauties"></a>\n\
                                    <p>Busty Beauties - It would be a pity to miss out on these titties!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-bb.jpg" alt="Busty Beauties"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block bm" data-site="bossy-milfs">\n\
                            <div class="site-name">\n\
                                    <a href="http://www.google.com"><img src="images/site-assets/bossymilfs.png" alt="Bossy Milfs"></a>\n\
                                    <p>Bossy Milfs - These cougars are large and in charge!</p>\n\
                                    <a href="#" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-bm.jpg" alt="Bossy Milfs"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block sbd" data-site="scary-big-dicks">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/scarybigdicks.png" alt="Scary Big Dicks"></a>\n\
                                    <p>Scary Big Dicks - The hottest chicks vs. the biggest dicks!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-sbd.jpg" alt="Scary Big Dicks"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block bh" data-site="scary-big-dicks">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/beaverhunt.png" alt="BeaverHunt"></a>\n\
                                    <p>BeaverHunt - The nastiest amateurs on the Net</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-bh.jpg" alt="BeaverHunt"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block pd" data-site="hustler-parodies">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/parodies.png" alt="Hustler Parodies"></a>\n\
                                    <p>Hustler Parodies - All your favs remade Hustler Style!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-pd.jpg" alt="Hustler Parodies"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block dgl" data-site="daddy-gets-lucky">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/daddygetslucky.png" alt="Daddy Gets Lucky"></a>\n\
                                    <p>Daddy Gets Lucky - Your dad needs pussy too!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-dgl.jpg" alt="Daddy Gets Lucky"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block hg" data-site="hometown-girls">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/hometowngirls.png" alt="Hometown Girls"></a>\n\
                                    <p>Hometown Girls - Your next door Neighbor</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-hg.jpg" alt="Hometown Girls"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block hd" data-site="hustler-hd">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/hustlerhd.png" alt="Hustler HD"></a>\n\
                                    <p>Hustler HD - Experience High Quality HD Porn!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-hd.jpg" alt="Hustler HD"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block hm" data-site="hottie-moms">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/hottiemoms.png" alt="Hottie Moms"></a>\n\
                                    <p>Hottie Moms - Some things get better with age</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-hm.jpg" alt="Hottie Moms"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block cg" data-site="hustlers-college-girls">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/collegegirls.png" alt="Hustler\'s College Girls"></a>\n\
                                    <p>Hustler\'s College Girls - Educating young women since 1974</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-cg.jpg" alt="Hustler\'s College Girls"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block hz" data-site="hustlaz">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/hustlaz.png" alt="Hustlaz"></a>\n\
                                    <p>Hustlaz - Hot black chicks & monster dicks</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-hz.jpg" alt="Hustlaz"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block lb" data-site="hustlers-lesbians">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/lesbians.png" alt="Hustler\'s Lesbians"></a>\n\
                                    <p>Hustler’s Lesbians - Hot girl on girl action!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-lb.jpg" alt="Hustler\'s Lesbians"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block ml" data-site="muchas-latinas">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/muchaslatinas.png" alt="Muchas Latinas"></a>\n\
                                    <p>Muchas Latinas - Muy caliente "Aye Papi"!</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-ml.jpg" alt="Muchas Latinas"></a>\n\
                            </div>\n\
                    </div>\n\
                    <div class="content-block site-block tt" data-site="too-many-trannies">\n\
                            <div class="site-name">\n\
                                    <a href="/"><img src="images/site-assets/toomanytrannies.png" alt="Too Many Trannies"></a>\n\
                                    <p>Too Many Trannies - Chicks with dicks</p>\n\
                                    <a href="/" data-role="button" data-inline="true" data-mini="true" class="btn-sites">View All Scenes</a>\n\
                            </div>\n\
                            <div class="site-update">\n\
                                    <a href="/"><img src="images/site-assets/site-tt.jpg" alt="Too Many Trannies"></a>\n\
                            </div>\n\
                    </div>';
        $(".content-guts").append(html);
        var clickSite = function (e) {
            e.preventDefault();
            e.stopPropagation();
            hmCon.clearQuery();
            hmCon.query.page = 'home';
            hmCon.changeSite(e.data.site);
            hmCon.updateUiFooter('home');
        }
        $(".content-block").each(function(e) {
            var site = $(this).attr('data-site');
            $(this).click({ site: site }, clickSite);
        });
        $(".content-block a").each(function(e) {
            var site = $(this).parent().parent().attr('data-site');
            $(this).click({ site: site }, clickSite);
        });
    },
        
    fnClearSearchScreenClosure : function (page) {
        //var page = this.query.page;
        return function () {
            hmCon.fnClearSearchScreen(page, undefined, undefined, undefined);
        }
    },
    
    fnClearSearchScreen : function (page, currentPage, totalPages, contentId) {
        // OPTIONAL: currentPage, totalPages, contentId (for getting content ID of model)
        //var page = this.query.page;
//console.log('fnClearSearchScreenClosure: clear screen...');
            // global: clearPage, clearPageCount
            var cancel = false;
            if (clearPageCount != 0) {
                cancel = true;
                if (clearPage == 0) {
                    // initial content loaded will clear this page...
                    cancel = false;
                }
                // other content will not
                clearPage = clearPage + 1;
                if (clearPage == clearPageCount) {
                    clearPage = 0;
                    clearPageCount = 0;
                }
            }
            if (cancel == false) {
//console.log('initializing/clearing page: ' + page);
                hmCon.initPage(page, currentPage, totalPages, contentId);
            }
    },
    
    fnSearchScreenLoaded : function (fn) {
        return function () {
            // global: loadPageCount, loadPage
            loadPageCount = loadPageCount + 1;
//console.log('fnSearchScreenLoaded.... loadPageCount:' + loadPageCount + ' loadPage:' + loadPage);
            if (loadPageCount == loadPage || loadPage == 0) { //loadPageCount should never be 0, but just in case...
//console.log('PASSED loadPageCount. Content loaded. show page');
                loadPageCount = 0;
                loadPage = 0;
                fn();
            } else {
//console.log('FAILED');
                return false;
            }
        }
    }

};


function clone(obj) {
    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Date
    if (obj instanceof Date) {
        var copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        var copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        var copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
}

// if popup image fails to load...
$('#popupDialog div:eq(1) div:eq(0) ul li img').on("error", function(e) {
//console.log('no image for popup');
    $(this).attr('src', 'images/placeholder/photo-placeholder.png');
});

window.onhashchange = function () {
//console.log('hash changed to: ' + location.hash);
    if (location.hash == '') {
//console.log('no hash!');
        hmCon.init(hmCon.ajax.url); // the URL thing is bad, but works
    } else {
        hmCon.getHash();
    }
    hmCon.change();
};