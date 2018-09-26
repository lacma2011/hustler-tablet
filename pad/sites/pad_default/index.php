<!--
        jQuery Mobile Boilerplate
        videos.php
-->
<!doctype html>
<html>
    <head>
        <title>Hustler.com - Videos</title>

        <meta charset="utf-8">
        <meta name="description" content="Want to watch adult videos online? Watch free porn star videos, adult online videos and many more at online store Hustler.com. Visit now and watch exciting porn videos online" />
        <meta name="keywords" content="watch adult videos online, online porn video store, hustler porn video, free porn stars videos, free adult online videos, watch adult online videos, watch porn star videos, watch free porn now, watch free porn video now" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">

        <!-- Home screen icon  Mathias Bynens mathiasbynens.be/notes/touch-icons -->
        <!-- For iPhone 4 with high-resolution Retina display: -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon.png">
        <!-- For first-generation iPad: -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon.png">
        <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
        <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">
        <!-- For nokia devices and desktop browsers : -->
        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Mobile IE allows us to activate ClearType technology for smoothing fonts for easy reading -->
        <meta http-equiv="cleartype" content="on">

        <!-- jQuery Mobile CSS bits -->
        <link rel="stylesheet" href="css/jquery.mobile-1.3.0.min.css" /><!-- modified a bit, for image path-->

        <!-- Custom css -->
        <link rel="stylesheet" href="css/custom.css" />
        <link rel="stylesheet" href="css/mediaqueries.css" />

        <!-- Touch Carousel CSS -->
        <link rel="stylesheet" href="css/touchcarousel.css" />
        <link rel="stylesheet" href="css/touchcarousel-skin.css" />

        <!-- Javascript includes -->
        <script src="js/jquery-1.9.1.min.js"></script>

        <script src="js/ios-orientationchange-fix.min.js"></script>
        <script type="text/javascript">
             // disable jqM's CHANGE event for the sort dropdown
            $(document).on("mobileinit", function(){
                    $.mobile.ajaxEnabled = false;
                    $.mobile.ns = '';
                    $.mobile.pushStateEnabled = false;
                    $.mobile.hashListeningEnabled = false;
                    //$.mobile.linkBindingEnabled = false;  // needed for popups
            });
            
            function jqm_alert(strText) {
                alert(strText);
            }
            
        </script>
        <script src="js/jquery.mobile-1.3.0.min.js"></script>
        <script src="js/jquery.touchcarousel-1.2.min.js"></script>
        <script src="js/application.min.js"></script>

        <!-- TODO: load these in gallery page only ??? -->
        <!-- Gallery CSS -->
        <link rel="stylesheet" href="css/photoswipe.css" />
        <!-- Gallery JS -->
        <script src="js/klass.min.js"></script>
        <script src="js/code.photoswipe.jquery-3.0.5.min.js"></script>

        <script src="js/hustler/ajax/ajax.min.js"></script>
    </head> 
    <body>
        <!-- begin page -->
        <div data-role="page" data-title="Hustler.com - Videos">

            <div data-position="fixed" data-role="header" data-id="header">
                <h1 id="header_logo_wrap"><a href="#" class="logo">Hustler.com</a></h1>
                
                <a href="#" id="members-link" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn-right">Members</a>
                <div data-role="subheader" class="ui-bar">
                    <h1><a href="#" class="sub-logo">Hustler</a></h1>
                    <fieldset data-role="controlgroup" data-type="horizontal" class="submenu">
                        <a href="#popupSearch" class="search-btn" data-role="button" data-icon="search" data-iconpos="notext" data-rel="popup" data-position-to="window" data-transition="slidedown">Search</a>
                        <label for="sites" class="select">Sites</label>
                        <select name="sites" id="sites">
                            <optgroup label="Main Sites">
                                <option value="hustler">Hustler</option>
                                <option value="barely-legal">Barely Legal</option>
                                <option value="hustlers-taboo">Hustler's Taboo</option>
                                <option value="vcaxxx">VCA XXX</option>
                            </optgroup>
                            <optgroup label="Specialty Sites">
                                <option value="anal-hookers">Anal Hookers</option>
                                <option value="asian-fever">Asian Fever</option>
                                <option value="beaver-hunt">Beaver Hunt</option>
                                <option value="bossy-milfs">Bossy Milfs</option>
                                <option value="busty-beauties">Busty Beauties</option>
                                <option value="daddy-gets-lucky">Daddy Gets Lucky</option>
                                <option value="hometown-girls">Hometown Girls</option>
                                <option value="hottie-moms">Hottie Moms</option>
                                <option value="hustlaz">Hustlaz</option>
                                <option value="hustler-hd">HustlerHD</option>
                                <option value="hustler-girls">Hustler Girls</option>
                                <option value="hustlers-lesbians">Hustler's Lesbians</option>
                                <option value="hustlers-college-girls">Hustler's College Girls</option>
                                <option value="muchas-latinas">Muchas Latinas</option>
                                <option value="scary-big-dicks">Scary Big Dicks</option>
                                <option value="too-many-trannies">Too Many Trannies</option>
                                <option value="hustler-parodies">Hustler Parodies</option>
                                <!--option value="his-classics">HIS Classics</option-->
                            </optgroup>				   
                        </select>
                        <label for="sort" class="select">Sort by</label>
                        <select name="sort" id="sort">
                            <option value="/">Hustler</option>
                            <option value="/">Barely Legal</option>
                            <option value="/">Taboo</option>
                        </select>
                        <a href="joinpage.php" class="member-join joinlink" data-role="button" data-icon="plus" data-iconpos="right">Join</a>
                    </fieldset>
                </div>
            </div>            
            <!-- Popup Search Content -->
            <div data-role="popup" id="popupSearch" data-overlay-theme="a" class="my-dialog search-dialog" data-corners="false">
                <form id="form1">
                    <div data-role="header">
                        <h1>Search</h1>
                        <a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
                    </div>
                    <div data-role="content" data-theme="d" class="ui-content">
                        <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" data-corners="false" class="filter-by">
                            <legend>Filter by:</legend>
                            <input name="select-video" id="select-video" type="checkbox">
                            <label for="select-video">Videos</label>
                            <input name="select-dvd" id="select-dvd" type="checkbox" checked="checked">
                            <label for="select-dvd">DVDs</label>
                            <input name="select-model" id="select-model" type="checkbox">
                            <label for="select-model">Models</label>
                            <input name="select-photo" id="select-photo" type="checkbox">
                            <label for="select-photo">Photos</label>
                        </fieldset>
                        <div class="multi-select">
                            <label for="category">Pick Categories:</label>
                            <select name="category" id="category" data-native-menu="false" data-theme="d" data-overlay-theme="a">
                                <option value="none">All Categories</option>
                                <option value="119:69">69</option>
                                <option value="109:amateur">Amateur</option>
                                <option value="104:anal">Anal</option>
                                <option value="110:asian">Asian</option>
                                <option value="121:asslicking">Asslicking</option>
                                <option value="128:bigass">Big Ass</option>
                                <option value="112:bigcock">Big Cock</option>
                                <option value="107:bigtits">Big Tits</option>
                                <option value="113:black">Black</option>
                                <option value="131:blonde">Blonde</option>
                                <option value="132:blowjob">Blowjob</option>
                                <option value="133:bondage">Bondage</option>
                                <option value="137:brunette">Brunette</option>
                                <option value="144:creampie">Creampie</option>
                                <option value="146:cumswapping">Cum Swapping</option>
                                <option value="147:cumshot">Cumshot</option>
                                <option value="410:doggystyle">Doggystyle</option>
                                <option value="153:facial">Facial</option>
                                <option value="157:footfetish">Foot Fetish</option>
                                <option value="158:gangbang">Gangbang</option>
                                <option value="111:groupsex">Group Sex</option>
                                <option value="163:hairypussy">Hairy Pussy</option>
                                <option value="165:hardcore">Hardcore</option>
                                <option value="182:pool">Hot Tub / Pool</option>
                                <option value="166:interracial">Interracial</option>
                                <option value="115:latina">Latina</option>
                                <option value="114:lesbian">Lesbian</option>
                                <option value="169:lingerie">Lingerie</option>
                                <option value="117:masturbation">Masturbation</option>
                                <option value="106:milf">MILF</option>
                                <option value="173:naturalbreasts">Natural</option>
                                <option value="177:oralsex">Oral</option>
                                <option value="178:outdoorsex">Outdoor Sex</option>
                                <option value="108:parody">Parody</option>
                                <option value="188:roughsex">Petite</option>
                                <option value="181:piercings">Piercings</option>
                                <option value="183:pov">POV</option>
                                <option value="187:redhead">Redhead</option>
                                <option value="148:dildo">Sex Toys</option>
                                <option value="193:smalltits">Small Tits</option>
                                <option value="196:spanking">Spanking</option>
                                <option value="198:squirting">Squirting</option>
                                <option value="199:stockings">Stockings</option>
                                <option value="200:strapon">Strap-On</option>
                                <option value="201:tattoos">Tattoos</option>
                                <option value="105:teen18">Teen 18+</option>
                                <option value="202:threesome">Threesome</option>
                                <option value="203:tittyfuck">Titty Fuck</option>
                            </select>
                        </div>
                        <label for="search">Search:</label>
                        <input name="search" id="search" value="" placeholder="Search Anything..." type="search">
                        <span class="search-example">Example: asa akira anal dvds</span>
                        <button id="search-button" type="submit" data-theme="b" data-icon="check" data-iconpos="right">Search</button>
                    </div>
                </form>
            </div>            
            <!-- Member Login Content -->
            <div data-role="popup" id="popupLogin" data-theme="a" data-overlay-theme="a" class="my-dialog login-dialog" data-corners="false">
                <form id="form2">
                    <div style="padding:10px 20px;">
                        <h3>Please Sign In</h3>
                        <label for="un" class="ui-hidden-accessible">Username:</label>
                        <input name="user" id="un" value="" placeholder="username" data-theme="a" type="text">
                        <label for="pw" class="ui-hidden-accessible">Password:</label>
                        <input name="pass" id="pw" value="" placeholder="password" data-theme="a" type="password">
                        <button type="submit" data-theme="b" data-icon="check">Sign in</button>
                    </div>
                </form>
            </div>		


            <div data-role="content" class="content" id="content">
                <div class="content-guts">


                </div>  		

                <div class="sub-footer">
                    <p><a href="http://www.hustler.com/2257.html" target="_blank">18 U.S.C 2257 Record-Keeping Requirements Compliance Statement</a></p>
                    <p>All models appearing on this site are 18 or older. &copy; 2013 LFP Internet Group, LLC, All Rights Reserved</p>
                    <p>LFP Internet Group, LLC, 8484 Wilshire Blvd. #900, Beverly Hills, CA 90211 USA</p>
                    <p>Vendo is our authorized reseller</p>
                    <nav class="sub-footer-nav" role="navigation">
                        <a href="http://www.hustler.com/privacy.html" target="_blank">Privacy Policy</a> |
                        <a href="http://hustler.com/rsaci.html" target="_blank">Parental Blocking</a> | 
                        <a href="http://www.lfpcareers.com/" target="_blank">Employment</a> |
                        <a href="mailto:vhurley@lfp.com">Media Inquiries</a> |
                        <a href="http://www.hustler.com/model" target="_blank">Models Wanted</a> |
                        <a href="http://hustlercash.com/" target="_blank">Affiliate Program</a>
                    </nav>
                </div>
            </div>


            <!-- Popup Video Dialog Content -->
            <div data-role="popup" id="popupDialog" data-overlay-theme="a" class="my-dialog video-dialog" data-corners="false">
                <div data-role="header">
                    <h1>Lily LaBeau & Capri Cavalli</h1>
                    <a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
                </div>

                <div data-role="content" data-theme="d" class="ui-content">
                    <div class="dialog-content" id="popup-single-image">
                        <ul>
                            <li>
                                <img src="images/video-assets/2.jpg" alt="scene-title" class="">
                            </li>
                        </ul>
                        <a href="#" data-role="button" data-inline="true" class="btn-play" data-theme="b" rel="external">Play Movie</a>
                        <a href="joinpage.php" data-role="button" data-inline="true" class="btn-join joinlink" data-theme="b" rel="external">See Full Video</a>
                    </div>
                    <div class="dialog-content">
                        <h3>My Roommate's a Lesbian</h3>
                        <p class="dialog-date">March 11, 2012 <span>Ratings ?</span></p>
                        <p class="dialog-desc">A few glasses of wine with your lezbo roomie can only end one way. With a face-full of hot, glistening snatch! And if you're gonna walk around with your titties all hanging out, you know she won't be able to help herself.</p>
                        <span class="dialog-title">Tags:</span>
                        <span class="categories"></span>
                        <span class="dialog-title">Stars:</span>
                        <span class="models"></span>
                        <div id="fave">
                            <a href="joinpage.php" data-role="button" data-inline="true" data-mini="true" class="btn-fav joinlink">Join Now</a>
                        </div>
                        
                    </div>
                </div>
                <div data-role="content" data-theme="c" class="ui-content">
                    <!--h3>More Like:</h3-->
                </div>
            </div>


            <div data-role="footer" data-id="footer" data-position="fixed">
                <div data-role="navbar">
                    <ul class="apple-navbar-ui">
                        <li><a href="#" data-icon="video">Videos</a></li>
                        <li><a href="#" data-icon="photo">Photos</a></li>
                        <li><a href="#" data-icon="model">Models</a></li>
                        <li><a href="#" data-icon="site">Sites</a></li>
                        <li><a href="#" data-icon="cat">Categories</a></li>
                        <li><a href="#" data-icon="mag">Magazines</a></li>
                        <li><a href="#" data-icon="special">Specials</a></li>
                        <li><a href="#" data-icon="fav">Favorites</a></li>
                        <li><a href="#" data-icon="join">Join</a></li>
                    </ul>
                </div>
                </div>
            </div>
        </div>
        <!-- end page -->


        <script src="js/hustler/ajax/favorites.min.js"></script>
        <script>
            loadScript( 'js/hustler/ajax/content.min.js', function() {

                 $('#popupSearch .ui-submit').on('vclick', function (e) {
                     
                     // global: stopPropagation (in padcontrol.js)
                     if (stopPropagation == true) {
                         e.stopImmediatePropagation();
                         e.stopPropagation();
                     }
                     e.preventDefault();
                     $('#popupSearch-screen').css('display','none');

                     len = $(".ui-controlgroup-controls :checkbox:checked").length;
                     if (len == 0) {
                        jqm_alert("Need to select at least one type: videos, dvds, models, photos");
                        return;
                     }
                     
                     if ($("#popupSearch-popup #search").val() == "") {
                        jqm_alert("A search term is required");
                        return;
                     }
                 
                     searchVideos = 0;
                     searchDvds = 0;
                     searchModels = 0;
                     searchPhotos = 0;
                     if ($(".ui-controlgroup-controls #select-video").is(":checked")) {
                        searchVideos = 1;
                     }
                     if ($(".ui-controlgroup-controls #select-dvd").is(":checked")) {
                        searchDvds = 1;
                     }
                     if ($(".ui-controlgroup-controls #select-model").is(":checked")) {
                        searchModels = 1;
                     }
                     if ($(".ui-controlgroup-controls #select-photo").is(":checked")) {
                        searchPhotos = 1;
                     }
                     
                     searchText = $("#popupSearch-popup #search").val();
                     
                     category = $("#popupSearch-popup #category").val();
                     if (category == "all") {
                        category_id = 0;
                     } else {
                        category_id = category.split(":");
                        category_id = category_id[0];

                     }
                     
//                    // have to re-bind search button??
//                    $('.search-btn').off("click");
//                    $('.search-btn').on("click", function(e) {
//                        $('#popupSearch').popup("open", {});
//
//                    });

                    $('#popupSearch').on({
                        popupafterclose: function() {
                            // first reset this even to do nothing
                            $('#popupSearch').on({
                                popupafterclose: function() {}}
                            );
                            hmCon.search('search', searchText, {videos: searchVideos, dvds: searchDvds, models: searchModels, photos: searchPhotos, category_id:category_id });
                        }
                    });
                    
                    //$('#popupSearch-popup').fadeOut("slow", function() {
                        $('#popupSearch').popup("close"); 
                    //});

                  });

		  // For the search box: a "Search" button on apple online keyboard makes form submission possible
		  var searchSubmit = function(e) {
		      $('#search-button').trigger('tap');
		      e.preventDefault();
		      return true;
		  };
		  $('#form1').submit(searchSubmit);
		  //$('#form2').submit(searchSubmit);

loadScript( 'js/ajax/success.min.js', function() {
    loadScript( 'js/ajax/home.success.min.js', function() {
        loadScript( 'js/ajax/print.success.min.js', function() {
                            

                
                

                loadScript( 'js/padcontrol.min.js', function() {

                    // fix join links
                    $('.joinlink').attr('href', joinPad);

                    hmCon.init('ajax/');
                     // can't do changePage() or bookmark hashes won't work for getting content!
                    hmCon.getHash();

                    // site logo
                    $('.sub-logo').css('background-image', 'url("images/logos/' + hmCon.query.site + '.png")');
                    $('.sub-logo').css('display', 'block');

                    $('#sites').val(hmCon.query.site);

                    //bind clicks of logos
                    $('.logo').on('click', function(e) {
                        e.stopImmediatePropagation();
                        e.stopPropagation();
                        e.preventDefault();
                        window.location = '\\';
                    });
                    $('.sub-logo').on('click', function(e) {
                        e.stopImmediatePropagation();
                        e.stopPropagation();
                        e.preventDefault();
                        hmCon.goHome();
                    });
                    
                    hmCon.change('home');                     
                });

        });
        loadScript( 'js/ajax/search.success.min.js', $.noop());
        loadScript( 'js/ajax/fave.success.min.js', $.noop());
    });
});

            });
            // app constants:
            // 'js/ajax/' = PAD_JS
            // '/ajax/' = MOBILE_AJAX_SCRIPT
            // 'js/hustler/ajax/' = MOBILE_JS_AJAX
            // '//' = DOCROOT
        </script>
    </body>
</html>
