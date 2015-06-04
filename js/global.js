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
 
$(document).ready(function()
{  
    updateClock();
    setInterval('updateClock()', 1000);
});