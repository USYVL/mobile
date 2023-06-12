<?php
require_once("mwfMobileSiteClass.php");

class indexMobile extends mwfMobileSite {
    function __construct(){
        parent::__construct();
    }
    function registerExtendedFunctions(){
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
        $this->registerFunc('rules'        , 'dispRules'        );
        $this->registerFunc('diag'         , 'dispDiagram'      );
    }
    ////////////////////////////////////////////////////////////////////////////
    // Below here, functions should be the functions registered with registerFunc
    ////////////////////////////////////////////////////////////////////////
    function dispMain(){
        //$this->collectValidateDataChain('season');
        $this->initArgs('states',array('mode','season'));

        $m = "";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=about\">About</a></li>\n";
        $seasons = $this->sdb->fetchList("distinct evseason from ev");
        foreach($seasons as $season){
            $this->args['season'] = $season;
            $m .= $this->buildURL_li($_SERVER['PHP_SELF'],$this->args,"$season Programs","class=\"nonereally\"");
        }
        $m .= "  <li><a href=\"./scorekeeper.php?team_a=Team A&team_b=Team B&tshirt_a=cyan&tshirt_b=yellow\">Score Keeper</a></li>\n";
        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Locator Mode</a></li>\n";
        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=settings\">Settings</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=credits\">Credits</a></li>\n";
        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=indev\">In Development</a></li>\n";

        $b = $this->contentList("Main Menu",$m);
        return "$b";
    }
    function dispStates(){
        $this->initArgs('programs',array('mode','season'));
        $this->title = "USYVL Mobile - Select State {$this->args['season']}";

        //$this->args['mode'] = 'programs';

        $m = "";
        $states = $this->sdb->fetchListNew("SELECT DISTINCT lcstate FROM ev LEFT JOIN lc ON ev_lcid = lcid WHERE evseason=? ORDER BY lcstate",array($this->args['season']));
        foreach( $states as $state){
            $this->args['state'] = $state;
            $m .= $this->buildURL_li($_SERVER['PHP_SELF'],$this->args,"$state programs","class=\"nonereally\"");
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=programs&season=$season&state=$state\">$state</a></li>\n";
        }

        $b = $this->contentList("Select State",$m);
        return "$b";
    }
    function dispPrograms(){
        $this->initArgs('program_info',array('mode','season','state'));
        $this->title = "USYVL Mobile - Select Program from {$this->args['state']} for {$this->args['season']}";

        $m = "";
        $programs = $this->sdb->fetchListNew("SELECT DISTINCT evprogram FROM ev LEFT JOIN lc ON ev_lcid = lcid WHERE ( lcstate=? AND evseason=? ) ORDER BY evprogram",array($this->args['state'],$this->args['season']));
        foreach( $programs as $program){
            $this->args['mode'] = 'program_info';
            $this->args['program'] = $program;
            $m .= $this->buildURL_li($_SERVER['PHP_SELF'],$this->args,"$program","class=\"nonereally\"");
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=divisions&season=$season&state=$state&program=$program\">$program</a></li>\n";
        }

        $b = $this->contentList("Select Program",$m);
        return "$b";
    }
    function dispProgramInfo(){
        $this->initArgs('launch',array('mode','season','state','program'));
        $this->title = "USYVL Mobile - {$this->args['state']} Program {$this->args['program']} information for {$this->args['season']}";
        $b = "";
        $program = $this->args['program'];

        $bb = $this->buildURL_wrapped("./instSummaries.php",$this->args,"Daily Schedules",'li');
        $b .= $this->contentList("$program",$bb);

        $ev_refids = $this->sdb->fetchListNew("SELECT ev_refid FROM ev WHERE evprogram=? AND evistype=?",array($this->args['program'],'GAME'));
        $this->setArg('ev_refid',$ev_refids[0]);
        //print "{$this->args['ev_refid']}<br>\n";
        $b .= $this->addPDFMaterialsLinks(array('INSTRUCT','GAMES','RULES'));

        $m = "";
        //$m  = "<p class=\"content nopadding\">Day to Day schedule.</p>\n";
        //$m .= $this->buildURL_li("./instSummaries.php",$this->args,"$season Schedule for<br />$program","class=\"nonereally\"");

        $m .= $this->buildURL_wrapped("./gameSummaries.php"  ,$this->args,"Games"       ,"li");
        $m .= $this->buildURL_wrapped("./tournSummaries.php" ,$this->args,"Tournaments" ,"li");
        $m .= $this->buildURL_wrapped("./teamMatches.php"    ,$this->args,"Team Matches","li");
        $b .= $this->contentList("Alternate Schedule Listings for<br />$program",$m);

        $bb  = "<h3>Daily Schedules</h3>";
        $bb .= "<p>This provides the seasons day to day schedule of Instruction, Games and Tournaments.</p>";
        $bb .= "<h3>Game Summaries</h3>";
        $bb .= "<p>This provides direct links to the seasons Game days for this program.</p>";
        $bb .= "<h3>Tournament Summaries</h3>";
        $bb .= "<p>This provides direct links to the seasons Tournaments for this program.</p>";
        $bb .= "<h3>Team Matches</h3>";
        $bb .= "<p>The Team Matches display is designed to display all matches for a single team for the entire season.  ";
        $bb .= "<span class=\"r\">NOTE:</span>  Because of some data collection and structure issues, ";
        $bb .= "Intersite Gamedays are currently displayed with the title for the Home (hosting) programs description as a Home Game. ";
        $bb .= "This problem is being looked into. </p>";
        $b .= $this->contentDiv("Description of Program Listings",$bb);

        //$b .= "<div class=\"content\">";
        //$b .= "<h2>Block Title</h2>";
        //$b .= "<div class=\"button\">";
        //$b .= "<a href=\"#\">";
        //$b .= "<div class=\"label\">Label</div>\n";
        //$b .= "</a>";
        //$b .= "</div>";
        //
        //$b .= "<div class=\"button\">";
        //$b .= "<a href=\"#\">Label";
        //$b .= "</a>";
        //$b .= "</div>";

        /////$b .= "<div class=\"button not-padded light\">";
        ///$b .= "<a class=\"button not-padded light\" href=\"#\">Label";
        ///$b .= "</a>";
        /////$b .= "</div>";

        //$b .= "<div class=\"button not-padded light\">";
        //$b .= "<div class=\"button no-padding light\">";
        //$b .= "<a href=\"#\">Label</a>";
        //$b .= "</div>";


        //$b .= "</div>";

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
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=rules\">Rules</a></li>\n";
        $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=diag\">Diagram</a></li>\n";
        $m .= "  <li><a href=\"./testing/testLocator.php\">Locator Testing</a></li>\n";

        $b .= $this->contentList("In Dev Menu",$m);
        return "$b";
    }
    function dispRules(){
        $b = "";
        $bb  = "<p>The following describes the game rules for USYVL matches.</p>";
        $bb .= "<h3>Important Reminders:</h3>";
        $bb .= "<p>Net heights indicated on the pool sheets must be followed!</p>";
        $bb .= "<p>7-8 & 9-10 div. court lines must be tied in 2 1/2 feet at each corner!</p>";
        $b .= $this->contentDiv("USYVL Game Rules",$bb);

        $bb  = "<li>One player from each team will roll the ball from one sideline to the other.  ";
        $bb .= "<li>Closest one to the line will choose the serve or the side.  ";
        $bb .= "<li>The ball can go past the line.</li>";
        $b .= $this->contentDiv("The Ball Roll<br /><span class=\"header_sm\">(used to determine which team serves)</span>",$bb);

        $bb  = "";
        $bb .= "<li>Serving from inside the end line, but no further than the middle of the court is permitted for the underhand serve only.";
        $bb .= "<li>The ball may not hit the floor during play.";
        $bb .= "<li>The Let Serve is legal.";
        $b .= $this->contentDiv("Common Rules<br /><span class=\"header_sm\">(For all age divisions)</span>",$bb);

        $bb  = "<li>Every first contact should be a catch.";
        $bb .= "<li>Every first ball that crosses the net should be caught in the traditional passing stance.";
        $bb .= "<li>The second contact should be a set (the set cannot be caught).";
        $bb .= "<li>The third contact should be a spike (the spike cannot be caught).";
        $bb .= "<li>Each player gets two consecutive serves.  Regardless of which team won the rally, the serve turns over to the opposing team.";
        $bb .= "<li>Score is not kept.";
        $bb .= "";
        $b .= $this->contentDiv("Hot Potato<br /><span class=\"header_sm\">(For 7-8 age division only)</span>",$bb);

        $bb  = "<li>Traditional pass, set and spike.";
        $bb .= "<li>Modified Rally scoring to 25.";
        $bb .= "<li>Team must win by 2 points.";
        $bb .= "<li>Players have a two consecutive serve maximum.  Team, whose player is serving, has to win the rally to earn the second serve.  After the second serve, regardless of which team won the rally, the serve turns over to the opposing team.";
        $bb .= "";
        $bb .= "<li>If a game to 25 is complete before the whistle is blown, switch sides and begin another game until the
long whistle ends the game.";
        $b .= $this->contentDiv("Double Max<br /><span class=\"header_sm\">(For 9-10, 11-12 & 13-15 age divisions only)</span>",$bb);


        $bb  = "<p class=\"glossary\"><span class=\"glossary_term\">Let Serve:  </span><span class=\"glossary_definition\">A serve that hits the net when the ball is put in play and lands in the receiving team's court.</span></p>";
        $bb .= "<p class=\"glossary\"><span class=\"glossary_term\">Rally Scoring:  </span><span class=\"glossary_definition\">A point is awarded on every rally; the team that wins the rally earns the point.</span></p>";
        $bb .= "";
        $bb .= "";
        $bb .= "";
        $b .= $this->contentDiv("Glossary",$bb);

        return "$b";
    }
    function dispDiagram(){
        $b = "";
        //$b .= "<canvas id=\"myCanvas\" width=\"200\" height=\"200\" class=\"myCanvasClass\"></canvas>";
        //$b .= "<script>\n";
        //$b .= "var c=document.getElementById(\"myCanvas\");\n";
        //$b .= "var ctx=c.getContext(\"2d\");\n";
        //$b .= "ctx.moveTo(0,0);\n";
        //$b .= "ctx.lineTo(200,100);\n";
        //$b .= "ctx.stroke();\n";
        //$b .= "</script>\n";


        $b .= "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\">\n";
        //$b .= "  <g transform=\"translate(10,10)\">\n";
        $b .= "<rect  x=\"20\" y=\"10\" width=\"200\" height=\"200\" stroke=\"black\" stroke-width=\"1\" fill=\"none\" />\n";
        $b .= "<rect  x=\"20\" y=\"210\" width=\"200\" height=\"200\" stroke=\"black\" stroke-width=\"1\" fill=\"none\" />\n";
        $b .= "<circle id=\myca\" cx=\"10\" cy=\"190\" r=\"10\" stroke=\"black\" stroke-width=\"2\" fill=\"white\" >\n";
        $b .= "      <animateMotion  path=\"m 0 0 l 170 0\" dur=\"5s\" fill=\"freeze\" />\n";
        $b .= "</circle>\n";
        //$b .= "    <text id=\"TextElement\" x=\"0\" y=\"0\" style=\"font-family:Verdana;font-size:24\"> It's SVG!\n";
        //$b .= "      <animateMotion path=\"M 100 100 L 0 0\" dur=\"5s\" fill=\"freeze\" />\n";
        //$b .= "    </text>\n";
        $b .= "<circle id=\mycb\" cx=\"10\" cy=\"230\" r=\"10\" stroke=\"black\" stroke-width=\"2\" fill=\"white\" >\n";
        $b .= "      <animateMotion  path=\"m 0 0 l 230 0 \" dur=\"5s\" fill=\"freeze\" />\n";
        //$b .= "      <animateMotion  path=\"m 0 0 l 360 0 \" dur=\"5s\" fill=\"freeze\" begin=\"mycb.end\" />\n";
        $b .= "</circle>\n";
        //$b .= "  </g> \n";
        $b .= "</svg> \n";


        return "$b";
    }
}
?>
