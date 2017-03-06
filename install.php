<?php
	
	/* Fast&dirty installation */
		
	require_once "config/codebasehq.php";
	require_once "config/telegram.php";
	require_once "config/database.php";	
	
	require_once "install/sql_import.php";
	
	function checkConfig() {
		
		function codebasehq() {
			if(empty(CODEBASE_API_KEY) 
				|| empty(CODEBASE_ACCOUNT_NAME) 
				|| empty(CODEBASE_USER_NAME)
			)
				throw new Exception("Please configure CodebaseHQ account in config/codebasehq.php");
				
			echo "CodebaseHQ account configured<br/>";
		}
		
		function telegram() {
			if(empty(TELEGRAM_API_KEY) 
				|| empty(TELEGRAM_BOT_NAME) 
				|| empty(TELEGRAM_HOOK_URL) || TELEGRAM_HOOK_URL == "https://{BOT_URL}/hook_telegram.php"
				|| empty(TELEGRAM_ADMIN_USERNAME) || TELEGRAM_ADMIN_USERNAME == "@username"
			)
				throw new Exception("Please configure your Telegram bot in config/telegram.php");
				
			echo "Telegram bot configured<br/>";
		}
		
		function database() {
			if(empty(DB_HOST) 
				|| empty(DB_USER) 
				|| empty(DB_PASSWORD)
				|| empty(DB_NAME)
				|| empty(DB_PORT)
			)
				throw new Exception("Please configure database in config/database.php");
				
			echo "Database configured<br/>";
		}
		
		codebasehq();
		telegram();
		database();
	}
	
	
	function deployDatabase() {
		
		$sql_file_name = "2017-02-28.sql";
		
		try {
			$dbh = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			PDODbImporter::importSQL('install/'.$sql_file_name, $dbh);
		} catch (PDOException $e) {
		    print "Error copying database: " . $e->getMessage() . "<br/>";
		    die();
		}
		
		echo "Database (probably) copied<br>";
	}
	
	
	function setupTelegramHook() {
		
// 		require "telegram_hook_unset.php";
		require "telegram_hook_set.php";
		echo "<br>";
	}
	
	function fetchCodebaseHQstuff() {
		
		echo "CodebaseHQ stuff loaded: ";
		require "codebasehq_update_projects.php";
		echo "<br>";
	}
	
	
	
	echo "<style>pre { display:inline; }</style>";
	
	checkConfig();
	deployDatabase();
	setupTelegramHook();
	fetchCodebaseHQstuff();
	
	echo "<br>You're all set<br>";
	echo "Try <pre>/start</pre> your bot!";
	
	echo "<br><br>Also, REMOVE <pre>install.php</pre>, <pre>install/</pre>, optionally <pre>telegram_hook_set.php</pre> & <pre>telegram_hook_unset.php</pre>";
	echo "<br>You can add <pre>codebasehq_update_projects.php</pre> to a CRON job to refresh your users subscriptions once in a while";
	