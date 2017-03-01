<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Event;
	
abstract class Event {
	
	public $json;
	public $raw;
	public $project_name = null;
	public $codebasehq_actor_username = null;
	public $codebasehq_subject_username = null;
	
	function __construct($event_json, $event_raw = null) {
		$this->json = $event_json;
		$this->raw = $event_raw;
	}
	
	public function payload() {
		return $this->json->payload;
	}
	
	public function formatted() {
		return json_encode($this->json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}
	
	public function prettyJson() {
		return json_encode($this->json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}
	
	public function projectNameFromUrl($url) {
		$project_url_path = parse_url($url, PHP_URL_PATH);
		$project_url_components = explode('/', $project_url_path);
		$project_short_name = end($project_url_components);
		$project_short_name = str_replace("-", "_", $project_short_name);
		return $project_short_name;
	}
	
}