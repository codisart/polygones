<?php

require_once('PointClass.php');
require_once('SegmentClass.php');
require_once('PolygoneCollectionClass.php');

$coordUn = [[0,0],[0,1],[1,2],[1,0],[0,0]];
$coordDeux = [[2,0],[2,1],[3,1],[3,0],[2,0]];
$coordTrois = [[2,1],[2,2],[3,2],[3,1],[2,1]];


// $coordUn = json_decode('[[100,0],[102,1],[103,2],[100,0]]');
// $coordDeux = json_decode('[[105,0],[103,2],[102,1],[105,0]]');




function intersect($segmentA, $segmentB) {
	var_dump($segmentA);
	var_dump($segmentB);
	die;
	$coefA = ($segmentA[1][1] - $segmentA[0][1]) / ($segmentA[1][0] - $segmentA[0][0]);
	$coefB = ($segmentB[1][1] - $segmentB[0][1]) / ($segmentB[1][0] - $segmentB[0][0]);
	
	$ordonneA = $segmentA[0][1] - ($segmentA[0][0]*$coefA);	
	$ordonneB = $segmentB[0][1] - ($segmentB[0][0]*$coefB);
	
	$x = ($ordonneB-$ordonneA)/($coefA-$coefB);
	$y = $coefA*$x + $ordonneA;
	
	return array($x, $y);
}

function odd_even($point, $polygon){
	
	$intersecs = 0;	
	foreach ($polygon as $pointPoly) {
		$pointPoly2 = next($polygon);
		
		if($pointPoly2) {

			if($pointPoly[0] == $point[0]){
				return true;
			}
			
			if(($pointPoly[1] - $pointPoly2[1]) == 0) {
				if( $pointPoly[0] < $point[0]) {
					$intersecs++;
				}
			}
			else if(($pointPoly[0] - $pointPoly2[0]) != 0) {
				$pente= ($pointPoly[0] - $pointPoly2[0]) / ($pointPoly[1] - $pointPoly2[1]);
				
				if($pente*$point[1] < $point[0]) {
					$intersecs++;
				}
				else if($pente*$point[1] == $point[0]){
					return true;
				}
			}
			else if(($pointPoly[0] - $pointPoly2[0]) == 0) {
				if($pointPoly[0] < $point[0]) {
					$intersecs++;
				}
			}			
		}
	}
	return $intersecs % 2 !== 0;
}

function isBetween($int,$first,$second){
	$min = min($first,$second);
	$max = max($first,$second);
    return ($min<$int && $int<$max);
}


$segmentAB = array(array(2,0), array(4,2));
$segmentBC = array(array(2,2), array(4,0));





$polygones = new PolygoneCollection();
$polygones[] = new Polygone($coordUn);
$polygones[] = new Polygone($coordDeux);
$polygones[] = new Polygone($coordTrois);

$fusionPolygones = $polygones->union();

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
	<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>



	<title>Polygones</title>

	<script type="text/javascript">
	window.onload = function() {
		var mp1 = {
		  "type": "Feature",
		  "geometry": {
		    "type": "MultiPolygon",
		    "coordinates": [
		        [
		          
		          <?php echo $fusionPolygones->toJSON();?>
		        ]
		      ]
		  },
		  "properties": {
		    "name": "MultiPolygon",
		    "style": {
		        color: "black",
		        opacity: 1,
		        fillColor: "white",
		        fillOpacity: 1
		    }
		  }
		};
		var map1 = L.map('poly1', {
			center: [0,2],
  			zoom: 6
		});
		new L.GeoJSON(mp1, {
		  style: function(feature) {
		      return feature.properties.style
		  }
		}).addTo(map1);
		
		var mp2 = {
		  "type": "Feature",
		  "geometry": {
		    "type": "MultiPolygon",
		    "coordinates": [
		        [
		          
		          <?php 
		         	echo $polygones->toJSON();
		          ?>
		        ]
		      ]
		  },
		  "properties": {
		    "name": "MultiPolygon",
		    "style": {
		        color: "black",
		        opacity: 1,
		        fillColor: "white",
		        fillOpacity: 1
		    }
		  }
		};
		var map2 = L.map('poly2', {
			center: [0,2],
  			zoom: 6
		});
		new L.GeoJSON(mp2, {
		  style: function(feature) {
		      return feature.properties.style
		  }
		}).addTo(map2);
		
	};
	</script>

	<style>
	#map { width: 1000px; height: 400px; }
	</style>

</head>

<body>
	<div id="poly1" style="float:left;height:480px; width:300px;margin:0;"></div>
	<div id="poly2" style="left;height:480px; width:300px;margin:0;"></div>
	<div id="poly12" style="left;height:480px; width:300px;margin:0;"></div>
	<div id="polyUnion" style="left;height:480px; width:300px;margin:0;"></div>
</body>

</html>
