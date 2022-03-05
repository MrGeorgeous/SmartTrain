<?php

require_once('cms.php');
//header('Content-Type: text/html; charset=utf-8');

$content = '';

if (isset($_GET['test'])) {
	$test = getTest($link, $_GET['test']);
	if ($test === false) {
		html('<div class="cnt"><h2 style="padding-bottom: 10px; margin-top: -30px;">SmartTrain</h2>Такого теста не существует.</div>', false);
	}
	$testname = $test['name'];
	$testnick = $test['nick'];
	$disableChoice = $test['disablechoice'];
	$testdata = stripslashes($test['data']);
	$html = <<<_END
<div class="container" id="kiss">
<div class="cnt"><h2 style="padding-bottom: 10px; margin-top: -30px;"><a href="/"> $testname </a></h2></div>
<div class="gallery">
	<img class="img-thumbnail" src="">
</div>
<p id="question" style="padding-top:18px;"></p>
<div class="answerbox">
	<div id="variants"></div>
	<div id="fieldbox"><input type="text" id="textanswer" placeholder="Нажмите enter для отправки" /></div>
</div>
<hr>
<div class="navigation" style="padding-bottom: 0px;">
	<div class="row">
    <div class="col">
      <div id="progress">0/0</div>
    </div>
    <div class="col">
      <div id="time">0:00</div>
    </div>
    <div class="col">
      <div id="correctpercentage">100%</div>
    </div>
 	</div>
</div>
</div>

<script>

var stop = false;
var i = 0;
function go () {
	if (stop) { return }
    $("#time").html(~~(i/60) + ":" + "<span style=\"font-size: 0pt;\">&#x2006;</span>" + (i%60<10 ? "0" : "")+ (i%60));
    i++;
    setTimeout(go, 1000);
}

function startTimer() {
	stop = false;
	go();
}
function stopTimer() {
	stop = true;
}

function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex;

  // While there remain elements to shuffle...
  while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }

  return array;
}

function preloadImages(array) {
    if (!preloadImages.list) {
        preloadImages.list = [];
    }
    var list = preloadImages.list;
    for (var i = 0; i < array.length; i++) {
        var img = new Image();
        img.onload = function() {
            var index = list.indexOf(this);
            if (index !== -1) {
                // remove image from the array once it's loaded
                // for memory consumption reasons
                list.splice(index, 1);
            }
        }
        list.push(img);
        img.src = array[i];
    }
}


var name = "$testnick";
var Gdata = $testdata;
var disableChoice = $disableChoice;

var q = 0;
var correct = 0;
var right = "";
var currcorr = true;
var data = Object();


var pics = [];
$.each(Gdata, function(i, item) {
	pics.push(item.img);
});
preloadImages(pics);

function startTest() {

	$("#fieldbox").hide();
	if (Gdata.length == 0) {
		$(".gallery").detach();
		$("#question").html('У теста пока нет вопросов.');
		return;
	}

	startTimer();
	$("#correctpercentage").html('100%');
	$("#time").html('0:00');

	data = shuffle(Gdata);

	q = -1; correct = 0; right = ""; currcorr = true; i=0;
	loadQuestion(data[q+1]);
}

function loadQuestion(json) {

	q+=1;

	currcorr = true;
	$("#progress").html((q+1) + '/' + data.length);
	$("#question").show();
	$("#textanswer").attr("placeholder", 'Нажмите enter для отправки');
	$("#fieldbox").hide();
	$("#textanswer").val('');
	$("#variants").html('');
	$(".img-thumbnail").attr("src", '');
	$(".img-thumbnail").attr("src", 'content/' + name + '/' + json.img);
	$("#question").html(json.q);
	right = json.as[0];
	if ((json.as.length == 1) || (disableChoice)) {
		$("#fieldbox").show();
		$('#textanswer').keypress(function(event){
        	var keycode = (event.keyCode ? event.keyCode : event.which);
        	if(keycode == '13'){ submitAnswer($('#textanswer').val()); }
    	});
		/*$('#textanswer').submit(function() {
			submitAnswer($('#textanswer').val());
		});*/
	} else {
		$.each(shuffle(json.as), function(i, item) {
    		$("#variants").append("<button type=\"button\" class=\"btn btn-secondary answerbutton\" id=\"variant" + (i+1) + "\">" + item + "</button>"); 
    	});
		$(".answerbutton").click(function() {
		  	submitAnswer($(this).text());
		});
	}
}

function submitAnswer(a) {
	if (a == '') { return; }
	if (a.toLowerCase() == right.toLowerCase()) {
		if (currcorr) { correct+=1; $("#correctpercentage").html(Math.round(100 * correct / (q + 1)) + "%");}
		if ((q+1) != data.length) {
			loadQuestion(data[q+1]);
		} else {
			finishTest();
		}
	} else {
		$("#correctpercentage").html(Math.round(100 * correct / (q+1)) + "%");
		currcorr = false;
		$(".answerbutton").each(function(index) {
			if ($(this).text() == a) { $(this).addClass("btn-danger"); $(this).removeClass("btn-secondary"); }
			if ($(this).text() == right) { $(this).addClass("btn-success"); $(this).removeClass("btn-secondary"); }
		});
		$("#textanswer").val('');
		$("#textanswer").addClass("shake");
		setTimeout(function() { $("#textanswer").removeClass("shake"); }, 200);
		$("#textanswer").attr("placeholder", right);
	}
}

function finishTest() {
	stopTimer();
	
	/* This thing shows the level of progress */
	/* $("#progress").html((q+1) + '/' + data.length); */
	$("#progress").html(correct + '/' + data.length);


	var result = correct / (data.length);
	var status = 0;
	if (result >= 0.9) { status = 1;}
	if ((result < 0.9) && (result >= 0.66)) { status = 2;}
	if ((result < 0.66) && (result >= 0.35)) { status = 3;}
	if (result < 0.35) { status = 4;}
	$(".img-thumbnail").attr("src", '');
	$(".img-thumbnail").attr("src", 'images/gifs/' + status + '-' + (Math.floor(Math.random()*4) + 1) + '.gif');

	q=data.length;

	$("#fieldbox").hide();
	$("#question").hide();
	$("#variants").html('');
	$("#variants").show();
	$("#variants").append("<button type=\"button\" class=\"btn btn-primary\" id=\"retry\" onclick=\"window.location.reload(false); \">Ещё раз</button>");
	$("#variants").append("<button type=\"button\" class=\"btn btn-danger\" id=\"exit\">Закончить</button>"); 
	$("#exit").click(function() {
		  window.location.replace('http://smarttrain.mrgeorgeous.com');
	});
}

startTest();

</script>


_END;
	html($html, false);
} else {
	$tests = getTests($link, true);
	for ($i=0; $i < count($tests); $i++) {

		if (count(json_decode(stripslashes($tests[$i]['data']), true)) == 0) {
			$content .= '<a style="margin-top: 5px; font-size: 8pt;" class="btn btn-secondary" href="?test=' . $tests[$i]['nick'] . '">' . $tests[$i]['name'] . '</a><br>';
		} else {
			$content .= '<a style="margin-top: 5px;" class="btn btn-secondary" href="?test=' . $tests[$i]['nick'] . '">' . $tests[$i]['name'] . '</a><br>';
		}
		
	}
	html('<img class="logo" src="images/logo.png"><div class="cnt"><h2 style="padding-bottom: 10px; margin-top: -30px;">SmartTrain</h2>' . $content . '</div>', true);
}


?>