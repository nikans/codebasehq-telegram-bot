<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Dictionary;
	
class TicketField {
	
	private static $fields = [
		
	];
	
	private static $changes = [
		'status_id' => "Status",
		'assignee_id' => "Assignee",
		'estimated_time_string' => "Estimated Time",
		'subject' => "Subject",
		'ticket_type_id' => "Type",
		'sprint_ids' => "Sprints",
		'priority_id' => "Priority",
		'category_id' => "Category",
		'milestone_id' => "Milestone"
	];
	
	public static function changeLabel($change) {
		return isset(self::$changes[$change]) ? self::$changes[$change] : "Some field";
	}
	
}