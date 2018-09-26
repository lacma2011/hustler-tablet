
lastId = 0; // counter to keep track of # of clips loaded. For making unique ID for divs of clip content

var hmajax = {
    url : null, // the ajax api url
    pageBarUrl: null, // the ajax api url for pagebar
    query: {
        page : 0,
        type: 'videos',
        order: 0,
        filters: null,
        code: null
        //site: 'hustler'
    },
    pagebar: {
        totalRecords: null,
        currentPage: null,
        perPage: null,
        siteType: null,
        pagebars: null
    },
    scenesRendered : {
        'videos': [],
        'photos': [],
        'photodetail': [],
        'models': [],
        'dvds': [],
        'mags': []
    },
    scenesLoaded : {
        'videos': [],
        'photos': [],
        'photodetail': [],
        'models': [],
        'dvds': [],
        'mags': []
    },
    newPage : null,


    clearScenes : function () {
        this.scenesRendered = {
            'videos': [],
            'photos': [],
            'photodetail': [],
            'models': [],
            'dvds': [],
            'mags': []
        };
        this.scenesLoaded = {
            'videos': [],
            'photos': [],
            'photodetail': [],
            'models': [],
            'dvds': [],
            'mags': []
        };
    },

    getFave : function(page, newpage) {
        this.query.filters = [];
        this.query.filters['fav'] = 1;
        this.get(page, newpage);
    },

    getSite : function(siteId, page, newpage) {
        this.query.filters = [];
        this.query.filters['s'] = siteId;
        this.get(page, newpage);
    },

    getCategory : function(categoryId, page, newpage) {
        this.query.filters = [];
        this.query.filters['cat'] = categoryId;
        this.get(page, newpage);
    },

    getModel : function(modelId, page, newpage) {
        this.query.filters = [];
        this.query.filters['mod'] = modelId;
        this.get(page, newpage);
    },
    
    /* begin ... another way to filter content is to do method chaining */
    setModel: function (modelId) {
        this.query.filters['mod'] = modelId;
    },
    
    setCategory: function (categoryId) {
        this.query.filters['cat'] = categoryId;
    },
    
    setPhotosetSceneId: function (sceneId) {
        this.query.filters['photoset_id'] = sceneId;
    },
    setSearchString: function (searchString) {
        this.query.filters['search_text'] = searchString;
    },
    // I know there's already a setCategory, but something tells me we should separate search categories from the main category stuff
    setSearchCategory: function ( searchCategoryId ) {
        this.query.filters['search_category'] = searchCategoryId;
    },
    
    
    setSite: function (siteId) {
//console.log('change site: ' + siteId);
        if (this.query.filters == null) this.query.filters = [];
        this.query.filters['s'] = siteId;
    },

    setType: function (typeId) { // videos, models
//console.log('change type: ' + typeId);
        this.query.type = typeId;
    },
    
    setFav : function() { // get favorites
        if (this.query.filters == null) this.query.filters = [];
        this.query.filters['fav'] = 1;
    },

    /* end */
    
    /* begin ... set "order" types ... with method chaining */
    setOrder: function (orderId) {
//console.log('change order: ' + orderId);
        this.query.order = orderId;
    },

    setOrderFeatured : function() {
        this.setOrder(1) ;
    },
    
    setOrderOldest : function() {
        this.setOrder(2);
    },
    
    setOrderViews : function() {
        this.setOrder(3);
    },
    
    setOrderTop : function() {
        this.setOrder(4);
    },

    setOrderWatch : function() {
        this.setOrder(5);
    },
    
    setOrderUpcoming : function() {
        this.setOrder(6);       
    },
    /* end */

    addFilters : function() {
        if (this.query.filters == null) return '';
        var str = '';
        for (var x in this.query.filters) {
            str += x + '=' + this.query.filters[x] + '|';
        }
//console.log(str);
        return str;
    },
    
    makeRequest : function () {
        // there are to be many kinds of requests.
        // VIDEO
        // PHOTO
        // MAGAZINES
        if (this.query.filters == null) {
            return this.url + 'get/con/' + this.query.type + '/' + this.query.order + '/' + this.query.page + '/';
        }
        return this.url + 'get/con/' + this.query.type + '/' + this.query.order + '/' + this.addFilters() + '/' + this.query.code + '/' + this.query.page + '/';

    },
    
    makeRequestData : function () {
        return '';   
    },
    
    getDvd : function(group_id, page, callback, successOptions) {
        // get scenes for a DVD
        // page will always be 1 (unless DVDs ever get more than 12 (or whatever we load per page) scenes
        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });

        $.ajax({
            myobj: this,
            url: this.url + 'get/con/dvd/' + group_id + '/' + page + '/',
            siteType: $.siteType(),
            type: "GET",
            data: '',
            cache: false,
            successOptions: successOptions // hash of parameters, maybe even functions, to make available to success callback function

        }).done(callback); // as of jquery 1.5
    },
    
    getDetailDvd : function(group_id, successOptions, callback) {
        var page = 1; // page will always be 1-- maybe serve as a code later?
        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });

        $.ajax({
            myobj: this,
            url: this.url + 'get/con/dvddetail/' + group_id + '/' + page + '/',
            siteType: $.siteType(),
            type: "GET",
            data: '',
            cache: false,
            successOptions: successOptions // hash of parameters, maybe even functions, to make available to success callback function

        }).done(callback);
    },

//hmajax.getDetailMag(hmajax.scenesLoaded[type][id]['grp'], successOptions, function(json) {});
    getDetailMag : function(mag_id, successOptions, callback) {
        var page = 1; // page will always be 1-- maybe serve as a code later?
        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });

        $.ajax({
            myobj: this,
            url: this.url + 'get/con/magdetail/' + mag_id + '/' + page + '/',
            siteType: $.siteType(),
            type: "GET",
            data: '',
            cache: false,
            successOptions: successOptions // hash of parameters, maybe even functions, to make available to success callback function

        }).done(callback);
    },

    getDetailModel : function(model_id, successOptions, callback) {
        var page = 1; // page will always be 1-- maybe serve as a code later?
        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });

        $.ajax({
            myobj: this,
            url: this.url + 'get/con/modeldetail/' + model_id + '/' + page + '/',
            siteType: $.siteType(),
            type: "GET",
            data: '',
            cache: false,
            successOptions: successOptions // hash of parameters, maybe even functions, to make available to success callback function

        }).done(callback);
    },

    getDetailScene : function(scene_id, successOptions, callback) {
        var page = 1; // page will always be 1-- maybe serve as a code later?
        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });

        $.ajax({
            myobj: this,
            url: this.url + 'get/con/scene/' + scene_id + '/' + page + '/',
            siteType: $.siteType(),
            type: "GET",
            data: '',
            cache: false,
            successOptions: successOptions // hash of parameters, maybe even functions, to make available to success callback function

        }).done(callback);
    },
    
    getSitesList : function(successOptions, callback) {
        $.ajax({
            myobj: this,
            url: this.url + 'get/con/sites/',
            siteType: $.siteType(),
            type: "GET",
            data: '',
            cache: false,
            successOptions: successOptions

        }).done(callback);
    },

    get : function(page, newpage, fnSuccess, successOptions) {
        // fnSuccess is our own callback function after ajax, if we don't want to use successPhone()
        this.newPage = newpage;
        //console.log('in get... this.newPage: ' + this.newPage);

        if (page == false) {
            // false means "more", or next page
            page = this.query.page + 1;
            this.setPage(page);
        } else {
            this.setPage(page);
        }

        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });
        
        var successPhone = function (json) {
            // callback on Ajax Success, for the original mobile site (phone)
            var clips = this.myobj.parseJSON(json);
            var nextpage = 1;
            //console.log('this.myobj.newPage: ' + this.myobj.newPage);
            nextpage = parseFloat(this.myobj.query.page) + 1;
            if (this.myobj.newPage == true) {
                $('#ajax-content').html(this.myobj.print(clips));
            } else {
                $('#ajax-content').append(this.myobj.print(clips));
            }
            if (nextpage > clips.info.totalPages) {
                $('#ajax-more').empty ();
            } else {
                $('#ajax-more').html('Load Page ' + nextpage + ' Clips');
            }
            $('#ajax-more').attr('data-page', nextpage);
            // set max pages and current pages in this div (pagebar javascript needs it)
            $('#ajax-bar').attr('data-max', parseFloat(clips.info.totalPages));
            $('#ajax-bar').attr('data-curr', parseFloat(clips.info.currentPage));

            // initialize data for pagebar()
            this.myobj.pagebar.totalRecords = clips['info']['totalRecords'];
            this.myobj.pagebar.currentPage = clips['info']['currentPage'];
            this.myobj.pagebar.perPage = clips['info']['params']['perPage'];
            this.myobj.pagebar.siteType = this.siteType;

            // make the pagebar(s)
            for (var i = 0; i < this.myobj.pagebar.pagebars.length; i++) {
                var pages_shown = this.myobj.pagebar.pagebars[i].pagesShown;
                var pagebarId = this.myobj.pagebar.pagebars[i].pagebarId;
                this.myobj.makePagebar(pages_shown, pagebarId);
            }

//TODO: place ad for tour
            // place ad
            this.myobj.placeAd();
            
            // do favorites
            hmajaxFave.setUrl(this.myobj.url).setPhone().setType('video');
            $('.favorite').bind('click', function(event) {
                if(!faveActive){ // prevent doubleclicking ajax during ajax call
                    return;
                }
                faveActive = false; // turn buttons off
                hmajaxFave.update($(this).attr('data-fave'), this);
            });

        }
        
        var successCallback;
        if (fnSuccess === undefined) {
            successCallback = successPhone;
        } else {
            successCallback = fnSuccess;
        }

        if (successOptions === undefined) {
            successOptions = {};
        }

        var url = this.makeRequest();
        var data = this.makeRequestData();
        //var data = 'page=' + document.location.hash.replace(/^.*#/, ''); //TODO: put real data
        $.ajax({
            myobj: this,
            url: url,
            siteType: $.siteType(),
            type: "GET",
            data: data,
            cache: false,
            successOptions: successOptions // hash of parameters, maybe even functions, to make available to success callback function

        }).done(successCallback)
        .error(function () { console.log('error!');}); // as of jquery 1.5
    },
    
    setUrl : function (url) {
        this.url = url;
        this.pageBarUrl = url + 'get/pagebar/';
    },

    setPage : function (page) {
        this.query.page = page;
    },
    
    setCode : function (code) {
        this.query.code = code;
    },
    
    
    initPagebar : function (pagesShown, pagebarId) {
        if (this.pagebar.pagebars == null) {
            this.pagebar.pagebars = [];
        }
        var arr = [];
        arr['pagesShown'] = pagesShown;
        arr['pagebarId'] = pagebarId;
        this.pagebar.pagebars.push(arr);
    },
    
    makePagebarRequest : function (pages_shown) {
        return this.pageBarUrl + this.pagebar.totalRecords + '/' + this.pagebar.perPage + '/' + 
            this.pagebar.currentPage + '/' + pages_shown + '/';
    },
    
    makePagebar : function (pages_shown, pagebarId) {
        // use ajax to get the pagebar ;)
        // global: showHelpText
//TODO (MAYBE): convert php class for pagebar to javascript so it can be done on client?        

            $.ajax({
                myobj: this,
                type: "GET",
                url: this.makePagebarRequest(pages_shown),
                data:  '',
                cache: false,

                success: function (html) {
                    $(pagebarId).empty();
                    var processed = $(html);
                    var thisobj = this;

                    processed.find('a').each(function(index) {
                        if ($(this).attr('data-page') > 0) {
                            $(this).bind('click', function(){thisobj.myobj.get($(this).attr('data-page'), true);}); // thisobj.myobj.get($(this).attr('data-page'))
                        }
                    });
                    $(pagebarId).append(processed)
                    pagebarInit();
                    if (showHelpText == true) {
                        $('#pagehelp').css('display', 'block');
                        $('#pagehelp').bind('inview', helpText);
                    }

                    //scroll to first clip loaded
                    var firstId = $('#ajax-newclip').attr('data-clip');
                    var new_position = $(firstId).offset();
//console.log(firstId);
                    window.scrollTo(new_position.left,new_position.top);                   
                }
            });
        
    },

    parse : function (json) {
        var myData = this.parseJSON(json);
        var data;
        for (var i in myData['videos']) {
            //console.log(myData['data'][i]);
            data['title'] = myData['data'][i]['title'];
        }
        
    },
    
    parseJSON : function (html) {
        var myData;
        try {
            myData = JSON.parse(html, function (key, value) {
                var type;
                if (value && typeof value === 'object') {
                    type = value.type;
                    if (typeof type === 'string' && typeof window[type] === 'function') {
                        return new (window[type])(value);
                    }
                }
                return value;
            });
        } catch (e) {
console.log('JSON PARSE FAILED!');
            return null;
        }
        
        return myData;
    },
    
    placeAd : function () {
        
    },
    
    print : function (myData) {
//TODO: play button doesn't show as an overlay?
        // global var: lastId        
//console.log(myData);
        var myobj = self;
        var site_type = myData['misc']['site_type'];
        var mobile_images = myData['misc']['mobile_images'];
        var mobile_url = myData['misc']['mobile_url'];
        var reset = myData['misc']['reset'];
        var session_page_count = myData['misc']['session_page_count'];
        var tour_play_limit = myData['misc']['tour_play_limit'];
        var session_tour_plays = myData['misc']['session_tour_plays'];
        var tour_reset = myData['misc']['tour_reset'];
        var tour_max_pages = myData['misc']['tour_max_pages'];
        var current_page_url = myData['misc']['current_page_url'];
        
        var scene = '';
	var firstId = null;
        for (var i in myData['videos']) {
            //console.log(myData['data'][i]);
            var title = myData['videos'][i]['title'];
            var clip = myData['videos'][i]['clip'];
            //var count = myData['videos'][i]['count'];
            var count = lastId;
            if (firstId == null) {
		firstId = count;
	    }
            lastId++;
            var ss = myData['videos'][i]['ss'];
            var date = myData['videos'][i]['date'];
            var fav_status = myData['videos'][i]['fav_status'];
            var str_cat = myData['videos'][i]['str_cat'];
            var description = myData['videos'][i]['description'];
            var performers = myData['videos'][i]['performers'];
            var scene_id = myData['videos'][i]['scene_id'];
            
            scene += '\
                        <!--///////////////////////////////////////start scene //////////////////////////////////////////////--> \
                        <div class="scene" id="scene_' + count + '"> \
                            <div class="scene_play_button"> \
                                <a href="' + clip + '"><img src="' + mobile_images + 'play_button.png" data-target="' + clip + '"></a> \
            <!--img src="' + mobile_images + 'play_button.png" data-target="' + clip + '"--> \
                        </div> \
                        <!--a href="' + clip + '"--><img src="' + ss + '" class="scene_image" data-target="' + clip + '" /><!--/a--> \
                        <div style="position:relative;top:15px;">&nbsp;</div> \
                        <div class="info_frame"> \
                            <h1><a name="clip_' + count + '" id="clip_' + count + '"></a>' + title + '</h1> \
                                <div class="info" id="more_' + count + '" style="float:none;text-align:right;position:relative;top:-16px;border:none;"> \
                                    <a href="#" id="more_detail_' + count + '" class="more_link" style="margin-right:auto;margin-left:auto;text-align:center;" \
                                    onClick="javascript:descClick(document.getElementById(\'more_' + count + '\'),document.getElementById(\'info_' + count + '\'),document.getElementById(\'less_' + count + '\'));return false;"><img src="' + mobile_images + 'plus.jpg' + '" style="width:20px;border:none" /></a> \
                                </div> \
                                <div class="info" id="less_' + count + '" style="display:none;float:none;text-align:right;position:relative;top:-21px;left:4px;border:none;"> \
                                    <a href="#" id="less_detail_' + count + '" class="more_link" style="margin-right:auto;margin-left:auto;text-align:center;" \
                                       onClick="javascript:descClickLess(document.getElementById(\'more_' + count + '\'),document.getElementById(\'info_' + count + '\'), document.getElementById(\'less_' + count + '\'));return false;"><img src="' + mobile_images + 'minus.jpg' + '" style="width:25px;border:none;" /></a> \
                                </div> \
                            <div class="info" id="info_' + count + '" style="display:none;float:none;"> \
                                <p class="released">Released: ' + date + '</p>';
                        if (site_type != 'tour') {
                            scene += '\
                                <p class="favorite" data-fave="' + scene_id + '"><img src="' + mobile_images + 'icon_menu_favorite.png" style="width:8px;border:none;"/>';
                                    if (fav_status != 1) {
                                       scene += 'Favorite this';
                                    } else {
                                       scene += 'Unfavorite this';
                                    }
                               scene += '</p>';
                        }
                        scene += ' \
                                <p class="categories">Categories: ' + str_cat + '</p> \
                                <p class="description" id="desc_' + count + '">' + description + '</p> \
                                <p class="performers">Pornstars: ' + performers + '</p>';

                        if (site_type == 'tour' && true === tour_reset) {
                                scene += '<p class="testtext"><a href="' + mobile_url + reset + '">Trailer Clips played:' + session_tour_plays + ' / ' + tour_play_limit;
                                scene += '&nbsp;&nbsp;&nbsp;Video pages browsed: ' + session_page_count + '/' + tour_max_pages + ' </a> (refresh screen to update) </p>';
                        }
                        scene += ' \
                            </div><!--end info --> \
                        </div> \
                    </div> \
            <!--///////////////////////////////////////end scene //////////////////////////////////////////////--> \
            ';

            
        }

	$('#ajax-newclip').attr('data-clip', '#scene_' + firstId);

        return scene;
    }

};