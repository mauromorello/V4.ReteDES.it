<?php


$stmt = $db->prepare("SELECT COUNT(*) FROM maaking_users WHERE isactive=1;");
$stmt->execute();
$row = $stmt->fetch();
$totale_utenti = $row[0];

$stmt = $db->prepare("SELECT COUNT(*) FROM retegas_gas WHERE id_des>0;");
$stmt->execute();
$row = $stmt->fetch();
$totale_gas = $row[0];

$stmt = $db->prepare("SELECT COUNT(*) FROM retegas_ordini;");
$stmt->execute();
$row = $stmt->fetch();
$totale_ordini = $row[0];

$stmt = $db->prepare("SELECT Sum(retegas_dettaglio_ordini.qta_arr*retegas_dettaglio_ordini.prz_dett_arr) FROM retegas_dettaglio_ordini");
$stmt->execute();
$row = $stmt->fetch();
$totale_netto = round($row[0]);

$stmt = $db->prepare("SELECT COUNT(*) FROM retegas_ditte;");
$stmt->execute();
$row = $stmt->fetch();
$totale_ditte = $row[0];

$stmt = $db->prepare("SELECT COUNT(maaking_users.fullname) FROM maaking_users WHERE (time_to_sec(timediff(now(),maaking_users.last_activity))/60)<2;");
$stmt->execute();
$row = $stmt->fetch();
$row[0]==1 ? $user_online= "Ci sei solo tu, ma..." : $user_online= $row[0]." utenti, e intanto...";


$notifications = notifications();


?>
<!DOCTYPE html>
<html lang="it-IT" <?php echo implode(' ', array_map(function($prop, $value) {
			return $prop.'="'.$value.'"';
		}, array_keys($page_html_prop), $page_html_prop)) ;?>>
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title>ReteDES.it : <?php echo $page_title != "" ? $page_title : ""; ?></title>
		<meta name="description" content="Il gestionale Artigianale che unisce i GAS e le reti di GAS">
		<meta name="author" content="mimmoz01">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/smartadmin-production.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/smartadmin-skins.min.css">

		<!-- SmartAdmin RTL Support is under construction-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/smartadmin-rtl.min.css">

        <!-- TABLESORTER-->
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/js_rd4/plugin/tablesorter/css/theme.bootstrap.css">

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/your_style.css"> -->

		<?php

			if ($page_css) {
				foreach ($page_css as $css) {
					echo '<link rel="stylesheet" type="text/css" media="screen" href="'.ASSETS_URL.'/css/'.$css.'">';
				}
			}
		?>


		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_URL; ?>/css/demo.min.css">

		<!-- FAVICONS -->
		<link rel="shortcut icon" href="<?php echo ASSETS_URL; ?>/img/favicon/php399.ico" type="image/x-icon">
		<link rel="icon" href="<?php echo ASSETS_URL; ?>/img/favicon/php399.ico" type="image/x-icon">

		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

		<!-- Specifying a Webpage Icon for Web Clip
			 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
		<link rel="apple-touch-icon" href="<?php echo ASSETS_URL; ?>/img/splash/sptouch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo ASSETS_URL; ?>/img/splash/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo ASSETS_URL; ?>/img/splash/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo ASSETS_URL; ?>/img/splash/touch-icon-ipad-retina.png">

		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<!-- Startup image for web apps -->
		<link rel="apple-touch-startup-image" href="<?php echo ASSETS_URL; ?>/img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
		<link rel="apple-touch-startup-image" href="<?php echo ASSETS_URL; ?>/img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
		<link rel="apple-touch-startup-image" href="<?php echo ASSETS_URL; ?>/img/splash/iphone.png" media="screen and (max-device-width: 320px)">
	</head>
	<body <?php echo implode(' ', array_map(function($prop, $value) {
			return $prop.'="'.$value.'"';
		}, array_keys($page_body_prop), $page_body_prop)) ;?>>
		<!-- POSSIBLE CLASSES: minified, fixed-ribbon, fixed-header, fixed-width
			 You can also add different skin classes such as "smart-skin-1", "smart-skin-2" etc...-->
		<?php
			if (!$no_main_header) {

		?>
				<!-- HEADER -->
				<header id="header">
					<div id="logo-group">

						<!-- PLACE YOUR LOGO HERE -->
						<span id="logo"> <img src="<?php echo ASSETS_URL; ?>/img/logo_rd4.png" alt="ReteDES.it V4"> </span>
						<!-- END LOGO PLACEHOLDER -->

						<!-- Note: The activity badge color changes when clicked and resets the number to 0
						Suggestion: You may want to set a flag when this happens to tick off all checked messages / notifications -->
                        <?php if($notifications>0){ ?>
                        <span id="activity" class="activity-dropdown"> <i class="fa fa-user"></i> <b class="badge"> <?php echo $notifications; ?> </b> </span>

						<!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
						<div class="ajax-dropdown">

							<!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->

							<div class="btn-group btn-group-justified" data-toggle="buttons">
                                <label class="btn btn-default">
                                    <input type="radio" name="activity" id="<?php echo APP_URL; ?>/ajax/notify/tasks.html">
                                    ORDINI </label>
                                <label class="btn btn-default">
									<input type="radio" name="activity" id="<?php echo APP_URL; ?>/ajax/notify/mail.html">
									UTENTI </label>
								<label class="btn btn-default">
									<input type="radio" name="activity" id="<?php echo APP_URL; ?>/ajax/notify/notifications.html">
									CASSA </label>

							</div>

							<!-- notification content -->
							<div class="ajax-notifications custom-scroll">

								<div class="alert alert-transparent">
									<h4 class="text-center">Hai delle notifiche:</h4>
                                    <ul class="notification-body">
									    <li class="text-lg"><i class="fa fa-2x fa-user"></i>&nbsp;  sugli utenti o account: <strong><?php echo notifications_users(); ?></strong> </li>
                                        <li><i class="fa fa-2x fa-shopping-cart"></i>&nbsp;   sugli ordini: <strong><?php echo notifications_ordini(); ?></strong> </li>
                                        <li><i class="fa fa-2x fa-bank"></i>&nbsp;   sulla cassa: <strong><?php echo notifications_cassa(); ?></strong> </li>
                                    </ul>
                                </div>

							</div>
							<!-- end notification content -->

							<!-- footer: refresh area
							<span> Ultimo aggiornamento: 12/12/2013 9:43AM
								<button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
									<i class="fa fa-refresh"></i>
								</button> </span>
							<!-- end footer -->

						</div>
						<!-- END AJAX-DROPDOWN -->
                        <?php } //ENDIF NOTIFCATION?>
					</div>

					<!-- projects dropdown -->
					<div class="project-context hidden-xs">

						<span class="label txt-color-blue">Online adesso:</span>
						<span id="project-selector" class="popover-trigger-element dropdown-toggle" data-toggle="dropdown"><?php echo $user_online;?> <i class="fa fa-angle-down"></i></span>

						<!-- Suggestion: populate this list with fetch and push technique -->


						<ul class="dropdown-menu">
                            <li>
								<a href="javascript:void(0);">...ad oggi siamo <span class="font-xl"><?php echo $totale_utenti;?></span> famiglie, in <span class="font-xl"><?php echo $totale_gas;?></span> GAS;</a>
							</li>
							<li>
								<a href="javascript:void(0);">Insieme abbiamo gestito <span class="font-xl"><?php echo $totale_ordini;?></span> ordini da <span class="font-xl"><?php echo $totale_ditte;?></span> ditte,</a>
							</li>
							<li>
								<a href="javascript:void(0);">per un totale di <span class="font-xl"> € <?php echo $totale_netto;?></span>, tolti alla grande distribuzione;</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="javascript:void(0);"><i class="fa fa-power-off"></i> Chiudi</a>
							</li>
						</ul>
						<!-- end dropdown-menu-->

					</div>
					<!-- end projects dropdown -->

					<!-- pulled right: nav area -->
					<div class="pull-right">

						<!-- collapse menu button -->
						<div id="hide-menu" class="btn-header pull-right">
							<span> <a href="javascript:void(0);" title="Collapse Menu" data-action="toggleMenu"><i class="fa fa-reorder"></i></a> </span>
						</div>
						<!-- end collapse menu -->

						<!-- #MOBILE -->
						<!-- Top menu profile link : this shows only when top menu is active -->
						<ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
							<li class="">
								<a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown">
									<img src="<?php echo src_user(_USER_ID,64); ?>" alt="John Doe" class="online" />
								</a>
								<ul class="dropdown-menu pull-right">
									<li>
										<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-cog"></i> Setting</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="#ajax/profile.php" class="padding-10 padding-top-0 padding-bottom-0"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> Full <u>S</u>creen</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="login.php" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i> <strong><u>L</u>ogout</strong></a>
									</li>
								</ul>
							</li>
						</ul>

						<!-- logout button -->
						<div id="logout" class="btn-header transparent pull-right">
							<span> <a href="<?php echo APP_URL; ?>/login.php" title="Sign Out" data-action="userLogout" data-logout-msg="Per aumentare la tua privacy, una volta disconnesso chiudi il browser ;)"><i class="fa fa-sign-out"></i></a> </span>
						</div>
						<!-- end logout button -->

						<!-- search mobile button (this is hidden till mobile view port)
						<div id="search-mobile" class="btn-header transparent pull-right">
							<span> <a href="javascript:void(0)" title="Search"><i class="fa fa-search"></i></a> </span>
						</div>
						 end search mobile button -->

						<!-- input: search field
						<form action="#ajax/search.php" class="header-search pull-right">
							<input type="text" name="param" placeholder="Find reports and more" id="search-fld">
							<button type="submit">
								<i class="fa fa-search"></i>
							</button>
							<a href="javascript:void(0);" id="cancel-search-js" title="Cancel Search"><i class="fa fa-times"></i></a>
						</form>
						 end input: search field -->

						<!-- fullscreen button -->
						<div id="fullscreen" class="btn-header transparent pull-right">
							<span> <a href="javascript:void(0);" title="Full Screen" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i></a> </span>
						</div>
						<!-- end fullscreen button -->

						<!-- #Voice Command: Start Speech
						<div id="speech-btn" class="btn-header transparent pull-right hidden-sm hidden-xs">
							<div>
								<a href="javascript:void(0)" title="Voice Command" data-action="voiceCommand"><i class="fa fa-microphone"></i></a>
								<div class="popover bottom"><div class="arrow"></div>
									<div class="popover-content">
										<h4 class="vc-title">Voice command activated <br><small>Please speak clearly into the mic</small></h4>
										<h4 class="vc-title-error text-center">
											<i class="fa fa-microphone-slash"></i> Voice command failed
											<br><small class="txt-color-red">Must <strong>"Allow"</strong> Microphone</small>
											<br><small class="txt-color-red">Must have <strong>Internet Connection</strong></small>
										</h4>
										<a href="javascript:void(0);" class="btn btn-success" onclick="commands.help()">See Commands</a>
										<a href="javascript:void(0);" class="btn bg-color-purple txt-color-white" onclick="$('#speech-btn .popover').fadeOut(50);">Close Popup</a>
									</div>
								</div>
							</div>
						</div>
						 end voice command -->

						<!-- multiple lang dropdown : find all flags in the flags page

						<ul class="header-dropdown-list hidden-xs">
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-us" alt="United States"> <span> English (US) </span> <i class="fa fa-angle-down"></i> </a>
								<ul class="dropdown-menu pull-right">
									<li class="active">
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-us" alt="United States"> English (US)</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-fr" alt="France"> Français</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-es" alt="Spanish"> Español</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-de" alt="German"> Deutsch</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-jp" alt="Japan"> 日本語</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-cn" alt="China"> 中文</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-it" alt="Italy"> Italiano</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-pt" alt="Portugal"> Portugal</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-ru" alt="Russia"> Русский язык</a>
									</li>
									<li>
										<a href="javascript:void(0);"><img src="<?php echo ASSETS_URL; ?>img/blank.gif" class="flag flag-kp" alt="Korea"> 한국어</a>
									</li>
								</ul>
							</li>
						</ul>

						 end multiple lang -->

					</div>
					<!-- end pulled right: nav area -->

				</header>
				<!-- END HEADER -->

				<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
				Note: These tiles are completely responsive,
				you can add as many as you like
				-->
				<div id="shortcut">
					<ul>
						<li>
							<a href="#ajax/inbox.php" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-envelope fa-4x"></i> <span>Mail <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
						</li>
						<li>
							<a href="#ajax/calendar.php" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
						</li>
						<li>
							<a href="#ajax/gmap-xml.php" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
						</li>
						<li>
							<a href="#ajax/invoice.php" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Invoice <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
						</li>
						<li>
							<a href="#ajax/gallery.php" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
						</li>
						<li>
							<a href="#ajax/profile.php" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
						</li>
					</ul>
				</div>
				<!-- END SHORTCUT AREA -->

		<?php
			}
		?>