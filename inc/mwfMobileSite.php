<?php
class mwfMobileSite {
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
    function getTitle(){
        return $this->title;
    }
    function disclaimer(){
        $bb  = "";
        $bb .= "<p>\n";
        $bb .= "This is a very preliminary version of the mobile site";
        $bb .= "designed to allow mobile access to schedules during the course of the season.\n";
        $bb .= "It is still very much under development.\n";
        $bb .= "</p>\n";
        $b = $this->contentDiv("Disclaimer",$bb);
        return $b;
    }
    function processGET(){
        if( isset($_GET['mode'])) $this->mode = $_GET['mode'];
    }
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
    function button($href = "", $label = ""){
        $b = "";
        $b .= "<a href=\"" . $href . "\" class=\"button button-padded\">$label</a>\n";
        return $b;
    }
    // This is meant to be overloaded with functions particular to child classes
    function registerCoreFunctions(){
        $this->registerFunc(''         , 'dispMain'      );
        $this->registerFunc('seasons'  , 'dispMain'      );
        $this->registerFunc('states'   , 'dispStates'    );
        $this->registerFunc('programs' , 'dispPrograms'  );
    }
    function registerFunc($key,$method,$args = null){
        $this->regsiteredFunctions[$key] = $method;
        $this->regFunctionsArgs[$key] = $args;
    }
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
        
        $b .= $this->button("http://www.usyvl.org","USYVL Website");
        $b .= $this->button("./","Main Menu");
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
    function contentDiv($label = "", $content = ""){
        $b = "";
        $b .= "<div class=\"content\">\n";
        if( $label != "" ) $b .= "<h2 class=\"light\">$label</h2>\n";
        $b .= $content;
        $b .= "</div>\n";
        return "$b";
    }
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
    function initArgs($mode = "", $keys = array()){
        if( isset($this->args)) unset($this->args);
        $this->args = array();
        foreach($keys as $key){
            if( isset($_GET[$key])) $this->args[$key] = $_GET[$key];
            else                    $this->args[$key] = "";
        }
        $this->args['mode'] = $mode;
    }
    function setArg($key,$val){
        $this->args[$key] = $val;
        return $this->args[$key];
    }
    function getArg($key){
        return $this->args[$key];
    }
    ////////////////////////////////////////////////////////////////////////////
    // Below here, functions should be the functions registered with registerFunc
    ////////////////////////////////////////////////////////////////////////////
    function dispMain(){
        $this->collectValidateDataChain('season');
        $seasons = $this->sdb->fetchList("distinct evseason from ev");
        $this->initArgs('states',array('mode','season'));
                
        $m = "";
        foreach($seasons as $season){
            $this->args['season'] = $season;
            $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"$season Event Schedules","class=\"nonereally\"");
            $m .= $this->buildURL("./instSummaries.php",$this->args,"$season Inst. Summaries","class=\"nonereally\"");
            $m .= $this->buildURL("./tournSummaries.php",$this->args,"$season Tourn. Summaries","class=\"nonereally\"");
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=states&season=$season\">$season Event Schedules</a></li>\n";
            //$m .= "  <li><a href=\"./instSummaries.php?mode=states&season=$season\">$season Inst. Summaries</a></li>\n";
            //$m .= "  <li><a href=\"./tournSummaries.php?mode=states&season=$season\">$season Tourn. Summaries</a></li>\n";
        }
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Auto Mode</a></li>\n";
        $m .= "  <li><a href=\"./scorekeeper.php?team_a=Team C&team_b=Team D\">Score Keeper</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=settings\">Settings</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=credits\">Credits</a></li>\n";

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
        $this->initArgs('divisions',array('mode','season','state'));
        $this->title = "USYVL Mobile - Select Program from {$this->args['state']} for {$this->args['season']}";

        //$this->args['mode'] = 'divisions';
        
        $m = "";
        //$programs = $this->sdb->fetchListNew("distinct evprogram from ev left join lc on ev_lcid = lcid ","( lcstate='$state' and evseason='$season' )",array($state,$season));
        $programs = $this->sdb->fetchListNew("select distinct evprogram from ev left join lc on ev_lcid = lcid where ( lcstate=? and evseason=? )",array($this->args['state'],$this->args['season']));
        foreach( $programs as $program){
            $this->args['program'] = $program;
            $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"$program","class=\"nonereally\"");
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=divisions&season=$season&state=$state&program=$program\">$program</a></li>\n";
        }
        
        $b = $this->fMenu("Select Program",$m);
        return "$b";
    }
}

?>
