
var map, heatmap;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: new google.maps.LatLng(26.096255,-15.264894),
    zoom: 3,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    zoomControl: true,
        zoomControlOptions: {
        style: google.maps.ZoomControlStyle.SMALL,
        },
    disableDoubleClickZoom: true,
    mapTypeControl: false,
    scaleControl: false,
    scrollwheel: true,
    panControl: true,
    streetViewControl: false,
    draggable : true,
    overviewMapControl: true,
    overviewMapControlOptions: {
            opened: true,
        },
    styles: [
    {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [
            {
                "hue": "#00ffff"
            },
            {
                "saturation": -84
            },
            {
                "lightness": 59
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "labels",
        "stylers": [
            {
                "hue": "#ffffff"
            },
            {
                "saturation": -100
            },
            {
                "lightness": 100
            },
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "geometry",
        "stylers": [
            {
                "saturation": 1
            },
            {
                "lightness": -15
            }
        ]
    },
    {
        "featureType": "poi.school",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -60
            },
            {
                "lightness": 23
            },
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [
            {
                "hue": "#ffffff"
            },
            {
                "saturation": -100
            },
            {
                "lightness": 100
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels",
        "stylers": [
            {
                "hue": "#bbbbbb"
            },
            {
                "saturation": -100
            },
            {
                "lightness": 26
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "simplified"
            },
            {
                "hue": "#ffda00"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [
            {
                "hue": "#ffcc00"
            },
            {
                "saturation": 100
            },
            {
                "lightness": -35
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "transit.station",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "hue": "#0095ff"
            },
            {
                "saturation": 55
            },
            {
                "lightness": -6
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "labels",
        "stylers": [
            {
                "hue": "#7fc8ed"
            },
            {
                "saturation": 55
            },
            {
                "lightness": -6
            },
            {
                "visibility": "off"
            }
    	]
    	}
	]
  });

	loadPoints(1);
    getSatisfaction(1);
}

var points = [];

function loadPoints(keywordId)
{
	$('body').dimmer('show');

	$.get('/api.php?action=points&id='+keywordId, function(data) {
		points = [];

		$.each(data, function(index, value) {
			points.push(new google.maps.LatLng(value.latitude, value.longitude));
		});

		if (typeof heatmap == 'undefined') {
			heatmap = new google.maps.visualization.HeatmapLayer({
			    data: points,
			    map: map
			  });
		} else {
			heatmap.setData(points);
		}
		
		$('body').dimmer('hide');
	}, 'json');
}

function loadKeywords()
{
	$.get('/api.php?action=keywords', function(data) {
		$('#filter-trends-menu .item').remove();
		$.each(data, function(index, value) {
			$('#filter-trends-menu .header').after('<div class="item" data-keyword-id="'+value.id+'"><div class="ui black empty circular label"></div>'+value.keyword+' | '+value.total+' locations</div>');
		});
	}, 'json');
}

function getSatisfaction(keywordId)
{
    $.get('/api.php?action=satisfaction&id='+keywordId, function(data) {
        $('#stats-likes').text(data[0].positive);
        $('#stats-dislikes').text(data[0].negative);
    }, 'json');
}

function loadTrends()
{
    $.get('/api.php?action=trends', function(data) {
        $('#trends-list').empty();
        $.each(data, function(index, value) {
            $('#trends-list').append('<option value="'+value.id+'">'+value.keyword+'</option>');
        });
    }, 'json');
}

function drawChart(list) {
    var data = google.visualization.arrayToDataTable(list);

    var options = {
        title: 'Hourly mentions',
        hAxis: {title: 'Time',  titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0},
        chartArea:{left:'10%',top:'20%',width:'80%',height:'75%'}
    };


    $('#chart_div').show();
    chart.draw(data, options);
}

function compareTrends()
{
    $.post(
        '/api.php?action=compare',
        $('#compare-trends-form').serialize(),
        function(data) {
            $('#comparision-table').show();
            $('#comparision-table tbody').empty();
            $.each(data, function(index, value) {
                var countries = '';
                $.each(value.countries, function(i, c) {
                    countries += '<i class="'+c.country_code.toLowerCase()+' flag"></i>'+c.num+'&nbsp; &nbsp;';
                });
                $('#comparision-table tbody').append('<tr><td>'+value.name+'</td><td>'+value.mentions+' tweets</td><td>'+value.satisfaction.positive+' <i class="thumbs outline up icon"></td><td>'+value.satisfaction.negative+' <i class="thumbs outline down icon"></td><td>'+countries+'</td></tr>');
            });
        },
        'json'
    );

    $.post(
        '/api.php?action=chart',
        $('#compare-trends-form').serialize(),
        function(data) {
            chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
            drawChart(data);
        },
        'json'
    );
}

$(document).ready(function() {
    if ($('body').hasClass('reports')) {
        $('#trends-list').dropdown({
            onChange: function() {
                $('#trends-list').dropdown('hide');
            }
        });

        loadTrends();

        $('#compare-trends').click(function() {
            compareTrends();
            return false;
        });

        $('#comparision-table').tablesort();
    }

	$('#filter-trends-menu').dropdown({
		onChange: function(value, text, $selectedItem) {
            var keywordId = $selectedItem.data('keyword-id');
			loadPoints(keywordId);
            getSatisfaction(keywordId);
			return false;
    	}
	});

	$('#add-keyword').click(function() {
		$('.add-trend').modal({
	    	onApprove : function() {
	    		$('.add-trend .error.message').remove();
	      		$.get('/api.php?action=add&keyword='+$('#form-keyword').val(), function(data) {
					if (data.status == 'ok') {
						loadKeywords();
						$('.add-trend .deny').click();
					} else {
						$('#form-keyword').after('<div class="ui error message"><ul class="list"><li>'+data.message+'</li></ul></div>')
					}
				}, 'json');

				return false;
	    	}
	    }).modal('show');
	});

	loadKeywords();

	$('.about-app-link').click(function() {
		$('.ui.modal.about-app').modal('show');
		return false;
	});

    $('.help-link').click(function() {
        $('.ui.modal.help').modal('show');
        return false;
    });
});