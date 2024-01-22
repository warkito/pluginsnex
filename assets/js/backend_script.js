//Bind events to the page
function copytoclipboard() {
        //var me =jQuery(this);
        var shortcode = jQuery('.map_shortcode_callback').find('p input');
        shortcode[0].select();
        document.execCommand("Copy");
       // console.log(shortcode);
    }
//Bind events to the page
jQuery(document).ready(function (jQuery) {

	if(beauty_centers_json_encoded  != null){
		jQuery('#tolalcount').text(beauty_centers_json_encoded.length);
	}
    jQuery('#beauty_center_country').change(function () {
        if (jQuery('#beauty_center_country').val() == 'United States') {
            jQuery('#beauty_center_state').parents('tr').show();
        }
        else {
            jQuery('#beauty_center_state').parents('tr').hide();
            jQuery('#beauty_center_state').val('');
        }
    });
    jQuery('#beauty_center_map_layout').change(function () {
        var me =jQuery(this);
        if(me.val()=='regular'){
            jQuery(document).find('#wpbody .postbox').addClass('regularMapadmin');
        }
        else{
             jQuery(document).find('#wpbody .postbox').removeClass('regularMapadmin');
        }
         if(me.val()=='fullscreen'){
            jQuery(document).find('#wpbody .postbox').addClass('fullscreenMapadmin');
        }
        else{
             jQuery(document).find('#wpbody .postbox').removeClass('fullscreenMapadmin');
        }
    });
    jQuery('.settings_switcher_wpmsl').on('click',function () {
        var me =jQuery(this);
        if(me.attr('checked')=='checked'){
            me.closest('.global_settings_switcher').next('div').fadeOut();
        }
        else{
             me.closest('.global_settings_switcher').next('div').fadeIn();
        }
    });
    // bind onchange event on selecting open/close store days
    jQuery('#beauty_center_hours input[type="radio"]').change(function (e) {
        if (jQuery(this).val() == '1') {
            var start_elem = jQuery(this).attr('name').replace('[status]', '[start]');
            var end_elem = jQuery(this).attr('name').replace('[status]', '[end]');
            jQuery('[name="'+start_elem+'"]').show();
            jQuery('[name="'+end_elem+'"]').show();
        }
        else {
            var start_elem = jQuery(this).attr('name').replace('[status]', '[start]');
            var end_elem = jQuery(this).attr('name').replace('[status]', '[end]');
            jQuery('[name="'+start_elem+'"]').val('');
            jQuery('[name="'+start_elem+'"]').hide();
            jQuery('[name="'+end_elem+'"]').val('');
            jQuery('[name="'+end_elem+'"]').hide();
        }
    });

    jQuery('#beauty_center_sales').select2();
    jQuery('#beauty_center_grid_columns').select2();
    jQuery("#beauty_center_grid_columns").on('change', function(){
        var data = jQuery(this).select2('data');
        var array = [];
        jQuery.each(data, function(index, val) {
            array[index]=val.id;
        });
        jQuery("input[name='beauty_center_grid[columns]']").val( array );
    });
    jQuery('#beauty_center_single_items').select2();
    jQuery("#beauty_center_single_items").on('change', function(){
        var data = jQuery(this).select2('data');
        var array = [];
        jQuery.each(data, function(index, val) {
            array[index]=val.id;
        });
        jQuery("input[name='beauty_center_single[items]']").val( array );
    });



	//import store via ajax
	jQuery( ".import_js_beauty_center" ).click(function(e) {

		jQuery('.irc_mi_wp_str').fadeIn();
  if ( jQuery('.import_js_beauty_center').attr('disabled') != "disabled" ) {
	  jQuery('.import_js_beauty_center').attr("disabled","disabled");
		if(beauty_centers_json_encoded.length > 0){
				var i = 0;
				var del = 0;
				var totalTime = 1000;
				setInterval(function(){
							var ajaxTime= new Date().getTime();

							if(beauty_centers_json_encoded[i] != null){
							jQuery.ajax({
								type: "POST",
								url: ajax_url,
								data: 'action=import_js_beauty_center_action&import_js_beauty_center_post=' + JSON.stringify(beauty_centers_json_encoded[i]),
							}).always(function(html) {
									var totalTime = new Date().getTime()-ajaxTime;
									var htmls = jQuery.parseJSON( html );
									jQuery('.beauty_center_id_'+beauty_centers_json_encoded[del].Code).css('background-color','#dff0d8');
									setTimeout(function(){
										// jQuery('.beauty_center_id_'+beauty_centers_json_encoded[del].Code).css('text-decoration','line-through');
										jQuery('.beauty_center_id_'+beauty_centers_json_encoded[del].Code +' + br').remove();
										jQuery('.beauty_center_id_'+beauty_centers_json_encoded[del].Code).fadeOut('slow');
										jQuery('#current-count').text(del);
										del++;
									}, 500);
							  });
							 i++;
							} else {
								jQuery('.irc_mi_wp_str').fadeOut();
								jQuery('.import_js_beauty_center').removeAttr("disabled","disabled");
								 totalTime++;
							}
				}, totalTime+500);

			// }
}}

	});





});

    function  initializeMapBackend() {
        jQuery('#map_loader').hide();
        // Handle google maps
        var oldMarker;
        var updateMapDuration;
        var mapOptions = {
            scrollwheel: false,
            zoom: 13,
            center: new google.maps.LatLng(1, 1)
        };

        //display default address on map
        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        setTimeout(function () {

            if (jQuery('#beauty_center_lat').val() && jQuery('#beauty_center_lng').val()) {
                var currentLatLng = new google.maps.LatLng(parseFloat(jQuery('#beauty_center_lat').val()), parseFloat(jQuery('#beauty_center_lng').val()));
                marker = new google.maps.Marker({
                    position: currentLatLng,
                    map: map
                });
                oldMarker = marker;
                map.setCenter(currentLatLng);
            } else {
                var addressString = jQuery('#beauty_center_address').val();
                addressString     = (jQuery('#beauty_center_city').val()) ?(addressString+" "+ jQuery('#beauty_center_city').val()):addressString;
                addressString     = (jQuery('#beauty_center_state').val()) ?(addressString+", "+ jQuery('#beauty_center_state').val()):addressString;
                addressString     = (jQuery('#beauty_center_country').val()) ?(addressString+" "+ jQuery('#beauty_center_country').val()):addressString;
                addressString     = (jQuery('#beauty_center_zipcode').val()) ?(addressString+" "+ jQuery('#beauty_center_zipcode').val()):addressString;

                var address = (addressString) ? addressString : "United State";
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        jQuery('#beauty_center_lat').val(results[0].geometry.location.lat());
                        jQuery('#beauty_center_lng').val(results[0].geometry.location.lng());
                        var currentLatLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                        marker = new google.maps.Marker({
                            position: currentLatLng,
                            map: map
                        });
                        oldMarker = marker;
                        map.setCenter(currentLatLng);
                    }
                });
            }
        }, 1000);
        // move marker when click on map
        google.maps.event.addListener(map, "click", function (event) {
            marker = new google.maps.Marker({
                position: event.latLng,
                map: map
            });

            if (oldMarker != undefined)
                oldMarker.setMap(null);

            oldMarker = marker;
            jQuery('#beauty_center_lat').val(event.latLng.lat());
            jQuery('#beauty_center_lng').val(event.latLng.lng());
        });


        jQuery('#beauty_center_address, #beauty_center_city, #beauty_center_zipcode, #beauty_center_country, #beauty_center_state').change(function () {
//        jQuery(document).on("change keypress", '#beauty_center_address, #beauty_center_city, #beauty_center_zipcode, #beauty_center_country, #beauty_center_state'), function () {

            clearTimeout(updateMapDuration);
            jQuery('#map_loader').show();
            updateMapDuration = setTimeout(function () {
                var addressString = jQuery('#beauty_center_address').val();
                addressString     = (jQuery('#beauty_center_city').val()) ?(addressString+" "+ jQuery('#beauty_center_city').val()):addressString;
                addressString     = (jQuery('#beauty_center_state').val()) ?(addressString+", "+ jQuery('#beauty_center_state').val()):addressString;
                addressString     = (jQuery('#beauty_center_country').val()) ?(addressString+" "+ jQuery('#beauty_center_country').val()):addressString;
                addressString     = (jQuery('#beauty_center_zipcode').val()) ?(addressString+" "+ jQuery('#beauty_center_zipcode').val()):addressString;
                var address = (addressString) ? addressString : "United State";
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        jQuery('#map_loader').hide();
                        jQuery('#beauty_center_lat').val(results[0].geometry.location.lat());
                        jQuery('#beauty_center_lng').val(results[0].geometry.location.lng());
                        var currentLatLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                        marker = new google.maps.Marker({
                            position: currentLatLng,
                            map: map
                        });
                        if (oldMarker != undefined)
                            oldMarker.setMap(null);

                        oldMarker = marker;
                        map.setCenter(currentLatLng);
                    }
                });
            }, 1000);
        });
    }


jQuery(document).ready(function (jQuery) {
    var custom_uploader;
    var caller;
    jQuery('.upload_image_button').click(function (e) {
        e.preventDefault();
        caller = jQuery(this);
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            caller.prev('input').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();
    });
});

jQuery(document).ready(function($){
    jQuery(document).on('click','.wpmsl_custom_marker_upload',function(e) {
             e.preventDefault();
         var placeInstance = jQuery(this);
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // jQuery("#variation_remove_id_"+id[2]).addClass("remove_variation_img");
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            // var image_url = uploaded_image.toJSON().url;
            //Let's assign the url value to the input field
            placeInstance.find('img').attr("src",uploaded_image.attributes.url);
            placeInstance.find('input[type="hidden"]').attr("value",uploaded_image.attributes.url);
            placeInstance.find('p').html('Remove');
            placeInstance.addClass('wpmsl_custom_marker');
            placeInstance.removeClass('wpmsl_custom_marker_upload');
           // jQuery(this).find('img').attr("src",uploaded_image.attributes.url);

        });
    });
    jQuery(document).on('click','.wpmsl_custom_marker',function(e) {
        e.preventDefault();
         var placeInstance = jQuery(this);

         placeInstance.find('img').attr("src",wpmsl_url+"assets/img/upload.png");
         placeInstance.find('input[type="hidden"]').attr("value","");
         placeInstance.find('p').html('Upload');
         placeInstance.addClass('wpmsl_custom_marker_upload');
         placeInstance.removeClass('wpmsl_custom_marker');
    });
});
