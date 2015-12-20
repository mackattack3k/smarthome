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
    $('.traffic-result').each(function(index, el) {
        //Departureses current hour and minute
        var departureTime          = $(this)
                                  .children('.traffic-third')
                                  .children('.departure-time')
                                  .attr('value');
        var departureDate = new Date (departureTime);


        //console.log(currentTime + ' ' + departureDate); //Used for debugging when the departures arent removed...
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
    //console.log('currentlyUpdatingTrafic; ' + currentlyUpdatingTrafic);
    var numberOfDepartures = $('#traffic-results').children('.traffic-result').length;
    if (numberOfDepartures <= 2 && !currentlyUpdatingTrafic) {
      currentlyUpdatingTrafic = true;
      console.log('Too few departures, getting departures');
      getDepartures();
    }
 }
function getDepartures() {
    newNotification('Updating public transport', 'info');
    //Check if the value of traffic-search is set and use it
    var getStation = $('#traffic-search-input').val();
    var stationID = 7424920; //Åmänningevägen = 7453026, Årstaberg station = 7424920, Gullmarsplan = 7421705


    console.log("Getting departures for: " + getStation + " id: " + stationID);


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
                currentlyUpdatingTrafic = true; //Changing it to true so that it doesnt continue to update traffic
            } else {
                newNotification('Public transport updated', "success");
                currentlyUpdatingTrafic = true;
            }

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
        url: "php/weather.php?htmlCall",
        data: {htmlCall: "true"},
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
        }
    });
}
function newNotification(outputText, type, duration) {
  type = typeof type !== 'undefined' ? type : 'info'; //Default type of notification
  duration = typeof duration !== 'undefined' ? duration : 5000;

    var iconTypes = {
        notifcation:"bell",
        info:"info",
        success:"check",
        error: "ban",
        warning: "exclamation-triangle",
    };
    for(key in iconTypes) {
        if(iconTypes.hasOwnProperty(type)) {
            var iconType = iconTypes[type];
            //do something with value;
            //console.log(iconType);
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
function getTime(dateInput) {
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
    return hour+":"+min+":"+sec;
}


/*
* Global scope variables
*/

var currentlyUpdatingTrafic = true;
var regexContainsErrorText = /\b[Ee][Rr][Rr][Oo][Rr]\b/;

/*
* End of global variable scope
*/

$(document).ready(function(){

    updateClock();
    setInterval('updateClock()', 1000);

    getDepartures();

    getWeather();
    setInterval('getWeather()', 1800000); //getWeather every 30 minutes



    //Toggle the class when a lights button is clicked (this changes the bg-color) and change the state in the json file
    $( ".lights-yellow" ).click(function() {
        $(this).toggleClass('active');

        //Variables to post to the changelamp function
        var id      =   $(this).attr('id')
        var state   =   ($(this).hasClass('active'))? 'on' : 'off';

        $.ajax({ url: 'functions.php',
         data: {function: 'editLamps',var1: id, var2: state},
         type: 'get',
         success: function(output) {
                      newNotification(output);
                  }
        });
    });

    //Toggle all the lights buttons and change them in the json file
    $( ".lights-all" ).click(function() {
        $(".lights-yellow").removeClass('active');

        //Variables to post to the changelamp function
        var state   =   $(this).attr('id').substring(4,this.length);

        $.ajax({ url: 'functions.php',
         data: {function: 'editLamps',var1: 'all', var2: state},
         type: 'get',
         success: function(output) {
                      newNotification(output);
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

    $(".notifications-container").on({
        mouseleave: function () {//Mouse leaves the notification
            $(this).children('.notification').fadeOut(3000, function () {
                $(this).remove()
            });
        },
        mouseenter: function () {//Mouse enters the notification
            $(this).children('.notification').stop(true, false).fadeIn(500);
        }
    });
    $(".notifications-container").on({
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