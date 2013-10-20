if (! $()) {
    //alert("Hi There");
    // ideally this should agree with the version used in the tpl file
    document.write('<script src="js/jquery-1.10.2.min.js"><\/script>');
}

//if (!window.jQuery) {
//}
// window.jQuery || document.write('<script src="Assets/jquery-1.8.3.min.js"></script>);
// allowed scoreTypes
// Rally
// DoubleMax
// HotPotato
// SideOut
$(function() {
        var scoreTypes = [ "DoubleMax","HotPotato","Rally","SideOut"];
        var shirtColors = [ "red","white","blue"];
        var shirtColorsLen = shirtColors.length;
        var len = scoreTypes.length;
        var scoreMax = 25;
        var winByTwo = true;        
        var gameOver = false;
        var scoreType  = "DoubleMax"; // type of scoring - only rally currently supported
        var scoreCap = 35;  // where we no longer need to win by two
        var switchOn = 7;  // indicate a side change on multiples of this 
        var switchAt = 0;  // indicate a single switch in the middle of the game
        
        var $tmA = $('#tmA');
        var $tmB = $('#tmB');
        var $tmAminus = $('#tmA_minus');
        var $tmBminus = $('#tmB_minus');
        var $toggleServe = $('#toggle_serve');
        var tmAscore = parseInt($tmA.html());
        var tmBscore = parseInt($tmB.html());
        var consServes = new Array();
        var iServed = false;
        consServes["a"] = 0;
        consServes["b"] = 0;
        
        //var tmAcons = 0;
        //var tmBcons = 0;
        var notes = "";
        
        function updateNotes(){
            notes="<p>Scoring Type: "+scoreType+"<br />\n";
            notes+="Score Max: "+scoreMax+"<br />\n";
            notes+="Score Cap: "+scoreCap+"<br />\n";
            notes+="Win By Two: ";
            notes+=(winByTwo)?"Yes":"No";
            notes+="<br />\n";
            notes+="Side Change On: "+switchOn.toString()+"<br />\n";
            notes+="Side Change At: "+switchAt.toString()+"<br />\n";
            notes+="</p>\n";
            $('#notes').html(notes);
        }
        updateNotes();
        
        $().ready(function(){
                // hack to load shirt colors from html once page is loaded
                $('#tmA').css('background-color',$('#tmA_color').html());
                $('#tmB').css('background-color',$('#tmB_color').html());
        });
        // 
        $tmA.click(function(){
                if( gameOver ) return;
                servStatus='#tmA_service';
                
                iServed = $(servStatus).isVisible();
                serviceTracker(iServed,'a','b');
                
                //var tmAscore = parseInt($tmA.html()) + 1;
                if( scoreType == "SideOut") {
                    if(  iServed ) tmAscore++;
                }
                else     tmAscore++;
            
                $tmA.html(tmAscore.toString());
                checkControls(tmAscore,tmBscore);
                updateServiceIndicator(servStatus,consServes['a'],consServes['b']);
                //if ($('#tmB_service').isVisible()){
                //    serviceChange();
                //}
                checkForWinner(tmAscore,tmBscore);
                checkSideChange(tmAscore,tmBscore);
        });
        $tmB.click(function(){
                if( gameOver ) return;
                
                servStatus='#tmB_service';
                iServed = $(servStatus).isVisible();
                //if( iServed ) {
                //    alert("team B served");
                //}
                serviceTracker(iServed,'b','a');
                
                
                if( scoreType == "SideOut") {
                    if(  iServed ) tmBscore++;
                }
                else     tmBscore++;
            
                //var tmBscore = parseInt($tmB.html()) + 1;
                $tmB.html(tmBscore.toString());
                checkControls(tmAscore,tmBscore);
                updateServiceIndicator(servStatus,consServes['b'],consServes['a']);
                checkForWinner(tmAscore,tmBscore);
                checkSideChange(tmAscore,tmBscore);
                //if ($('#tmA_service').isVisible()){
                //    serviceChange();
                //}
        });
        $tmAminus.click(function(){
                if( gameOver ) gameOver = false;
                tmAscore--;
                //var tmAscore = parseInt($tmA.html()) - 1;
                $tmA.html(tmAscore.toString());
                checkControls(tmAscore,tmBscore);
        });
        $tmBminus.click(function(){
                if( gameOver ) gameOver = false;
                //var tmBscore = parseInt($tmB.html()) - 1;
                tmBscore--;
                $tmB.html(tmBscore.toString());
                checkControls(tmAscore,tmBscore);
        });
        $('#tmA_color').click(function(){
                
                shirtColorA = $(this).html();
                //alert(shirtColorA);
                for( var i=0; i<shirtColors.length; i++){
                    if( shirtColorA == shirtColors[i] ){
                        nexti = (i+1)%shirtColors.length;
                    }
                }
                shirtColorA = shirtColors[nexti];
                $('#tmA_color').html(shirtColorA);
                $('#tmA').css('background-color',shirtColorA);
                //updateNotes();
        });
        $('#tmB_color').click(function(){
                
                shirtColorB = $(this).html();
                //alert(shirtColorB);
                for( var i=0; i<shirtColors.length; i++){
                    if( shirtColorB == shirtColors[i] ){
                        nexti = (i+1)%shirtColors.length;
                    }
                }
                shirtColorB = shirtColors[nexti];
                //$('#tmB_color').html(shirtColorB);
                $(this).html(shirtColorB);
                $('#tmB').css('background-color',shirtColorB);
                //updateNotes();
        });
        
        $('#scoreType').click(function () {
                // cycle through the options
                // first find which one we are currently set for:
                scoreType = $(this).html();
                for( var i=0; i<len; i++){
                    if( scoreType == scoreTypes[i] ){
                        nexti = (i+1)%len;
                    }
                }
                scoreType = scoreTypes[nexti];
                $('#scoreType').html(scoreType);
                updateNotes();
                
        });
        
        $('#switch_sides').click(function (){
                $('#c1_wrapper').toggleClass('lfloat');
                $('#c1_wrapper').toggleClass('rfloat');
                $('#c2_wrapper').toggleClass('lfloat');
                $('#c2_wrapper').toggleClass('rfloat');
        });
        
        // show/hide controls based on whether a score has been registered yet
        function checkControls(a,b) {
            if( a == 0 && b == 0){
                $toggleServe.show();
                $('#scoreType').show();
                $('#tmA_color').show();            
                $('#tmB_color').show();            
            }
            else {
                $toggleServe.hide();
                $('#scoreType').hide();
                $('#tmA_color').hide();            
                $('#tmB_color').hide();            
            }
        }
        function checkForWinner(a,b){
            if( a >= scoreMax || b >= scoreMax ){
                if( a >= scoreCap || b >= scoreCap && winByTwo ){
                    winByTwo = false;
                }
                if( winByTwo ){
                    if( Math.abs(a-b) >= 2){
                        declareWinner(a,b);
                    }
                    else {
                        $('#winner').html("Need to Win by two");
                    }
                }
                else {
                    declareWinner(a,b);
                }
            }
        }
        function declareWinner(a,b){
            gameOver = true;
            if( a > b ){
                var name = $('#tmAname').html();
            }
            else {
                var name = $('#tmBname').html();
            }
            $('#winner').html(name+" Wins!");
        }
        
        // get scores and see if there is a winner
        //$tmAscore = parseInt($tmA.html());
        //$tmBscore = parseInt($tmB.html());
        function serviceTracker(served,y,n){
            if( served){
                consServes[y]++;
                consServes[n]=0;  // pretty sure this breaks doublemax
            }
            else         {
                consServes[y]=0;
                consServes[n]++;  // pretty sure this breaks doublemax
            }
        }
        // check to see if service needs to change
        // the choice of when to switch depends on what type of scoring is used
        //  hot-potato - two serves regardless of results
        //  rally scoring - team that gets the point serves
        //  double-max - modified rally scoring, team that gets point gets up to two serves
        //  sideout - points only awarded if serving, winner of rally serves
        
        // This function currently does rally scoring
        function updateServiceIndicator(which,cons1,cons2){
            switch(scoreType){
            case "Rally":
                rallyService(which);
                break;
            case "DoubleMax":
                doubleMaxService(which,cons1);
                break;
            case "HotPotato":
                hotPotatoService(which,cons1,cons2);
                break;
            case "SideOut":
                sideOutService(which);
                break;
            default:
                doubleMaxService(which,cons1);
            }
        }
        function checkSideChange(a,b){
            if(switchOn>0){
                if( ((a+b)%switchOn) == 0){
                    $('#switch_sides').addClass("sideChangeAlert");
                    $('#switch_sides').fadeIn(100).fadeOut(100).delay(100).fadeIn(100).fadeOut(100).delay(100).fadeIn(100);
                }
                else {
                    $('#switch_sides').removeClass("sideChangeAlert");
                }
            }
            if(switchAt>0){
                if((a+b) == switchAt){
                   $('#switch_sides').addClass("sideChangeAlert");
                    $('#switch_sides').fadeIn(100).fadeOut(100).delay(100).fadeIn(100).fadeOut(100).delay(100).fadeIn(100);
                }
                else {
                    $('#switch_sides').removeClass("sideChangeAlert");
                }
            }
        }
        function sideOutService(which){
            if (! $(which).isVisible()){
                serviceChange();
            }
        }
        
        // doubleMax is a modified rally scoring.
        // point is awarded to winner of each rally but a team is allowed no more than 2 serves in a row
        // so even if the serving team wins the rally on its second serve, service goes to the other team
        function doubleMaxService(which,cons){
            if ($(which).isVisible()){
                if( cons>=2 ) serviceChange();
            }
            else serviceChange();
        }
        function rallyService(which){
            if (! $(which).isVisible()){
                serviceChange();
            }
        }
        function hotPotatoService(which,cons1,cons2){
            // uses the globals, not super elegant
            if( cons1>=2 || cons2>=2 ){
                serviceChange();
                consServes["a"]=0;
                consServes["b"]=0;
            }
        }
        
        function serviceChange(){
            $('#tmA_service').toggle();
            //$('#tmB_service').removeClass('hideservice');
            //$('#tmB_service').toggleClass('hideservice');
            $('#tmB_service').toggle();
            //$tmA.toggle();
            //$tmB.toggle();
        }
        
        $toggleServe.click(function() {
            serviceChange();
        });
        
        $.fn.isVisible = function() {
            return $.expr.filters.visible(this[0]);
        };
            
        var whistleSingle = document.createElement('audio');
        whistleSingle.setAttribute('src', 'media/whistle-single.mp3');
        whistleSingle.setAttribute('src', 'media/whistle-single.wav');
        //whistleSingle.setAttribute('autoplay', 'autoplay');
        //whistleSingle.load()
        $.get();
        whistleSingle.addEventListener("load", function() {
                whistleSingle.play();
        }, true);
        
        var whistleDouble = document.createElement('audio');
        whistleDouble.setAttribute('src', 'media/whistle-double.mp3');
        whistleDouble.setAttribute('src', 'media/whistle-double.wav');
        //whistleDouble.setAttribute('autoplay', 'autoplay');
        //whistleDouble.load()
        $.get();
        whistleDouble.addEventListener("load", function() {
                whistleDouble.play();
        }, true);
        
        
        $('#play-single').click(function() {
                whistleSingle.play();
        });
        $('#play-double').click(function() {
                whistleDouble.play();
        });       
});

$(function() {
});

