<?php
	$allowedExts = array("csv");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	
	if (($_FILES["file"]["size"] < 20000) && in_array($extension, $allowedExts)) {
		if ($_FILES["file"]["error"] > 0) {
			echo "Error: " . $_FILES["file"]["error"] . "<br>";
		} 
		else {
			/*
			echo "Upload: " . $_FILES["file"]["name"] . "<br>";
			echo "Type: " . $_FILES["file"]["type"] . "<br>";
			echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
			echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br>";
			*/
			if (!file_exists("upload/" . $_FILES["file"]["name"])) {
				move_uploaded_file($_FILES["file"]["tmp_name"],
				"upload/" . $_FILES["file"]["name"]);
				echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br>";
				//echo $_FILES["file"]["name"] . " already exists. " . "<br>";
			}

			echo "<h3>" . $_FILES["file"]["name"] . "</h3>";
			
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
				if ($rpm[$i]> 2000 && $rpm[$i] < 7000 && $throttle[$i] > 90) {
					$temp_rpm[] = $rpm[$i];
					$temp_boost[] = $boost[$i];
					$temp_td_boost_err[] = $td_boost_err[$i];
				}
			}
			$rpm = $temp_rpm;
			$boost = $temp_boost;
			$td_boost_err = $temp_td_boost_err;
			
			echo "<script> var rpm = " . json_encode($rpm) . "\n var boost = " . json_encode($boost)
				 . "\n var td_boost_err = " . json_encode($td_boost_err) . "</script>\n";
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
		<canvas id="canvas" height="450" width="800"></canvas>
		<script>
			
			var target_boost = new Array();
			for (var i=0; i < boost.length; i++) {
				var num = parseFloat(boost[i]) + parseFloat(td_boost_err[i]);
				target_boost.push(num);
			}

			for (var i=0; i < target_boost.length; i++) {
				console.log(target_boost[i]);
			}
			console.log(boost.length);
			console.log(td_boost_err.length);
			console.log(target_boost.length);

			var lineChartData = {
				labels : rpm,
				datasets : [
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						data : boost
					},
					{
						fillColor : "rgba(236,50,87,0.5)",
						strokeColor : "rgba(236,50,87,0.5)",
						pointColor : "rgba(236,50,87,1)",
						pointStrokeColor : "#fff",
						data : td_boost_err
					},
					{
						fillColor : "rgba(60,193,82,0.5)",
						strokeColor : "rgba(60,193,82,0.5)",
						pointColor : "rgba(60,193,82,1)",
						pointStrokeColor : "#fff",
						data : target_boost
					}						
				]				
			}

			var lineChartOptions = {		
				scaleOverlay : true,
				scaleOverride : true,
				scaleSteps : 14,
				scaleStepWidth : 2,
				scaleStartValue : -6,
				scaleLineColor : "rgba(0,0,0,.6)",
				scaleLineWidth : 3,
				scaleGridLineColor : "rgba(0,0,0,0.15)",
				scaleGridLineWidth : 0.5,	
				bezierCurve : false,
				pointDot : false,
				datasetStrokeWidth : 3,
				datasetFill : false,
				animationSteps : 70,
				animationEasing : "easeOutQuart",

				//Function - Fires when the animation is complete
				onAnimationComplete : null
			}
		var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, lineChartOptions);
		
		</script>
		<br>
		<h3 style="color: rgba(60,193,82,0.9)">Target Boost </h3>
		<h3 style="color: rgba(151,187,230,1)">Boost </h3>
		<h3 style="color: rgba(236,50,87,0.8">TD Boost Error </h3>
	</body>
</html>