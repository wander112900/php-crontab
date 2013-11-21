<?php
require ('../conf/init.php');
class Order extends CronTabInterface{
	/**
	 * @var string 活跃用户按天统计信息表
	 */
	public static $TABLE_NAME = 'order_minute';
	
	public function process($cid)
	{
//		CLogger::log('notice', 'Order process start', __FILE__, __LINE__);
		self::statisticOrderByMinute();
//		CLogger::log('notice', 'Order process end', __FILE__, __LINE__);
	}
	
	private static function statisticOrderByMinute(){
		$db = new Mysql();
		$sql = "SELECT 
					count(*) as order_num,(SUM(goods_amount) + SUM(shipping_fee)) as order_money 
				FROM 
					ecs_order_info 
				WHERE from_unixtime(add_time,'%Y-%m-%d') = CURDATE()";
		$row = $db->getRow($sql,array(),false);
		if($row){
			$sql = "INSERT INTO ".self::$TABLE_NAME." (add_time,order_num,order_money) VALUES (:add_time,:order_num,:order_money)";
			$db->query($sql,array('add_time'=>time(),'order_num'=>$row['order_num'],'order_money'=>$row['order_money']),TRUE);
			//$sql = "INSERT INTO ".self::$TABLE_NAME." (add_time,order_num,order_money) VALUES (".time().",".$row['order_num'].",".$row['order_money'].")";
			//$db->query($sql);
		}
	}
}
$cid = $argv[1];
$task = new Order($cid);
$task->run();
?>