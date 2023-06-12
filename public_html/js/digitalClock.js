$(document).ready(function() {
        //alert('loaded');
        // Create two variable with the names of the months and days in an array
        var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]; 
        var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]
        
        // Create a newDate() object
        //var newDate = new Date();
        // Extract the current date from Date object
        //newDate.setDate(newDate.getDate());
        //$('#Date').html(  newDate.getFullYear() + "-" + padDigits(newDate.getMonth() + 1,2) + "-" + padDigits(newDate.getDate(),2));
        
        var digitalClock_Cfg = {
            mode   : '12',  // 12 or 24
            secs   : '#digitalClock_sec',
            mins   : '#digitalClock_min',
            hrs    : '#digitalClock_hours',
            date   : '#digitalClock_date',
            merid  : '#digitalClock_merid',
        };
        
	function digitalClock(cfg){
	    var intDate = new Date();
	    var seconds = intDate.getSeconds();
	    var minutes = intDate.getMinutes();
	    var hours   = intDate.getHours();
	    var date    = intDate.getDate();
	    var dateSt  = '';
	    
	    if( cfg.mode === "12"){
	        if( hours > 12 ) {
	            hours -= 12;
	            merid = "PM";
	        }
	        else {
	            merid = "AM";
	        }
	        $(cfg.merid).html("&nbsp;" + merid);
	    }
	    // Add a leading zero to the hours value
	    $(cfg.secs).html(( seconds < 10 ? "0" : "" ) + seconds);
	    $(cfg.mins).html(( minutes < 10 ? "0" : "" ) + minutes);
	    $(cfg.hrs).html(( hours < 10 ? "0" : "" ) + hours);
	    if( date !== dateSt ){
	        // day has changed so update the html
                $(cfg.date).html(intDate.getFullYear() + "-" + padDigits(intDate.getMonth() + 1,2) + "-" + padDigits(date,2));
	        dateSt = date;
	    }
	}
	// Do an initial call to get the display up right at page load 
	digitalClock(digitalClock_Cfg);
	// After that update every second 
	setInterval( function () { digitalClock(digitalClock_Cfg) }, 1000);	
});
function padDigits(number, digits) {
    return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
}
