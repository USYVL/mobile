$(function() {
        var location = new Array();
        location['latitude'] = 34.4;
        location['longitude'] = -119.9;
        location['accuracy'] = "Click to Get Actual Position";
        var str = "";
        
        function validGeoLocation(pos){
            str="<strong>Latitude: </strong>"+pos['latitude']+"<br /><strong>Longitude: </strong>"+pos['longitude']+"<br /><strong>Accuracy: </strong>"+pos['accuracy'];
            $('#device_location').html(str);
            location=pos;  // reset location to results of lookup
        }
        
        validGeoLocation(location);
        
        $('#device_location').click(function() {
                mwf.standard.geolocation.setTimeout(900000);  // make value valid for 15 minutes
                mwf.standard.geolocation.getCurrentPosition(
                    function(pos) { 
                        validGeoLocation(pos);
                        $.ajaxSetup ({  
                                cache: false  
                        });  
                        
                        // immediately do an ajax call
                        var ajax_load = "<img src='img/load.gif' alt='loading...' />";  
                        var scriptUrl = "ajax/getDistanceAjax.php?lat="+pos['latitude']+"&lon="+pos['longitude'];
                        $('#proximal_events').html(ajax_load).load(scriptUrl);
                    }, 
                    function(err){ alert("Err:"+err.message); }
                    );
                
                //alert("click of location");
        });
        $('#select-schedule-display').change(function(){
                //alert("Changing schedule display to: "+$('#select-schedule-display').val());
                if( $('#select-schedule-display').val() == 'All' ){
                    $('.Practices').css("display","block");
                    $('.Games').css("display","block");
                    $('.Tournaments').css("display","block");
                }
                if( $('#select-schedule-display').val() == 'Practices' ){
                    $('.Practices').css("display","block");
                    $('.Games').css("display","none");
                    $('.Tournaments').css("display","none");
                }
                if( $('#select-schedule-display').val() == 'Games' ){
                    $('.Practices').css("display","none");
                    $('.Games').css("display","block");
                    $('.Tournaments').css("display","none");
                }
                if( $('#select-schedule-display').val() == 'Tournaments' ){
                    $('.Practices').css("display","none");
                    $('.Games').css("display","none");
                    $('.Tournaments').css("display","block");
                }
                // so this is close, but actually hides the one selected instead of hiding the others.
                // Also need to activate the others when changing, so need some logic here.
                //$("."+$('#select-schedule-display').val()).css("display","none");
        });
});

