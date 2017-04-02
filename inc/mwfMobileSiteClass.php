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
    function registerExtendedFunctions(){
        // This is designed to be overloaded by child classes
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
    function contentList($label = "",$menuitems = ""){
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

        $this->chaindata['seasons'] = $this->sdb->fetchList("DISTINCT evseason FROM ev ORDER BY evseason");
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
    // Still have to build this out, but want to build the url with this
    function buildURL_base($atag,$queryargs,$label = ""){
        $qa = array();
        if( is_array($queryargs)){
            foreach($queryargs as $qk => $qv){
                $qa[] = "$qk=$qv";
            }
            $qstr = implode("&",$qa);
        }
        else $qstr=$queryargs;

        $u = "\n";
        $u .= "<a ";

        // set up a hash with any optional entries and then append...

        // so exargs['href'] = "?arg=val&arg=val"

        if( is_array($atag)){
            if( isset($atag['ajax_result'])) $u .= " ajax_result=\"" . $atag['ajax_result'] . "\"";
            if( isset($atag['class'])) $u .= " class=\"" . $atag['class'] . "\"";
            if( isset($atag['href'])) $u .= " href=\"" . $atag['href'] ;
        }
        else {
            $u .= "href=\"" . $atag;
        }

        if( $qstr != "" ) $u .= "?$qstr";
        $u .= "\">";
        if( $label != "" ) $u .= "$label";
        $u .= "</a>\n";

        return $u;
    }
    ////////////////////////////////////////////////////////////////////////////
    function buildURL_li($url,$queryargs,$label = "",$li = null ,$classes = ""){
        $u = "";
        if( ! is_null( $li)) {
            $u .= "<li";
            $u .= ( $classes == "" ) ? "" : " class=\"$classes\"";
            if( $li != "" ) $u .= " $li";
            $u .= ">";
        }

        $u .= $this->buildURL_base($url,$queryargs,$label);

        if( ! is_null( $li)) $u .= "</li>\n";
        return $u;
    }
    ////////////////////////////////////////////////////////////////////////////
    function buildURL_wrapped($url,$queryargs,$label = "",$wrapper = null ,$attrs = ""){
        $u = "";
        if( ! is_null( $wrapper )) {
            $u .= "<$wrapper";
            $u .= ( $attrs == "" ) ? "" : " $attrs";
            //if( $li != "" ) $u .= " $li";
            $u .= ">";
        }

        $u .= $this->buildURL_base($url,$queryargs,$label);

        if( ! is_null( $wrapper)) $u .= "</$wrapper>";
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
    function buildPDFMaterialsLink($refid,$cat,$label){
        $b = '';
        $pdid = $this->sdb->fetchVal("pdid from pdfs","pdf_refid = ? and pdfcat = ?",array($refid,$cat));
        if ($pdid != ""){
            $b .= "<li class=\"nonereally\"><a href=\"displayPDF.php?pdid=$pdid\">$label</a></li>\n";
        }
        return $b;
    }
    ////////////////////////////////////////////////////////////////////////////
    function addPDFMaterialsLinks($pdfMaterialKeys){
        $b = '';

        foreach($pdfMaterialKeys as $pdfMaterialKey){
            if ( $pdfMaterialKey == 'INSTRUCT'){
                $b .= $this->buildPDFMaterialsLink($this->args['ev_refid'],$pdfMaterialKey,'Instructional Summary PDF');
            }
            elseif($pdfMaterialKey == 'GAMES'){
                $b .= $this->buildPDFMaterialsLink($this->args['ev_refid'],$pdfMaterialKey,'Games PDF');
            }
            elseif($pdfMaterialKey == 'INTERSITE'){
                $b .= $this->buildPDFMaterialsLink($this->args['ev_refid'],$pdfMaterialKey,'Tournament PDF');
            }
            elseif($pdfMaterialKey == 'RULES'){
                $b .= $this->buildPDFMaterialsLink(0,$pdfMaterialKey,'Rules PDF');
                //$pdfid = $this->sdb->fetchVal("pdid from pdfs","pdfcat = 'RULES';");
                //if ($pdfid != ""){
                //    $b .= "<li class=\"nonereally\"><a href=\"displayPDF.php?pdid=$pdfid\">Rules PDF</a></li>\n";
                //}
            }
        }
        return $this->contentList("PDF Materials Links",$b);
        //return $this->contentList("PDF Materials Links ({$this->args['ev_refid']})",$b);
    }
}

?>
