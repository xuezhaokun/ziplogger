<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- jQuery Mobile -->
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
    <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <!--<script src="script.js"></script> -->
    
    <script type="text/javascript">
    var signup = function() {
        $("#error-message").empty();
        var signup_username = $('input#username').val();
        var signup_password = $('input#password').val();
        var signup_password2 = $('input#password2').val();
        var signup_fullname = $('input#fullname').val();
        var signup_email = $('input#email').val();
        var signup_identity = $('select#identity').val();
        var signup_music = $('select#favmusic').val();
        var signup_gender = $('select#gender').val();
        var signup_zipcode = $('input#zipcode').val();
        var signup_latitude = $('input#latitude').val();
        var signup_longitude = $('input#longitude').val();

        var error_message = "";
        if(signup_username == ""){
            error_message += "* Please input a username. <br />";
        }
        if(signup_password == ""){
            error_message += "* Please input a password. <br />"
        }
        if(signup_password != signup_password2){
            error_message += "* Confirm password and Password do not match. <br />";
        }
        if(!isEmail(signup_email)){
            error_message += "* Invalid email address. <br />";
        }
        if(error_message != ""){
            $("#error-message").append(error_message);
            return false;
        }else{
            $.ajax({
                type: "POST", 
                url: "checksignup.php",
                data:{ 
                    username: signup_username, 
                    password: signup_password,
                    fullname: signup_fullname,
                    email: signup_email,
                    identity: signup_identity,
                    music: signup_music,
                    gender: signup_gender,
                    zipcode: signup_zipcode,
                    latitude: signup_latitude,
                    longitude: signup_longitude
                }, 
                success: function(data) {
                    if (data == 'success') {
                        window.location.replace("index.php");
                
                    }else if (data == 'registered') {
                        $("#error-message").append('* Username is already registered. <br />');
                    }else if(data == 'invalid'){
                        $("#error-message").append('* Invalid Username or Password. <br />');
                    }
                }
            });
        }
        
    }
    
    var isEmail = function(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    var getLocation = function(){
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(getLatLong);
        }else{ 
            alert("Geolocation is not supported by this browser.");
        }
    }

    var getLatLong = function(position) {
        $("input#latitude").val(position.coords.latitude);
        $("input#longitude").val(position.coords.longitude);
    }
    var getGeoZipcode = function() {
        if(navigator.geolocation) {
            var fallback = setTimeout(function() { fail('10 seconds expired'); }, 10000);
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    clearTimeout(fallback);
                    //console.log('pos', pos);
                    var point = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                    new google.maps.Geocoder().geocode({'latLng': point}, function (res, status) {
                        if(status == google.maps.GeocoderStatus.OK && typeof res[0] !== 'undefined') {
                            var zip = res[0].formatted_address.match(/,\s\w{2}\s(\d{5})/);
                            if(zip) {
                                $("input#zipcode").val(zip[1]);
                            }else{
                                fail('Failed to parse');
                            }
                        }else{
                        fail('Failed to reverse');
                        }
                    });
                }, function(err) {
                    fail(err.message);
                }
            );
        }else{
            $("input#zipcode").val("Geolocation unsupported!");
        }
        function fail(err) {
            console.log('err', err);
            $("input#zipcode").val(err);
        }
    }

    var getZip = function(){getGeoZipcode()};

    $(window).load(function() {
        getZip();
        getLocation();

    });
    
    </script>
</head>
<body>
<div data-role="page">

    <div data-role="header">
        <a href="index.html" data-rel="back">Home</a>
        <h1>Register Page</h1>
    </div>

    <div data-role="content">

        <!--<form action="" method="post">-->
            
            <div data-role="fieldcontain">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div data-role="fieldcontain">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div data-role="fieldcontain">
                <label for="password2">Confirm Password:</label>
                <input type="password" name="password2" id="password2" required>
            </div>
            <div data-role="fieldcontain">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" required>
            </div>
            <div data-role="fieldcontain">
                <label for="identity">I am a:</label>
                <select id="identity" name="identity">
                    <option value="NULL">Select One</option>
                    <option>Fan</option>
                    <option>Artist</option>
                    <option>Band</option>
                    <option>Venue</option>
                    <option>Store/Company</option>
                </select>
            </div>
            <div data-role="fieldcontain">
                <label for="fullname">Full Name:</label>
                <input type="text" name="fullname" id="fullname">
            </div>
            <div data-role="fieldcontain">
                <label for="gender">Gender:</label>
                <select id="gender" name="gener">
                    <option value="NULL">Select One</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div data-role="fieldcontain">
                <label for="favmusic">Favorite Music:</label>
                <select id="favmusic" name="favmusic">
                    <option value="NULL">Select One</option>
                    <option>Pop</option>
                    <option>Classical</option>
                    <option>Rock&Roll</option>
                    <option>Country</option>
                    <option>Jazz</option>
                    <option>Blues</option>
                    <option>Others</option>
                </select>
            </div>

            <div data-role="fieldcontain">
                <label for="zipcode">Zip Code</label>
                <input type="text" name="zipcode" id="zipcode">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>
            <span id="error-message" style="color:red"></span>
            <input type="submit" onclick="signup()" value="Register">

        <!--</form>-->

    </div>
</div>

</body>
</html>