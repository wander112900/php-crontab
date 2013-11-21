<?php
/**
 * 计划任务接口，所有计划任务都将继承此接口
 *
 * @author wander <wangdingyi@joyport.com>
 * @version 1.0
 **/
abstract class CronTabInterface
{
	/**
	 * 计划任务编号
	 */
	public $cid;
	
	/**
	 * 构造函数
	 * @param int 计划任务编号
	 */
	public function __construct($cid)
	{
		$this->cid = $cid;
	}
	
	public function run()
	{
		if ($this->_isRun()==='0')
		{
			$this->_updateStartTime();
			$res = $this->process($this->cid);
			$this->_updateEndTime($res);
		}
		else
		{
			CLogger::log('warning', 'The task is run or error now, The task cid:'.$this->cid, __FILE__, __LINE__);
		}
	}
	/**
	 * 负责处理计划任务中的具体事务
	 *
	 **/
	abstract public function process($cid);
	
	/**
	 * 判断当前进程是否正在运行
	 */
	private function _isRun()
	{
		$reportdb = new Mysql();
		$sql = "SELECT is_run FROM cron_task WHERE cid = $this->cid";
        $res = $reportdb->getOne($sql);
        if(false===$res)
        {
            CLogger::log('error', 'SELECT task error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return $res;
	}
	
	/**
	 * 更新任务执行开始时间
	 * @param int 任务编号
	 */
	private function _updateStartTime()
	{
		$reportdb = new Mysql();
		$sql = "UPDATE cron_task SET start_time=unix_timestamp(),is_run=1 WHERE cid = $this->cid";		
        $res = $reportdb->query($sql);
        if(FALSE===$res)
        {
            CLogger::log('error', 'update task error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return $res;
	}
	/**
	 * 更新任务执行的结束时间如果执行失败则失败次数累加
	 * @param int 任务编号
	 * @param bool 成功/失败
	 */
	private function _updateEndTime($res)
	{
		$reportdb = new Mysql();
		$sql = "UPDATE cron_task SET is_run=0, end_time=unix_timestamp()";
		if(FALSE == $res)
		{
			$sql.= " ,retry=retry+1 ";
		}
		$sql .= " WHERE cid = $this->cid";
        $result = $reportdb->query($sql);
        if(FALSE==$result)
        {
            CLogger::log('error', 'update task error, sql:'.$sql, __FILE__, __LINE__);
            return false;
        }
        return $result;
	}
}
?>