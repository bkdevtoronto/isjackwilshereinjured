<?php

	/*
	 * Global defaults
	 */

	$curl = true; //will send a curl request on first load of each day to check status
	$debug = false; //will show error logs
	
	$defaultInjured = true; //the default injury status
	$defaultDetail = "calf/shin injury";
	$defaultLength = "6 weeks";

	/* 
	 * Error reporting
	 */

	if($debug){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}


	/*
	 * Check subdomain and redirect (301) if incorrect
	 */

	$sub = array_shift((explode(".", $_SERVER['HTTP_HOST'])));
	if($sub!="is"){
		header("Location: https://is.jackwilshereinjured.com");
		exit;
	}


	/*
	 *  cURL to physioroom.com
	 */

	if($curl){
		//Check if we've already looked up the status today
		$today = date("Y-m-d");
		if(file_exists("checks/$today.txt")){
			$todayContent = json_decode(file_get_contents("checks/$today.txt"));
			$injured = $todayContent[0];
			$detail = $todayContent[1];
			$length = $todayContent[2];
		} else {
			//Initiate and set parameters
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.physioroom.com/affiliate/4thegame/epl_injury_table.php");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);

			//Check that it worked and set default injury to "true"
			if(curl_errno($ch)){
				echo "<!-- cURL error: ".curl_error($ch)."-->";
				$injured = $defaultInjured;
			}

			//Close connection
			curl_close($ch);

			//Find Jack Wilshere's name
			$re = "/(\\<td.*J Wilshere.*\\<\\/td\\>)\\s*(\\<td.*\\<\\/td\\>)\\s*(\\<td.*\\<\\/td\\>)/"; 
			preg_match($re, $data, $matches);
			$injured = count($matches>0) ? true : false;

			//Set $detail to the injury detail and $length for the length of time out
			if($injured){
				preg_match("/\\<a.*\\>(.*)\\<\\/a\\>/",$matches[2],$detail);
				$detail = strtolower($detail[1]);
				preg_match("/\\<td\\>(.*)\\<\\/td\\>/",$matches[3],$length);
				$length = $length[1]=="no date" ? "an indeterminate amount of time" : "around ".strtolower($length[1]);
			}

			//Put data for today in a file
			file_put_contents("checks/$today.txt", json_encode(array($injured, $detail, $length)));
		}
	} else {
		$injured = $defaultInjured;
		$detail = $defaultDetail;
		$length = "around $defaultLength";
	}

	/*
	 * Page meta
	 */

	$title = "Jack Wilshere Is ".($injured ? null : "Not ")."Currently Injured!";
	$image = "https://is.jackwilshereinjured.com/ijwi.jpg";
	$desc  = "Your Number 1 source to find out if everyone's favourite glass footballer has been sidelined because he's smoked too many cigarettes- and how long he'll be out for!";

	/*
	 * Set sharing data
	 */

	$sharingURL = "https://is.jackwilshereinjured.com";
	$sharingTitle = str_replace(' ','%20',$title);

	/*
	 * Start page header
	 */

?><!doctype html>
<html>
<head>

	<title><?php echo $title ?></title>

	<!-- Meta Tags -->
	<link rel="canonical" href="https://is.jackwilshereinjured.com" >
	<meta name="keywords" content="jack,wilshere,jack wilshere,injury,injured,premier league,arsenal,is currently injured,course he is,wilshere is currently">
	<meta name="description" content="<?php echo $desc ?>" >
	<meta name="viewport" content="width=device-width, initial-scale=1.0" >
	<meta charset="utf-8">
    <!-- OG tags (Facebook Open Graph) -->
    <meta property="og:url" content="https://is.jackwilshereinjured.com" >
    <meta property="og:type" content="website" >
    <meta property="og:image" itemprop="image primaryImageOfPage" content="<?php echo $image ?>" >
    <!-- Twitter tags -->
	<meta name="twitter:image" content="<?php echo $image ?>">
    <meta name="twitter:card" content="summary_large_image" >
    <meta name="twitter:domain" content="jackwilshereinjured.com" >
    <meta name="twitter:creator" content="@BenKahanMMC">
    <meta name="twitter:title" property="og:title" itemprop="title name" content="Is Jack Wilshere Injured?" >
    <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $desc ?>" >

	<!-- Styles -->
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans:400,800|Pacifico|Ultra">
	<!--<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">-->
	<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

	<section>
		<div class="hero" style="background-image:linear-gradient(rgba(80,0,0,0.6),rgba(120,0,0,0.6)),url(//i.imgur.com/BKAN3kk.jpg)">
			<div class="header">
				<div class="curly"></div>
				<h1><?php echo $injured ? "Of Course He Is" : "Not Yet" ?></h1>
				<h2 class="subtitle"><?php echo $injured ? "He's got a $detail and will return in $length" : "I'd give it a week, two tops" ?></h2>
				<div class="curly bottom"></div>
			</div>
		</div>
	</section>

	<footer>
		<div class="container">
			<div class="col">
				Photo taken shamelessly from The Independent
			</div>
			<div class="col" style="text-align: center;">
				<span class='st_facebook_hcount' displayText='Facebook'></span>
				<span class='st_twitter_hcount' displayText='Tweet'></span>
				<span class='st_linkedin_hcount' displayText='LinkedIn'></span>
			</div>
			<div class="col" style="text-align: right; float:right;">
				<a href="https://bkdev.co.uk" alt="BKDev"><img src="//img.bkdev.co.uk/img/logo-light.png" alt="Website built and maintained by BKDev" height=20></a>
			</div>
			<div style="clear:both;"></div>
		</div>
	</footer>
	<noscript>Is Jack Wilshere Injured? works best when you are using JavaScript</noscript>
</body>

<!-- JS -->
<script src="//code.jquery.com/jquery-2.1.4.min.js" type="text/javascript"></script>
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "3519ae4e-94ce-464a-9e7c-714a612de1b0", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>

</html>