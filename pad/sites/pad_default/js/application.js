/* 
	jQuery Mobile Boilerplate
	application.js
*/

loadCarouselVideo =
    function(event) {


        $("#video-carousel").touchCarousel({
            pagingNav: false,
            snapToItems: false,
            itemsPerMove: 2,				
            scrollToLast: false,
            loopItems: false,
            scrollbar: false
        });
    //  $("#sort").change(function() {
    // 	window.location = $("#sort option:selected").val();
    // })

    };

loadCarouselDvd =
    function(event) {
        // custom code goes here
        $("#dvd-carousel").touchCarousel({					
            pagingNav: false,
            snapToItems: false,
            itemsPerMove: 2,				
            scrollToLast: false,
            loopItems: false,
            scrollbar: false
        });

        //  $("#sort").change(function() {
        // 	window.location = $("#sort option:selected").val();
        // })

        $("#sort").selectmenu("refresh");

        $("#sort").change(function() {
            var page = $(this).val();
            $.mobile.changePage( page, {
                transition: "fade"
            } );
        });
    };

loadCarouselPhoto =
    function(event) {
        $("#photo-carousel").touchCarousel({
            pagingNav: false,
            snapToItems: false,
            itemsPerMove: 2,				
            scrollToLast: false,
            loopItems: false,
            scrollbar: false
        });
    };
    
loadCarouselModel =
    function(event) {
        $("#model-carousel").touchCarousel({
            pagingNav: false,
            snapToItems: false,
            itemsPerMove: 2,				
            scrollToLast: false,
            loopItems: false,
            scrollbar: false
        });
    };

loadCarouselMag =
    function(event) {
        $("#mag-carousel").touchCarousel({
            pagingNav: false,
            snapToItems: false,
            itemsPerMove: 2,				
            scrollToLast: false,
            loopItems: false,
            scrollbar: false
        });
    };

/* 
	slider.tooltip.js for pagination 
*/
(function( $, undefined ) {

    $.widget( "mobile.slider", $.mobile.slider, {
        options: {
            popupEnabled: false,
            showValue: false
        },

        _create: function() {
            var o = this.options,
            popup = $( "<div></div>", {
                'class': "ui-slider-popup ui-shadow ui-corner-all ui-body-" + ( o.theme ? o.theme : $.mobile.getInheritedTheme( this.element, "c" ) )
            });

            this._super();

            $.extend( this, {
                _currentValue: null,
                _popup: popup,
                _popupVisible: false,
                _handleText: this.handle.find( ".ui-btn-text" )
            });

            this.slider.before( popup );
            popup.hide();

            this._on( this.handle, {
                "vmousedown" : "_showPopup"
            } );
            this._on( this.slider.add( $.mobile.document ), {
                "vmouseup" : "_hidePopup"
            } );
            this._refresh();
        },

        // position the popup centered 5px above the handle
        _positionPopup: function() {
            var dstOffset = this.handle.offset();
            this._popup.offset( {
                left: dstOffset.left + ( this.handle.width() - this._popup.width() ) / 2,
                top: dstOffset.top - this._popup.outerHeight() - 5
            });
        },

        _setOption: function( key, value ) {
            this._super( key, value );

            if ( key === "showValue" ) {
                if ( value ) {
                    this._handleText.html( this._value() ).show();
                } else {
                    this._handleText.hide();
                }
            }
        },

        // show value on the handle and in popup
        refresh: function() {
            this._super.apply( this, arguments );

            // necessary because slider's _create() calls refresh(), and that lands
            // here before our own _create() has even run
            if ( !this._popup ) {
                return;
            }

            this._refresh();
        },

        _refresh: function() {
            var o = this.options, newValue;

            if ( o.popupEnabled ) {
                // remove the title attribute from the handle (which is
                // responsible for the annoying tooltip); NB we have
                // to do it here as the jqm slider sets it every time
                // the slider's value changes :(
                this.handle.removeAttr( 'title' );
            }

            newValue = this._value();
            if ( newValue === this._currentValue ) {
                return;
            }
            this._currentValue = newValue;

            if ( o.popupEnabled ) {
                this._positionPopup();
                this._popup.html( newValue );
            }

            if ( o.showValue ) {
                this._handleText.html( newValue );
            }
        },

        _showPopup: function() {
            if ( this.options.popupEnabled && !this._popupVisible ) {
                this._handleText.hide();
                this._popup.show();
                this._positionPopup();
                this._popupVisible = true;
            }
        },

        _hidePopup: function() {
            if ( this.options.popupEnabled && this._popupVisible ) {
                this._handleText.show();
                this._popup.hide();
                this._popupVisible = false;
            }
        }
    });

})( jQuery );


// add cookie support
(function($) {
    if (!$.setCookie) {
        $.extend({
            setCookie: function(c_name, value, exminutes) { // or... exdays instead of exminutes
                try {
                    if (!c_name) return false;
//                    var exdate = new Date();
//                    exdate.setDate(exdate.getDate() + exdays);
                    var nowdate = new Date(), exdate = new Date();
                    var add = exminutes * 60 * 1000;
                    exdate.setTime(nowdate.getTime() + (add));
                    var c_value = escape(value) + ((exminutes==null) ? "" : "; expires="+exdate.toUTCString());
                    document.cookie = c_name + "=" + c_value;
                }
                catch(err) {
                    return false;
                };
                return true;
            }
        });
    };
    if (!$.getCookie) {
        $.extend({
            getCookie: function(c_name) {
                try {
                    var i, x, y,
                    ARRcookies = document.cookie.split(";");
                    for (i = 0; i < ARRcookies.length; i++) {
                        x = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
                        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
                        x = x.replace(/^\s+|\s+$/g,"");
                        if (x == c_name) return unescape(y);
                    };
                }
                catch(err) {
                    return false;
                };
                return false;
            }
        });
    };
})(jQuery);


// query string
(function($) {
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);

// popup cookie expire in minutes
var popupExpire = 10;

// TOUR splash page expire in minutes
var splashExpire = 24*60;

// TOUR join link (note: links also have to be updated in index.html and index.php)

var joinPad = 'joinpage.php';
if ($.QueryString["nats"] !== undefined) {
    joinPad += '?nats=' + $.QueryString["nats"];
}

// milliseconds to delay before reloading images
var reloadDelay1 = 200;
var reloadDelay2 = 300;
var reloadDelay3 = 200;

// how many milliseconds between each ajax call when there's multiple ajax calls for different content
var multiAjaxInterval = 130;

// for faster popups, stop propagation in some events like drop down searchbox and clicking on scenes
var stopPropagation = true;

// categories
hustlerCategories = {
    '0:none': 'All Categories', '119:69': 'Action',
    'adventure:Adventure': 'Amateur', '104:comedy': 'Comedy', '110:crime': 'Crime & Gangster', '121:drama': 'Drama', '128:epics': '>Epics/Historical', 
    '112:horror': 'Horror', '107:musical': 'Musical/Dance', '113:scifi': 'Science Fiction', '131:war': 'War', 'westerns:Westerns': 'Westerns'
};

hustlerMagazines = {
    'hustler' : 'Hustler',
    'barely-legal' : 'Hustler #2',
    'hustlers-taboo' : 'Hustler #3'
};

// categories
hustlerSites = { // Site Code : Full Name
    'hustler' : 'Hustler',
    'barely-legal' : 'Barely Legal',
    'hustlers-taboo' : 'Hustler\'s Taboo',
    'vcaxxx' : 'VCA XXX',
    'hook' : 'Hook',
    'fever' : 'Asian Fever',
    'hunt' : 'Hunt',
    'bossy' : 'Bossy',
    'beauties' : 'Beauties',
    'daddy' : 'Daddy',
    'hometown' : 'Hometown',
    'hottie' : 'Hottie',
    'hustlaz' : 'Hustlaz',
    'hustler-hd' : 'HustlerHD',
    'hustlerg' : 'Hustler G',
    'hustlers-bueno' : 'Hustler\'s Bueno',
    'college' : 'College',
    'muchas' : 'Muchas',
    'scary' : 'Scary',
    'too-many' : 'Too Many',
    'parodies' : 'Parodies'
};

// sort orders

var hustlerOrder = { 0: 'latest updates', 3: 'most viewed', 2: 'oldest', 4: 'top rated', 1: 'featured', 7: 'alphabetical' };

$(document).on("pagebeforecreate", function() {
    //console.log('site type is ' + $.siteType());

    $('#sort').off('change'); // another way to disable jqM's CHANGE event for the sort dropdown
    $('#sites').off('change');

    $('#sites').on("change", {}, function(e) {
//                    e.preventDefault();
//                    e.stopPropagation();
        hmCon.changeSite($('#sites').val());
    });

    // members vs tours
    if ($.siteType() == 'members') {
        $('.member-join').css('display','none');
        $('.apple-navbar-ui').addClass('nav-members');
    } else {
        $('.apple-navbar-ui').addClass('nav-tour');
    }
    $('.btn-join').css('margin','0px'); // spacing for button

    // footer nav
    $('div[data-role="footer"] li').each(function() {
        var li = this;
        $(this).children('a').each(function() {
            if ($(this).attr('data-icon') != 'join') {
                $(this).bind("tap", {}, function(e) {
                    hmCon.changePage($(this).attr('data-icon'));;
                });
            } else {
                $(this).bind("tap", {}, function(e) {
                    window.location = joinPad;
                });
            }

            // members vs tour
            if ($(this).attr('data-icon') == 'join') {
                // join button. Hide for members
                $.siteType() == 'members' ? $(li).css('display', 'none') : $(li).css('display', 'list-item');
            }
            if ($(this).attr('data-icon') == 'fav') {
                // fav button. Hide for tour
                $.siteType() == 'members' ? $(li).css('display', 'list-item') : $(li).css('display', 'none');
            }
            if ($(this).attr('data-icon') == 'special') {
                // specials button. Hide for tour
                $.siteType() == 'members' ? $(li).css('display', 'list-item') : $(li).css('display', 'none');
            }
        });
    });

    // member vs tour : "members" button
    var btnMember = $('div:eq(0) div:eq(0) a:eq(1)');
    $.siteType() == 'members' ? btnMember.css('display', 'none') : btnMember.css('display', 'inline-block')
    $('#members-link').on('tap', function() {
        if (window.location.href.indexOf("beta.") > -1) {
            window.location = 'http://beta.members.tablet.hustler.com/';
        } else {
            window.location = 'http://members.tablet.hustler.com/';
        }
    });

    //disable clicks of the bottom menu ("apple") navbar
    $('div.ui-navbar.ui-mini').each(function() {
        $(this).click(function(e) {
                e.preventDefault();
                //e.stopPropagation();
        });
    });

    // load google stuff
    if ($.sitetype == 'members') {
        loadScript( 'js/members.google.js', $.noop());
    } else {
        loadScript( 'js/tour.google.js', $.noop());
    }
});

$(document).on("pageinit", function() {
    //these will run after ajax call:
    //loadCarouselVideo();
    //loadCarouselDvd();
    
    $("#sort").selectmenu("refresh");

    $("#sort").change(function() {
        var page = $(this).val();
        $.mobile.changePage( page, {
            transition: "fade"
        } );
    });
    
    // popup detail window
    // clear popups after closing...
    var clearPopUp = function(event, ui) {
//        // clear text and images in popup
//        $('#popupDialog div:eq(0) h1').text('Loading...');
//        $('#popupDialog div:eq(1) div:eq(1) h3').text('Loading...');
//          $('#popupDialog div:eq(1) div:eq(0) ul li img').hide(); // will screw up popup repositioning after new content added
//        $('#popupDialog div:eq(1) div:eq(0) ul li img').attr('alt', '');
//
//        $('#popupDialog div:eq(1) div:eq(1) p.dialog-date').empty();
//        $('#popupDialog div:eq(1) div:eq(1) p.dialog-desc').empty();
//
//        $('#popupDialog .categories').empty();
//        $('#popupDialog .models').empty();
//
//        $('#popupDialog .btn-play .ui-btn-text').text('');
//        console.log('closing popup...');
        // make opaque
        $('#popupDialog img').css('opacity', '0');
        $('#popupDialog div:eq(1) div:eq(0) ul li img').attr('src', 'images/placeholder/photo-placeholder.png');

        // defaults for dialog, because they get changed when we add .btn-join for tour video scenes
        $('.btn-join').css('display', 'none'); // the join button below the trailer preview is usually off
        $('.my-dialog .dialog-content:eq(0)').css('width', 'auto');
        $('.my-dialog .btn-play').css('width', '100%');
    };
    clearPopUp();
    // some events
    $('#popupDialog').on({
        popupafterclose: clearPopUp, // when a popup closes, clear it again
        updated: function() { // when a popup is updated with new details, fade it in
            //$('#popupDialog div:eq(1) div:eq(0) ul li img').show();
            $('#popupDialog img').fadeTo(170, .7, function() {
//console.log('popup updated');
                  $('#popupDialog img').fadeTo(50, 1,  $.noop());
            });
        },
        popupafteropen: function() {
//console.log('popup opened');
            $(this).trigger('updated');
        }
    });


});
