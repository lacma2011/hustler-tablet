var countImages = []; // an array, with an index for each content type (assuming only one type per page), to count images loaded for each type

var successSearch = function(json) {
    var conType = this.successOptions.type;
    var page = this.successOptions.page;    
    var clips = this.myobj.parseJSON(json);

    // API failed
    if (clips == null) {
        //console.log('API FAIL for ' + conType);
        this.successOptions.pageInitFn();
        this.successOptions.pageLoadedFn();
        
        // print negative results message
        switch (conType) {
            case 'videos':
                successSearchNone('.video-search', 'Videos')
                break;
            case 'models':
                successSearchNone('.model-search', 'Models')
                break;
            case 'dvds':
                successSearchNone('.dvd-search', 'DVDs')
                break;
            case 'photos':
                successSearchNone('.photo-search', 'Photos')
                break;
            case 'mags':
                successSearchNone('.mag-search', 'Magazines')
                break;
        }
        return false;
    }

    // possible: may need content ID of content that was searched for
    var contentId = null;
    if (page == 'd') {
        contentId = clips['info']['params']['videos_in_dvd'];
    }
    if (page == 'm') {
        contentId = clips['info']['params']['model'];
    }

    // API returned no results
    if ((clips[conType].length == 0)) {
        //console.log('API 0 results for ' + conType);
        if (page == 'd') {
            this.successOptions.pageInitFn(undefined, undefined, contentId)
        }
        else if (page == 'm') {
            this.successOptions.pageInitFn(undefined, undefined, contentId)
        } else {
            this.successOptions.pageInitFn(page);
        }
        this.successOptions.pageLoadedFn();
        return false;
    }

    //console.log('successSearch(): ' + conType);
    //console.log(clips);
    //console.log(conType + ': clearPageCount: ' + clearPageCount + ' clearPage:' + clearPage);

    // prepare the favorites object
    hmajaxFave.setUrl(this.myobj.url);

    if (page == 'd') {
        this.successOptions.pageInitFn(undefined, undefined, contentId)
    }
    else if (page == 'm') {
        this.successOptions.pageInitFn(undefined, undefined, contentId)
    } else {
        this.successOptions.pageInitFn(page);
    }

    switch (conType) {
        case 'videos':
            successSearchVideo(clips);
            selector = '.video-block';
            break;
        case 'models':
            successSearchModel(clips);
            selector = '.model-block';
            break;
        case 'dvds':
            successSearchDvd(clips);
            selector = '.dvd-block';
            break;
        case 'photos':
            successSearchPhoto(clips);
            selector = '.photo-block';
            break;
        case 'mags':
            successSearchMag(clips);
            selector = '.mag-block';
            break;
    }


    // check if all images are loaded, reload if necessary
    loadImageCheck(conType, selector, page, this.successOptions.pageLoadedFn, this.successOptions.jobId);
    
    // check for cookie to do returning popup
    popUpCookie(conType);
    
    // check for cookie if it did returning popup. If it happened, keep this content menu open. Otherwise close/collapse.
    switch (conType) {
        case 'models':
            doCollapsing(conType, '.model-search');
            break;
        case 'dvds':
            doCollapsing(conType, '.dvd-search');
            break;
        case 'photos':
            doCollapsing(conType, '.photo-search');
            break;
        case 'mags':
            doCollapsing(conType, '.mag-search');
            break;
        default:
            break;
    }
}

var successSearchVideo = function (clips) {
    // uses printVideos() for formatting data on page
    var conType = 'videos';
    var selector = '.content-guts .video-search';
    
    $(selector).append( printSearch(clips, printVideos(clips), 'Videos') ); // populate content area
    
    // page bar
    padPagebarCreate(selector + ' .ui-collapsible-content', clips['info']['currentPage'], clips['info']['totalPages'], 'search-' + conType);
    
    // bind clicks to scenes...
    bindScenes('.content-block.video-block', conType);
    
    // bind click to collapse icon
    var contentSection = '.video-search';
    $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
    $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchCollapseToggle(contentSection, true);
    });
}

var successSearchNone = function (selector, title) {
    var selector = '.content-guts ' + selector;
    var clips = {
        'info': {
            'totalRecords': 0,
            'currentPage': 0,
            'totalPages': 0
        }
    }
    $(selector).append( printSearch(clips, '', title) );
    $(selector + ' a.ui-collapsible-heading-toggle').off('vclick');
    $(selector + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchCollapseToggle(selector, true);
    });
}

var successSearchModel = function (clips) {
    // uses printModels() for formatting data on page
    var conType = 'models';
    var selector = '.content-guts .model-search';
    
    // populate content area
    $(selector).append( printSearch(clips, printModels(clips), 'Models') );

    // page bar
    padPagebarCreate(selector + ' .ui-collapsible-content', clips['info']['currentPage'], clips['info']['totalPages'], 'search-' + conType);
    
    // bind clicks to scenes...
    bindScenes('.content-block.model-block', conType);
    
    // bind click to collapse icon
    var contentSection = '.model-search';
    $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
    $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchCollapseToggle(contentSection, true);
    });
}

var successSearchDvd = function (clips) {
    // uses printDvds() for formatting data on page
    var conType = 'dvds'; // page: 'dvds'
    var selector = '.content-guts .dvd-search';

    // populate content area
    $(selector).append( printSearch(clips, printDvds(clips), 'DVDs') );

    // page bar
    padPagebarCreate(selector + ' .ui-collapsible-content', clips['info']['currentPage'], clips['info']['totalPages'], 'search-' + conType);

    // bind clicks to scenes...
    bindScenesDvd('.content-block.dvd-block');

    // bind click to collapse icon
    var contentSection = '.dvd-search';
    $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
    $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchCollapseToggle(contentSection, true);
    });
}

var successSearchPhoto = function (clips) {
    // uses printPhotos() for formatting data on page
    var conType = 'photos';
    var selector = '.content-guts .photo-search';

    // populate content area
    $(selector).append( printSearch(clips, printPhotos(clips), 'Photos') );

    // page bar
    padPagebarCreate(selector + ' .ui-collapsible-content', clips['info']['currentPage'], clips['info']['totalPages'], 'search-' + conType);

    // bind clicks to scenes...
    bindScenes('.content-block.photo-block', conType);
    
    // bind click to collapse icon
    var contentSection = '.photo-search';
    $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
    $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchCollapseToggle(contentSection, true);
    });
}

var successSearchMag = function (clips) {
    // uses printMags() for formatting data on page
    var conType = 'mags';
    var selector = '.content-guts .mag-search';
    
    // populate content area
    $(selector).append( printSearch(clips, printMags(clips), 'Magazines') );

    // page bar
    padPagebarCreate(selector + ' .ui-collapsible-content', clips['info']['currentPage'], clips['info']['totalPages'], 'search-' + conType);
    
    // bind clicks to scenes...
    bindScenes('.content-block.video-block', conType);
    
    // bind click to collapse icon
    var contentSection = '.mag-search';
    $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
    $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchCollapseToggle(contentSection, true);
    });
}

var searchCollapseToggle = function (contentSection, toggle) {
    // collapse the content area
    // var toggle:
    //     true = content is collapsed
    //     false = content is expanded

    if (toggle === true) {
        $(contentSection).addClass('ui-collapsible-collapsed');
        $(contentSection + ' .ui-collapsible-content').addClass('ui-collapsible-content-collapsed');
        $(contentSection + ' .ui-collapsible-content').attr('aria-hidden', 'true');
        $(contentSection + ' .ui-icon').removeClass('ui-icon-minus');
        $(contentSection + ' .ui-icon').addClass('ui-icon-plus');
        $(contentSection + ' .ui-collapsible-heading').addClass('ui-collapsible-heading-collapsed');
        // update click event to COLLAPSE to the icon
        $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
        $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
            e.preventDefault();
            e.stopPropagation();
            searchCollapseToggle(contentSection, false);
        });
    } else {
        $(contentSection).removeClass('ui-collapsible-collapsed');
        $(contentSection + ' .ui-collapsible-content').removeClass('ui-collapsible-content-collapsed');
        $(contentSection + ' .ui-collapsible-content').attr('aria-hidden', 'false');
        $(contentSection + ' .ui-icon').addClass('ui-icon-minus');
        $(contentSection + ' .ui-icon').removeClass('ui-icon-plus');
        $(contentSection + ' .ui-collapsible-heading').removeClass('ui-collapsible-heading-collapsed');
        // update click event to EXPAND to the icon
        $(contentSection + ' a.ui-collapsible-heading-toggle').off('vclick');
        $(contentSection + ' a.ui-collapsible-heading-toggle').on('vclick', function(e) {
            e.preventDefault();
            e.stopPropagation();
            searchCollapseToggle(contentSection, true);
        });
    }

}

var doCollapsing =  function (conType, contentSection) {
    // check for cookie to do returning popup. If it happened, keep this content section open. Otherwise close.
    if (popUpCookie(conType) === true) {
        //console.log('pop up found... keep open');
        searchCollapseToggle(contentSection, false);
    } else {
        //console.log('pop up NOT found... collapse if new search');
        //console.log('my cookie...');
        //console.log($.getCookie('prevPage'));
//        if ($.getCookie('prevPage') != 'search') {
//            searchCollapseToggle(contentSection, true);
//        }
    }
    $.setCookie('searchResults', $.getCookie('searchResults') - 1);
    if ($.getCookie('searchResults') == 0) { // we have done all our results...
        $.setCookie('prevPage', 'search'); // set cookie so that next time we don't collapse...
    }
}