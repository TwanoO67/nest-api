<?php
//Preparation des données de graph	  
$min_temp = 99;
$max_temp = 0;

$min_hum  = 100;
$max_hum  = 0;

$temp_graph_data = "";
$humi_graph_data = "";

//Remplissage des données de cretes
foreach($infos as $date => $donnee){
    //recuperation des data
    $wanted = is_array($donnee['target']['temperature'])?$donnee['target']['temperature'][0]:$donnee['target']['temperature'];
    $mesured = $donnee['current_state']['temperature'];
    $external = isset($donnee['current_state']['outside_temperature'])?$donnee['current_state']['outside_temperature']:"null";
    $external_hum = isset($donnee['current_state']['outside_humidity'])?$donnee['current_state']['outside_humidity']:"null";
    
    //calcul des valeurs de cretes
    $cur_temp_min = min($mesured,$wanted,($external=='null'?99:$external));
    $cur_temp_max = max($mesured,$wanted,($external=='null'?0:$external));
    
    $min_temp = $cur_temp_min < $min_temp ? $cur_temp_min:$min_temp;    
    $max_temp = $cur_temp_max > $max_temp ? $cur_temp_max:$max_temp;
    
    $cur_hum_min = min($donnee['current_state']['humidity'],($external_hum=='null'?100:$external_hum));
    $cur_hum_max = max($donnee['current_state']['humidity'],($external_hum=='null'?0:$external_hum));
        
    $min_hum = $cur_hum_min < $min_hum ? $cur_hum_min:$min_hum;    
    $max_hum = $cur_hum_max > $max_hum ? $cur_hum_max:$max_hum; 

    
    //construction de l'affichage temp
    $temp_graph_data .= "{ date: '".date("Y-m-d H:i:s",$date)."',";
    $temp_graph_data .= " degree: '".$mesured."',";
    $temp_graph_data .= " target: '".$wanted."',";
    $temp_graph_data .= " chauffe: ".($donnee['current_state']['heat']?"HEAT_PLACEHOLDER":"null").",";
    $temp_graph_data .= " external: '".$external."',";
    $temp_graph_data .= " auto_away: ".($donnee['current_state']['auto_away']?"HEAT_PLACEHOLDER":"null").",";
    $temp_graph_data .= " manual_away: ".(!$donnee['current_state']['auto_away']&&$donnee['current_state']['manual_away']?"HEAT_PLACEHOLDER":"null").",";
    $temp_graph_data .= " },";

    //construction de l'affichage humidity

    $humi_graph_data .= "{ date: '".date("Y-m-d H:i:s",$date)."', humidity: ".$donnee['current_state']['humidity'].", ext_humidity: ".$external_hum."  }, ";

}

//on coupe au borne haute et basse
$min_temp = isset($min_temp_conf)&&!empty($min_temp_conf) ? $min_temp_conf : floor($min_temp);
$max_temp = isset($max_temp_conf)&&!empty($max_temp_conf) ? $max_temp_conf : ceil($max_temp);

$min_hum = isset($min_hum_conf)&&!empty($min_hum_conf) ? $min_hum_conf : floor($min_hum);
$max_hum = isset($max_hum_conf)&&!empty($max_hum_conf) ? $max_hum_conf : ceil($max_hum);


//remplacement des placeholder
if(isset($heat_temp_conf) && is_numeric($heat_temp_conf))
    $temp_graph_data = str_replace('HEAT_PLACEHOLDER', $heat_temp_conf, $temp_graph_data);
elseif(isset($heat_temp_conf) && $heat_temp_conf == 'min')
    $temp_graph_data = str_replace('HEAT_PLACEHOLDER', $min_temp, $temp_graph_data);
else
    $temp_graph_data = str_replace('HEAT_PLACEHOLDER', $max_temp, $temp_graph_data);


?>

<script>
    new Morris.Line({
	  // ID of the element in which to draw the chart.
	  element: 'temperature1',
	  //behaveLikeLine: true,
	  smooth: false,
	  // Chart data records -- each entry in this array corresponds to a point on
	  // the chart.
	  data: [
		  <?php echo $temp_graph_data; ?>
	  ],
	  // The name of the data record attribute that contains x-values.
	  xkey: 'date',
	  // A list of names of data record attributes that contain y-values.
	  ykeys: ['degree','target','chauffe','external','auto_away','manual_away'],
	  ymin: <?php echo $min_temp; ?>,
	  ymax: <?php echo $max_temp; ?>,
	  pointSize: 0,
	  postUnits: ' C°',
	  // Labels for the ykeys -- will be displayed when you hover over the
	  // chart.
	  labels: ['Intérieur','Voulu','Chauffage allumé','Extérieur', 'Auto absent', 'Manuel absent'],
	  lineColors: ["rgb(11, 98, 164)","rgb(122, 146, 163)",'red','green','grey','black']
	});
	
	new Morris.Line({
	  // ID of the element in which to draw the chart.
	  element: 'humidity1',
	  smooth: false,
	  // Chart data records -- each entry in this array corresponds to a point on
	  // the chart.
	  data: [
		  <?php echo $humi_graph_data; ?>
	  ],
	  // The name of the data record attribute that contains x-values.
	  xkey: 'date',
	  // A list of names of data record attributes that contain y-values.

	  ykeys: ['humidity','ext_humidity'],
	  postUnits: ' %',
	  ymin: <?php echo $min_hum; ?>,
	  ymax: <?php echo $max_hum; ?>,
	  pointSize: 0,
	  // Labels for the ykeys -- will be displayed when you hover over the
	  // chart.
	  labels: ['Humidité Intérieure','Humidité Exterieure'],
	  lineColors: ["rgb(11, 98, 164)",'green']
	});
</script>