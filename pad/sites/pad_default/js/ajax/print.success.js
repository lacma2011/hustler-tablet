var printVideos = function (myData) {
    // global var: lastId, hmajax
    var myobj = hmajax;
    var conType = 'videos';
    var scene = '';
    for (var i in myData[conType]) {
        //console.log(myData[conType][i]);
        var title = myData[conType][i]['title'];
        var scene_name = myData[conType][i]['name'];
        var title_name = myData[conType][i]['title_name'];
        var count = lastId;
        lastId++;
        var ss = myData[conType][i]['ss'];
        var scene_id = myData[conType][i]['id'];
        scene += '<div class="content-block video-block" id="clip_' + count + '" data-content-id="' + scene_id + '"> \
                        <a href="#popupDialog" data-icon="play" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + ss + '" alt="' + title + '"></a> \
                        <a href="#popupDialog" data-scene-click="' + scene_id + '" data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#popupDialog" class="ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right"><span class="ui-btn-inner"><span class="ui-btn-text">' + scene_name + ' - ' + title_name + '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a> \
                </div>';
        myobj.scenesLoaded[conType][scene_id] = myData[conType][i];
    }
    myobj.scenesRendered[conType] = scene;
    //console.log(myobj.scenesLoaded[conType]);
    return myobj.scenesRendered[conType];
}

var printDvds = function (myData) {
    // global var: lastId, hmajax
    var conType = 'dvds';
    var myobj = hmajax;
    var scene = '';
    for (var i in myData[conType]) {
        //console.log(myData[conType][i]);
        var name = myData[conType][i]['name'];
        var id = myData[conType][i]['id'];
        var img_front = myData[conType][i]['img_front'];
        var count = lastId;
        lastId++;
        scene += ' \
                <div class="content-block dvd-block" id="clip_' + count + '" data-content-id="' + id + '"> \
                             <a href="#popupDialog" data-scene-click="' + id + '" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + img_front + '" alt="' + name + '"></a> \
                </div>';
        myobj.scenesLoaded[conType][id] = myData[conType][i];
    }
    myobj.scenesRendered[conType] = scene;
    //console.log(myobj.scenesLoaded[conType]);
    return myobj.scenesRendered[conType];
}

var printMags = function (myData) {
    // global var: lastId, hmajax
    var conType = 'mags';
    var myobj = hmajax;
    var scene = '';
    for (var i in myData[conType]) {
        //console.log(myData[conType][i]);
        var name = myData[conType][i]['name'];
        var id = myData[conType][i]['id'];
        var img_front = myData[conType][i]['img_front'];
        var count = lastId;
        lastId++;
        scene += '<div class="content-block mag-block" id="clip_' + count + '" data-content-id="' + id + '"> \
                            <a href="#popupDialog" data-scene-click="' + id + '" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + img_front + '" alt="' + name + '"></a>\
                            <a href="#popupDialog" data-scene-click="' + id + '"  data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#magDialog" class="ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right"><span class="ui-btn-inner"><span class="ui-btn-text">' + name + '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a>\
                </div>';
        myobj.scenesLoaded[conType][id] = myData[conType][i];
    }
    myobj.scenesRendered[conType] = scene;
    //console.log(myobj.scenesLoaded[conType]);
    return myobj.scenesRendered[conType];
}

var printPhotos = function (myData) {
    // global var: lastId, hmajax
    var conType = 'photos';
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

var printModels = function (myData) {
    // global var: lastId, hmajax
    var conType = 'models';
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
    myobj.scenesRendered[conType] = scene;
    //console.log(myobj.scenesLoaded[conType]);
    return myobj.scenesRendered[conType];
}

var printSearch = function (clips, clipsPrinted, title) {
    var pages =  '. Page ' + clips.info.currentPage.toString() + '/' + clips.info.totalPages.toString();
    if (clips.info.totalPages == 0) {
        pages = '. Try another search.';
    }
    return '\n \
        <h3 class="ui-collapsible-heading">\n\
            <a href="#" class="ui-collapsible-heading-toggle ui-btn ui-fullsize ui-btn-icon-left ui-btn-up-a" data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="span" data-icon="plus" data-iconpos="left" data-theme="a" data-mini="false">\n\
                <span class="ui-btn-inner">\n\
                    <span class="ui-btn-text">' + title + ' \n\
                        <span>(' + clips.info.totalRecords.toString() + ' found' + pages + ')</span>\n\
                        <span class="ui-collapsible-heading-status"> click to collapse contents</span>\n\
                    </span>\n\
                    <span class="ui-icon ui-icon-shadow ui-icon-minus">&nbsp;</span>\n\
                </span>\n\
            </a>\n\
        </h3>\n\
        <div class="ui-collapsible-content ui-body-a" aria-hidden="false">' + clipsPrinted + '</div>\n\
    ';
}