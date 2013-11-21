<?php
require_once(dirname(__FILE__) . "/../../conf/init.php");
if(isset($_REQUEST['type'])&& '' != trim($_REQUEST['type']))
{
	$type = $_REQUEST['type'];
	switch ($type)
	{
		case 1://添加
			addTask();
			break;
		case 2://修改
			modifyTask();
			break;
		case 3://删除
			deleteTask();
			break;
		case 4://获取
			getTask();
			break;
	}
}
function addTask()
{
	$isChecked = true;
	$res = array('Success'=>'', 'MessageInfo'=>'');
	$task = array();	
	if (isset($_POST['name'])){
		$task['name']= $_POST['name'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='名称不能为空！';
	}
	if (isset($_POST['time'])){
		$task['time']= $_POST['time'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='周期不能为空！';
	}
	if (isset($_POST['program'])){
		$task['program']= $_POST['program'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='要执行的程序路径不能为空！';
	}
	if (isset($_POST['enable'])){
		$task['enable']= $_POST['enable'];
	}
	if($isChecked==true)
	{
		if(CronTaskManager::addTask($task))
		{
			$res['Success'] = true;
			$res['MessageInfo']='添加成功！';
		}
		else 
		{
			$res['Success'] = false;
			$res['MessageInfo']='添加失败！';
		}
	}
	echo json_encode($res);
}

function modifyTask()
{
	$isChecked = true;
	$res = array('Success'=>'', 'MessageInfo'=>'');
	$task = array();
	if (isset($_POST['cid'])){
		$task['cid']= $_POST['cid'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='参数不正确！';
	}
	if (isset($_POST['name'])){
		$task['name']= $_POST['name'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='名称不能为空！';
	}
	if (isset($_POST['time'])){
		$task['time']= $_POST['time'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='周期不能为空！';
	}
	if (isset($_POST['program'])){
		$task['program']= $_POST['program'];
	}
	else
	{
		$isChecked = false;
		$res['Success'] = false;
		$res['MessageInfo'].='要执行的程序路径不能为空！';
	}
	if (isset($_POST['enable'])){
		$task['enable']= $_POST['enable'];
	}
	if($isChecked==true)
	{
		if(CronTaskManager::modifyTask($task))
		{
			$res['Success'] = true;
			$res['MessageInfo']='修改成功！';
		}
		else 
		{
			$res['Success'] = false;
			$res['MessageInfo']='修改失败！';
		}
	}
	echo json_encode($res);
}
function deleteTask()
{
	$res = array('Success'=>'', 'MessageInfo'=>'');
	if (isset($_POST['cid'])){
		if(CronTaskManager::deleteTask($_POST['cid']))
		{
			$res['Success']=true;
			$res['MessageInfo']='删除成功!';
		}
		else 
		{
			$res['Success']=false;
			$res['MessageInfo']='删除失败!';
		}
	}
	else 
	{
		$res['Success']=false;
		$res['MessageInfo']='传入的参数不正确！';
	}
	echo json_encode($res);
}

function getTask()
{
	$task = array('Success'=>'', 'Task'=>array());
	if (isset($_GET['cid'])){
		$task['Success']=true;
		$task['Task'] = CronTaskManager::getTaskByCid($_GET['cid']);
	}
	echo json_encode($task);
}
?>