<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
		<style type="text/css">
			html { height: 100% }
			body { height: 100%; margin:50px; padding: 0 }
			table {width:600px;}
			map_canvas { height: 100% }
            .myInput{
				width:450px;
            }

			.myButton {
				background-color:#4EBFD3;
			}

			.myTable tr:nth-child(even) {background: white}
			.myTable tr:nth-child(odd) {background: #DBE0E7}
			.myTable {width:100%;}
			.myTable td{height:60px;}
		</style>
		<script type="text/javascript"
			src="http://maps.googleapis.com/maps/api/js?sensor=false">
		</script>
		<script type="text/javascript">
			var map;
			function initialize() {
				var mapOptions = {
					center: new google.maps.LatLng(-34.397, 150.644),
					zoom: 8,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById("map_canvas"),
											  mapOptions);
			}
			function clickcenter(lati,long){
				var latLng = new google.maps.LatLng(lati, long); //Makes a latlng
				map.panTo(latLng); //Make map global
			}
		
		</script>

	</head>
	<body onload="initialize()">

		<div id="map_canvas" style="width:600px; height:600px;float:right;"></div>

		<form name="query" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="text"  name="querycontent" class="myInput">
			<input type="submit" value="search"><br>
			<input type="radio" name="querytype" value="user" >user
			<input type="radio" name="querytype" value="tweets" checked>tweets
		</form>
		<br><br>

		<div id="result" style="height: 530px; width: 530px; font-size: 14px; overflow: auto;float:left;">

			<?php
				
				if (isset($_POST["querytype"]) && $_POST["querytype"]=="user")
				{
					$queryoption = 0;
				}
				else
				{
					$queryoption = 1;
				}
				//echo "type=".$queryoption;
				//echo "query_content=".$query_content;
				if (isset($_POST["querycontent"]))
				{
					$query_content = $_POST["querycontent"];
					//echo "query_content=".$query_content;
					system("python ./Search.py $query_content $queryoption",$ret);
					//echo "ret=".$ret."<br>";
					if ($ret==0)
					{
						$filename = './result.txt';
						$file = fopen($filename,"r") or exit("Unable to open file!");
						print "<table class=\"myTable\">";
						while (!feof($file))
						{
						$resjson = json_decode(fgets($file),true);
						echo "<tr><td>";
						if(!feof($file))
						{print "@".$resjson['user'].":".$resjson['text']."<br>" ;}
						$lat = 34.0522222;
						$lon = -118.2427778;
						if($resjson['lat'])
						{
							$lat = $resjson['lat'];
							$lon = $resjson['lon'];
						}
						print "<button type=\"button\" class=\"myButton\" onclick=\"clickcenter($lat,$lon)\">Show on map!</button><br>";
						echo "</td></tr>\n";
						}
						fclose($file);
						print "</table>";
					}
				}
				
			?>
		</div>

	</body>
</html>