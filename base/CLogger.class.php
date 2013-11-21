<?php

/**
 * 记录日志类
 *
 * @version 1.0
 **/
class CLogger
{
	/**
	 * @var string 日志文件名
	 **/
	private static $LOG_FILE = "/../log/statistic/";

	/**
	 * @var string 日志文件后缀名
	 **/
	private static $LOG_EXTENSION = array(
		'notice' => '.log',
		'warning' => '.warn',
		'error' => '.err',
	);

	/**
	 * 向文件中记录日志
	 *
	 * @param string 日志类型
	 * @param string 日志消息
	 * @param string 文件名
	 * @param string 文件行号
	 * @param string 日志文件名
	 **/
	public static function log($type, $msg, $file, $line, $filename = null)
	{
		if (!(defined('DEBUG_MODE') && DEBUG_MODE) && $type != 'error')
		{
			return;
		}
		if(null === $filename)
		{
			$logfile = dirname(__FILE__) . self::$LOG_FILE;
		}
		else
		{
			$logfile = dirname(__FILE__) . "/../$filename";
		}

		$arrLogLevels = array_keys(self::$LOG_EXTENSION);
		if(!in_array($type, $arrLogLevels))
		{
			$type = 'error';
		}

		$filename = $logfile . date('YmdH') . self::$LOG_EXTENSION[$type];
		$date = date('Y-m-d H:i:s');
		file_put_contents($filename, "[$date] [$msg] [$file:$line]\r\n", FILE_APPEND);
	}
}

function testCLogger()
{
	CLogger::log('error', 'error test', __FILE__, __LINE__);
	CLogger::log('notice', 'notice test', __FILE__, __LINE__);
	CLogger::log('unknown', 'unknown test', __FILE__, __LINE__);
}

//testCLogger();

?>
