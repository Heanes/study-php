<?php
$title = '。 标题(title): ' . $_POST ['title'];
$name = '早上好，' . $_POST ['name'];
$content = '。 内容(content): ' . $_POST ['content'];

$arr = array (
		'title' => $title,
		'name' => $name,
		'content' => $content 
);

$string = json_encode ( $arr );

echo $string;

