<?php

$link = mysqli_connect("127.0.0.1", "root", "root", "u279500796_smtrn");
mysqli_set_charset($link, 'utf8');
date_default_timezone_set('Europe/Moscow');

function getTests($link, $active) {
	if ($active) { $a = " WHERE `active`='1'"; } else { $a = '';}
	$tests = array();
	$result = mysqli_query($link, "SELECT * FROM `tests`$a;");
	while ($row = mysqli_fetch_assoc($result)) {
        $tests[] = $row;
    }
    return $tests;	
}

function getTest($link, $id) {
	$id = w($link, $id);
	$test = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `tests` WHERE `nick`='$id';"));
	if ($test == false) { return false; }
	//$test[0]['data'] = json_decode(stripslashes($test['data']), true);

	return $test;
}

function w($link, $x) {
    return mysqli_real_escape_string($link, $x);
}

/*function mysqli_fetch_all ($result, $result_type = MYSQLI_BOTH) {
	$rows = array();
    while ($row = mysqli_fetch_array($result, $result_type)) {
        $rows[] = $row;
    } 
    if (count($rows) == 0) { return false; }
    return $rows;
}*/

function html($content, $loader) {

	if ($loader === false) { $contentlogo = false; $logo = false;} else { $contentlogo = 'contentlogo'; $logo = 'logo';}

	$text = <<<_END

<!DOCTYPE HTML>
<!--
	Identity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
--> 
<html>
	<head>
		<title>SmartTrain by MrGeorgeous</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		<!--[if lte IE 8]><script src="assets/js/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

		<link href="assets/icons/font/css/open-iconic-bootstrap.css" rel="stylesheet">

		<style>

		#main {

		}
		a:link {
		    text-decoration: none;
		     color: black;
		    
		}

		a:visited {
		    text-decoration: none;
		    color: black;
		}

		a:hover {
		    text-decoration: none;
		    color: black;
		}

		a:active {
		    text-decoration: none;
		    color: black;
		}

		img {
			user-drag: none; 
			user-select: none;
			-moz-user-select: none;
			-webkit-user-drag: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}

		.cnt {
			position: relative;
		}

		.cnt {
			z-index: 1;
			animation-name: $contentlogo;
			animation-duration: 2s;
			-webkit-animation-name: $contentlogo;
			-webkit-animation-duration: 2s;
			opacity: 1.0;
		}

		.logo {
			opacity: 0.0;
			width: 200px;
			height: 200px;
			margin-left: -100px;
			margin-top: -100px;
			top: 50%;
			left: 50%;
			z-index: 10;
			position: absolute;
			animation-name: $logo;
			animation-duration: 2s;
			-webkit-animation-name: $logo;
			-webkit-animation-duration: 2s;
		}

		#footer {

			animation-name: $contentlogo;
			animation-duration: 2s;
			-webkit-animation-name: $contentlogo;
			-webkit-animation-duration: 2s;

		}

		@keyframes logo {
			0% {
				opacity: 1.0;
			}
			85% {
				opacity: 1.0;
			}
			100% {
				opacity: 0.0;
			}
		}

		@-webkit-keyframes logo {
			0% {
				opacity: 1.0;
			}
			85% {
				opacity: 1.0;
			}
			100% {
				opacity: 0.0;
			}
		}

		@keyframes contentlogo {
			0% {
				opacity: 0.0;
			}
			85% {
				opacity: 0.0;
			}
			100% {
				opacity: 1.0;
			}
		}

		@-webkit-keyframes contentlogo {
			0% {
				opacity: 0.0;
			}
			85% {
				opacity: 0.0;
			}
			100% {
				opacity: 1.0;
			}
		}

		.gallery {
			position: relative;
			margin-left: -50%;
			padding-top: 20px;
			padding-bottom: 9px;
			left: 50%;
			width: 100%;
			/*height: 40vh;*/
		}

		.gallery img {
			position: relative;
			width: auto;
			height: auto;
			max-height: 40vh !important;
    		overflow:hidden;
    		background: url(images/loading.gif) 50% no-repeat;
		}

		#kiss {
			width: 50vw;
			/*max-height: 80vh;*/
		}

		.col {
			min-width: 150px;
		}

		.btn-danger:hover, .btn-primary:hover {
			color: white !important;
		}

		.btn-secondary:hover {
			color: black !important;
		}


		#textanswer, textarea {
			text-align: center;
			text-transform: uppercase !important;
		}

		#textanswer::placeholder {
			opacity: 0.25;
		}

		@-webkit-keyframes shake {
		    8%, 41% {
		        -webkit-transform: translateX(-10px);
		    }
		    25%, 58% {
		        -webkit-transform: translateX(10px);
		    }
		    75% {
		        -webkit-transform: translateX(-5px);
		    }
		    92% {
		        -webkit-transform: translateX(5px);
		    }
		    0%, 100% {
		        -webkit-transform: translateX(0);
		    }
		}

		.shake {
			-webkit-animation: shake .2s linear;
		}

		.answerbutton, #retry, #exit {
			padding-left: 18px; margin-left: 5px; margin-top: 4px;
			padding-right: 18px; margin-right: 5px; margin-bottom: 4px;
		}

		input, label, .alert {
			text-align: center;
			text-transform: none;
		}

		table.jsoneditor-tree {
			min-height: 100%;
		}

		.jsoneditor-outer, .ace-jsoneditor {
			min-height: 40vh !important;
		}

		.jsoneditor-menu {
			min-width: 200px !important;
		}


		</style>

	</head>
	<body class="is-loading">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Main -->

					<section id="main">
						<noscript><h1>Для корректной работы сайта необходимо включить JavaScript!</h1></noscript>
						<div id="jschecked" style="display: none;">$content</div>
					</section>

				<!-- Footer -->
					<footer id="footer">
						<ul class="copyright">
							<li>&copy; MrGeorgeous</li><li>2018</li>
						</ul>
					</footer>

			</div>

		<!-- Scripts -->
			<!--[if lte IE 8]><script src="assets/js/respond.min.js"></script><![endif]-->
			<script>

				$('#jschecked').attr('style', '');

				if ('addEventListener' in window) {
					window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
					document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
				}
				setTimeout(
			    function() {
			    	 $(".logo").remove();
			    }, 2000);

			$(function () {
			  $('[data-toggle="popover"]').popover()
			})
			</script>

    </script>

	</body>
</html>


_END;

	header('Cache-Control: no-cache, no-store, must-revalidate; Pragma: no-cache; Expires: 0; Content-Type: text/html; charset=utf-8;');
	die($text);
}

?>