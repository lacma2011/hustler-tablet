var successHomeVideo = function (clips) {
    var conType = 'videos';
    var print = function (myData) {
        // global var: lastId, hmajax
        var myobj = hmajax;
        var scene = '';
        for (var i in myData[conType]) {
            //console.log(myData[conType][i]);
            var title = myData[conType][i]['title'];
            var count = lastId;
            lastId++;
            var ss = myData[conType][i]['ss'];
            var scene_id = myData[conType][i]['id'];
            scene += ' \
                        <!--///////////////////////////////////////start scene //////////////////////////////////////////////--> \
                        <li class="touchcarousel-item video-block" id="' + conType + '_clip_' + count + '" data-content-id="' + scene_id + '"> \
                                <div class="content-block video-block"> \
                                    <a href="#popupDialog" data-icon="play" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + ss + '" alt="' + title + '"></a> \
                                    <a href="#popupDialog" data-scene-click="' + scene_id + '" data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#popupDialog" class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right ui-btn-up-c"><span class="ui-btn-inner">                  <span class="ui-btn-text">' + title + '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a> \
                                </div> \
                        </li> \
                    <!--///////////////////////////////////////end scene //////////////////////////////////////////////-->';
            myobj.scenesLoaded[conType][scene_id] = myData[conType][i];
        }
        myobj.scenesRendered[conType] = scene;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    var left = $('.touchcarousel-container:eq(0)').css('left');
    var s = '<ul class="touchcarousel-container">' + print(clips) + ' </ul>';
    $('#video-carousel').empty().append(s);
    loadCarouselVideo();
    $('.touchcarousel-container:eq(0)').css('left', left); // go back to ye old place        

    // swipe broke, so re-binding some :/
    bindCarouselMoves(conType);
    
    // bind clicks to scenes...
    bindScenes('.touchcarousel-item.video-block', conType);
}


var successHomeDvd = function (clips) {
    var conType = 'dvds';
    var print = function (myData) {
        // global var: lastId, hmajax
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
                <li class="touchcarousel-item dvd-block" id="' + conType + '_clip_' + count + '" data-content-id="' + id + '"> \
                    <div class="content-block dvd-block"> \
                            <a href="" class="ui-link" data-scene-click="' + id + '"><img src="' + img_front + '" alt="' + name + '"></a> \
                    </div> \
                </li>';
            myobj.scenesLoaded[conType][id] = myData[conType][i];
        }
        myobj.scenesRendered[conType] = scene;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    var left = $('.touchcarousel-container:eq(1)').css('left');
    var s = '<ul class="touchcarousel-container">' + print(clips) + ' </ul>';
    $('#dvd-carousel').empty().append(s);
    loadCarouselDvd()
    $('.touchcarousel-container:eq(1)').css('left', left); // go back to ye old place        

    // swipe broke, so re-binding some :/
    bindCarouselMoves(conType);
    
    // bind clicks to scenes...
    bindScenesDvd('.touchcarousel-item.dvd-block');
}


var successHomePhoto = function (clips) {
    var conType = 'photos';
    var print = function (myData) {
        // global var: lastId, hmajax
        var myobj = hmajax;
        var scene = '';
        for (var i in myData[conType]) {
            //console.log(myData[conType][i]);
            var name = myData[conType][i]['title_name'];
            if (name == null) {
                name = myData[conType][i]['name'];
            } else {
                name = myData[conType][i]['name']  + ' - ' + name;
            }
            var id = myData[conType][i]['id'];
            var img_front = myData[conType][i]['image'];
            var count = lastId;
            lastId++;
            scene += ' \
                <li class="touchcarousel-item photo-block" id="' + conType + '_clip_' + count + '" data-content-id="' + id + '"> \
                    <div class="content-block photo-block"> \n\
                        <a href="#popupDialog" data-icon="play" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + img_front + '" alt="' + name + '"></a> \
                        <a href="#popupDialog" data-scene-click="' + id + '" data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#popupDialog" class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right ui-btn-up-c"><span class="ui-btn-inner"><span class="ui-btn-text">' + name + '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a> \
                    </div> \
                </li>';
            myobj.scenesLoaded[conType][id] = myData[conType][i];
        }
        myobj.scenesRendered[conType] = scene;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    var left = $('.touchcarousel-container:eq(1)').css('left');
    var s = '<ul class="touchcarousel-container">' + print(clips) + ' </ul>';
    $('#photo-carousel').empty().append(s);
    loadCarouselPhoto()
    $('.touchcarousel-container:eq(1)').css('left', left); // go back to ye old place        

    // swipe broke, so re-binding some :/
    bindCarouselMoves(conType);

    bindScenes('.touchcarousel-item.photo-block', conType);
}


var successHomeModel = function (clips) {
    var conType = 'models';
    var print = function (myData) {
        // global var: lastId, hmajax
        var myobj = hmajax;
        var scene = '';
        for (var i in myData[conType]) {
            //console.log(myData[conType][i]);
            var name = myData[conType][i]['name'];
            var id = myData[conType][i]['id'];
            var img_front = myData[conType][i]['image'];
            var count = lastId;
            lastId++;
            scene += ' \
                <li class="touchcarousel-item model-block" id="' + conType + '_clip_' + count + '" data-content-id="' + id + '"> \
                    <div class="content-block model-block"> \
                        <a href="#popupDialog" data-icon="play" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-link"><img src="' + img_front + '" alt="' + name + '"></a> \
                        <a href="#popupDialog" data-scene-click="' + id + '" data-role="button" data-icon="plus" data-inline="true" data-mini="true" data-iconpos="right" data-rel="popup" data-position-to="window" data-transition="pop" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" aria-haspopup="true" aria-owns="#popupDialog" class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-right ui-btn-up-c"><span class="ui-btn-inner"><span class="ui-btn-text">' + name + '</span><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></span></a> \
                    </div> \
                </li>';
            myobj.scenesLoaded[conType][id] = myData[conType][i];
        }
        myobj.scenesRendered[conType] = scene;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    var left = $('.touchcarousel-container:eq(1)').css('left');
    var s = '<ul class="touchcarousel-container">' + print(clips) + ' </ul>';
    $('#model-carousel').empty().append(s);
    loadCarouselModel()
    $('.touchcarousel-container:eq(1)').css('left', left); // go back to ye old place        

    // swipe broke, so re-binding some :/
    bindCarouselMoves(conType);

    bindScenes('.touchcarousel-item.model-block', conType);
}

var successHomeMag = function (clips) {
    var conType = 'mags';
    var print = function (myData) {
        // global var: lastId, hmajax
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
                <li class="touchcarousel-item mag-block" id="' + conType + '_clip_' + count + '" data-content-id="' + id + '"> \
                    <div class="content-block mag-block"> \
                            <a href="" class="ui-link" data-scene-click="' + id + '"><img src="' + img_front + '" alt="' + name + '"></a> \
                    </div> \
                </li>';
            myobj.scenesLoaded[conType][id] = myData[conType][i];
        }
        myobj.scenesRendered[conType] = scene;
        //console.log(myobj.scenesLoaded[conType]);
        return myobj.scenesRendered[conType];
    }

    var left = $('.touchcarousel-container:eq(1)').css('left');
    var s = '<ul class="touchcarousel-container">' + print(clips) + ' </ul>';
    $('#mag-carousel').empty().append(s);
    loadCarouselMag()
    $('.touchcarousel-container:eq(1)').css('left', left); // go back to ye old place        

    // swipe broke, so re-binding some :/
    bindCarouselMoves(conType);

    bindScenes('.touchcarousel-item.mag-block', conType);
}

function bindCarouselMoves(type) {
    switch (type) {
        case 'dvds':
            var carousel = '#dvd-carousel';
            var offset = 4;
            break;
        case 'videos':
            var carousel = '#video-carousel';
            var offset = 3;
            break;
        case 'photos':
            var carousel = '#photo-carousel';
            var offset = 4;
            break;
        case 'models':
            var carousel = '#model-carousel';
            var offset = 4;
            break;
        case 'mags':
            var carousel = '#mag-carousel';
            var offset = 4;
            break;
    }
    var sliderInstance = $(carousel).touchCarousel({}).data('touchCarousel');
    $(carousel).swiperight(function(e) {
        var x = sliderInstance.getCurrentId();
        x = x - offset;
        if (x < 0) x = 0;
        sliderInstance.goTo(x);
    });
    $(carousel).swipeleft(function(e) {
        var x = sliderInstance.getCurrentId();
        x = x + offset;
        if (x > 34) x = 34;
        sliderInstance.goTo(x);
    });
    $(carousel + ' .arrow-holder.left').on('click', {}, function(e) {
        var x = sliderInstance.getCurrentId();
        x = x - offset;
        if (x < 0) x = 0;
        sliderInstance.goTo(x);
    });
    $(carousel + ' .arrow-holder.right').click(function(e) {
        var x = sliderInstance.getCurrentId();
        x = x + offset;
        if (x > 34) x = 34;
        sliderInstance.goTo(x);
    });
}
