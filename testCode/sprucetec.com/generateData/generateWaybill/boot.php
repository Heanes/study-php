<?php
declare(ticks = 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set("memory_limit", "2058M");
date_default_timezone_set("PRC");

class _ {
	private static $app = null;
	private $dbs = [];
	private static $conf;

	public static function shutdownHandler() {
		$msg = "Exit ";
		if($error = error_get_last()){
			$msg .= "[error] " . var_export($error, 1);
		}
		self::log($msg);
	}

	public static function signalHandler() {
		if($signo == 14){
			// 忽略alarm信号 SIGALRM
			$msg = "Ignore alarm signo[{$signo}]";
			self::log($msg);
		}else{
			$msg = "Exit signo[{$signo}]";
			self::log($msg);
			exit();
		}
	}

	private static function ping(PDO $conn) {
		return ($conn && $conn->query("SELECT 1"));
	}

	private static function connect($dsn, $user, $pwd) {
		try {
			$conn = new PDO($dsn, $user, $pwd);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch (PDOException $e) {
			self::log("Connection {$dsn};user:{$user};pwd:{$pwd}; failed: " . $e->getMessage());
			exit();
		}
	}

	public static function boot() {
		self::app();
	}

	public static function app() {
		if(self::$app) {
			return self::$app;
		}

		self::$conf = require(__DIR__ . "/conf.php");

		register_shutdown_function("_::shutdownHandler");

		if(defined('SIGHUP')) {
			$sigarr = [SIGTERM, SIGHUP, SIGINT, SIGQUIT, SIGILL, SIGPIPE, SIGALRM];
			foreach ($sigarr as $sig) {
				pcntl_signal($sig, "_::signalHandler");
			}
		}
		self::$app = new static();

		return self::$app;
	}

	public static function log($msg) {
		$time = date("Y-m-d H:i:s");
		$msg = "[{$time}] {$msg}" . PHP_EOL;

		$io = self::$conf["log"]["io"];
		if ((LOG_ECHO & $io) === LOG_ECHO) {
			echo $msg, PHP_EOL;
		}
		if ((LOG_FILE & $io)  === LOG_FILE) {
			file_put_contents(self::$conf["log"]["file"], $msg, FILE_APPEND | LOCK_EX);
		}
	}

	public function db($name) {
		if(!isset(self::$conf["dbs"][$name])) {
			exit("db {$name} not set");
		}
		if(!isset($this->dbs[$name]) || self::ping($this->dbs[$name]) === false) {
			$dbConf = self::$conf["dbs"][$name];
			$this->dbs[$name] = self::connect($dbConf["dsn"], $dbConf["user"], $dbConf["pwd"]);
		}
		return $this->dbs[$name];
	}
}


_::boot();
