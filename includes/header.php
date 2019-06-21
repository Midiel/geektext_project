<!DOCTYPE html>
	<html>
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
			<meta http-equiv="X-UA-Compatible" content="ie=edge">
			<title>GeekText</title>

			 

			<!-- Midiel: The navbar was created using bootstrap 4.3.1 
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
			-->

			<!-- to include bootstrap 4.3.1 locally -->
			<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
			

			<!-- Midiel: <head> from Yasmany's login and Register pages
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
			
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
			<link rel="stylesheet" href="css/geektext-lr.css">
			-->

			<!-- Midiel: From Gersch's index.php page
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
			-->

			<!-- Midiel: This contradicts with registe.php and login.php
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
			
			
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="js/indexThumbnails.js"></script>
			<link rel="stylesheet" type="text/css" href="css/index.css">
			-->
			
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


		</head>

	<!-- open tag for <body>, closing tag located in footer.php -->	
	<body>

		<?php 

			// Midiel: You need the includes/config.php file for narvbar.php to function. See config.php
			//require_once('includes/config.php');

			// include the navbar for all pages
			require('navbar.php');

			// start sessions for all pages
			//session_start();
		
		?>
