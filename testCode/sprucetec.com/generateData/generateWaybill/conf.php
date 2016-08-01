<?php
// required = true
define("LOG_ECHO", 1);
define("LOG_FILE", 2);

return [
	// required = true
	"dbs" => [
		"tmc_test" => [
			"dsn" 	=> "mysql:host=127.0.0.1;port=3306;dbname=tmc;charset=utf8;",
			"user"	=> "root",
			"pwd"	=> "root",
		],
		"tmc" => [
			"dsn" 	=> "mysql:host=127.0.0.1;port=3306;dbname=tmc;charset=utf8;",
			"user"	=> "root",
			"pwd"	=> "root",
		],
		"tms" => [
			"dsn" 	=> "mysql:host=127.0.0.1;port=3306;dbname=tms;charset=utf8;",
			"user"	=> "root",
			"pwd"	=> "root",
		],
		"basic" => [
			"dsn" 	=> "mysql:host=127.0.0.1;port=3306;dbname=tms_basic;charset=utf8;",
			"user"	=> "root",
			"pwd"	=> "root",
		],
	],
	// required = true
	"log" => [
		"io" => LOG_ECHO | LOG_FILE,
		"file"  => __DIR__ . "/log",
	],
];
