<?php
	
use CodebasehqTelegramBot\Vscale\VscaleStatusUpdater;
	
require_once 'vendor/autoload.php';
require_once 'config/vscale.php';

VscaleStatusUpdater::notifyOnLowBalanceIfNeeded();