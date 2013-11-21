<?php
/**
 * 计划任务管理器
 * @author wander <wangdingyi@joyport.com>
 * @version 1.0
 **/
class CronTaskManager{
	
	/**
	 * @var string 计划任务表名
	 **/
	private static $TABLE_NAME = 'cron_task';
	
	/**
	 * 
	 * 获取所有计划任务
	 */
	public static function getAllTask()
	{
		$reportdb = new Mysql();
        $arrTasks = array();
        $sql = "SELECT * FROM ". self::$TABLE_NAME;
        $rows = $reportdb->getAll($sql);
        if(!$rows)
        {
            CLogger::log('error', 'get all task list error', __FILE__, __LINE__);
            return false;
        }

		if(0 == count($rows))
		{
            CLogger::log('notice', 'there is no task in database', __FILE__, __LINE__);
            return $arrTasks;
		}

        return $rows;
	}
	
	/**
	 * 
	 * 根据CID获取任务信息
	 * @param int 计划任务编号
	 */
	public static function getTaskByCid($cid)
	{
		$reportdb = new Mysql();
		$task = array();
        $sql = "SELECT * FROM ". self::$TABLE_NAME ." WHERE cid = $cid";
        $rows = $reportdb->getRow($sql);
        if(!$rows)
        {
            CLogger::log('error', 'get task by cid error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return $rows;
	}
	/**
	 * 
	 * 删除任务
	 * @param int $cid
	 */
	public static function deleteTask($cid)
	{
		$reportdb = new Mysql();
        $sql = "DELETE FROM ". self::$TABLE_NAME ." WHERE cid = $cid";
        $res = $reportdb->query($sql);
        if(FALSE==$res)
        {
            CLogger::log('error', 'delete task by cid error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return TRUE;
	}
	/**
	 * 
	 * 修改计划任务
	 * @param array $task
	 */
	public static function modifyTask($task)
	{
		$reportdb = new Mysql();
		$sql = sprintf("UPDATE %s SET `name`='%s',`time`='%s',`program`='%s',`enable`=%d WHERE cid = %d",
						self::$TABLE_NAME,mysql_escape_string($task['name']),
						mysql_escape_string($task['time']),mysql_escape_string($task['program']),
						$task['enable'],$task['cid']
				);
        $res = $reportdb->query($sql);
        if(FALSE==$res)
        {
            CLogger::log('error', 'update task error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return TRUE;
	}
	/**
	 * 
	 * 增加计划任务
	 * @param array $task
	 */
	public static function addTask($task)
	{
		$reportdb = new Mysql();
		$sql = sprintf("INSERT INTO %s (`name`,`time`,`program`,`enable`) VALUES ('%s','%s','%s',%d)",
						self::$TABLE_NAME,mysql_escape_string($task['name']),
						mysql_escape_string($task['time']),mysql_escape_string($task['program']),
						$task['enable']
				);
        $res = $reportdb->query($sql);
        if(FALSE==$res)
        {
            CLogger::log('error', 'insert task error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return TRUE;
	}
}
?>