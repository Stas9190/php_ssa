<!-- Базовый шаблон -->
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title></title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	</head>
	<body>	
		<?php 
			// включение контента страницы
			if ($CONTENT != "") 
			{
				if ($TYPE_CONTENT == "html")
					echo $CONTENT;
				else if ($TYPE_CONTENT == "file")
					require $CONTENT;
				else
					echo 'Тип содержимого контента не установлен!';
			}
		?>
	</body>
</html>