/* 
 *  
 */

//TODO: make a base class for all ajax objects, like hmajax() and hmajaxFave(),
// and include member functions like parseJSON

$.extend({
    siteType: function(){
        var url = window.location.href.replace('http://','');
        url = url.split('.')
        for (var x in url) {
            if (url[x] == 'members') {
                return 'members';
            } else if (url[x] == 'tour') {
                return 'tour';
            } else if (url[x].search('/') > -1) {
                return null;
            }
        }
    }
});

$.extend({
    sitetype: $.siteType()
});

function loadScript(url, callback) {
    // adding the script tag to the head as suggested before
   var head = document.getElementsByTagName('head')[0];
   var script = document.createElement('script');
   script.type = 'text/javascript';
   script.src = url;

   // then bind the event to the callback function 
   // there are several events for cross browser compatibility
   script.onreadystatechange = callback;
   script.onload = callback;

   // fire the loading
   head.appendChild(script);
}

function getCurrentJsScript() {
    var scriptEls = document.getElementsByTagName( 'script' );
    var thisScriptEl = scriptEls[scriptEls.length - 1];
    var scriptPath = thisScriptEl.src;
    var scriptFolder = scriptPath.substr(0, scriptPath.lastIndexOf( '/' )+1 );
    return [scriptPath, scriptFolder];
}

// if we wanted to load other scripts from this folder...
// var myJsPath = getCurrentJsScript()[1];
// loadScript(myJsPath + 'content.js', callBackFunction());

