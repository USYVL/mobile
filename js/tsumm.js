$(document).ready(function () {
    // Function code here.
    //alert("page loaded!")
});

// if (! $()) {
//     //alert("Hi There");
//     // ideally this should agree with the version used in the tpl file
//     // document.write('<script src="js/jquery-1.10.2.min.js"><\/script>');
//     document.write('<script src="//code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>');
// }


// This should get called on ANY and ALL events .... right???
$(function() {
    // this works, so getting into the main
    // alert("getting into tsumm.js main");

    $('.ajax_tsumm').click(function(){
        // working now, had wrong name
        //alert("is this getting caught?");

        url = $(this).attr('href');
        result = $(this).attr('ajax_result');

        // make ajax call
        $.get(url,function(data){
            $(result).html(data);
        },'html');


        //alert(result);
        // return true for time being to allow the current link to succeed.
        return false;
    });
});
