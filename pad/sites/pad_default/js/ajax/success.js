var successCallback = function(json) {

    var conType = this.successOptions.type;
    var page = this.successOptions.page;
    var clips = this.myobj.parseJSON(json);

    var fail = false;
    if (clips == null) {
        // API failed
        //console.log('API FAIL for ' + conType);
        this.successOptions.pageInitFn();
        this.successOptions.pageLoadedFn();
        
        fail = true;
    } else if ((clips[conType].length == 0)) {
        //console.log('API 0 results for ' + conType);
        this.successOptions.pageInitFn();
        this.successOptions.pageLoadedFn();
        
        fail = true;
    }
    if (fail === true) {
        // print negative results message
        var selector = '', title = '';
        switch (conType) {
            case 'videos':
                selector = '.content-guts.all-videos';
                if (page == 'home') selector = '#video-carousel';
                title = 'Videos';
                break;
            case 'models':
                selector = '.content-guts.all-models';
                if (page == 'home') selector = '#model-carousel';
                title = 'Models';
                break;
            case 'dvds':
                selector = '.content-guts.all-dvds';
                if (page == 'home') selector = '#dvd-carousel';
                title = 'DVDs';
                break;
            case 'photos':
                selector = '.content-guts.all-photos';
                if (page == 'home') selector = '#photo-carousel';
                title = 'Photos';
                break;
            case 'mags':
                selector = '.content-guts.all-mags';
                if (page == 'home') selector = '#mag-carousel';
                title = 'Magazines';
                break;
        }
        if (page == 'home') $(selector).empty();
        $(selector).append('<h3>No ' + title + '  found. Try another selection.</h3>');
        return false;
    }
    
    //console.log('successCallback(): ' + conType);
    //console.log(clips);
    //console.log(conType + ': clearPageCount: ' + clearPageCount + ' clearPage:' + clearPage);

    this.successOptions.pageInitFn(clips['info']['currentPage'], clips['info']['totalPages']);

    // prepare the favorites object
    hmajaxFave.setUrl(this.myobj.url);

    var selector;
    if ( conType != "photodetail" )
        deactivate_photodetail_elems();
    switch (conType) {
        case 'videos':
            selector = '.video-block';
            if (page == 'home') {
                successHomeVideo(clips);
            } else {
                successVideo(clips);
            }
            break;
        case 'models':
            selector = '.model-block';
            if (page == 'home') {
                successHomeModel(clips);
            } else {
                successModel(clips);
            }
            break;
        case 'dvds':
            selector = '.dvd-block';
            if (page == 'home') {
                successHomeDvd(clips);
            } else {
                successDvd(clips);
            }
            break;
        case 'mags':
            selector = '.mag-block';
            if (page == 'home') {
                successHomeMag(clips);
            } else {
                successMag(clips);
            }
            break;
        case 'photos':
            selector = '.photo-block';
            if (page == 'home') {
                successHomePhoto(clips);
            } else {
                successPhoto(clips);
            }
            break;
        case 'photodetail':
            $('div[data-role="subheader"]').hide(); 
            $("#header_logo_wrap a.logo").hide();
            $('#header_logo_wrap').append('<a href="#" class="gallery-title">' + hmCon.query.scene_name + '</a>');
            $('<a id="photodetail_back_btn" data-ajax="false" data-direction="reverse" class="ui-btn-left ui-btn ui-btn-up-a ui-shadow ui-btn-corner-all" href="javascript:history.back()" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a"><span class="ui-btn-inner"><span class="ui-btn-text">Back</span></span></a>').insertAfter($("#header_logo_wrap"));
            successPhotoDetail(clips);
            selector = 'ul li a';
            break;
    }

    // check if all images are loaded, reload if necessary
    loadImageCheck(conType, selector, page, this.successOptions.pageLoadedFn, this.successOptions.jobId);

    // check for cookie to do returning popup
    popUpCookie(conType);
}

var loadImageCheck = function(conType, selector, page, pageLoadedFn, jobId) {
    // remove 'page loading' icon (by running pageLoaded function)
    // so basically, 'page loading' darkened overlay will be removed after all images, all content is loaded
    // remove 'page loading' icon, and run pageLoaded function, when images are loaded
    // also this 'page loading' will be removed after all content is loaded
    // check if images are all loaded.... reload ones that fail the first time.    
    var countImages = 0;
    var unloaded = [];
    var images = $('.content-guts ' + selector).find('img').length;
console.log('# images for ' + selector + ': ' + images);
    
    $('.content-guts ' + selector).find('img').on('load', {pageLoadedFn: pageLoadedFn}, function(e){
        if ($(this).attr('data-reload') !== undefined) {
            // this image is on the reload list already! 
            return false;
        }
        countImages++;
        if (countImages == images){
            countImages = 0;
            $(document).trigger(conType + '-reload', [unloaded, conType, e.data.pageLoadedFn, selector, jobId]);
        }
    }).on('error', {pageLoadedFn: pageLoadedFn}, function(e){
        if ($(this).attr('data-reload') !== undefined) {
            // this image is on the reload list already! 
            return false;
        }
        if (page == 'home') {
            var missingImg = $(this).parent().parent().parent().attr('id');
        } else if (conType == 'photodetail') {
            var missingImg = this;
        } else {
            var missingImg = $(this).parent().parent().attr('id');
        }
//console.log('missing ' + conType + ' ' + missingImg);
        unloaded.push(missingImg);
        countImages++;
//console.log('counter: ' + countImages + ' vs ' + images);
        if (countImages == images){
            countImages = 0;
            $(document).trigger(conType + '-reload', [unloaded, conType, e.data.pageLoadedFn, selector, jobId]);
        }
    });

    $(document).off(conType + '-reload');
    $(document).on(conType + '-reload', function(e, unloaded, conType, pageLoadedFn, selector, jobId) {
        var attempt = 1; // first attempt to reload. affects whether it does a regular image refresh or api call for new imagecache
        reloadImages(unloaded, conType, pageLoadedFn, selector, jobId, attempt);
    });
}


var reloadImages = function (unloaded, conType, pageLoadedFn, selector, jobId, attempt) {
    // attempt: 
    // 1: refresh missing images.
    // 2: refresh images after content is shown to user, and longer interval
    // 3: refresh images with an API call to get new cached images
    if (unloaded.length == 0) {
        //console.log('no need to reload any images for ' + conType);
        // finish loading page
        pageLoadedFn();
        // check the favorites status for this content
        loadFavorites(conType, selector);
        return false;
    }

console.log(conType + ' Attempt #' + attempt + ' to reload ' + unloaded.length + ' images');
//console.log(' selector: ' + selector);
//console.log(unloaded);
    //console.log('pageLoadedFn:'); console.log(pageLoadedFn);

    var addPlaceholderImg  = function (imgObj) {
//console.log('putting placeholder: ' + conType + '...' + imgObj.attr('src'));
        var img = '';
        switch (conType) {
            case 'photodetail':
                img = 'images/placeholder/gallery-landscape-placeholder.png';
                img = 'images/placeholder/gallery-portrait-placeholder.png'; // how to choose???
                break;
            case 'photos':
            case 'models':
                img = 'images/placeholder/photo-placeholder.png';
                break;
            case 'dvds':                
            case 'mags':
                img = 'images/placeholder/magazine-placeholder.png';
                break;
            case 'videos':
            default:
                img = 'images/placeholder/video-placeholder.png';
                break;
        }
        imgObj.attr('src',img);
         // firefox bug, needs to resize image
        var aLink = imgObj.parent();
        aLink.css('width','inherit');
    }

    var reloadAgain = function(unloaded) {
        // reloadAgain() will be used as a second (and third) measure if the first reload does not work.
        (function() {
            if (unloaded.length == 0) {
                return true;
            }
//console.log('for reload:');
//console.log(unloaded);
//console.log('reload again..');console.log('unloaded:');console.log(unloaded);console.log('conType: ' + conType + ' selector: ' + selector + ' jobId: ' + jobId);
            // we can do a new try on missing images... "rereload"
            if (attempt === 1) {
                // after our first reload try, do another reload attempt after content is shown, and give a lot more time
                setTimeout(
                    function() {
                        var attempt = 2;
                        reloadImages(unloaded, conType, $.noop, selector, jobId, attempt);
                    }, 
                    reloadDelay2
                );
            } else if (attempt === 2){
                // after our second reload try, hit the API again
                var unloaded3 = [];
                $(unloaded).each(function(){
                    unloaded3.push({
                        id: '' + this,
                        sceneId: $('#' + this).attr('data-content-id')
                    });
                });
                setTimeout(
                    function() {
                        $(document).trigger(conType + '-rereload', [jobId, conType, selector, unloaded3]);
                    }, 
                    reloadDelay3
                );
            } else {
                console.log(conType + ' fail reloads:');
                console.log(unloaded);
            }
        })();
    };
    
    var reloadedImages = 0;// counter for reloaded images

    var unloaded2 = []; // second round... images that didn't reload
                            
    setTimeout(
        function() {
            $(unloaded).each(function(){
//console.log(conType);
//console.log(this);
                if (conType == 'photodetail') {
                    var missingImg = '.content-guts ' + selector + ' img[src="' + $(this).attr('src') + '"]';
                } else {
                    var missingImg = '.content-guts ' + selector + '#' + this + ' img';
                }
//console.log('get image:'); 
//console.log(missingImg);

                imgObj = $(missingImg);
                if (attempt !== 1) {
                    imgObj.off('load').off('error'); // unbind previous
                    // redo source
                    var sceneId = $('#' + this).attr('data-content-id');
                    var imageField = getImageField(conType);
                    var ss = hmajax.scenesLoaded[conType][sceneId][imageField] + '?timestamp=' + new Date().getTime();
//console.log(conType + ' attempt #' + attempt + ' to reload image: ' +  ss);
                    imgObj.attr('src', ss);
                }
                if (imgObj.attr('src') != 'false') {
                    if (imgObj.attr('data-reload') === undefined) {
//console.log('changing: ' + missingImg);
                        imgObj.attr('src', imgObj.attr('src') + '?timestamp=' + new Date().getTime());
                        imgObj.attr('data-reload', 'true');
                    }
//console.log('changed to:'); console.log(imgObj);
                    imgObj.on('load', {pageLoadedFn: pageLoadedFn}, function(e){
                        reloadedImages++;
//console.log('load at' + reloadedImages);
                        if (reloadedImages == unloaded.length){
                            // finish loading page
                            e.data.pageLoadedFn();
                            // check the favorites status for this content
                            if (attempt === 1) {
                                loadFavorites(conType, selector);
                            }

                            // our LAST measures! will do another reload at first call. Second call it will reload API.
                            reloadAgain(unloaded2);

                        }
                     }).on('error', {pageLoadedFn: pageLoadedFn, imgId : '' + this}, function(e){
//console.log('failed reloading missing image ' + conType);
                        $(this).off('load').off('error'); // so that adding this placeholder image does not trigger these
                        addPlaceholderImg($(this));

                        // add to second list... the failed reloads
                        unloaded2.push(e.data.imgId);

                        reloadedImages++;
                        if (reloadedImages == unloaded.length){
                            // finish loading page
                            e.data.pageLoadedFn();
                            // check the favorites status for this content
                            if (attempt === 1) {
                                loadFavorites(conType, selector);
                            }
                            
                            // our LAST measures! will do another reload at first call. Second call it will reload API.
                            reloadAgain(unloaded2);
                        }
                     });

                } else {
//console.log('place holder done for FALSE/null image');
                    imgObj.off('load').off('error'); // so that adding this placeholder image does not trigger these
                    addPlaceholderImg(imgObj);
                    reloadedImages++;
                    if (reloadedImages == unloaded.length){
                        // finish loading page
                        pageLoadedFn();
                        // check the favorites status for this content
                        if (attempt === 1) {
                            loadFavorites(conType, selector);
                        }

                        // our LAST measures! will do another reload at first call. Second call it will reload API.
                        reloadAgain(unloaded2);                       
                    }
                }

            });
        },
        reloadDelay1
    );
        
    $(document).off(conType + '-rereload');
    $(document).on(conType + '-rereload', function(e, jobId, conType, selector, unloaded2) {
        // let's do this API call one more time to try to get images...
        hmCon.jobs.run(jobId, conType, selector, unloaded2, hmajax, successReloadApi);
    });
}

var successReloadApi = function(json) {
    var clips = this.myobj.parseJSON(json);
    var type = this.successOptions.type;
    if (clips == null) {
        // API failed
        return false;
    } else if ((clips[this.successOptions.type].length == 0)) {
        // no results?
        return false;
    }
//console.log('reload API.');
//console.log(clips);
//console.log(this.successOptions);
    var unloaded = []; // make a list of images needing reloading to pass back to reloadImages()
    for (var x in this.successOptions.missingImgs) {
        var id = this.successOptions.missingImgs[x].id;
        var sceneId = this.successOptions.missingImgs[x].sceneId;
//console.log(hmajax.scenesLoaded[type]);
        var imageField = getImageField(type);
        for (var y in clips[type]) {
            if (clips[type][y]['id'] == sceneId) {
//console.log(type);
//console.log('found... ');
//console.log(clips[type][y]);
//console.log('replace in ... ');
//console.log(hmajax.scenesLoaded[type][sceneId]);
                hmajax.scenesLoaded[type][sceneId][imageField] = clips[type][y][imageField];
            }
        }
        unloaded.push(id);
    }
    var attempt = 3;
    reloadImages(unloaded, type, $.noop, this.successOptions.selector, this.successOptions.jobId, attempt);
}

var successPhoto = function (clips) {
    // global var: lastId, hmajax
    var conType = 'photos';
    var print = function (myData) {
        var myobj = hmajax;
        var scene = '';
        for (var i in myData[conType]) {
            //console.log(myData[conType][i]);
            var name = myData[conType][i]['name'];
            var image = myData[conType][i]['image'];
            var scene_name = myData[conType][i]['name'];
            var count = lastId;
            lastId++;
            var scene_id = myData[conType][i]['id'];
            scene += '<div class="content-block photo-block" id="clip_' + count + '" data-content-id="' + scene_id + '"> \
                            <a href="#popupDialog" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + image + '" alt="' + name + '"></a> \
                            <a href="#popupDialog" data-scene-click="' + scene_id + '" data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#popupDialog" class="ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right"><span class="ui-btn-inner"><span class="ui-btn-text">' + scene_name +  '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a> \
                    </div>';            
            myobj.scenesLoaded[conType][scene_id] = myData[conType][i];
        }
        myobj.scenesRendered[conType] = scene;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    // add content to page
    var selector = '.content-guts.all-photos';
    $(selector).append(print(clips));
    // add pagebar
    padPagebarCreate(selector, clips['info']['currentPage'], clips['info']['totalPages'], conType);
    // bind clicks to scenes
    bindScenes('.content-block.photo-block', conType);
}


var successPhotoDetail = function (clips) {
    var conType = 'photodetail';
    var print = function (myData) {
        // global var: lastId, hmajax
        var myobj = hmajax;
        var gallery_pics = '<ul class="gallery">';
        for (var i in myData[conType]) {
            //console.log(myData[conType][i]);
            var image = myData[conType][i]['image'];
            if ($.siteType() == 'members') {
                var full_size_image = myData[conType][i]['image_large'];
            } else {
                var full_size_image = joinPad;
            }
            lastId++;
            gallery_pics += '<li><a href="' + full_size_image + '" rel="external"  class="ui-link"><img src="' + image + '"></a></li>';
        }
        gallery_pics += '</ul>'; 
        myobj.scenesRendered[conType] = gallery_pics;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    // add content to page
    var selector = '.content-guts.all-individ_photos';
    $(selector).append(print(clips));
    // now feed the photo image <a>'s to the photoswipe plugin
    if ($.siteType() == 'members') {
        options = {};
        var myPhotoSwipe = Code.PhotoSwipe.attach( window.document.querySelectorAll(".content-guts.all-individ_photos .gallery a"), options );
    }
}


var successVideo = function (clips) {
    var conType = 'videos';
    var selector = '.content-guts.all-videos';

    // create flip switch (video/dvd slider)
    toggleDvdSwitch(selector, false);
    // add content to page with pagebar
    $(selector).append(printVideos(clips));
    // add pagebar
    padPagebarCreate(selector, clips['info']['currentPage'], clips['info']['totalPages'], conType);
    // bind clicks to scenes...
    bindScenes('.content-block.video-block', conType);
}


var successDvd = function (clips) {
    var conType = 'dvds'; // page: 'dvds'
    var selector = '.content-guts.all-dvds';

    // create flip switch (video/dvd slider)
    toggleDvdSwitch(selector, true);
    // add content to page
    $(selector).append(printDvds(clips));
    // add pagebar
    padPagebarCreate(selector, clips['info']['currentPage'], clips['info']['totalPages'], conType);
    // bind clicks to scenes
    bindScenesDvd('.content-block.dvd-block');
}


var successMag = function (clips) {
    var conType = 'mags';
    var selector = '.content-guts.all-mags';
    
    // add content to page
    $(selector).append(printMags(clips));
    // add pagebar
    padPagebarCreate(selector, clips['info']['current_page'], clips['info']['totalPages'], conType);
    // bind clicks to scenes
    bindScenes('.content-block.mag-block', conType);
}


var successModel = function (clips) {
    var conType = 'models';
    var print = function (myData) {
        var myobj = hmajax;
        var scene = '';
        for (var i in myData[conType]) {
            //console.log(myData[conType][i]);
            var name = myData[conType][i]['name'];
            var count = lastId;
            lastId++;
            var image = myData[conType][i]['image'];
            var model_id = myData[conType][i]['id'];
            scene += '<div class="content-block model-block" id="model_' + count + '" data-content-id="' + model_id + '"> \
                            <a href="#popupDialog" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + image + '" alt="scene-title"></a> \
                            <a href="#popupDialog" data-scene-click="' + model_id + '" data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#popupDialog" class="ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right"><span class="ui-btn-inner"><span class="ui-btn-text">' + name + '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a> \
                    </div>';
            myobj.scenesLoaded[conType][model_id] = myData[conType][i];
        }
        if (myobj.scenesRendered[conType] == null) {
            myobj.scenesRendered[conType] = scene;
        } else {
            myobj.scenesRendered[conType] = myobj.scenesRendered[conType] + scene;
            myobj.scenesRendered[conType] = scene;
        }
        //console.log(myobj.scenesLoaded);
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    // add content to page
    var selector = '.content-guts.all-models';
    $(selector).append(print(clips));
    // add pagebar
    padPagebarCreate(selector, clips['info']['currentPage'], clips['info']['totalPages'], conType);
    // bind clicks to scenes
    bindScenes('.content-block.model-block', conType);
}

var successSites = function(json) {
    var conType = 'sites';
    var myData = this.myobj.parseJSON(json);
    var sites = clone(myData[conType]);
    //console.log(sites);

    // remove a couple sites: hustler, his
    var sitesRemove = [
        'hustler',
        'his-classics',
'hustler-girls', // need logo
    ];
    var remove = [];
    for (var i in sites) {
        for (var j in sitesRemove) {
            if (sites[i]['site_code'] == sitesRemove[j]) {
                remove.push(i);
            }
        }
    }
    var sites2 = clone(sites);
    while (remove.length > 0) {
        var s = remove.pop();
        sites2.splice(s, 1);
    }
    sites = sites2;
    //console.log(sites);

    var bindSceneSite = function (selector, id, name, title_name, ss, date) { // type = 'videos';
        // bind clicks...
        //console.log('add click events to: ' + selector);
        //console.log(jQuery._data( $(selector).get(0), "events" )); // show events
        $(selector).on("tap", function (e) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
            var x = id;
            var scene_name = name;
            var scene_description = formatDescription(' ');

            var popup_image_width = '240px';
            date = date + ' Rating: <span> </span>';

            $('#popupDialog div:eq(0) h1').text(scene_name);
            $('#popupDialog div:eq(1) div:eq(1) h3').text(title_name);
            $('#popupDialog div:eq(1) div:eq(0) ul li img').attr('src', ss);
            $('#popupDialog div:eq(1) div:eq(0) ul li img').css('width', popup_image_width);

            $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);
            $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);

            var options = {};
            hmajax.getDetailScene(x, options, successSceneDetail);

        });
    }

    // callback for each site call. Get latest clip
    var successSite = function(json) {
        var myData = this.myobj.parseJSON(json);
        var clip = myData['videos'][0];
        clearPageCount++;
        if (clearPageCount == clearPage) {
            //console.log('show page at ' + this.successOptions.siteCode);
            this.successOptions.pageLoadedFn();
        }
        //console.log(this.successOptions.siteCode);
        //console.log(clip);

        var update = '<div class="site-update"> \n\
                <p>Latest Update</p> \n\
                <a href="#popupDialog" data-icon="play" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"> \n\
                    <img src="' + clip.ss + '" alt="scene-title"> \n\
                </a> \n\
                <a href="#popupDialog" data-role="button" data-icon="" data-inline="true" data-mini="true" \n\
                    data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" \n\
                    data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" \n\
                    aria-owns="#popupDialog" class="ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right"> \n\
                    <span class="ui-btn-inner">\n\
                        <span class="ui-btn-text">' + clip.title + '</span>\n\
                    </span>\n\
                </a> \n\
            </div>';
        $('.content-block.' + this.successOptions.cssClass).append(update);
        // if image is missing...
        $('.site-update a img').on("error", function() {
            //console.log('site image broken');
            $(this).attr('src', 'images/placeholder/video-placeholder.png');
        });        
        bindSceneSite('.content-block.' + this.successOptions.cssClass + ' .site-update a', clip.id, clip.name, clip.title_name, clip.ss, clip.date);
    };
    
    var pageLoaded = function (msg) {
        $.mobile.hidePageLoadingMsg();
        //$('#content').css('opacity', '1');
        $('#content').fadeTo(250, 1,  $.noop());//  fade out content page, speed in milliseconds. Opacity 1
    };

    clearPageCount = 0;
    clearPage = sites.length - 1;
    for (var i in sites) {        
        var siteCode = sites[i]['site_code'];
        if (siteCode != 'hustler') {
            var desc, cssClass;
            switch(siteCode) {
                case 'barely-legal':
                    desc = 'Barel Legal - The Youngest and Tightest Girls on the Web';
                    cssClass = 'bl';
                    break;
                case 'anal-hookers':
                    desc = 'Hustler&rsquo;s Anal Hookers - Cum thru the backdoor!';
                    cssClass = 'ah';
                    break;
                case 'beaver-hunt':
                    desc = 'BeaverHunt - The nastiest amateurs on the Net';
                    cssClass = 'bh';
                    break;
                case 'busty-beauties':
                    desc = 'Busty Beauties - It would be a pity to miss out on these titties!';
                    cssClass = 'bb';
                    break;
                case 'hustler-parodies':
                    desc = 'Hustler Parodies - All your favs remade Hustler Style!';
                    cssClass = 'parodies';
                    break;
                case 'hustlers-taboo':
                    desc = 'Hustler&rsquo;s Taboo - The Kinkiest Site on the Web!';
                    cssClass = 'tb';
                    break;
                case 'vcaxxx':
                    desc = 'VCA Classics - The Classics Never Die!';
                    cssClass = 'vca';
                    break;
                case 'asian-fever':
                    desc = 'Asian Fever - The girls of the Orient have never been hotter than this!';
                    cssClass = 'af';
                    break;
                case 'hustlers-lesbians':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x1';
                    break;
                case 'hottie-moms':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x2';
                    break;
                case 'daddy-gets-lucky':
                    desc = 'daddy-gets-lucky';
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x3';
                    break;
                case 'hustlaz':
                    desc = 'hustlaz';
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x4';
                    break;
                case 'bossy-milfs':
                    desc = 'bossy-milfs';
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x5';
                    break;
                case 'too-many-trannies':
                    desc = 'too-many-trannies';
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x6';
                    break;
                case 'scary-big-dicks':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x7';
                    break;
                case 'hustler-girls':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x8';
                    break;
                case 'hustlers-college-girls':
                    desc = 'hustlers-college-girls';
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x9';
                    break;
                case 'hustler-hd':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x10';
                    break;
                case 'hometown-girls':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x11';
                    break;
                case 'muchas-latinas':
                    desc = 'NEED DESCRIPTION, CSS CLASS';
                    cssClass = 'x12';
                    break;
            }
            // add site's content section to layout
            var d=document.createElement('div');
            $(d).addClass('content-block')
                .addClass('site-block')
                .addClass(cssClass)
                .html('<div class="site-name"> \n\
                    <a href="' + '#' + siteCode + '" class="ui-link"><img src="images/logos/' + siteCode + '.png" alt="' + siteCode + '"></a> \n\
                    <p>' + desc + '</p> \n\
                    <a href="#" data-role="button" data-inline="true" data-mini="true" class="btn-sites ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all ui-mini ui-btn-inline" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c"><span class="ui-btn-inner"><span class="ui-btn-text">View All Scenes</span></span></a> \n\
                    </div>')
                .appendTo($(".all-sites"))
                .click({ site: siteCode }, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    hmCon.clearQuery();
                    hmCon.query.page = 'home';
                    hmCon.changeSite(e.data.site);
                });

            // do ajax call for site
            hmCon.ajax.clearScenes();
            hmCon.ajax.setType('videos');
            hmCon.ajax.setSite(siteCode);
            hmCon.ajax.setOrder(0);
            hmCon.ajax.setCode('latest');
            hmCon.ajax.get(
                1, // page number
                false,
                [
                    // success callback functions
                    successSite,
                ],
                { 
                    //some data for success
                    siteCode: siteCode,
                    cssClass: cssClass,
                    pageLoadedFn: pageLoaded
                }
            );
        }
    }
}

function popUpCookie(conType) {
    // Check cookie for showing the previously navigated popup for current page.
    // This is useful for when user returns to page after viewing a video
    if ($.getCookie("hPadPopup") && JSON.parse($.getCookie("hPadPopup")) != null) {
        var cookie = JSON.parse($.getCookie("hPadPopup"));
        if (cookie.type == conType) {
            //console.log('we have COOKIE, which we will use to show a popup:');
            //console.log(cookie);
            // if we are in search results page, let's hide all the content except for the content of the cookie
//            if (atResultsPage()) {
//                //console.log('POPUP ACTIVATED. At RESULTS Page.');
//                var selector;
//                switch (conType) {
//                    case 'videos':
//                        selector = '.video-search';
//                        break;
//                    case 'dvds':
//                        selector = '.dvd-search';
//                        break;
//                    case 'photos':
//                        selector = '.photo-search';
//                        break;
//                    case 'models':
//                        selector = '.model-search';
//                        break;
//                }
//                searchCollapseToggle(selector, false);
//            }
            if (cookie.url == window.location.href) {
                $.mobile.silentScroll(0); //jquery mobile way to scroll to top
                //console.log($('a[data-scene-click="' + cookie.scene + '"]'));
                //var x = jQuery._data( $('a[data-scene-click="' + cookie.scene + '"]').get(0), "events" ); console.log(x);
                $('a[data-scene-click="' + cookie.scene + '"]').tap();
                return true;
            }
            $.setCookie("hPadPopup", null); // remove cookie
        } else if (atResultsPage() && conType != 'videos') {// && conType != 'videos' // assures that videos will remain open always
            //console.log('POPUP *NOT* ACTIVATED. At RESULTS Page, so collapse all but videos');
//            var selector;
//            switch (conType) {
//                case 'videos':
//                    selector = '.video-search';
//                    break;
//                case 'dvds':
//                    selector = '.dvd-search';
//                    break;
//                case 'photos':
//                    selector = '.photo-search';
//                    break;
//                case 'models':
//                    selector = '.model-search';
//                    break;
//            }
//            searchCollapseToggle(selector, true);
        }
    }
    return false;
}

function atResultsPage() {
    switch (hmCon.query.page) {
        //case 'd': not dvd since it's just one set of content, videos
        case 'c':
        case 'm':
        case 'fav':
        case 'search':
            return true;
            break;
        default:
            return false;
            break;
    }
}

var padPagebarCreate = function(content, currentPage, totalPages, pagebarId) {
    if (totalPages == 1) {
        // only one page, don't need this!
        return false;
    }

    var pagebar = 'page-bar-' + pagebarId    
    $(content).append(hmCon.htmlWidgets.pagination(pagebar, currentPage, totalPages));
    pagebar = '#' + pagebar;
    $(pagebar).trigger("create");

    // bind events for pagebar

    var fnPageChange = function (e) {
        e.preventDefault();
        e.stopPropagation();
        fnGo(0, e.data.pagebar);
    };

    var fnPageArrow = function (e) {
        //console.log('scrollbar change prev/next by ' + e.data.add);
        e.preventDefault();
        e.stopPropagation();
        if (e.data.active) {
            fnGo(e.data.add, e.data.pagebar);  // change "add" many pages
        }
    };

    var fnGo = function (add, pagebarId) {
        if (add === undefined) {
            add = 0;
        }
        //var newPage = $(pagebar + ' .ui-slider-popup').text(); // the popup text-- not always reliable
        var newPage = $(pagebar + ' input[type=number]').val();
        if (newPage == currentPage && add == 0) {
            //console.log('same page! nevermind ' + newPage);
            return false;
        }
        var x = +newPage + +add;
        // let's figure out first how to change the page... If we are in search pages, then page change will behave differently
        //console.log("CHECK: " + pagebarId.substr(17));
        if (pagebarId.substr(1,15) == 'page-bar-search') {
            hmCon.changeSearchPageNum(hmCon.query.page, pagebarId.substr(17), x);
        } else {
            hmCon.changePageNum(x);
        }        
        //$("html, body").animate({ scrollTop: 0 }, "slow"); // scroll to top. Do this at a better level
    };

    $(pagebar + ' input').on("slidestop", { pagebar: pagebar }, fnPageChange); // read note below...

    $(pagebar + ' div input[type="submit"]').on("tap", { pagebar: pagebar }, fnPageChange);
    if (currentPage != 1) {
        $(pagebar + ' a.prev').on("tap", { active: true, add: -1, pagebar: pagebar }, fnPageArrow);
    } else {
        $(pagebar + ' a.prev').on("tap", { active: false, add: -1, pagebar: pagebar }, fnPageArrow);
    }
    if (currentPage != totalPages) {
        $(pagebar + ' a.next').on("tap", { active: true, add: 1, pagebar: pagebar }, fnPageArrow);
    } else {
        $(pagebar + ' a.next').on("tap", { active: false, add: 1, pagebar: pagebar }, fnPageArrow);
    }
}

function bindScenes (selector, type) { // type = 'videos';
    // bind clicks...
    // globals: joinPad, stopPropagation, popupExpire, hmajax
    //console.log('add click events to: ' + selector);
    //console.log(jQuery._data( $(selector).get(0), "events" )); // show events
    var modelPopup = false; // set to true if we want model popup to show up, instead of going directly to results
    if ($.siteType() == 'tour' && type == 'mags') {
        // these click to join page
        $(selector).on("tap", function (e) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
            window.location = joinPad;
        });
        return true;
    }
    if (modelPopup !== true && type == 'models') {
        // go directly to their results page
        $(selector).on("tap", function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            var x = $(this).attr('data-content-id'); // scene ID, model ID, etc.
            var title_name = hmajax.scenesLoaded[type][x]['name'];
            hmCon.search('m', x, {nameSeo: seo_url(title_name)});
        });
        return true;
    }
    $(selector).on("tap", function (e) {
        // global: stopPropagation, hmajax, joinPad
        if (stopPropagation == true) {
	    // This was once a problem. Scenes weren't getting popups after a textsearch was done (with search box). But it's okay as long as search box is closed properly.
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
        }

        if ($.siteType() == 'members') {
            //favorite's icon!
            if ($(e.target).hasClass('ui-icon-plus') || $(e.target).hasClass('ui-icon-minus')) {
                successFaveToggle($(this).attr('data-content-id'), type);
                return true;
            }
        }
        
        var x = $(this).attr('data-content-id'); // scene ID, model ID, etc.
        var scene_name = hmajax.scenesLoaded[type][x]['name'];
        if (type == 'models' || type == 'mags') {
            var title_name = scene_name;
            var scene_description = '.';
        } else {
            var title_name = hmajax.scenesLoaded[type][x]['title_name'];
            var scene_description = formatDescription(hmajax.scenesLoaded[type][x]['description'] + ' ');
        }
        //var title_name = hmajax.scenesLoaded[type][x]['imag'];
        if (type == 'videos') {
            popup_image_width = '240px';
            var ss = hmajax.scenesLoaded[type][x]['ss'];
        }
        else {
            var ss = hmajax.scenesLoaded[type][x]['image'];
            popup_image_width = '214px';
        }

        var rating = '';
        if (hmajax.scenesLoaded[type][x]['average_score'] !== undefined) rating = hmajax.scenesLoaded[type][x]['average_score'];
        var date = hmajax.scenesLoaded[type][x]['date'] + ' Rating: <span>' + rating + '</span>';
        
        $('#popupDialog div:eq(0) h1').text(scene_name);
        $('#popupDialog div:eq(1) div:eq(1) h3').text(title_name);
        $('#popupDialog div:eq(1) div:eq(0) ul li img').attr('src', ss);
        $('#popupDialog div:eq(1) div:eq(0) ul li img').css('width', popup_image_width);
        //$('#popupDialog div:eq(1) div:eq(0) ul li img').css('height', '266px');
        
        $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);
        $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);
        //console.log($('#popupDialog'));

        // category buttons
        var keys = [];
        if (type != 'models' && type != 'videos' && type != 'mags') {
            for (var k in hmajax.scenesLoaded[type][x]['categories'])keys.push(k)
        }
        bindPopupCategories(keys);

        // model buttons
        var tmp;
        if (type != 'models' && type != 'videos' && type != 'mags') {
            tmp = null;
        } else {
            tmp = hmajax.scenesLoaded[type][x]['performers_data'];
        }
        bindPopupModels(tmp);

        if (type == 'videos') {
            var options = {ss : ss};
            hmajax.getDetailScene(x, options, successSceneDetail);

        } else if (type == 'photos') {
           
            //$('.btn-play').attr("href","photoswipe.html");

            $('.btn-play').attr("id",x);
            //$('.btn-play').addClass("btn_get_photo_listing");
            $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">View Photos</span></span>');
            // attach click event
            // previous version, .live() was used, but it's removed in jquery 1.9, so have to use .on()
            $('.btn-play').off("tap");
            $('.btn-play').on("tap",  {pageUrl: window.location.href}, function(e) {
                closePopUp();
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                // add cookie for when user goes back to page, to show popup
                $.setCookie("hPadPopup", JSON.stringify({
                    type: type,
                    url: e.data.pageUrl,
                    scene: x
                }), popupExpire);
                //alert($(this).attr("id"));
                photoset_id = $(this).attr("id");
                //alert(photoset_id);
                //console.log(photoset_id);
                hmCon.changePageListingDetail('photo_listing_detail', photoset_id, scene_name );
            });
            
            // get more details of this photoset/scene
            var options = {};
            hmajax.getDetailScene(x, options, function(json) {
                var clips = this.myobj.parseJSON(json);
                //console.log('got photo scene...');
                //console.log(clips);
                var title_name = clips['info']['contentDetails']['scene']['title_name'];
                if (title_name == null) {
                    title_name = clips['info']['contentDetails']['scene']['name'];
                    $('#popupDialog div:eq(1) div:eq(1) h3').text(title_name);
                }
                var x = clips['info']['contentDetails']['scene']['id'];
                $('#popupDialog div:eq(1) div:eq(1) h3').text(title_name);
                //scene_name = clips['info']['contentDetails']['scene']['name'];
                var scene_description = formatDescription(clips['info']['contentDetails']['scene']['scene_description']);
                var rating = clips['info']['contentDetails']['scene']['average_score'];
                date = clips['info']['contentDetails']['scene']['date'] + ' Ratings: <span>' + rating + '</span>';

                $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);
                $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);

                // category buttons
                var keys = [];
                for (var k in clips['info']['contentDetails']['scene']['categories']) {
                    keys.push(k);
                }
                bindPopupCategories(keys);

                // model buttons
                var tmp = clips['info']['contentDetails']['scene']['performers_data'];
                bindPopupModels(tmp);
                
                //fave button
                bindFave('photo', clips['info']['contentDetails']['scene'], x, this.myobj.url);

                if (stopPropagation == true) {
                    $('#popupDialog').popup("open"); // must manually open popup because of events propagation having been stopped
                } else {
                    //$('#popupDialog').popup("open"); // popup opens twice, but it's faster then waiting for overlay transition fadeout.
                }
            });

        } else if (type == 'models') {
            var successOptions = { }
            hmajax.getDetailModel(x, successOptions, function(json) {
                var clips = this.myobj.parseJSON(json);
                //console.log(clips);
                scene_name = 'Model';
                var scene_description = formatDescription(clips['info']['contentDetails']['model']['performer_description']);
                var rating = clips['info']['contentDetails']['model']['average_score'];
                date = clips['info']['contentDetails']['model']['performer_timestamp'] + ' Ratings: <span>' + rating + '</span>';

                $('#popupDialog div:eq(0) h1').text(scene_name);
                $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);
                $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);

                // category buttons
                var keys = [];
                for (var k in clips['info']['contentDetails']['model']['categories']) {
                    keys.push(clips['info']['contentDetails']['model']['categories'][k]['category_id']);
                }
                bindPopupCategories(keys);
                
                //fave button
                bindFave('model', clips['info']['contentDetails']['model'], x, this.myobj.url);

                if (stopPropagation == true) {
                    $('#popupDialog').popup("open"); // must manually open popup because of events propagation having been stopped
                } else {
                    //$('#popupDialog').popup("open"); // popup opens twice, but it's faster then waiting for overlay transition fadeout.
                }
            });

            //e.preventDefault();
            //e.stopPropagation();
            $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">View Model</span></span>');

            // attach click event
            // previous version, .live() was used, but it's removed in jquery 1.9, so have to use .on()
            $('.btn-play').off("tap");
            $('.btn-play').on("tap",  {pageUrl: window.location.href}, function(e) {
                closePopUp();
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                // add cookie for when user goes back to page, to show popup
                $.setCookie("hPadPopup", JSON.stringify({
                    type: type,
                    url: e.data.pageUrl,
                    scene: x
                }), popupExpire);
                hmCon.search('m', x, {nameSeo: seo_url(title_name)});
            });
        } else if (type == 'mags') {
            var successOptions = { }
            hmajax.getDetailMag(x, successOptions, function(json) {
                var clips = this.myobj.parseJSON(json);
                var clip = clips['info']['contentDetails']['mag'];
                //console.log(clips);
                // type, description (usually blank), score, date
                $('#popupDialog div:eq(0) h1').text(clip['type']);
                var scene_description = formatDescription(clip['desc']);
                $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);
                var date = clip['issue'] + ' Ratings: <span>' + clip['rating'] + '</span>';
                $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);

                // fave button
                bindFave('mag', clip, x, this.myobj.url);

                if (stopPropagation == true) {
                    $('#popupDialog').popup("open"); // must manually open popup because of events propagation having been stopped
                } else {
                    //$('#popupDialog').popup("open"); // popup opens twice, but it's faster then waiting for overlay transition fadeout.
                }
            });

            // image
            var popup_image_width = '214px';
            $('#popupDialog div:eq(1) div:eq(0) ul li img').css('width', popup_image_width);
            $('#popupDialog div:eq(1) div:eq(0) ul li img').attr('src', hmajax.scenesLoaded['mags'][x]['img_front']);
 
            // play button
            $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">View Digital Mag</span></span>');
            // attach click event
            var link;
            $.siteType() == 'members' ? link = hmajax.scenesLoaded[type][x]['content'] : link = joinPad;
            $('.btn-play').off("tap");
            $('.btn-play').on("tap",  {pageUrl: window.location.href}, function(e) {
                closePopUp();
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                // add cookie for when user goes back to page, to show popup
                $.setCookie("hPadPopup", JSON.stringify({
                    type: type,
                    url: e.data.pageUrl,
                    scene: x
                }), popupExpire);
                //console.log('view magazine at ' + hmajax.scenesLoaded[type][x]['content']);
                window.location = link;
            });
        }
    });
}

function bindScenesDvd (selector) { // DVDs
    // bind clicks...
    //console.log('add click events to: ' + selector);
    //console.log(jQuery._data( $(selector).get(0), "events" )); // show events
    $(selector).on("tap", function (e) {
        //global: stopPropagation, hmajax, hmCon
        if (stopPropagation == true) {
            // may be disabled for same reason as in bindScenes()
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
        }

        var type = 'dvds';
        var id = $(this).attr('data-content-id'); // scene ID, model ID, etc.
        var img_front = hmajax.scenesLoaded[type][id]['img_front'];
        var date = hmajax.scenesLoaded[type][id]['published'];
        // get updated info of this DVD
        var successOptions = { // pass to our callback
            img_front: img_front,
            date: date
        };
        hmajax.getDetailDvd(hmajax.scenesLoaded[type][id]['grp'], successOptions, function(json) {
            // assumes we only need page 1
            var clips = this.myobj.parseJSON(json);
            //console.log(clips);
            var titleName = clips['info']['contentDetails']['dvd']['group_name'];
            var sceneName = 'DVD';
            var scene_description = formatDescription(clips['info']['contentDetails']['dvd']['group_description']);
            var dvdId = id;
            var groupId = clips['info']['contentDetails']['dvd']['group_id'];
            var popup_image_width = '214px';
            var rating = clips['info']['contentDetails']['dvd']['average_score'];
            var date = this.successOptions.date + ' Rating: <span>' + rating + '</span>';
            var ss = this.successOptions.img_front;

            $('#popupDialog div:eq(0) h1').text(sceneName);
            $('#popupDialog div:eq(1) div:eq(1) h3').text(titleName);
            $('#popupDialog div:eq(1) div:eq(0) ul li img').attr('src', ss);
            $('#popupDialog div:eq(1) div:eq(0) ul li img').css('width', popup_image_width);
            //$('#popupDialog div:eq(1) div:eq(0) ul li img').css('height', '266px');

            $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);
            $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);

            // category buttons
            var keys = [];
            for (var k in clips['info']['contentDetails']['dvd']['categories']) {
                keys.push(clips['info']['contentDetails']['dvd']['categories'][k]['category_id']);
            }
            bindPopupCategories(keys);        

            // model buttons
            var performers_data = [];
            for (var k in clips['info']['contentDetails']['dvd']['performers']) {
                // get females only
                if (clips['info']['contentDetails']['dvd']['performers'][k]['performer_gender'].substr(0,1).toLowerCase() == 'f') {
                    performers_data.push({
                        id: clips['info']['contentDetails']['dvd']['performers'][k]['performer_id'],
                        name_full: clips['info']['contentDetails']['dvd']['performers'][k]['performer_name'],
                        name_seo: seo_url(clips['info']['contentDetails']['dvd']['performers'][k]['performer_name'])
                    });
                }

            }
            bindPopupModels(performers_data);

            //fave button
            bindFave('dvd', clips['info']['contentDetails']['dvd'], groupId, this.myobj.url);

            $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">View DVD Scenes</span></span>');
            $('.btn-play').off("tap");
            $('.btn-play').on("tap",  {pageUrl: window.location.href, dvdId: dvdId, groupId: groupId, titleName: titleName}, function(e) {
                    closePopUp();
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    var nameSeo = seo_url(titleName);
                    // add cookie for when user goes back to page, to show popup
                    $.setCookie("hPadPopup", JSON.stringify({
                        type: type,
                        url: e.data.pageUrl,
                        scene: e.data.dvdId
                    }), popupExpire);
                    
                    //console.log('go to DVD page! Go to results page: hmCon.changePageDvd("' + e.data.groupId + '")');
                    hmCon.search('d', e.data.groupId, {nameSeo: nameSeo});
                    //hmCon.changePageDvd(id);
            });
            if ($.siteType() != 'members') {
                var width = $('.btn-play').css('width');
                $('.my-dialog .dialog-content:eq(0)').css('width',width);
                $('.btn-join').css('display', 'inline-block');
                $('.btn-join .ui-btn-text').text('Join Now');
                $('.btn-play').css('width', width);
                $('.btn-join').css('width', width);
            }
            if (stopPropagation == true) {
                // disable because we could not prevent default and propagation :(
                $('#popupDialog').popup("open");
            } else {
                if (hmCon.query.page == 'home') {
                    // the touchCarousel doesn't bubble up event?
                    $('#popupDialog').popup("open");
                }
            }
        });
    });
}

function bindPopupCategories(categoryIds) {
    $('#popupDialog .categories').empty();
    if (categoryIds.length == 0 || categoryIds == null) {
        // no categories
        $('#popupDialog .categories').prev().css('display', 'none'); // hide text "Tags:"
        return false;
    }
    $('#popupDialog .categories').prev().css('display', 'block'); // unhide text "Tags:"

    for(var i in categoryIds) {
        if (hmCon.staticData.categories[categoryIds[i]] !== undefined) {
            var fullName = hmCon.staticData.categories[categoryIds[i]].fullName;
            var x = $('<a href="#" data-role="button" data-inline="true" data-mini="true" class="btn-tags ui-btn ui-shadow \n\
                ui-btn-corner-all ui-mini ui-btn-inline ui-btn-up-d" data-corners="true" data-shadow="true" \n\
                data-iconshadow="true" data-wrapperels="span" data-theme="d"><span class="ui-btn-inner"> \
                <span class="ui-btn-text">' + fullName + '</span></span></a>');
            x.on('vclick', { cat: categoryIds[i] }, function (e) {
                e.preventDefault();
                e.stopPropagation();
                closePopUp();
                hmCon.search('c', e.data.cat);
            });
            $('#popupDialog .categories').append(x);

        }
    }
}

function bindPopupModels(models) {
    // models = null -- if there are no models shown
    $('#popupDialog .models').empty();
    if (models == null) {
        $('#popupDialog .models').prev().css('display', 'none'); // hide text "Stars:"
        return false;
    }
    $('#popupDialog .models').prev().css('display', 'block'); // unhide text "Stars:"
    for(var i in models) {
        var nameFull = models[i].name_full;
        var comma;
        if (parseInt(i) + 1 == parseInt(models.length)) {
            comma = '';
        } else {
            comma = ',&nbsp;&nbsp;';
        }
        var x = $('<a href="#" class="ui-link">' + nameFull + '</a><span>' + comma + '</span>');
        x.on('vclick', { modelId: models[i].id, nameSeo: models[i].name_seo }, function (e) {
            e.preventDefault();
            e.stopPropagation();
            closePopUp();
            hmCon.search('m', e.data.modelId, {nameSeo: e.data.nameSeo});
        });
        $('#popupDialog .models').append(x);
    }
}

function successSceneDetail (json) {
    //globals: stopPropagation, joinPad, popupExpire
    var clips = this.myobj.parseJSON(json);
    var id = clips['info']['contentDetails']['scene']['id'];
    var type = 'videos';
    //console.log('got scene...'); console.log(clips);
    var scene_description = formatDescription(clips['info']['contentDetails']['scene']['scene_description']);
    var rating = clips['info']['contentDetails']['scene']['average_score'];
    var date = clips['info']['contentDetails']['scene']['date'] + ' Ratings: <span>' + rating + '</span>';
    var ss = this.successOptions.ss;

    $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty().append(date);
    $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty().append(scene_description);

    // category buttons
    var keys = [];
    for (var k in clips['info']['contentDetails']['scene']['categories']) {
        keys.push(k);
    }
    bindPopupCategories(keys);

    // model buttons
    var tmp = clips['info']['contentDetails']['scene']['performers_data'];
    bindPopupModels(tmp);

    // bind play button, if clip is available
    var clip = clips['info']['contentDetails']['scene']['dl_clip'];
    if (clip !== null) {
        var str;
        $.siteType() == 'members' ? str = 'Play Movie' : str = 'See Preview';
        $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">' + str + '</span></span>');
        $('.btn-play').off("tap");
        $('.btn-play').on("tap", {id: id, type: type, clip: clip, ss: ss}, function(e) {
            //console.log('let\'s download video for: ' + id);
            closePopUp();
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

           // add cookie for when user goes back to page, to show popup
            $.setCookie("hPadPopup", JSON.stringify({
                type: type,
                url: window.location.href,
                scene: id
             }), popupExpire);
            successPlay(clip, ss);
        });
        if ($.siteType() != 'members') {
            var width = $('.btn-play').css('width');
            $('.my-dialog .dialog-content:eq(0)').css('width',width);
            $('.btn-join').css('display', 'inline-block');
            $('.btn-join .ui-btn-text').text('See Full Video');
            $('.btn-play').css('width', width);
            $('.btn-join').css('width', width);            
        }
    } else {
        // clip is not available
        if ($.siteType() == 'members') {
            // members
            $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">Coming Soon</span></span>');
            $('.btn-play').off("tap");
        } else {
            // tours
            $('.btn-play').empty().append('<span class="ui-btn-inner"><span class="ui-btn-text">Join Now</span></span>');
            $('.btn-play').off("tap");
            $('.btn-play').on("tap", {}, function(e) {
                //console.log('let\'s download video for: ' + id);
                closePopUp();
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

               // add cookie for when user goes back to page, to show popup
                $.setCookie("hPadPopup", JSON.stringify({
                    type: type,
                    url: window.location.href,
                    scene: id
                 }), popupExpire);
                window.location = joinPad;
            });
        }
    }
        
    // fave button
    bindFave('video', clips['info']['contentDetails']['scene'], id, this.myobj.url);

    if (stopPropagation == true) {
        $('#popupDialog').popup("open"); // must manually open popup because of events propagation having been stopped
    } else {
        //$('#popupDialog').popup("open"); // popup opens twice, but it's faster then waiting for overlay transition fadeout.
    }
}

function toggleDvdSwitch(selector, turnOn) {
    // Create the toggle switch.
    // 
    // On = user selected DVDs
    // Off = user selected Videos (default)
    // 
    // Change page if we're not in the page toggled
    //
    // turnOn = true if we want it set On (for DVD)
    
    var slider = '#dvds';
    if (turnOn === true) {
        $(selector).append(hmCon.htmlWidgets.videoSlider);
    } else {
        $(selector).append(hmCon.htmlWidgets.dvdSlider);
    }
    $(slider).slider(); // initialize slider by calling plug-in
    $('.flip-switch').addClass('ui-field-contain').addClass('ui-body').addClass('ui-br'); // grr... have to add some styles manually
    if (turnOn === true) {
        $(slider).val('on');
        $(slider).slider('refresh');
    }
    $(slider).on('slidestop', function(event, ui) {
        var slider = '#dvds';
        var state = $(slider).val();
        var page = hmCon.query.page;
        if (state == 'on' && page != 'dvd') {
            // dvd toggled
            hmCon.changePage('dvd');
        } else if(state == 'off' && page != 'video') {
            // video toggled
            hmCon.changePage('video');
        }
    });
}

function successPlay(clip, ss) {
    window.location = 'vid.php?clip=' + clip + '&ss=' + ss;
}

function closePopUp() {
    $('#popupDialog .ui-header a').click();
}

function getImageField(type) {
    if (type == 'models' || type == 'photos') return 'image';
    if (type == 'mags' || type == 'dvds') return 'img_front';
    return 'ss';
}

function seo_url(input){
    input = input.toLowerCase();
    return input.replace(/\s+/g, '-');
}

function formatDescription(str) {
    var newStr = str;
    if (! strEmpty(str)) {
        var y = newStr.length;
        for (z=y; z<200; z++) {
            newStr += '&nbsp;';
        }
    }
    return newStr;
}

function strEmpty(str) {
    return (!str || /^\s*$/.test(str));
}

// Restore the look of the top part: Get the site's header back, hide back button, etc..
// when exiting the photo gallery page
function deactivate_photodetail_elems() {
    // remove the back button ------------
    if ( $("#photodetail_back_btn").is(":visible")) {
        $("#photodetail_back_btn").hide();
    }
    // re-show the ipad's site Header block ------------
    if ( $('div[data-role="subheader"]').is(":hidden")) {
        $('div[data-role="subheader"]').show();
    }
    // re-show the hustler.com logo image at top, but get rid of the photo scene name as well
    if ( $('#header_logo_wrap a.logo').is(":hidden")) {
        $('#header_logo_wrap a.logo').show();
        $('#header_logo_wrap a.gallery-title').hide();
        
    }
    
}