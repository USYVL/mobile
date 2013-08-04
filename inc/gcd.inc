<?php
// This is a similar calculation obtained from another source.
// like the arg layout better....

// RadiusofEarth can be any unit of measure: 
//    statute miles, nautical miles, or kilometers. 
//    The radius of Earth is 6378.1 kilometers or 3963.1 statute miles.
//
// Not really sure what the difference is between these two, I think that 
// the GCD may take into account the curvature of the earth, whereas calcDist 
// assumes a flat surface.
//
function calcDistPaired($a, $b) { 
    return calcDist($a[0],$a[1],$b[0],$b[1]);
}
////////////////////////////////////////////////////////////////////////////////
function calcDist($lat_A, $long_A, $lat_B, $long_B) { 

  $distance = sin(deg2rad($lat_A)) 
                * sin(deg2rad($lat_B)) 
                + cos(deg2rad($lat_A)) 
                * cos(deg2rad($lat_B)) 
                * cos(deg2rad($long_A - $long_B)); 

  $distance = (rad2deg(acos($distance))) * 69.09; 

  return $distance; 
}
////////////////////////////////////////////////////////////////////////////////
function gcd($lat1,$lon1,$lat2,$lon2){
    /* Convert all the degrees to radians */
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    
    /* Find the deltas */
    $delta_lat = $lat2 - $lat1;
    $delta_lon = $lon2 - $lon1;
	
    /* Find the Great Circle distance */
    $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
    
    $EARTH_RADIUS = 3956;
    
    $distance = $EARTH_RADIUS * 2 * atan2(sqrt($temp),sqrt(1-$temp));
    
    return $distance;
}
/*
function deg2rad($deg){	
    //$radians = $deg * M_PI/180.0;
    return $deg * M_PI / 180.0 ;
    //return($radians);
}
function rad2deg($rad){
  return $rad * 180.0 / M_PI ;
}
*/
?>
