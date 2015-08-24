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

    //Check if the time is the same as a bus and remove it
    $('.traffic-result').each(function(index, el) {
        //Busses current hour and minute
        var busTime          = $(this)
                                  .children('.traffic-third')
                                  .children('.departure-time')
                                  .attr('value');
        var busDate = new Date (busTime);


        //console.log(currentTime + ' ' + busDate); //Used for debugging when the busses arent removed...
        //If the bus is leaving now -- or -- the browser was idle and the bus has already left
        if (busDate <= currentTime) {
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

    //Check if there are less than 2 busses left
    if ($('#traffic-results').children('.traffic-result').length <= 2 && $('#traffic-results').children('.traffic-result').length > 0 ) {
      getBus();
      console.log('Too few buses, getting busses');
      newNotification('Too few bus departures. <br/>Getting new ones');
    }
 }
function getBus (){
    newNotification('Fetching the buses');
    //Check if the value of traffic-search is set and use it
    var getStation      =       $('#traffic-search-input').val();
    if (getStation == ''){
        var station     =       'Åmänningevägen';
    }else{
        var station     =       getStation;
    }

    console.log(getStation);


    //Adda spinning refresh icon before loading the bus times. Also removes previous bus times.
    $('#traffic-loading').addClass("traffic-loading-before");
    $('#traffic-loading').removeClass("traffic-loading-no-before");
    $('#traffic-loading').show();
    $( "#traffic-results" ).html( '' );
    $.ajax({
      url: "functions.php?function=getBusStop&var1=" + getStation,
      cache: false,
    })
      .done(function( html ) {
        //Remove spinning refresh icon and output the bus times.
        $('#traffic-loading').addClass("traffic-loading-no-before");
        $('#traffic-loading').removeClass("traffic-loading-before");
        $('#traffic-loading').hide();
        newNotification('New buses added');
        $( "#traffic-results" ).html( html );
      });
}
function getWeather() {
  newNotification('Fetching the weather');
  //View spinning icon and hide the previous weather results
  $('#weather-loading').addClass('weather-loading-before').removeClass('.weather-loading-no-before').css('margin', '20px');
  $('.weather-item-container').hide();

  //All the types we want to see
  var weatherArray = [
    'icon',
    'desc',
    'temp'
   ];
  var weatherResultObj = {};
  var i = 0;

  //fetch all the values woohoo
  $.each(weatherArray, function(index, el) {
    //console.log(weatherArray[i]);
    var type = weatherArray[i];

    $.ajax({
      url: "functions.php?function=getWeather&var1=" + type,
      cache: false,
      datatype: 'html',
      success: function (data) {
        weatherResultObj[type] = data; //Add the result to new array

        if (Object.keys(weatherResultObj).length == weatherArray.length) {//When the whole weatherarray has been looped :)
          //console.log(weatherResultObj);
          //Remove spinning icon
          $('#weather-loading').addClass('weather-loading-no-before').removeClass('.weather-loading-before').css('margin', '0px');
          //View the result
          $('#weather-current-icon').html( weatherResultObj.icon );
          $('.weather-current-details').empty().append( weatherResultObj.temp ).append( weatherResultObj.desc );
          $('.weather-item-container').show();
          newNotification('Weather updated');
        }

        /*
        if(Object.keys(weatherResultObj).length == weatherArray.length){ // When the ajax for each value in the array is done
          $('#weather-icon-container').html(weatherResultObj['icon']); //display the weather icon
          $('.weather-items').empty();
          $('.weather-items').append(weatherResultObj['desc']);
          $('.weather-items').append(weatherResultObj.temp);
          console.log(weatherResultObj);
          for ( property in weatherResultObj ) {
            console.log( property );
          }
        }
        */

      }
    });
    //Ajax over
    i++;

  });
  //Each over


}
function newNotification(outputText) {
  $('.notifications-container').append(
    "<div class='notification'>\
      <div class='remove-notification icon icon-times'></div>\
      " + outputText + "\
    </div>\
    "
  ).children('.notification').delay(5000).fadeOut(1500);
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
    return hour+":"+min+":"+sec;
}

$(document).ready(function(){
    updateClock();
    setInterval('updateClock()', 1000);
    getBus();
    getWeather();
    setInterval('getWeather()', 1800000); //getWeather every 30 minutes


    $('departure-time').each(function() {
        console.log($(this).attr('value'));
    });

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

    //When the refresh on traffic column is pressed. Update bus and animate it for a short while
    $(".column-content").on({
      mouseenter: function () {//Mouse enters the refresh icon
        $('#refresh-traffic').addClass('icon-spin');
      },
      mouseleave: function () {//Mouse leaves the refresh icon
        $('#refresh-traffic').removeClass('icon-spin');
      },
      click: function () {//Clicking the refresh icon
        getBus();
      }
    }, '#refresh-traffic');

    /*
    * Control notifications
    */

    $(".notifications-container").on({
      mouseenter: function () {//Mouse enters the notification
        $(this).children('.notification').stop(true, false).fadeIn(500);
      },
      mouseleave: function () {//Mouse leaves the notification
        $(this).children('.notification').fadeOut(3000, function(){
            $(this).remove()
        });
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
