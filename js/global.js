function updateClock ( ){
    var currentTime = new Date ( );
    var weekday = new Array(7);
    weekday[0] = "Söndag";
    weekday[1] = "Måndag";
    weekday[2] = "Tisdag";
    weekday[3] = "Onsdag";
    weekday[4] = "Torsdag";
    weekday[5] = "Fredag";
    weekday[6] = "Lördag";

    $("#currenttime").html(currentTime.toLocaleTimeString('sv-SE'));
    $("#currentday").html(weekday[currentTime.getDay()]);
    $("#currentdate").html(currentTime.toLocaleDateString('sv-SE'));

 }
function getBus (){
    //Check if the value of traffi-search is set and use it
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
        console.log('Done with new busses');
        $( "#traffic-results" ).html( html );
      });
}
function getWeather() {
  //View spinning icon and hide the previous weather results
  $('#weather-loading').addClass('weather-loading-before').removeClass('.weather-loading-no-before').css('margin', '20px');
  $('.weather-current').hide();
  $('.weather-current-header').hide();

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
          $('.weather-current-header').show();
          $('#weather-current-icon').html( weatherResultObj.icon );
          $('.weather-current-details').empty().append( weatherResultObj.temp ).append( weatherResultObj.desc );
          $('.weather-current').show();


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

$(document).ready(function()
{
    updateClock();
    setInterval('updateClock()', 1000);
    getBus();
    getWeather();
    setInterval('getWeather()', 1800000); //Call getWeather every 30 minutes

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
                      console.log(output);
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
                      console.log(output);
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




});
