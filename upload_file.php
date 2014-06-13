<?php
	$allowedExts = array("csv");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	
	if (($_FILES["file"]["size"] < 20000) && in_array($extension, $allowedExts)) {
		if ($_FILES["file"]["error"] > 0) {
			echo "Error: " . $_FILES["file"]["error"] . "<br>";
		} 
		else {
			echo "Upload: " . $_FILES["file"]["name"] . "<br>";
			echo "Type: " . $_FILES["file"]["type"] . "<br>";
			echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
			echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br>";
			if (file_exists("upload/" . $_FILES["file"]["name"])) {
				echo $_FILES["file"]["name"] . " already exists. " . "<br>";
			} 
			else {
				move_uploaded_file($_FILES["file"]["tmp_name"],
				"upload/" . $_FILES["file"]["name"]);
				echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
			}
			echo "<br>";
			
			$file = fopen("upload/" . $_FILES["file"]["name"],"r");
			while(!feof($file)) {
				$array = fgetcsv($file);
				$time[] = $array[0];
				$calc_load[] = $array[1];
				$dam[] = $array[2];
				$fb_knock[] = $array[3];
				$fkl[] = $array[4];
				$boost[] = $array[5];
				$td_boost_err[] = $array[6];
				$af_learn[] = $array[7];
				$dyn_adv[] = $array[8];
				$ign_tim[] = $array[9];
				$maf_gs[] = $array[10];
				$maf_v[] = $array[11];
				$rpm[] = $array[12];
				$throttle[] = $array[13];
			}
			fclose($file);

			for($i = 1; $i < count($rpm); $i++){
				if ($rpm[$i]> 2000 && $rpm[$i] < 6500) {
					$temp_rpm[] = $rpm[$i];
					$temp_boost[] = $boost[$i];
				}
			}
			$rpm = $temp_rpm;
			$boost = $temp_boost;
			
			echo "<script>\n            var rpm = " . json_encode($rpm)
         		 . "\n            var boost = " . json_encode($boost)
         		 . "\n        </script>\n";
		}		
	}
	else {
		echo "Invalid file!";
	}
?>

<html>
	<head>
		<title>Line Chart</title>
		<script src="chart/Chart.js"></script>
		<meta name = "viewport" content = "initial-scale = 1, user-scalable = no">
		<style>
			canvas{
			}
		</style>
	</head>
	<body>
		<h2>Boost (psi) vs. RPM</h2>
		<br>
		<canvas id="canvas" height="600" width="1000"></canvas>
		<script>
			var lineChartData = {
				labels : rpm,
				datasets : [
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						data : boost
					}
				]
				
			}

			var lineChartOptions = {

			//Boolean - If we show the scale above the chart data			
			scaleOverlay : true,
			
			//Boolean - If we want to override with a hard coded scale
			scaleOverride : true,
			
			//** Required if scaleOverride is true **
			//Number - The number of steps in a hard coded scale
			scaleSteps : 30,
			//Number - The value jump in the hard coded scale
			scaleStepWidth : 1,
			//Number - The scale starting value
			scaleStartValue : -12,

			//String - Colour of the scale line	
			scaleLineColor : "rgba(0,0,0,.1)",
			
			//Number - Pixel width of the scale line	
			scaleLineWidth : 1,

			//Boolean - Whether to show labels on the scale	
			scaleShowLabels : true,
			
			//Interpolated JS string - can access value
			scaleLabel : "<%=value%>",
			
			//String - Scale label font declaration for the scale label
			scaleFontFamily : "'Arial'",
			
			//Number - Scale label font size in pixels	
			scaleFontSize : 12,
			
			//String - Scale label font weight style	
			scaleFontStyle : "normal",
			
			//String - Scale label font colour	
			scaleFontColor : "#666",	
			
			///Boolean - Whether grid lines are shown across the chart
			scaleShowGridLines : true,
			
			//String - Colour of the grid lines
			scaleGridLineColor : "rgba(0,0,0,0.1)",
			
			//Number - Width of the grid lines
			scaleGridLineWidth : 1,	
			
			//Boolean - Whether the line is curved between points
			bezierCurve : false,
			
			//Boolean - Whether to show a dot for each point
			pointDot : true,
			
			//Number - Radius of each point dot in pixels
			pointDotRadius : 3,
			
			//Number - Pixel width of point dot stroke
			pointDotStrokeWidth : 1,
			
			//Boolean - Whether to show a stroke for datasets
			datasetStroke : true,
			
			//Number - Pixel width of dataset stroke
			datasetStrokeWidth : 2,
			
			//Boolean - Whether to fill the dataset with a colour
			datasetFill : true,
			
			//Boolean - Whether to animate the chart
			animation : true,

			//Number - Number of animation steps
			animationSteps : 60,
			
			//String - Animation easing effect
			animationEasing : "easeOutQuart",

			//Function - Fires when the animation is complete
			onAnimationComplete : null
			}
		var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, lineChartOptions);
		
		</script>
	</body>
</html>