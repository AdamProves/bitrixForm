<?php
require __DIR__ . '/bootstrap.php';
$dsn = sprintf('mysql:dbname=%s;host=127.0.0.1', $config['database']);
$fuck = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_CASE => PDO::CASE_LOWER]);
$sql = 'SELECT iin FROM baccalaureate';
foreach ($fuck->query($sql) as $row) { 
	// Получение и обработка данных без 0 на данные с 0
	$i = 0;
	$text = $row['iin'];
	$text0 = $row['iin'];
	$test = strlen($text);
	$good = 12 - $test;
	for ($i = 0; $i < $good; 	$i++){
		$text = '0' . $text;
	}
	$new = "UPDATE baccalaureate SET iin = '$text' WHERE iin = '$text0'";
	$fuck->query($new); 
}
// alter table workers modify id varchar(10); Для изменения данных на string
?>