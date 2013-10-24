<?php
class mwfMobileSite {
    ////////////////////////////////////////////////////////////////////////////
    function __construct(){
        $this->mode = "";
        $this->title = "Main Menu";
        $this->regsiteredFunctions = array();
        $this->regFunctionsArgs = array();
        $this->registerCoreFunctions();
        $this->registerExtendedFunctions();
        $this->processGET();
        $this->sdb = $GLOBALS['dbh']['sdb'];
        $this->mdb = $GLOBALS['dbh']['mdb'];
    }
    ////////////////////////////////////////////////////////////////////////////
    function getTitle(){
        return $this->title;
    }
    ////////////////////////////////////////////////////////////////////////////
    function disclaimer(){
        $bb  = "";
        $bb .= "<p>\n";
        $bb .= "This is a very preliminary version of the mobile site ";
        $bb .= "designed to allow mobile access to schedules during the course of the season.\n";
        $bb .= "It is still very much under development and may contain errors.\n";
        $bb .= "</p>\n";
        $b = $this->contentDiv("Disclaimer",$bb);
        return $b;
    }
    ////////////////////////////////////////////////////////////////////////////
    function processGET(){
        if( isset($_GET['mode'])) $this->mode = $_GET['mode'];
    }
    ////////////////////////////////////////////////////////////////////////////
    function fMenu($label = "",$menuitems = ""){
        $b  = "";
        $b .= "<div class=\"menu\">\n";
        $b .= "<h1 class=\"light menu-first\">$label</h1>\n"; 
        $b .= "<ul>\n";
        
        if( is_string($menuitems)) $b .= $menuitems;
        elseif( is_array($menuitems)){
            foreach($menuitems as $mitem) $b .= $mitem;
        }
        
        $b .= "</ul>\n";
        $b .= "</div><!-- Close Menu div -->\n";
        return $b;
    }
    ////////////////////////////////////////////////////////////////////////////
    function button($href = "", $label = ""){
        $b = "";
        $b .= "<a href=\"" . $href . "\" class=\"button button-padded\">$label</a>\n";
        return $b;
    }
    ////////////////////////////////////////////////////////////////////////////
    // This is meant to be overloaded with functions particular to child classes
    function registerCoreFunctions(){
        $this->registerFunc(''             , 'dispMain'         );
        $this->registerFunc('seasons'      , 'dispMain'         );
        $this->registerFunc('states'       , 'dispStates'       );
        $this->registerFunc('programs'     , 'dispPrograms'     );
        $this->registerFunc('program_info' , 'dispProgramInfo'  );
        $this->registerFunc('credits'      , 'dispCredits'      );
        $this->registerFunc('settings'     , 'dispSettings'     );
        $this->registerFunc('auto'         , 'dispAuto'         );
        $this->registerFunc('about'        , 'dispAbout'        );
        $this->registerFunc('indev'        , 'dispInDev'        );
    }
    ////////////////////////////////////////////////////////////////////////////
    function registerFunc($key,$method,$args = null){
        $this->regsiteredFunctions[$key] = $method;
        $this->regFunctionsArgs[$key] = $args;
    }
    ////////////////////////////////////////////////////////////////////////////
    function display(){
        // possibly use disp or page instead of mode
        $b = "";
        foreach( $this->regsiteredFunctions as $mode => $method){
            if( $this->mode == $mode ){
                if( method_exists($this,$method)){
                    if( is_callable(array($this,$method))){
                        $b .= call_user_func(array($this,$method));
                    }
                }
            }
        }
        
        $b .= $this->disclaimer();
        
        //$b .= $this->button("http://www.usyvl.org","USYVL Website");
        //$b .= $this->button("./","Main Menu");
        return "$b";
    }
    ////////////////////////////////////////////////////////////////////////////
    // Want to centralize the data collection/validation/storage since the chain
    // is pretty well defined:
    //   season
    //   state
    //   program
    //   division
    //   team
    // 
    // The overall chain leads to the display of a teams schedule for the entire 
    // season. Each step requires all the other pieces.  Of course, the automode 
    // will add the additional dimension of time.
    //
    // Debating on using the singular and plural forms of each chain element.
    // ie: state and states, season and seasons
    ////////////////////////////////////////////////////////////////////////////
    function collectValidateDataChain($next){
        // so, one thing to consider is whether I should try to figure out the 
        // mode from what elements are available...  
        // Interesting idea, but gets trickier if we end up having sequences that 
        // dont fit this chain idea.
        
        // Was toying with a mask type idea though  0x07
        // season 0x01
        // state 0x02
        // program 0x04
        // division 0x08
        
        // thus 0x0f would indicate the presence of all of those, 0x07 only the first three
        // but many of these values rely on the previous value (thus this idea of a chain)
        // But at some point we need to think of time (date) and what where that fits in
        // in the current scheme, we can pull the schedule for one team for that date,
        // but will I want to pull data from multiple sites on a given date?????
        
        $this->chain = array('season','state','program','division','team');
        
        $this->chaindata['seasons'] = $this->sdb->fetchList("distinct evseason from ev");
        $k = 'season';
        if( isset($_GET[$k])){
            $v = $_GET[$k];
        }
        // loop over chain elements, checking $_GET for values and verifying that
        // they exist and are valid
        // At some point in all this though, I need some handlers specific to the
        // data I need to examine validate...
        // Also, would be nice to use prepare for making the queries, would help
        // secure things a bit..
        foreach($this->chain as $chainelement){
            $datakey = $chainelement . "s";
            if( $next == $chainelement ) return $this->chaindata[$datakey];
            if( isset($_GET[$chainelement])) {
                $this->chainval[$chainelement] = $_GET[$chainelement];
                
                // need to validate the data now, hmmmm, we need this to lag by one
                // ie: to validate state data, we need a season value set
                // so we need to save previous loop entry
            }
        }
    }
    ////////////////////////////////////////////////////////////////////////////
    function contentDiv($label = "", $content = ""){
        $b = "";
        $b .= "<div class=\"content\">\n";
        if( $label != "" ) $b .= "<h2 class=\"light\">$label</h2>\n";
        $b .= $content;
        $b .= "</div>\n";
        return "$b";
    }
    ////////////////////////////////////////////////////////////////////////////
    function buildURL($url,$queryargs,$label = "",$li = null ){
        $qa = array();
        if( is_array($queryargs)){
            foreach($queryargs as $qk => $qv){
                $qa[] = "$qk=$qv";
            }
            $qstr = implode("&",$qa);
        }
        else $qstr=$queryargs;
        
        $u = "";
        if( ! is_null( $li)) {
            $u .= "<li";
            if( $li != "" ) $u .= " $li";
            $u .= ">";
        }
        $u .= "<a href=\"$url";
        if( $qstr != "" ) $u .= "?$qstr\"";
        $u .= ">";
        if( $label != "" ) $u .= "$label";
        $u .= "</a>";
        if( ! is_null( $li)) $u .= "</li>";
        return $u;
    }
    // Need to figure out if I want to do more with this: ie set keys and values????
    // look for values in get or session!!!
    ////////////////////////////////////////////////////////////////////////////
    function initArgs($mode = "", $keys = array()){
        if( isset($this->args)) unset($this->args);
        $this->args = array();
        foreach($keys as $key){
            if( isset($_GET[$key])) $this->args[$key] = $_GET[$key];
            else                    $this->args[$key] = "";
        }
        $this->args['mode'] = $mode;
    }
    ////////////////////////////////////////////////////////////////////////////
    function setArg($key,$val){
        $this->args[$key] = $val;
        return $this->args[$key];
    }
    ////////////////////////////////////////////////////////////////////////////
    function getArg($key){
        return $this->args[$key];
    }
    ////////////////////////////////////////////////////////////////////////////
    // Below here, functions should be the functions registered with registerFunc
    ////////////////////////////////////////////////////////////////////////////
    function dispMain(){
        //$this->collectValidateDataChain('season');
        $this->initArgs('states',array('mode','season'));
                
        $m = "";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=about\">About</a></li>\n";
        $seasons = $this->sdb->fetchList("distinct evseason from ev");
        foreach($seasons as $season){
            $this->args['season'] = $season;
            $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"$season Programs","class=\"nonereally\"");
        }
        $m .= "  <li><a href=\"./scorekeeper.php?team_a=Team A&team_b=Team B&tshirt_a=cyan&tshirt_b=yellow\">Score Keeper</a></li>\n";
        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Locator Mode</a></li>\n";
        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=settings\">Settings</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=credits\">Credits</a></li>\n";
        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=indev\">In Development</a></li>\n";

        $b = $this->fMenu("Main Menu",$m);
        return "$b";
    }
    function dispStates(){
        $this->initArgs('programs',array('mode','season'));
        $this->title = "USYVL Mobile - Select State {$this->args['season']}";
        
        //$this->args['mode'] = 'programs';
        
        $m = "";
        $states = $this->sdb->fetchListNew("select distinct lcstate from ev left join lc on ev_lcid = lcid where evseason=?",array($this->args['season']));
        foreach( $states as $state){
            $this->args['state'] = $state;
            $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"$state programs","class=\"nonereally\"");
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=programs&season=$season&state=$state\">$state</a></li>\n";
        }
        
        $b = $this->fMenu("Select State",$m);
        return "$b";
    }
    function dispPrograms(){
        $this->initArgs('program_info',array('mode','season','state'));
        $this->title = "USYVL Mobile - Select Program from {$this->args['state']} for {$this->args['season']}";
        
        $m = "";
        $programs = $this->sdb->fetchListNew("select distinct evprogram from ev left join lc on ev_lcid = lcid where ( lcstate=? and evseason=? )",array($this->args['state'],$this->args['season']));
        foreach( $programs as $program){
            $this->args['mode'] = 'program_info';
            $this->args['program'] = $program;
            $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"$program","class=\"nonereally\"");
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=divisions&season=$season&state=$state&program=$program\">$program</a></li>\n";
        }
        
        $b = $this->fMenu("Select Program",$m);
        return "$b";
    }
    function dispProgramInfo(){
        $this->initArgs('launch',array('mode','season','state','program'));
        $this->title = "USYVL Mobile - {$this->args['state']} Program {$this->args['program']} information for {$this->args['season']}";
        $b = "";
        $program = $this->args['program'];
        $m  = "<p>Descriptions of these various functional displays are below.</p>\n";
        $m .= $this->buildURL("./instSummaries.php",$this->args,"$season Schedule for<br />$program","class=\"nonereally\"");
        $m .= $this->buildURL("./tournSummaries.php",$this->args,"$season Tourn. Summaries","class=\"nonereally\"");
        $m .= $this->buildURL("./gameSummaries.php",$this->args,"$season Game Summaries","class=\"nonereally\"");
        $m .= $this->buildURL("./teamMatches.php",$this->args,"$season Team Matches","class=\"nonereally\"");
        $b .= $this->fMenu("Program Functions for: <br />$program",$m);
        
        $bb  = "<h3>Instructional Summaries</h3>";
        $bb .= "<p>This provides the seasons schedule of Instruction, Games and Tournaments.</p>";
        $bb .= "<h3>Tournament Summaries</h3>";
        $bb .= "<p>This provides direct links to the seasons Tournaments for this program.</p>";
        $bb .= "<h3>Team Matches</h3>";
        $bb .= "<p>The Team Matches display is designed to show you all matches for a single team for the entire season.  ";
        $bb .= "<span class=\"r\">NOTE:</span>  Because of some data collection and structure issues, ";
        $bb .= "Intersite Gamedays are currently displayed with the title for the Home (hosting) programs description as a Home Game. ";
        $bb .= "This problem is being looked into. </p>";
        $b .= $this->contentDiv("Description of Program Functions",$bb);
        
        return $b;
        // Now we split out the various functions
    }
    // Most of this is actually done with javascript/jquery
    function dispAuto(){
        $b = "Location:";
        $b .= "<div id=\"device_location\">NA</div>";
        $b .= "<div id=\"proximal_events\">Proximal Events: NA</div>";
        return $b;
    }
    function dispSettings(){
        $this->title = "USYVL Mobile - Settings";
        
        $b = $this->contentDiv("Settings","<p>\nSettings Coming Soon!\n</p>\n");
        
        return "$b";
    }
    function dispCredits(){
        $this->title = "USYVL Mobile - Credits";
        
        $b = "";
        $b .= $this->contentDiv("Version","<p class=\"credits author\">\nVersion: " . $GLOBALS['version'] . "\n</p>\n");
        $bb .= "<p class=\"credits\">\n";
        $bb .= "HTML 5\n";
        $bb .= "</p>\n";
        $bb .= "<p class=\"credits\">\n";
        $bb .= "CSS 3\n";
        $bb .= "</p>\n";
        $bb .= "<p class=\"credits\">\n";
        $bb .= "Mobile Web Framework (MWF) 1.3\n";
        $bb .= "</p>\n";
        $bb .= "<p class=\"credits\">\n";
        $bb .= "jQuery 1.10.x\n";
        $bb .= "</p>\n";
        $b .= $this->contentDiv("Tech",$bb);
        $bb = "<p class=\"credits author\">\nCreated for USYVL by Aaron Martin\n</p>\n";
        $bb .= "<p class=\"credits author\">A longtime clinician at our Goleta site, Aaron also volunteers time on USYVL IT related efforts.</p>";
        $b .= $this->contentDiv("Author",$bb);
        $b .= $this->contentDiv("Art/Graphics","<p class=\"credits\">\nProvided by USYVL</p>\n");
        return "$b";
    }
    function dispAbout(){
        $b = "";
        $bb = "<p>The United States Youth Volleyball League's mission is to provide ";
        $bb .= "every child between the ages of 7 and 15 a chance to learn and play ";
        $bb .= "volleyball in a fun, safe, supervised environment. One of the main ";
        $bb .= "tenets of the program is to encourage children to do their best with ";
        $bb .= "their abilities. With an emphasis on positive reinforcement, the program ";
        $bb .= "seeks to build confidence and self-esteem in each child.</p>";
        $b .= $this->contentDiv("Our Mission",$bb);
        
        $bb = "<h3 class=\"subhead\">Fun</h3><p>While the program teaches children the skills necessary to excel in the  ";
        $bb .= "sport of volleyball, the focus remains on participation, cooperation,  ";
        $bb .= "sportsmanship, responsibility and, of course, fun!</p>";
        
        $bb .= "<h3 class=\"subhead\">Action</h3><p>Strength, endurance and improved coordination result from the weekly  ";
        $bb .= "activity of the program. Furthermore, a season of league games is sure  ";
        $bb .= "to provide plenty of exciting volleyball action.</p>";
        
        $bb .= "<h3 class=\"subhead\">Skills</h3><p>Skills are taught using technically correct methods for peak performance and safety.";
        
        $bb .= "<h3 class=\"subhead\">Teamwork</h3><p>The USYVL is a family-oriented program where parents, siblings, grandparents, ";
        $bb .= "and friends are strongly encouraged to become involved. Parents and volunteers ";
        $bb .= "assist with coaching, registration, check-in and equipment set-up.</p>";
        $b .= $this->contentDiv("What will your child get out of USYVL?",$bb);
        
        
        //$b .= $this->contentDiv("About USYVL",$bb);

        return $b;
    }
    function dispInDev(){
        $b = "";
        $bb = "<p>The links on this page are to items under development</p>";
        $b .= $this->contentDiv("In Development",$bb);
        
                
        $m = "";
        //$m .= "  <li><a href=\"./scorekeeper.php?team_a=Team A&team_b=Team B&tshirt_a=cyan&tshirt_b=yellow\">Score Keeper</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Locator Mode</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=settings\">Settings</a></li>\n";

        $b = $this->fMenu("In Dev Menu",$m);
        return "$b";
    }
}

?>
