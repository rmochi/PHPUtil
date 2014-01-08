<?php
namespace rmochi\PHPUtil;

class Logger
{
	const LOG_LEVEL_DEV         = 0;
	const LOG_LEVEL_PROD_CHECK  = 1;
	const LOG_LEVEL_PROD        = 2;

	protected static $log_level = 2;

	protected static $log_level_config = array(
		'd' => array(
			'level' => 0,
			'text'  => 'DEBUG',
		),
		'i' => array(
			'level' => 1,
			'text'  => 'INFO',
		),
		'e' => array(
			'level' => 2,
			'text'  => 'ERROR',
		),
		'c' => array(
			'level' => 2,
			'text'  => 'CRIT',
		),
	);

	public static function setLogLevel($level)
	{
		static::$log_level = $level;
	}

	public static function __callStatic($name, $args)
	{
		if (isset(static::$log_level_config[$name])) {
			$config = static::$log_level_config[$name];

			if ($config['level'] >= static::$log_level) {
				static::writeLog('['.$config['text'].'] '.$args[0]);
			}
		}
	}

	protected static function writeLog($message)
	{
		error_log($message);
	}
}
