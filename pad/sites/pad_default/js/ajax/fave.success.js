var faveLink =     '<a href="#" id="addfave" data-role="button" data-inline="true" data-mini="true" data-icon="heart" data-iconpos="notext" class="btn-fav ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-notext ui-btn-up-d" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="d" title="Favorite"><span class="ui-btn-inner"><span class="ui-btn-text">Favorite</span><span class="ui-icon ui-icon-heart ui-icon-shadow">&nbsp;</span></span></a>';
var txtFavorited = '<a href="#" id="unfave"  data-role="button" data-inline="true" data-mini="true" data-icon="heart" data-iconpos="notext" class="selected btn-fav ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-notext ui-btn-up-d" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="d" title="Favorite"><span class="ui-btn-inner"><span class="ui-btn-text">Favorite</span><span class="ui-icon ui-icon-heart ui-icon-shadow">&nbsp;</span></span></a>';
var codeFave = 'a'; // can be anything but 'p' (for phone);

function successFave (event) {
    var id = event.data.id;
    var type = event.data.type;
    if(!faveActive){ // prevent doubleclicking ajax during ajax call
        return;
    }
    faveActive = false; // turn buttons off
    hmajaxFave.setType(type).update(id, null, { type: type }, function(json) {
        faveActive = true; // turn buttons back on
        var rsp = hmajax.parseJSON(json);
        if (rsp.error) {
            //console.log(rsp.error);
            return false;
        }
        //console.log(this);
        var selector;
        switch (this.successOptions.type) {
            case 'video':
                selector = '.video-block';
                break;
            case 'model':
                selector = '.model-block';
                break;
            case 'dvd':
                selector = '.dvd-block';
                break;
            case 'photo':
                selector = '.photo-block';
                break;
            case 'mag':
                selector = '.mag-block';
                break;
        }
        if (rsp[type][id]) {
            //console.log('favorite update "' + event.data.type + '" id ' + id);

            // got a good response if array with sceneID as key was found
            if (rsp[type][id]['fav'] == 1) {
                //console.log('in favorites');
                $('#fave').empty().append(txtFavorited); //.append(unfaveLink);
                $(selector + ' a[data-scene-click="' + id + '"]').addClass('selected');
                //console.log(selector + ' a[data-scene-click="' + id + '"]');
                $('#unfave').on('vclick', {id: id, type: type}, successFave);
            } else {
                $('#fave').empty().append(faveLink);
                $(selector + ' a[data-scene-click="' + id + '"]').removeClass('selected');
                //console.log(selector + ' a[data-scene-click="' + id + '"]');                
                $('#addfave').on('vclick', {id: id, type: type}, successFave);                
            }
        }
    });
}

function bindFave (type, details, id, ajaxUrl) {
    if ($.siteType() != 'members') return true;
    hmajaxFave.setUrl(ajaxUrl).setType(type).setCode(codeFave);
    bindFaveButton(type, details, id);
}

function bindFaveButton (type, details, id) {
    if (details['in_favorites'] == 1 || (type == 'dvd' && details['in_favorites'] !== null) ||
        (type == 'model' && details['in_favorites'] !== null)) {
        $('#fave').empty().append(txtFavorited);
        $('#unfave').on('vclick', {id: id, type: type}, successFave);
    } else {
        $('#fave').empty().append(faveLink);
        $('#addfave').on('vclick', {id: id, type: type}, successFave);
    }
}

var loadFavorites = function(conType, selector) {
    // load multiple favorites, based on what was loaded on the page
    if ($.siteType() != 'members') return true;
    //console.log('check faves for ' + conType + ' using ' + selector);
    var scenes = [];
    if (hmCon.query.page == 'home') {
        $('li' + selector).each(function() {
            scenes.push($(this).attr('data-content-id'));
        });
    } else {
        $('div' + selector).each(function() {
            scenes.push($(this).attr('data-content-id'));
        });
    }
    
    hmajaxFave.setType(conType).statusList(scenes, conType, function(json){
        var rsp = hmajax.parseJSON(json);
        if (rsp.error) {
            console.log(rsp.error);
            return false;
        }
        //console.log(rsp);
        for (var r in rsp.fav_status) {
            var ele = $(selector + ' a[data-scene-click="' + r + '"] .ui-btn-inner .ui-icon-plus');
            //console.log(ele);
            if (rsp.fav_status[r] == '1') {
                // this is a favorite
                $(selector + ' a[data-scene-click="' + r + '"]').addClass('selected');
            } else {
                $(selector + ' a[data-scene-click="' + r + '"]').removeClass('selected');
            }
        }
    });
}


var successFaveToggle = function (id, type) {
    if(!faveActive){ // prevent doubleclicking ajax during ajax call
        return;
    }
    var selector, otherType;
    switch (type) {
        case 'videos':
            selector = '.video-block';
            otherType = 'video';
            break;
        case 'models':
            selector = '.model-block';
            otherType = 'model';
            break;
        case 'dvds':
            selector = '.dvd-block';
            otherType = 'dvd';
            break;
        case 'photos':
            selector = '.photo-block';
            otherType = 'photo';
            break;
        case 'mags':
            selector = '.mag-block';
            otherType = 'mag';
            break;
    }
    faveActive = false; // turn buttons off
    hmajaxFave.setType(otherType).update(id, null, { }, function(json) {
        var rsp = hmajax.parseJSON(json);
        //console.log('clicked on a fav icon');console.log(rsp);
        //console.log('type=' + otherType + ' id=' + id);
        faveActive = true; // turn buttons back on
        if (rsp[otherType][id].fav == '1') {
            $(selector + ' a[data-scene-click="' + id + '"]').addClass('selected');
        } else {
            $(selector + ' a[data-scene-click="' + id + '"]').removeClass('selected');
        }


    });
};
