<?php
class Logger implements \Psr\Log\LoggerInterface {
	protected $log = true;

	public function __construct($log) {
		$this->setLog($log);
	}

	public function setLog($log) {
		$this->log = $log;
		return $this;
	}

	public function emergency($message, array $context = []) {
	}

	public function alert($message, array $context = []) {
	}

	public function critical($message, array $context = []) {
	}

	public function error($message, array $context = []) {
	}

	public function warning($message, array $context = []) {
	}

	public function notice($message, array $context = []) {
	}

	public function info($message, array $context = []) {
	}

	public function debug($message, array $context = []) {
	}

	public function log($level, $message, array $context = []) {
		if(!$this->log)
			return;

		switch($level) {
			case \Psr\Log\LogLevel::EMERGENCY:
				$this->emergency($message, $context);
				break;
			case \Psr\Log\LogLevel::ALERT:
				$this->alert($message, $context);
				break;
			case \Psr\Log\LogLevel::CRITICAL:
				$this->critical($message, $context);
				break;
			case \Psr\Log\LogLevel::ERROR:
				$this->error($message, $context);
				break;
			case \Psr\Log\LogLevel::WARNING:
				$this->warning($message, $context);
				break;
			case \Psr\Log\LogLevel::NOTICE:
				$this->notice($message, $context);
				break;
			case \Psr\Log\LogLevel::INFO:
				$this->info($message, $context);
				break;
			case \Psr\Log\LogLevel::DEBUG:
				$this->debug($message, $context);
				break;
		}
	}
}