<?php
//Config
require_once('config.php');
//recuperation des param
if(isset($_REQUEST['historique'])){
	$histo = true;
	$date = $_REQUEST['historique'];
	$period = 'complet';
}
else{
	$histo = false;
	$date = date('Y_m');
	$period = isset($_REQUEST['period'])?$_REQUEST['period']:"";
}
$file = $home_dir.$date.'.json';

function getInfosSinceDate($input,$date_min){
	$infos = array();
	foreach($input as $date => $value){
		if($date > $date_min){
			$infos[$date] = $value;
		}
	}
	return $infos;
}

function getLastInfos($input){
	$last_info = null;
	$last_info_date = 0;
	foreach($input as $date => $value){
		if($date > $last_info_date){
			$last_info_date = $date;
			$last_info = $value;
		}
	}
	return $value;
}

//recuperation des données
$input = json_decode(file_get_contents($file),true);
if($period=='complet'){
	$infos = $input;
}
elseif($period=='semaine'){
	$infos = getInfosSinceDate($input,mktime(0,0,0,date("n"),date("j")-7));
}
elseif($period=='24h'){
	$infos = getInfosSinceDate( $input, mktime(date("h")-24,0,0,date("n"),date("j")) );
}
else{
	$infos = getInfosSinceDate($input,mktime(0,0,0,date("n"),date("j")));
}

if(!$histo){
	$last = getLastInfos($infos);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="Content-Language" content="fr">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/png" href="soleil.png">

		<!-- social network metas -->
		<meta property="image" content="http://nest.weberantoine.fr/soleil.png"/>
		<meta property="og:image" content="http://nest.weberantoine.fr/soleil.png"/>
		<meta property="site_name" content="NEST Weber Antoine"/>
		<meta property="description" content="" />
		<meta name="description" content="" />

		<title>NEST API</title>

		<!-- Main style -->
		<link href="/lib/almsa/theme.css" rel="stylesheet">
		

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
    	<style>
        	.ligne_grise{
            	margin-top: 40px;
                text-align: center;
                background: #eee;
        	}
        	
        	.ligne_blanche{
            	margin-top: 40px;
                text-align: center;
                /*background: #eee;*/
        	}
        </style>
    	
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse-menu">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/"><i class="icon fa fa-sun-o"></i> Nest API</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="collapse-menu">
					<ul class="nav navbar-nav navbar-right">
						<?php
							if($histo){
								echo '<li><a href="/">Accueil</a></li>';
								echo '<li class="active" ><a href="#">Historique</a></li>';
							}
							else{ ?>
						<li <? if($period==""){ echo 'class="active"'; } ?>><a href="/">Aujourd'hui</a></li>
						<li <? if($period=="24h"){ echo 'class="active"'; } ?>><a href="/?period=24h">24 dernières heures</a></li>
						<li <? if($period=="semaine"){ echo 'class="active"'; } ?>><a href="/?period=semaine">7 derniers jours</a></li>
						<li <? if($period=="complet"){ echo 'class="active"'; } ?>><a href="/?period=complet">Mensuel</a></li>
						<?php } ?>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container -->
		</nav><!--/.navbar-->

		<div class="wrapper"> 
			<div class="home" style="padding: 40px 0 40px;">
				<div class="container">
					<div class="row">
<!--						<div class="col-sm-6">
							<h1 class="animated fadeInDown delay-1">
								Redevenez maitre de vos données !
							</h1>

							<ul class="labels-menu labels-danger list-unstyled">
								<li class="animated slideInLeft delay-1"><span><i class="fa fa-angle-right"></i> Température mesurée</span></li>
								<li class="animated slideInLeft delay-2"><span><i class="fa fa-angle-right"></i> Température voulue</span></li>
								<li class="animated slideInLeft delay-3"><span><i class="fa fa-angle-right"></i> Période de chauffe</span></li>
								<li class="animated slideInLeft delay-4"><span><i class="fa fa-angle-right"></i> <b class="text-danger">New:</b> Disponible sur <a href="https://github.com/TwanoO67/nest-api" class="text-danger">Github</a></span></li>
							</ul>

						</div><!--./col-md-6 -->
						<!--<div class="col-sm-6 presentation">-->
                            <h2 class="text-center">Températures</h2>
							<div class="animated slideInRight delay-1">
								<!--<div class="downloads" id="downloads"><i class="fa fa-download"></i> xx Downloads</div>-->
								<div id="temperature1" style="height: 350px;"></div>
								
							</div>
						<!--<</div><!--./col-md-6 -->
					</div><!-- /.row -->
				</div>
			</div><!-- /.home -->

<?php
	
	//DERNIER DONNEES
	if(!$histo){ ?>
	
			<div class="features-menu">
				<div class="container">
					<div class="row">
						<div class="col-xs-12 col-sm-6 col-md-2">
							<div class="feature-item animated fadeInUp delay-3">
								<i class="fa fa-sun-o"></i>
								<h4>Température intérieure</h4>
								<p>
									<?php echo $last['current_state']['temperature'].' °C'; ?>
								</p>
							</div>
						</div><!-- /.item -->

						<div class="col-xs-12 col-sm-6 col-md-2">
							<div class="feature-item animated fadeInUp delay-3">
								<i class="fa fa-cloud"></i>
								<h4>Humidité intérieure</h4>
								<p>
									<?php echo $last['current_state']['humidity'].' %'; ?>
								</p>
							</div>
						</div><!-- /.item -->

						<div class="col-xs-12 col-sm-6 col-md-2">
							<div class="feature-item animated fadeInUp delay-3">
								<i class="fa fa-line-chart"></i>
								<h4>Chauffage</h4>
								<p>
									<?php if($last['current_state']['heat'] == true){
											echo "Allumé"; }
										else{
											echo "Eteint";	
										}
										?>
									
								</p>
							</div>
						</div><!-- /.item -->
						
						<div class="col-xs-12 col-sm-6 col-md-2">
							<div class="feature-item animated fadeInUp delay-3">
								<i class="fa fa-paper-plane"></i>
								<h4>Mode absent</h4>
								<p>
									<?php if($last['current_state']['auto_away'] == 1){
											echo "Automatique"; }
										elseif($last['current_state']['manual_away'] == true){
											echo "Manuel";	
										}
										else{
											echo "Eteint";
										}
										?>
									
								</p>
							</div>
						</div><!-- /.item -->
						
						<div class="col-xs-12 col-sm-6 col-md-2">
							<div class="feature-item animated fadeInUp delay-3">
								<i class="fa fa-tree"></i>
								<h4>Température extérieure</h4>
								<p>
									<?php echo $last['current_state']['outside_temperature'].' °C'; ?>
								</p>
							</div>
						</div><!-- /.item -->

						<div class="col-xs-12 col-sm-6 col-md-2">
							<div class="feature-item animated fadeInUp delay-3">
								<i class="fa fa-umbrella"></i>
								<h4>Humidité extérieure</h4>
								<p>
									<?php echo $last['current_state']['outside_humidity'].' %'; ?>
									<!-- fa-leaf fa-tree fa-fire fa-place-->
								</p>
							</div>
						</div><!-- /.item -->
						
					</div><!-- /.row -->

				
				</div><!-- /.container -->
			</div><!-- /.features-menu-->
			
			<div class="ligne_grise">
				<div class="container">
					<div class="row">
						<h2 class="text-center">Humidité</h2>
						<div id="humidity1" style="height: 350px;"></div>
					</div>
				</div>
			</div>
			
			<div class="ligne_blanche">
				<div class="container">
					<div class="row" style="min-height: 150px;">
						<h2 class="text-center">Historique disponible</h2>
						
						<?php foreach( scandir($home_dir) as $fichier){
							if(strpos($fichier,'.json')){
								$date_histo = str_replace('.json', '', $fichier);
								echo '<a href="/?historique='.$date_histo.'" > Relevé statistique du '.$date_histo.'</a>';
							}
						}?>
						
					</div>
				</div>
			</div> 
			
	<?php } ?>

			<footer class="footer">
				<div class="container">
					<div class="row">
						<div class="col-sm-9 item">
							Copyright &copy; 2014 <a href="http://www.weberantoine.fr">WEBER Antoine</a>.
						</div>
						<div class="col-sm-3 text-center">
							<a href="https://github.com/TwanoO67/" class="btn btn-github btn-social" data-toggle="tooltip" data-placement="top" title="Github Repositories"><i class="fa fa-github"></i></a>
						</div>
					</div>
				</div>
			</footer>

		</div><!--/.wrapper -->

		<!-- jQuery 1.10.2 -->
		<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
		<!-- Latest compiled and minified Bootstrap JavaScript -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="/lib/morris.css">
		<script src="/lib/raphael-min.js"></script>
		<script src="/lib/morris.min.js"></script>
		
    	<script>
			$(function () {
				$('[data-toggle="tooltip"]').tooltip();
			});
		</script>
		<?php require_once('./includes/graph_script.php'); ?>
	</body>
</html>