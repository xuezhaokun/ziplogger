<?php
include('config.php');
session_start();
$user_check=$_SESSION['login_user'];


$ses_sql=mysqli_query($db,"select username from users where username='$user_check'; ");

$row=mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);

$login_session=$row['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zip Logger</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- jQuery Mobile -->
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-1igPwXUOAYB3ths9vL6TUuoMfZUq39c&sensor=false"></script>
   
    <script type="text/javascript">
    var map = null;
    var geocoder = new google.maps.Geocoder();
    var info_window = new google.maps.InfoWindow();
    var bounds = new google.maps.LatLngBounds();
    var image = 'img/maison.png';

    var logout = function() {
        $.ajax({ 
            type: "POST", 
            url: "logout.php",
            success:function(data){
                if(data == "success"){
                    window.location.replace("index.php");
                }
            }
        });
    }

    var login = function() {
        var login_username = $('input#username').val();
        var login_password = $('input#password').val();
        $.ajax({
            type: "POST", 
            url: "checklogin.php",
            data:{ 
                username: login_username, 
                password: login_password

            },
            success: function(data) {
                //alert("data");
                //console.log(data);
                if (data == "success") {
                    //alert("in");
                    window.location.replace("index.php");
                }
                else if (data == 'error_wrong') {
                    $("#error-message").text("invalid username or password");
                    $('input#password').val("");
                    //return false;
                }
            }
        });
        
    }


    var getLocation = function(){
        var currentTab = $('input[name=switch]:checked').val();
        $.mobile.loading('show');
        if (navigator.geolocation) {
            if(currentTab == "map-switch"){
                $("#list-view").hide();
                $("#map-canvas").show();
                $("#page-footer").hide();
                navigator.geolocation.getCurrentPosition(getMap);
            }else if(currentTab == "Venue" || currentTab == "Store/Company"){
                $("#list-view").show();
                $("#map-canvas").hide();
                $("#page-footer").show();
                navigator.geolocation.getCurrentPosition(getMap);
            }else{
                $("#list-view").show();
                $("#map-canvas").hide();
                $("#page-footer").show();
                navigator.geolocation.getCurrentPosition(getPeople);
            }
        }else{ 
            alert("Geolocation is not supported by this browser.");
        }
    }

    var getMap = function(position){
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        var currentTab = $('input[name=switch]:checked').val();
        $('#list-view').empty();
        $('#list-view').listview('refresh');
        $.ajax({
            type: "GET",
            url: "yelpMap.php",
            data:{
                latitude: lat,
                longitude: lon,
                tab: currentTab
                //currentTab: currentTab
            },
            dataType: "json",
            success: function(data){
                var json = JSON.parse(data);
                //alert('success');
               //console.log(data);
                //alert("hello");
                var markers = json.businesses;
                console.log(currentTab);
                if(currentTab == "Venue" || currentTab == "Store/Company"){
                    for (var i = 0; i < markers.length; i++) {
                        var distance_miles = parseFloat(markers[i].distance/1600).toFixed(2);
                        //var rating = '<img src ="' + markers[i].rating_img_url + '" />';
                        var url = markers[i].mobile_url;
                        var html = "<li><a href=" + url + " target='_blank'>"+ markers[i].name + "<span class='ui-li-count'>"+ distance_miles +" miles</span></a></li>";
                        $('#list-view').append(html).listview('refresh');
                    }
                }else{
                    var mapOptions = {
                        //center: new google.maps.LatLng('42.2743400', '-71.8097730'),
                        center: new google.maps.LatLng(json.region.center.latitude, json.region.center.longitude),
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

                    google.maps.event.addListener (map, 'click', function () {
                        info_window.close();
                    });

                    var geocoder = new google.maps.Geocoder();
                    for (var i = 0; i < markers.length; i++) {
                        geocodeAddress(markers[i]);
                    }
                    google.maps.event.addListenerOnce(map, 'idle', function() {map.fitBounds(bounds);});
                }
               $.mobile.loading('hide');
            }
        });
    }

    var getPeople = function(position){
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        var currentTab = $('input[name=switch]:checked').val();
        $('#list-view').empty();
        $('#list-view').listview('refresh');
        //$.mobile.loading('show');
        $.ajax({
            //beforeSend: function() { $.mobile.showPageLoadingMsg(); }, //Show spinner
            //complete: function() { $.mobile.hidePageLoadingMsg() }, //Hide spinner
            
            type: "GET",
            url: "getnearby.php",
            data:{
                latitude: lat,
                longitude: lon
            },
            dataType: "json",
            success: function (data) {
                console.log(data);
                $.each(data, function(index, element) {
                    var distance = 0;
                    if(element.distance != null){
                         distance = parseFloat(element.distance).toFixed(2);
                    }
                    if(currentTab == element.role){    
                        var html = "<li><a href='#'>"+element.username + " (" + element.favorite_music + ") "+"<span class='ui-li-count'>"+ distance +" miles</span></a></li>";
                        $('#list-view').append(html).listview('refresh');
                    }
                    //console.log(element);
                });
                $.mobile.loading('hide');
            }
        });
    }

    var geocodeAddress = function(markers) {
        var address = markers.location.address[0];
        var city = markers.location.city;
        var address_google_map = address + ', ' + city;
        var picture = '<img src ="' + markers.image_url + '" />';
        var rating = '<img src ="' + markers.rating_img_url + '" />';
        var name = '<b>' + markers.name + '</b>';

        var categories_array = markers.categories;
        var categories = '';

        for (var c = 0; c < categories_array.length; c++ ) {
                categories += categories_array[c][1] + ',';
                //console.log(categories);
        }

        var info_text = name + '<br />' + categories + '<br />' + rating + '<br />' + address + '<br />' + city + '<br />' + picture;
        
        geocoder.geocode ({'address': address_google_map}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                createMarker(results[0].geometry.location, info_text);
            } else { 
                console.log("geocode of "+ address +" failed:"+status);
            }
        });
    }

    var createMarker = function(latlng, html) {
        var marker = new google.maps.Marker ({
            map: map, 
            position: latlng,
            icon: image
        });
        google.maps.event.addListener(marker, 'click', function() {
            info_window.setContent(html);
            info_window.open(map, marker);
        });
        bounds.extend(latlng);
    }

    $(window).load(function() {
        getLocation();
        google.maps.event.addDomListener(window, 'load', getLocation);
        $("[name=switch]").change(function() {
            getLocation();   
        });
    });

    </script>
</head>
<body>

<div data-role="page" id="index-page" style="height:100%">
    <div data-role="header" id="index-header" data-position="fixed">
    <?php 
        //echo "<p>sign in as ".$login_session."</p>";
        if(!isset($login_session)) {
            echo '<h1 class="ui-title" role="heading"><img src="img/logo6.png"/></h1><a href="#popupLogin" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-check ui-btn-icon-left ui-btn-a" data-transition="pop">Log in</a>';
        }
        else {
            echo '<h1><img src="img/logo5.png"/></h1><a href="#" onclick="logout()" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-check ui-btn-icon-left ui-btn-a">Sign out</a>';    
        }

    ?>
        <div data-role="popup" id="popupLogin" data-theme="a" class="ui-corner-all">
                <div style="padding:10px 20px;">
                    <h3>Please sign in</h3>
                    <span id="error-message" style="color:red"></span>
                    <label for="username" class="ui-hidden-accessible">Username:</label>
                    <input type="text" name="username" id="username" value="" placeholder="username" data-theme="a">
                    <label for="password" class="ui-hidden-accessible">Password:</label>
                    <input type="password" name="password" id="password" value="" placeholder="password" data-theme="a">
                    <button type="submit" onclick="login()" class="ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check">Sign in</button>
                </div>
            
        </div>
        <?php
            if(!isset($login_session)){
                echo '<a href="signup.php" target="_self">Sign Up</a>';
            }else{
                echo "<a href='#'>Hi: ".$login_session."</a>";
            }
        ?>
        
    </div>
    <!--<div align="center">-->
        <!--<div role="main" class="ui-content">-->
    <div class="segmented-control ui-bar-d" style="text-align:center">
        <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
            <input type="radio" name="switch" id="fans-switch" value="Fan" checked="true">
            <label for="fans-switch">Fans</label>
            <input type="radio" name="switch" id="artists-switch" value="Artist">
            <label for="artists-switch">Artists</label>
            <input type="radio" name="switch" id="venues-switch" value="Venue">
            <label for="venues-switch">Venues</label>
            <input type="radio" name="switch" id="stores-switch" value="Store/Company">
            <label for="stores-switch">Stores</label>
            <input type="radio" name="switch" id="map-switch" value="map-switch">
            <label for="map-switch">Map</label>
        </fieldset>
    </div>
        <!--</div>-->
    <!--</div>-->
        <!--<div role="main" class="ui-content ui-content-list" style="display:show">-->
    <div role="main" class="ui-content" id="list-canvas" style="height:100%">
        <ul id="list-view" data-role="listview" data-count-theme="b" data-inset="true">
        </ul>
    </div>
    <div data-role="footer" id="page-footer">
        <div data-role="navbar" data-iconpos="left">
            <ul>
                <li><a href="http://www.musicrevolt.org" target="_blank" data-icon="star">Get Involved</a></li>
                <li><a href="http://www.vjmanzo.com/_redirects/ziplogger" target="_blank" data-icon="grid">Get Informed</a></li>
            </ul>
        </div>
    </div>

</div>
<div role="main" id="map-canvas" style="height:100%"></div>
</body>
</html>