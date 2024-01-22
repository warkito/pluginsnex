
//Bind events to the page
jQuery(document).ready(function (jQuery) {
   jQuery( "span.schedules-status" ).each(function( index ) {
        var $node = jQuery(this);
        var center_id = $node.data('center_id');
        console.log('#######################');
        console.log(center_id);
        console.log('#######################');
        if( center_id !="" || typeof center_id != "undefined"){
            set_schedules_content(center_id,$node)
        }
    });
});
function  set_schedules_content(center_id,$node){

    var data={};
    data['center_id'] = center_id;
    data['action'] = "set_schedules_content";
    data['nonce'] = beautyCenter.security;
    jQuery.ajax({
        url: beautyCenter.ajax_url,
        data: data,
        type: 'post',
        success: function (html) {
            //$node.html("");
            $node.replaceWith(html);

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
            jQuery(".load-img").css("display", "none");
            jQuery(".overlay-beauty-center").css("display", "none");
        }
    });
}
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
jQuery(document).ready(function() {
    jQuery(".search-options-btn").click(function(){
        jQuery(".beauty-center-search-fields").slideToggle();
    });
});
