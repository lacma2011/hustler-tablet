
var faveActive = true;

var hmajaxFave = {
    url : null, // the ajax Faves api url
    type: 'video',
    code: 'a', // p= phone.  everything else is nothing
    
    status : function(sceneId) {
        // true = is a favorite
    },

    statusList : function(sceneIds, type, successCallback) {
        // sceneIds = array
        // type = content type
        $.ajaxSetup({

        });
        $.ajax({
            url: this.url + 'get/con/faves/status/' + type + '/',
            type: "POST",
            data: { scenes: sceneIds },
            cache: false
        }).done(successCallback);
    },

    update : function(sceneId, element, successOptions, fnSuccess) {
        // fnSuccess is our own callback function after ajax, if we don't want to use successPhone()

        $.ajaxSetup({
//          beforeSend: function(request) {
//            request.setRequestHeader("User-Agent","iphone");
//          }
        });

        if (successOptions === undefined) {
            successOptions = {};
        }

        var successPhone = function (json) {
            // callback on Ajax Success, for the original mobile site (phone)
            faveActive = true; // turn buttons back on
            var type = 'video';
            var rsp = this.myobj.parseJSON(json);
            if (rsp.error) {
//console.log(rsp.error);
                return false;
            }
            if (rsp[type][sceneId]) {
                // got a good response if array with sceneID as key was found
                $(element).empty();
                if (rsp[type][sceneId].fav == 0) {
                    $(element).append(rsp[type][sceneId].details.image_html + 'Favorite this');
                } else if (rsp[type][sceneId].fav == 1) {
                    $(element).append(rsp[type][sceneId].details.image_html + 'Unfavorite this');
                }
            }
        }
        
        var successCallback;
        if (fnSuccess === undefined) {
            successCallback = successPhone;
        } else {
            successCallback = fnSuccess;
        }

        //var data = 'page=' + document.location.hash.replace(/^.*#/, ''); //TODO: put real data
        $.ajax({
            myobj: this,
            url: this.url + 'update/fav/' + this.type + '/' + this.code + '/' + sceneId + '/',
            //siteType: $.siteType(),
            type: "GET",
            data: "",
            cache: false,
            successOptions: successOptions
        }).done(successCallback);
    },

    parse : function (json) {
        var myData = this.parseJSON(json);
        var data;
        for (var i in myData['clips']) {
            console.log(myData['data'][i]);
            data['title'] = myData['data'][i]['title'];
        }
        
    },
    

    parseJSON : function (html) {
        var myData = JSON.parse(html, function (key, value) {
            var type;
            if (value && typeof value === 'object') {
                type = value.type;
                if (typeof type === 'string' && typeof window[type] === 'function') {
                    return new (window[type])(value);
                }
            }
            return value;
        });
        return myData;
    },
    
    setUrl : function (url, pageBarUrl) {
        this.url = url;
        this.pageBarUrl = pageBarUrl;
        return this;
    },

    setType : function(type) {
        this.type = type;
        return this;
    },
    
    setCode : function(code) {
        this.code = code;
        return this;
    },
    
    setPhone : function() {
        return this.setCode('p');
    }
 
};