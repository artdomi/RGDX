<?PHP
    session_start();
    require_once("../include/membersite_config.php");
    $fgmembersite->DBLogin();
    // $fgmembersite->ReorderPositions();  //MOVE QUEUE FORWARD IF MIN(POS)>1, SET TIME_ELAPSED TO NOW() FOR POS=1
    $fgmembersite->CheckTimer();    //CHECK REMAINING TIME OF THE 1'ST POS - FORCE KICKOUT AUTHORIZED
    $fgmembersite->CheckPulse();    //CHECK PULSE OF THE 1'ST USER - FORCE KICKOUT AUTHORIZED

    ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>EditableGrid - Minimal demo - Creating grid in Javascript</title>
		
		<script src="./bower_components/editablegrid/editablegrid.js"></script>
		<!-- [DO NOT DEPLOY] --> <script src="./bower_components/editablegrid/editablegrid_renderers.js" ></script>
		<!-- [DO NOT DEPLOY] --> <script src="./bower_components/editablegrid/editablegrid_editors.js" ></script>
		<!-- [DO NOT DEPLOY] --> <script src="./bower_components/editablegrid/editablegrid_validators.js" ></script>
		<!-- [DO NOT DEPLOY] --> <script src="./bower_components/editablegrid/editablegrid_utils.js" ></script>
		<!-- [DO NOT DEPLOY] --> <script src="./bower_components/editablegrid/editablegrid_charts.js" ></script>
		<link rel="stylesheet" href="./bower_components/editablegrid/editablegrid.css" type="text/css" media="screen">
		<script src="./bower_components/jquery/dist/jquery.min.js"></script>
	    <link href="./docs/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
	    <link href="./docs/dist/css/logo-nav.css" rel="stylesheet">
		
		
		<script>
			window.onload = function() {

				// this approach is interesting if you need to dynamically create data in Javascript 
				var metadata = [];
				metadata.push({ name: "voltage", label: "VOLTAGE", datatype: "double(V,,dot,,)", editable: true});
				metadata.push({ name: "pressure", label:"PRESSURE", datatype: "double(mTorr,,dot,,)", editable: true});
				metadata.push({ name: "distance", label: "DISTANCE", datatype: "double(cm,,dot,,)", editable: true});
				metadata.push({ name: "pxd", label: "PxD", datatype: "double(mTorrxcm,,dot,,)", editable: true});

				// a small example of how you can manipulate the object in javascript
				// metadata[4].values = {};
				// metadata[4].values["Europe"] = {"be":"Belgium","fr":"France","uk":"Great-Britain","nl":"Nederland"};
				// metadata[4].values["America"] = {"br":"Brazil","ca":"Canada","us":"USA"};
				// metadata[4].values["Africa"] = {"ng":"Nigeria","za":"South-Africa","zw":"Zimbabwe"};

				var data = [];
				data.push({id: 1, values: {"voltage":2000,"pressure":150,"distance":50,"pxd":15*50}});

		         
				editableGrid = new EditableGrid("DemoGridJsData");
				editableGrid.load({"metadata": metadata, "data": data});
				editableGrid.renderGrid("tablecontent", "table table-bordered");
			} 
		</script>
		<script type="text/javascript">
			$(function () {
			    $('#container').highcharts({
			        chart: {
			            type: 'scatter',
			            margin: [70, 50, 60, 80],
			            events: {
			                click: function (e) {
			                    // find the clicked values and the series
			                    var x = e.xAxis[0].value,
			                        y = e.yAxis[0].value,
			                        series = this.series[0];

			                    // Add it
			                    series.addPoint([x, y]);

			                }
			            }
			        },
			        title: {
			            text: 'Paschen Curve Plot'
			        },
			        subtitle: {
			            text: 'Click the plot area to add a point. Click a point to remove it.'
			        },
			        xAxis: {
			            gridLineWidth: 1,
			            minPadding: 0.2,
			            maxPadding: 0.2
			            // maxZoom: 60
			        },
			        yAxis: {
			            title: {
			                text: 'Value'
			            },
			            minPadding: 0.2,
			            maxPadding: 0.2,
			            maxZoom: 60,
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        legend: {
			            enabled: false
			        },
			        exporting: {
			            enabled: false
			        },
			        plotOptions: {
			            series: {
			                lineWidth: 0,
			                point: {
			                    events: {
			                        'click': function () {
			                            if (this.series.data.length > 1) {
			                                this.remove();
			                            }
			                        }
			                    }
			                }
			            }
			        },
			        series: [{
			            data: []
			        }]
			    });
			});
		</script>
	</head>
	<body>
<script src="./Highcharts-4.1.8/js/highcharts.js"></script>
<script src="./Highcharts-4.1.8/js/modules/exporting.js"></script>

		<div class ="container" id="container" style="min-width: 310px; height: 400px; max-width: 700px; margin: 0 auto"></div>

    	<div class="container">
        <h1 class="page-header">Paschen Curve Data</h1>
        <div class="table-responsive" id="tablecontent"></div>
		<button type="button" class="logoff" id="butlog">toggle logx</button>
		<button type="button" id="but1">New row</button>
		<button type="button" id="but2">Last pxd</button>
		<button type="button" id="butplot">Plot</button>
		<div id="democontent"></div>
	</div>

		<script type="text/javascript">
			$(document).ready(function(){
		        // var chart = $('#container').highcharts();
			    alert(<?php echo $_SESSION['$paschenTest']; ?>);
		</script>


	</body>

</html>
