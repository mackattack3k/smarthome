function updateClock ( )
{
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
function getBus ()
{
    $.ajax({
      url: "functions.php?function=getBusses&station=gullmarsplan",
      cache: false
    })
      .done(function( html ) {
        $( "#results" ).append( html );
      });
}
 
$(document).ready(function()
{  
    updateClock();
    setInterval('updateClock()', 1000);
    getBus();
    
    
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
    
    
});