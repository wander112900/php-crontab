<?php
require ('../conf/init.php');
class CronTab
{
	/**
	 * 
	 * 所有的任务数组
	 * @array 
	 */
	private $_Tasks = array();
		
	public function run()
	{
		while (true)
		{
			$allTasks = $this->_getTask();
			if (empty($allTasks))
			{
				CLogger::log('notice', 'get empty task', __FILE__, __LINE__);
				sleep(5);
				continue;
			}
			foreach($allTasks as $task)
			{
				$this->_startTask($task);
			}
			sleep(1);
		}
	}	
	/**
	 * 
	 * 执行任务
	 * @array 任务 $task
	 */
	private function _startTask($task)
	{
		$cid = $task['cid'];
		$time = $task['time'];
		$program = $task['program'];		
		$cron = new CronParser($time);
		$rantime = $cron->getLastRanUnix();
		if(time()==$rantime)
		{
			$crondb = new Mysql();
			//要执行的脚本
			$res = TRUE;
			$command = PHP_EXEC . " ".$program." $cid >/dev/null 2>&1 &";
			//var_dump($command);
			CLogger::log('notice', 'start task '.$command , __FILE__, __LINE__);
			$res=popen($command, 'r');
			if($res===FALSE)
			{
				//执行出错记录错误日志
				$sql = "UPDATE cron_task SET fail_time = ".time().", retry=retry+1 WHERE cid=$cid";
				$crondb->query($sql);
			}
			else 
			{
				$sql = "UPDATE cron_task SET last_excute = $rantime WHERE cid=$cid";			
				$crondb->query($sql);//更新最后执行时间
				
				CLogger::log('notice', 'the task cid:'.$cid.' rantime:'.date('Y-m-d H:i:s',$rantime), __FILE__, __LINE__);
			}
			pclose($res);
		}
	}
	
	/**
	 * 获取所有任务列表
	 * 
	 */
	private function _getTask()
	{
		$cron = new CronParser("0 * * * * *");//每分钟去取一次，防止频繁去取
		$rantime = $cron->getLastRanUnix();
		$task = array();
		if (!empty($this->_Tasks) && time()!==$rantime)
		{
			$task = $this->_Tasks;
		}
		else 
		{
			$crondb = new Mysql();
	        $arrTasks = array();
	        $sql = "SELECT * FROM cron_task WHERE enable = 1";
	        $rows = $crondb->getAll($sql);
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
			$this->_Tasks = $rows;
			$task = $this->_Tasks;	        
		}
		return $task;
	}
}
$cron = new Crontab();
$cron->run();
?>