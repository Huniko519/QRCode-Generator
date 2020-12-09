/*!
 * QRCDR setup
 */
// Print QRCODE
var win = null;
function printIt(printThis) {

	var infopanel = "";

	// EDIT to print data
	/*
	var thisdata = $("#create").find(".tab-pane.active :input").filter(function(index, element) {
			return $(element).val() != "";
		}).serializeArray();

	var formData = JSON.stringify(thisdata);
	var dede = $.parseJSON( formData );

	$('.infopanel').html("");

	$.each(dede, function(i, item) {
		var dato = item.name + ": " + item.value;
	    infopanel += "<br>" + dato;
	});
	*/
	// EDIT to print data END

	var title = document.title;
	var img = $(printThis).find('img').attr('src');
	var content = '<html><head><title>'+title+'</title></head><body><img src="'+img+'"/>'+infopanel+'<body></html>';
	win = window.open();
	self.focus();
	win.document.open();
	win.document.write(content);
	win.document.close();

	win.onload = function (){
		win.print();
		win.close();
	}
}

function initialize() {

	if ( $( "#map-canvas" ).length ) {
		// Google MAP
		var start = new google.maps.LatLng(40.7127837, -74.00594130000002);
		var marker;
		var map;
		var input = (document.getElementById('pac-input'));
		var getdata = (document.getElementById('latlong'));
		var latbox = document.getElementById('latbox');
		var lngbox = document.getElementById('lngbox');

		var searchBox;

	    var mapOptions = {
	        zoom: 10,
	        center: start
	    };

	    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	    searchBox = new google.maps.places.SearchBox((input));
	    marker = new google.maps.Marker({
	        map:map,
	        draggable:true,
	        animation: google.maps.Animation.DROP,
	        position: start
	    });

	    google.maps.event.addListener(marker, 'dragend', function(event) {
	        var latlang = marker.getPosition().lat()+","+marker.getPosition().lng();
	        updateposition(latlang);
	    });

	    map.controls[google.maps.ControlPosition.TOP_LEFT].push(getdata);

	    if ((latbox.value.length > 0 ) && (lngbox.value.length > 0)) {
	        setfirst(Number(latbox.value), Number(lngbox.value));
	    }

	    google.maps.event.addListener(searchBox, 'places_changed', function() {
	        var places = searchBox.getPlaces();

	        if (places.length == 0) {
	          return;
	        }

	        for (var i = 0, place; place = places[i]; i++) {
	            marker.setPosition(place.geometry.location);
	            map.setCenter(place.geometry.location);
	            updateposition();
	        }
	    });
	}

	function updateposition(){
	    latbox.value = marker.getPosition().lat();
	    lngbox.value = marker.getPosition().lng();
	}

	function setfirst(latvar, lngvar){
	    map.setCenter({lat: latvar, lng: lngvar});
	    marker.setPosition({lat: latvar, lng: lngvar});
	}
}

$('#submitcreate').on('click', function() {

	var $myForm = $('#create');

	if (!$myForm[0].checkValidity()) {
	  // If the form is invalid, submit it. The form won't actually submit;
	  // this will just cause the browser to display the native HTML5 error messages.
	  $myForm.find(':submit').click();
	}

	$('.colorpickerback').colorpicker('enable');

	$('.preloader').fadeIn(100,function(){

		var sendata = $("#create :input")
	    .filter(function(index, element) {
	        return $(element).val() != "";
	    })
	    .serialize();

        $.ajax({
            type: "POST",
            url: "include/process.php",
            cache: false,
            data: sendata
        })
        .fail(function(error) {
            $("#alert_placeholder").html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span class="error-response">'+error.statusText+'</span></div>');
        })
        .done(function(msg) {
	
			if ($('#trans-bg').prop('checked')) {
				$('.colorpickerback').colorpicker('disable')
			}

            var result = JSON.parse(msg);
            if (result.hasOwnProperty('errore')) {
                $("#alert_placeholder").html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span class="error-response">'+result.errore+'</span></div>');
                $('.resultholder img').attr('src', result.placeholder);
                $('.preloader').fadeOut('slow');
            } else {
                d = new Date();
				$(".resultholder img").one("load", function() {

					$(this).attr('src', result.placeholder+'?t='+d.getTime() );

	                $("#alert_placeholder").html('');
	                var linksholder = '<a class="btn btn-default" href="include/get.php?path='+result.png+'"><i class="fa fa-download"></i> PNG</a>';
	                linksholder = linksholder + '<a class="btn btn-default" href="include/get.php?path='+result.svg+'"><i class="fa fa-download"></i> SVG</a>';
	                // linksholder = linksholder + '<a class="btn btn-default" href="include/get.php?path='+result.eps+'"><i class="fa fa-download"></i> EPS</a>';
	                linksholder = linksholder + '<button class="btn btn-default print"><i class="fa fa-print"></i></button>';

	                $('.linksholder').html(linksholder);

	                $('.print').click(function(){
	                	printIt('.resultholder');
	                });

	            	$('.preloader').fadeOut('slow');

				}).each(function() {
					if(this.complete) {
						// $(this).load(); // For jQuery < 3.0 
						$(this).trigger('load'); // For jQuery >= 3.0 
					}
				});
        	}
        });
    });
});

$(document).ready(function(){

	// SET CURRENCY
    $("#setcurrency").change(function(){
        var value = $(this).val();
        $("#getcurrency").html(value);
    });

   	// PAYPAL BUTTON TYPE
    $("#pp_type").change(function(){
        var value = $(this).val();

        if(value === '_donations') {
        	$(".nodonation").addClass('hidden');
        	$(".yesdonation").removeClass('col-sm-3');
        } else {
        	$(".nodonation").removeClass('hidden');
        	$(".yesdonation").addClass('col-sm-3');
        }
    }); 

    // COLOR PICKER
    var backcol = $('.colorpickerback').val();
    var frontcol = $('.colorpickerfront').val();
    $('.getcol').css('background',backcol);
    $('.getcol').css('color',frontcol);

    $('#file').change(function(){
        $('#sottometti').submit();
    });

    $(".alert").alert();

    $('.colorpickerback').colorpicker().on('change', function(ev){
        var color = ev.color.toString('hex');
        $('.getcol').css('background',color);
    });
    $('.colorpickerfront').colorpicker().on('change', function(ev){
        var color = ev.color.toString('hex');
        $('.getcol').css('color',color);
    });

    $('.tooltipper').tooltip();
});

// Translarent background
$(document).on('change', '#trans-bg', function(){
	if ($(this).prop('checked')) {
		$('.colorpickerback').colorpicker('setValue', '#ffffff');
		$('.colorpickerback').colorpicker('disable')
	} else {
		$('.colorpickerback').colorpicker('enable');
	}
})

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var dest = $(e.target).attr('href');
    $("#getsec").val(dest);

    if (dest == "#location") {
        initialize();
    }
});

// validate form
(function() {
	'use strict';
	window.addEventListener('load', function() {
	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.getElementsByClassName('needs-validation');
	// Loop over them and prevent submission
	var validation = Array.prototype.filter.call(forms, function(form) {
		form.addEventListener('submit', function(event) {
			event.preventDefault();
			event.stopPropagation();
			form.classList.add('was-validated');
		}, false);
	});
	}, false);
})();

// Check bitcoin address
$(document).ready(function(){

	var btcInput = $('input[name=btc_account]');

	btcInput.on('input', function(){
		var address = btcInput.val();
		console.log(address);

		$.ajax({
		  method: "POST",
		  url: "include/btc-check.php",
		  data: { btc_account: address }
		})
		  .done(function( msg ) {
		    console.log( "Data Saved: " + msg );
			if (msg) {
			 	btcInput.removeClass('is-invalid').addClass('is-valid');
			} else {
			 	btcInput.removeClass('is-valid').addClass('is-invalid');
			}
		});
	});
});
