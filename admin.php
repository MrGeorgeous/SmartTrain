<?php

require_once('cms.php');

if (isset($_POST['login']) && isset($_POST['password'])) {
	$login = w($link, $_POST['login']);
	$password = w($link, $_POST['password']);
	$hash = md5('smarttrain' . $login . '!' . $password . time() . '123');
	mysqli_query($link, "UPDATE `users` SET `hash`='$hash' WHERE `login`='$login' AND `password`='$password';");
	setcookie('hash', $hash);
	header('Location: admin.php');
}

if (isAuthed($link)) {
	if (isset($_GET['act'])) {
		$act = $_GET['act'];
		if ($act == 'new') {
			if (isset($_POST['testname']) && isset($_POST['testnick'])) {
				$nick = w($link, $_POST['testnick']);
				$name = w($link, $_POST['testname']);
				if (($nick == '')||($name == '')) {
					header('Location: admin.php?act=new&error');
				}
				mysqli_query($link, "INSERT INTO `tests` (`id`, `nick`, `name`, `disablechoice`, `data`, `active`) VALUES (NULL, '$nick', '$name', '0', '[{\"q\":\"Варианты?\",\"img\":\"\",\"as\":[\"В1\",\"В2\",\"В3\",\"В4\"]},{\"q\":\"Слово?\",\"img\":\"\",\"as\":[\"Слово\"]}]', '1');");
				header('Location: admin.php?act=test&name=' . $nick);
			} else {
				$error = 'none';
				if (isset($_GET['error'])) { $error = 'inline'; }
				$html = <<<_END
					<div class="cnt"><h2 style="padding-bottom: 20px; margin-top: -30px;">Создать тест</h2>
					<form action="admin.php?act=new" method="post">
						<div class="form-group">
							<div class="alert alert-danger" style="max-width: 30vw; display: $error;" role="alert">
							  	<strong>Ошибка</strong> Заполните все поля!
							</div><br>
						</div>
						<div class="form-group">
					    	<input type="text" class="form-control" id="testname" placeholder="Название" name="testname" autocomplete="off">
					  	</div>
					  	<div class="form-group">
					    	<input type="text" class="form-control" id="testnick" placeholder="Псевдоним" name="testnick" autocomplete="off">
					     	<label style="padding-top: 9px;"><span data-toggle="popover" data-container="body" title="Что такое псевдоним?" data-content="Псевдоним – это англоязычное название теста без пробелов и знаков препинания. Его будет невозможно изменить в дальнейшем. Например, AncientWorld.">Как выбрать псевдоним?</span></label>
					  	</div> 
					  	<button type="submit" class="btn btn-primary" style="margin-top: 18px; width: 100%;">Далее</button>
					  	<a class="btn btn-secondary" style="margin-top: 18px; width: 100%;" href="admin.php">Назад</a>
					</form>
					</div>
_END;

			html($html, false);
			}
		}

		if ($act == 'test') {

			if (isset($_GET['name']) === false) { header('Location: admin.php'); }

			if (isset($_POST['testname']) && isset($_POST['data'])) {
				$nick = w($link, $_GET['name']);
				$testname = w($link, $_POST['testname']);
				if (isset($_POST['choice'])) { $choice = (w($link, $_POST['choice']) == 'on') ? 1 : 0; } else { $choice = 0;}
				if (isset($_POST['notshow'])) { $notshow = (w($link, $_POST['notshow']) == 'on') ? 0 : 1; } else { $notshow = 1;}
				$data = w($link, addslashes($_POST['data']));

				//die($data);
				//die(addslashes($_POST['data']));

				//die("UPDATE `tests` SET `name`='$testname', `disablechoice`=$choice, `active`=$notshow, `data`='$data' WHERE `nick`='$nick';");
				//die($nick . '/' . $testname . '/' . $choice . '/' . $notshow . '/' . $data);
				if (mysqli_query($link, "UPDATE `tests` SET `name`='$testname', `disablechoice`=$choice, `active`=$notshow, `data`='$data' WHERE `nick`='$nick';") !== FALSE) {
					header('Location: admin.php?act=test&name=' . $nick . '&success');
				} else{
					die('Ошибка. Обратитесь в поддержку.');
				}
			}

			$success = 'none'; if (isset($_GET['success'])) { $success = 'inline'; }
			$test = getTest($link, $_GET['name']);
			$testname = $test['name'];
			$testnick = $test['nick'];
			$disablechoice = ($test['disablechoice'] == 1) ? 'checked' : '';
			$notshow = ($test['active'] == 1) ? '' : 'checked';
			$testdataof = stripslashes($test['data']);

			$autocomplete = array();

			if (file_exists('./content/' . $_GET['name'])) {
    			$autocomplete = scandir('./content/' . $_GET['name']);
				array_shift($autocomplete); array_shift($autocomplete);
			}
            $photos = '';
            for ($i=0; $i < count($autocomplete); $i++) {
                $j = $i + 1; $na = $autocomplete[$i]; $linky = 'http://smarttrain.mrgeorgeous.com/content/' . $_GET['name'] . '/' . $na;
                $photos .= <<<_END
    <tr>
      <th scope="row">$j</th>
      <td>$na</td>
      <td><a href="$linky" target="_blank">см.</a></td>
    </tr>                
_END;
            }
            
			$autocomplete = json_encode($autocomplete);
		


			$html = <<<_END


						<link href="assets/json/jsoneditor.css" rel="stylesheet" type="text/css">
						<script src="assets/json/jsoneditor.js.php"></script>

					<div class="cnt"><h2 style="padding-bottom: 20px; margin-top: -30px;">$testname</h2>
					<form action="admin.php?act=test&name=$testnick" method="post" onsubmit="onSubmit();" style="width: 700px;  " class="container">
						<div class="form-group" style="display: $success;">
							<div class="alert alert-success" style="" role="alert">
							  	<strong>OK</strong> Настройки сохранены
							</div><br>
						</div>
						<div class="form-group">
					    	<input type="text" class="form-control" id="testname" placeholder="Название" name="testname" value="$testname" autocomplete="off">
					  	</div>
					  	<div class="form-check">
					  		<label class="form-check-label" style="letter-spacing: normal;">
							    <input class="form-check-input" type="checkbox" style="all: revert; margin-right: 10px;" name="choice" $disablechoice>
							    Убрать варианты ответов из вопросов
							 </label>
					  	</div>
					  	<div class="form-check">
					  		<label class="form-check-label" style="letter-spacing: normal;">
							    <input class="form-check-input" type="checkbox" style="all: revert; margin-right: 10px;" name="notshow" $notshow>
							    Не показывать в списке
							 </label>
					  	</div>

						<!-- <div class="row">
						    <div class="col"><button type="submit" class="btn btn-primary" style="margin-top: 18px; width: 90%;">Сохранить</button></div>
						    <div class="col"><button class="btn btn-secondary" style="margin-top: 18px; width: 90%;" id="adminback">Назад</button></div>
						</div> -->

<div class="form-group">
					     	<label style="padding-top: 9px;"><span data-toggle="popover" data-container="body" title="Как указать картинку?" data-content="Начните вводить название картинки в поле img. Все варианты появятся в окошке. Список всех загруженных картинок находится ниже.">Как указать картинку?</span></label>
					  	</div>
					  	
						<input type="hidden" value="" name="data" id="source"/>

						
						<div id="testdata" style="width: 100%; margin-top: 30px; margin-bottom: 15px;  text-transform: none;"></div>
						<p>Дополнительные настройки: <b onclick="editor.setMode('tree');">Дерево</b>/<b onclick="editor.setMode('code');">Код</b>. </p>
						<button type="submit" class="btn btn-primary" style="margin-top: 9px; width: 30vw;">Сохранить</button>
						


					</form>
					<button class="btn btn-secondary" style="margin-top: 20px; width: 30vw;" id="adminback">Назад</button>


					<form target="_blank" action="admin.php?act=upload&name=$testnick" method="post" enctype="multipart/form-data" style="margin-top: 50px;">
					  Добавить новые картинки (в новой вкладке):<br />
						<input name="upload[]"  type="file" multiple="multiple" style="text-align:center;"/>
					  	<button type="submit" class="btn btn-primary" style="margin-top: 9px; width: 150px;">Загрузить</button>
					</form>
					
					

					
					<table class="table" style="width:300px; margin-top:50px;">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Имя файла</th>
      <th scope="col">Просмотр (в новой вкладке)</th>
    </tr>
  </thead>
  <tbody>
    $photos
</tbody>
</table>
						


						
						<script>
							$('#adminback').click(function() {
								window.location = window.location.href.split("?")[0];
							});
							
							var container = document.getElementById("testdata");
        					 var options = {
							    autocomplete: {
							      getOptions: function () {
							        return $autocomplete;
							      }
							    }
							 };
							var editor = new JSONEditor(container, options);

							var dataof = $testdataof;

							editor.set(dataof);

							function onSubmit() {
								$('#source').val(JSON.stringify(editor.get()));
							}

							editor.setName('Вопросы');

							editor._onChange(function () {
								editor.refresh();
							});

						</script>
					</div>
_END;

			html($html, false);
			
		}

		if ($act == 'upload') {

			if (getTest($link, $_GET['name'])['nick'] != $_GET['name']) {
				html('Такого теста не существует.', false);
			}

			if (!file_exists('./content/' . $_GET['name'])) {
    			mkdir('./content/' . $_GET['name'], 0777, true);
			}

			$total = count($_FILES['upload']['name']);
			for($i=0; $i<$total; $i++) {
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
				if ($tmpFilePath != ""){
				    $newFilePath = "./" . 'content/' . $_GET['name'] . '/' . $_FILES['upload']['name'][$i];
					if(move_uploaded_file($tmpFilePath, $newFilePath)) {
				    } else {
				    	html('Не получилось загрузить файлы. Обратитесь в поддержку.', false);
				    }
				 }
			}

			html('Все файлы (' . $total . ') были успешно загружены для теста "' . getTest($link, $_GET['name'])['name'] . '". <br> Обновите предыдущую страницу, чтобы при вводе имени изображения появлялись имена возможных файлов.', false);

		}

		if ($act == 'logout') {
			mysqli_query($link, "UPDATE `users` SET `hash`='' WHERE `login`='$login' AND `password`='$password';");
			setcookie('hash', '');
			header('Location: /');
		}


	} else {
		$content = '';
		$tests = getTests($link, false);
		for ($i=0; $i < count($tests); $i++) {
			$content .= '<a style="margin-top: 5px;" class="btn btn-secondary" href="admin.php?act=test&name=' . $tests[$i]['nick'] . '">' . $tests[$i]['name'] . '</a><br>';
		}
		$content .= '<a style="margin-top: 5px;" class="btn btn-link" href="admin.php?act=new">Создать тест</a><br>';
		html('<div class="cnt"><h2 style="padding-bottom: 10px; margin-top: -30px;">Тесты</h2>' . $content . '</div><hr><a href="admin.php?act=logout">Выйти из ' . getAccount($link)['login'] . '</a>', false);
	}
	

} else {
	$login = <<<_END
<img class="logo" src="images/logo.png">
<div class="cnt"><h2 style="padding-bottom: 20px; margin-top: -30px;">Администратор</h2>
<form action="admin.php" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="login" placeholder="Логин" name="login">
  </div>
  <div class="form-group">
    <input type="password" class="form-control" id="password" placeholder="Пароль" name="password">
  </div>
  <button type="submit" class="btn btn-primary" style="margin-top: 18px; width: 100%;">Войти</button>
</form>
<a href="/">к тестам</a>
</div>
_END;

	html($login, true);

}

function isAuthed($link) {
	if (!isset($_COOKIE["hash"])) { return false; }
	$hash = w($link, $_COOKIE["hash"]);
	if ($hash == '') { return false; }
	return (mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `users` WHERE `hash`='$hash';"))['hash'] != NULL);
}

function getAccount($link) {
	$hash = w($link, $_COOKIE["hash"]);
	return (mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `users` WHERE `hash`='$hash';")));
}

?>