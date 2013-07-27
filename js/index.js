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
            //alert($str);        
        }
        
        validGeoLocation(location);
        
        $('#device_location').click(function() {
                mwf.standard.geolocation.setTimeout(900000);  // make value valid for 15 minutes
                mwf.standard.geolocation.getCurrentPosition(
                    function(pos) { 
                        validGeoLocation(pos)
                    }, 
                    function(err){ alert("Err:"+err.message); }
                    );
                
                //alert("click of location");
        });
});

