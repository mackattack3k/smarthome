function updateClock ( ){
    var currentTime = new Date ();
    var weekday = new Array(7);
    weekday[0] = "Söndag";
    weekday[1] = "Måndag";
    weekday[2] = "Tisdag";
    weekday[3] = "Onsdag";
    weekday[4] = "Torsdag";
    weekday[5] = "Fredag";
    weekday[6] = "Lördag";

    $("#currenttime").html(getTime(currentTime));
    $("#currentday").html(weekday[currentTime.getDay()]);
    $("#currentdate").html(currentTime.toLocaleDateString('sv-SE'));

    //Check if the time is the same as a departure and remove it
    $('.traffic-result').each(function() {
        //Departures current hour and minute
        var departureTime          = $(this)
                                  .children('.traffic-third')
                                  .children('.departure-time')
                                  .attr('data-date');
        var departureDate = new Date (departureTime);

        //console.log("departureTime: " + departureTime)
        //console.log('currentTime: ' + currentTime + ' departure: ' + departureDate); //Used for debugging when the departures aren't removed...
        //If the departure is leaving now -- or -- the browser was idle and the departure has already left
        if (departureDate <= currentTime) {
          $(this).animate( //Animate a fade and remove
            {
              bottom: '0px',
              opacity: 0.25,
              height: 'toggle',
              padding: '0px',
              margin: '0px'
            },
            1500,
            'easeInQuart',
            function () {
            $(this).remove();
          });
        }
    });

    //Check if there are less than 2 departures left
    //console.log('Checking if we should get new departures.');
    //console.log('currentlyUpdatingTraffic; ' + currentlyUpdatingTraffic);
    //TODO: This only works for chrome... wonder why
    var numberOfDepartures = $('#traffic-results').children('.traffic-result').length;
    if (numberOfDepartures < 2 && !currentlyUpdatingTraffic) {
      currentlyUpdatingTraffic = true;
      console.log('Too few departures, getting departures');
      getDepartures();
    }
 }
function getDepartures() {
    newNotification('Updating public transport', 'info');
    //Check if the value of traffic-search is set and use it
    var defaultStationName = 'Åmänningevägen';
    var defaultStationID = 7453026; //Åmänningevägen = 7453026, Årstaberg station = 7424920, Gullmarsplan = 7421705
    var stationInfo = document.querySelector('#station-menu').selected;
    var stationIDFromSettings = stationInfo.substring(stationInfo.length-7,stationInfo.length);
    var stationNameFromSettings = stationInfo.substring(0,stationInfo.length-8);
    var stationName = defaultStationName;
    var stationID = defaultStationID;

    if (stationIDFromSettings !== undefined || stationIDFromSettings !== '' ||
        stationIDFromSettings !== null || stationNameFromSettings !== undefined ||
        stationNameFromSettings !== '' || stationNameFromSettings !== null){

        stationID = stationIDFromSettings;
        stationName = stationNameFromSettings;
    }
    $('#traffic-search-input').html(stationName);


    console.log("Getting departures for: " + stationName + " id: " + stationID);


    //Adda spinning refresh icon before loading the departure times. Also removes previous departure times.
    $('#traffic-loading')
        .addClass("traffic-loading-before")
        .removeClass("traffic-loading-no-before")
        .show();
    $("#traffic-results").html('');
    $.ajax({
        url: "php/publicTransport.php",
        data: {function: "fullHTML", stationid: stationID},
        cache: false,
        datatype: 'html',
        success: function (trafficData) {
            //Remove spinning refresh icon and output the departure times.
            $('#traffic-loading').addClass("traffic-loading-no-before")
                .removeClass("traffic-loading-before")
                .hide();
            $("#traffic-results").html(trafficData);

            if (regexContainsErrorText.test(trafficData)) {
                newNotification('Error updating public transport', "error", 10000);
                currentlyUpdatingTraffic = true; //Changing it to true so that it doesn't continue to update traffic
            } else {
                newNotification('Public transport updated', "success");
                currentlyUpdatingTraffic = false;
            }
            var date = new Date();
            $('#traffic-last-updated').html("Senast uppdaterad: "+getTime(date,'swedish-full'));

        }
    })
}
function getWeather() {
    newNotification('Updating weather', 'info');
    //View spinning icon and hide the previous weather results
    $('#weather-loading')
        .addClass('weather-loading-before')
        .removeClass('.weather-loading-no-before')
        .css('margin', '20px');
    $('.weather-item-container').hide();

    $.ajax({
        url: "php/weather.php",
        data: {htmlCall: "true", debug: "false"},
        cache: false,
        datatype: 'html',
        success: function (trafficData) {
            $('#weather-loading')
                .addClass('weather-loading-no-before')
                .removeClass('.weather-loading-before').css('margin', '0px');
            //View the result
            $('#weather-data').html(trafficData);

            if (regexContainsErrorText.test(trafficData)) {
                newNotification('Error getting weather', "error", 10000);
            } else {
                newNotification('Weather updated', "success");
            }
            var date = new Date();
            $('#weather-last-updated').html("Senast uppdaterad: "+getTime(date,'swedish-full'));
        }
    });
}
function getStocks(){
    newNotification('Updating weather', 'info');
    //View spinning icon and hide the previous weather results
    $('#stocks-loading')
        .addClass('weather-loading-before')
        .removeClass('.weather-loading-no-before')
        .css('margin', '20px');
    //$('.stock-items').hide();

    $.ajax({
        url: "php/stocks.php",
        data: {function: "html", debug: "false", stocks: "AAPL,FB,GOOG,TSLA,MSFT"},
        cache: false,
        datatype: 'html',
        success: function (stocksData) {
            $('#stocks-loading')
                .addClass('weather-loading-no-before')
                .removeClass('.weather-loading-before').css('margin', '0px');
            //View the result
            $('.stock-items').html(stocksData);

            if (regexContainsErrorText.test(stocksData)) {
                newNotification('Error getting stocks', "error", 10000);
            } else {
                newNotification('Stocks updated', "success");
            }
            var date = new Date();
            $('#stocks-last-updated').html("Senast uppdaterad: "+getTime(date,'swedish-full'));
        }
    });
}
function newNotification(outputText, type, duration) {
  var notificationsEnabled = $('#notifications-toggle').attr('checked');
    //Check if notifcations is enabled
    if (typeof notificationsEnabled !== typeof undefined && notificationsEnabled !== false) {
        var type = typeof type !== 'undefined' ? type : 'info'; //Default type of notification
        var duration = typeof duration !== 'undefined' ? duration : 5000;

        var iconTypes = {
            notification:"bell",
            info:"info",
            success:"check",
            error: "ban",
            warning: "exclamation-triangle"
        };
        for(iconTypes.length in iconTypes) {
            if(iconTypes.hasOwnProperty(type)) {
                var iconType = iconTypes[type];
            }
        }

        $('.notifications-container').append(
            "<div class='notification " +type+ "'>\
          <div class='remove-notification icon icon-times'></div>\
          <div class='notification-data'>\
              <div class='notification-icon icon icon-" + iconType + "'></div>\
              <div class='notification-text'>" + outputText + "</div>\
          </div>\
        </div>"
        ).children('.notification')
            .delay(duration)
            .fadeOut(1500, function(){
                $(this).remove()
            });
    }

}
function getTime(dateInput, format) {
    format = typeof format !== 'undefined' ? format : "none";
    var day = dateInput.getDate();
    var month = dateInput.getMonth() + 1; // Note the `+ 1` -- months start at zero.
    var year = dateInput.getFullYear();
    var hour = dateInput.getHours();
    var min = dateInput.getMinutes();
    var sec = dateInput.getSeconds();
    if (sec < 10) {
      sec = "0" + sec;
    }
    if (min < 10) {
      min = "0" + min;
    }
    if (hour < 10) {
      hour = "0" + hour;
    }
    if (format == "swedish-full"){
        return year+"-"+month+"-"+day+" "+hour+":"+min+":"+sec;
    }
    return hour+":"+min+":"+sec;
}
function setSettingsFromCookies(){
    var cookieNotification = Cookies.get('notifcations');
    if (typeof cookieNotification !== typeof undefined
        && cookieNotification !== false
        && cookieNotification == "checked"
        ) {
        console.log($( "#notifications-toggle" ));
    }

    /* Regular inputs */
    var cookiesStocks = Cookies.get('stocks-input');
    var cookiesNumberOfDepartures = Cookies.get('numberofdepartures-input');
    var cookiesStation = Cookies.get('station-input');
    var cookieLatitude = Cookies.get('latitude-input');
    var cookieLongitude = Cookies.get('longitude-input');
    var cookiesTimezone = Cookies.get('timezone-input');

    var cookiesObject = {
        "stocks-input": cookiesStocks,
        "numberofdepartures-input": cookiesNumberOfDepartures,
        "latitude-input": cookieLatitude,
        "longitude-input": cookieLongitude,
        "timezone-input": cookiesTimezone
    };

    var cookie;
    for (var cookieKey in cookiesObject) {
        if (cookiesObject.hasOwnProperty(cookieKey)) {
            cookie = cookiesObject[cookieKey];
            if (typeof cookie !== typeof undefined
                && cookie !== false
                && cookie !== null
            ) {
                //Set input to cookie
                document.getElementById(cookieKey).value = cookie;

                if (cookieKey == 'longitude-input' ||  cookieKey == 'latitude-input'){
                    //If its a gps cookie you can remove the auto button. Unsure about this
                    $('#auto-gps-button').hide();
                }
            }
        }
    }
    var stationInput = document.querySelector('#station-menu');
    if (typeof cookiesStation !== typeof undefined
        && cookiesStation !== false
        && cookiesStation !== null
    ) {
        stationInput.select(cookiesStation);
    }


}
function gpsSuccess(position) {
    var longitude = position.coords.longitude;
    var latitude = position.coords.latitude;
    document.getElementById('longitude-input').value = longitude;
    Cookies.set('longitude-input', longitude);
    document.getElementById('latitude-input').value = latitude;
    Cookies.set('latitude-input', latitude);
}
function gpsFail() {
    // Could not obtain location
    newNotification("Could not get coordinates. Is your browser allowing 'navigator.geolocation*'?"
        , "error", 10000);
}


/*
* Global scope variables
*/

var currentlyUpdatingTraffic = true;
var regexContainsErrorText = /\b[Ee][Rr][Rr][Oo][Rr]\b/;

/*
* End of global variable scope
*/

$(document).ready(function(){
    /*
     * Settings slideout menu
     */
    var slideout = new Slideout({
        'panel': document.getElementById('panel'),
        'menu': document.getElementById('slide-menu'),
        'padding': 260,
        'tolerance': 70
    });
    // Toggle button
    document.querySelector('.slideout-toggle-button').addEventListener('click', function() {
        slideout.toggle();
    });
    slideout.open();

    $('#auto-gps-button').click(function(){
        if (navigator.geolocation) {
            // Call getCurrentPosition with success and failure callbacks
            navigator.geolocation.getCurrentPosition(gpsSuccess, gpsFail);
        }
        else {
            newNotification("Sorry, your browser does not support geolocation services.", "error");
        }
        $(this).hide();

    });

    /*
    * Start updating things
    */

    setSettingsFromCookies();

    updateClock();
    setInterval('updateClock()', 1000); //Update time every second

    getDepartures();

    getWeather();
    setInterval('getWeather()', 1800000); //getWeather every 30 minutes

    getStocks();
    setInterval('getStocks()', 1800000); //Get stocks every 30 minutes




    //Toggle the class when a lights button is clicked (this changes the bg-color) and change the state in the json file
    $( ".lights-purple" ).click(function() {
        $(this).toggleClass('active');

        //Variables to post to the changelamp function
        var channel      =   $(this).attr('data-channel');
        var state   =   ($(this).hasClass('active'))? '1' : '0';

        $.ajax({
            url: "php/lights.php",
            data: {function: "toggleLight", debug: "true", channel: channel, state: state},
            cache: false,
            datatype: 'html',
            success: function (lightsData) {
                console.log(lightsData);

                if (regexContainsErrorText.test(lightsData)) {
                    newNotification('Error setting light', "error", 10000);
                } else {
                    newNotification('Lights updated', "success");
                }
            }
        });
    });

    //Toggle all the lights buttons and change them in the json file
    $( ".lights-all" ).click(function() {


        //Variables to post to the changelamp function
        var stateText   =   $(this).attr('id').substring(4,this.length);
        var state = (stateText== "off") ? "0" : "1";
        console.log(stateText +" "+ state);

        if (stateText == "off"){
            $(".lights-purple").removeClass('active');
        } else {
            $(".lights-purple").removeClass('active');
            $(".lights-purple").addClass('active');
        }

        $.ajax({
            url: "php/lights.php",
            data: {function: "toggleAllLights", debug: "true", state: state},
            cache: false,
            datatype: 'html',
            success: function (lightsData) {
                console.log(lightsData);

                if (regexContainsErrorText.test(lightsData)) {
                    newNotification('Error setting  all lights', "error", 10000);
                } else {
                    newNotification('Lights updated', "success");
                }
            }
        });
    });

    //When the refresh on traffic column is pressed. Update departure and animate it for a short while
    $(".column-content").on({
      mouseenter: function () {//Mouse enters the refresh icon
        $('#refresh-traffic').addClass('icon-spin');
      },
      mouseleave: function () {//Mouse leaves the refresh icon
        $('#refresh-traffic').removeClass('icon-spin');
      },
      click: function () {//Clicking the refresh icon
        getDepartures();
      }
    }, '#refresh-traffic');

    /*
    * Control notifications
    */

    var notificationsContainer = $(".notifications-container");
    notificationsContainer.on({
        mouseleave: function () {//Mouse leaves the notification
            $(this).children('.notification').fadeOut(3000, function () {
                $(this).remove()
            });
        },
        mouseenter: function () {//Mouse enters the notification
            $(this).children('.notification').stop(true, false).fadeIn(500);
        }
    });
    notificationsContainer.on({
        click: function () {//Mouse clicks the remove button
            $(this).parent('.notification').animate( //Animate a fade and remove
                {
                    opacity: 0
                },
                500,
                'easeInQuart',
                function () {
                    $(this).remove();
                });
        }
    }, '.remove-notification');

    /* Notifcations toggle */
    var notificationsToggle = document.querySelector('#notifications-toggle');
    notificationsToggle.addEventListener('change', function () {
        if (this.checked) {
            Cookies.set('notifcations', "checked");
        } else {
            Cookies.set('notifcations', "unchecked");
        }
    });

    /* Notifcations toggle */
    var debugToggle = document.querySelector('#debug-toggle');
    debugToggle.addEventListener('change', function () {
        if (this.checked) {
            Cookies.set('debug', "on");
        } else {
            Cookies.set('debug', "off");
        }
    });

    var stationMenu = document.querySelector("#station-menu");
    stationMenu.addEventListener("iron-select", function(){
        Cookies.set('station-input', stationMenu.selected);
        getDepartures();
    });


    var timer;
    $('.settings-input').on('input', function(){
        clearTimeout(timer);
        var value = $(this).val();
        var fullID = $(this).attr('id');
        var id = fullID.substring(0,fullID.length-6);
        var isValid = !$(this).attr('invalid');

        timer = setTimeout(function() {
            if (isValid){
                Cookies.set(fullID, value);
                console.log("Cookie: " + fullID + " val:" + value);
            }

        }, 2000);
        console.log(isValid);



        //setCookie();

    });



/*
    $.ajax({
        url: 'https://minasidor.jamtkraft.se/Api/ServiceProxy/Login',
        type: 'POST',
        success: function(res) {
            var headline = $(res.responseText).find('a.tsh').text();
            console.log(res);
        }
    });
*/


});
