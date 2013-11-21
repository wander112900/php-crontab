<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>计划任务管理</title>
	<link rel="stylesheet" href="css/site.css" />
    <script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
</head>
<body id="right">
	<div class="tit">
        <div class="titleft"></div>
        	当前位置：计划任务管理
        <div class="titright"></div>
    </div>
    <div class="space">
    </div>
    <div class="space">
        <input type="button" class="btn4word" onclick="javascript:showTask();" value="添加新任务" />
    </div><br />
    <div class="space">
        <table style="width: 100%" cellpadding="0" cellspacing="0" class="mytab">
            <tr>
                <th class="titab">计划编号</th>
                <th class="titab">名称</th>
                <th class="titab">执行周期</th>
                <th class="titab">执行任务文件</th>
                <th class="titab">是否启用</th>
                <th class="titab">最后执行时间</th>
                <th class="titab">出错次数</th>
                <th class="titab">最后出错时间</th>
                <th class="titab">执行开始时间</th>
                <th class="titab">执行结束时间</th>
                <th class="titab">操作</th>
            </tr>
          <?php
            require_once(dirname(__FILE__) . "/../conf/init.php");
            $tasks = CronTaskManager::getAllTask();
            foreach ($tasks as $task):?>
            <tr>
            	<td><?php echo $task['cid'];?></td>
            	<td><?php echo $task['name'];?></td>
                <td><?php echo $task['time'];?></td>
                <td><?php echo $task['program'];?></td>
                <td><?php echo $task['enable']==1?'启用':'未启用';?></td>
                <td><?php echo date('Y-m-d H:i:s',$task['last_excute']);?></td>
                <td><?php echo $task['retry'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$task['fail_time']);?></td>
                <td><?php echo date('Y-m-d H:i:s',$task['start_time']);?></td>
                <td><?php echo date('Y-m-d H:i:s',$task['end_time']);?></td>
                <td><a href="javascript:showModifyTask(<?php echo $task['cid'];?>);">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:deleteTask(<?php echo $task['cid'];?>);">删除</a></td>
            </tr>
            <?php endforeach;?>
         </table>
     </div>
     <div id="add_dialog" style="display: none">
        <div class="error-message">
        </div>
        <div class="item-label">
            <label>名称：</label></div>
        <div class="item-field">
            <input type="text" name="name" />
        </div>
        <div class="item-label">
            <label>执行周期：</label></div>
        <div class="item-field">
            <input type="text" name="time" />
        </div>
        <div class="item-label">
            <label>程序路径:</label></div>
        <div class="item-field">
            <input type="text" name="program" style="width:200px;" />
        </div>
        <div class="item-label">
            <label>是否启用:</label></div>
        <div class="item-field">
        	<select name="enable">
        		<option value="1">启用</option>
        		<option value="0">暂不启用</option>
        	</select>
        </div>
    </div>
    <div id="modify_dialog" style="display: none">
    	<input type="hidden" name="cid" />
        <div class="error-message">
        </div>
        <div class="item-label">
            <label>名称：</label></div>
        <div class="item-field">
            <input type="text" name="name" />
        </div>
        <div class="item-label">
            <label>执行周期：</label></div>
        <div class="item-field">
            <input type="text" name="time" />
        </div>
        <div class="item-label">
            <label>程序路径:</label></div>
        <div class="item-field">
            <input type="text" name="program" style="width:200px;" />
        </div>
        <div class="item-label">
            <label>是否启用:</label></div>
        <div class="item-field">
        	<select name="enable">
        		<option value="1">启用</option>
        		<option value="0">暂不启用</option>
        	</select>
        </div>
    </div>
    <script type="text/javascript">
	    function deleteTask(cid) {
	        if (confirm('删除该计划任务后相关业务将不能正常运行，真的要删除这个任务吗？')) {
	            $.post('code/TaskController.php',
	                    {type:3, cid: cid,t: Math.random() },
	                    function(json) {
	                        if (json.Success == true) {
	                            alert(json.MessageInfo);
	                            window.location.reload();
	                        } else {
	                            alert(json.MessageInfo);
	                        }
	                    },
	                    "json"
	                );
	        }
	    }
    	function showModifyTask(cid)
    	{
    		$("#modify_dialog input[name='cid']").val(cid);
    		 $.get('code/TaskController.php',
                     {type:4, cid: cid, t: Math.random() },
                     function(json) {
                         if (json.Success = true) {
                             $("#modify_dialog input[name='name']").val(json.Task.name);
                             $("#modify_dialog input[name='time']").val(json.Task.time);
                             $("#modify_dialog input[name='program']").val(json.Task.program);
                             $("#modify_dialog select[name='enable']").val(json.Task.enable);
                             $('#modify_dialog').dialog('open');
                         } else {
                             alert("获取数据出错！");
                         }
                     },
                     "json"
                 ); 
    		
    	}
    	function showTask()
    	{
    	  $("#add_dialog .error-message").html("");
          $('#add_dialog').dialog("open");
    	}
    	function modifyTask()
    	{
    		$("#modify_dialog .error-message").html("");
    		var cid = $("#modify_dialog input[name='cid']").val();
            var name = $("#modify_dialog input[name='name']").val();
            var time = $("#modify_dialog input[name='time']").val();
            var program = $("#modify_dialog input[name='program']").val();
            var enable = $("#modify_dialog select[name='enable']").val();            
            if (name == "") {
            	$("#modify_dialog .error-message").html("名称不能为空!");
            	return;
	        } else if (time == "") {
	            $("#modify_dialog .error-message").html("时间周期不能为空!");
	            return;
	        } else if (program == "") {
	            $("#modify_dialog .error-message").html("要执行的程序路径不能为空!");
	            return;
	        }
            $.post('code/TaskController.php',
                    { type: 2,cid:cid, name: name, time: time, program: program,enable:enable },
                    function(json) {
                    	if (json.Success == true) {
                            alert(json.MessageInfo);
                            window.location.reload();
                        } else {
                            alert(json.MessageInfo);
                        }
                    },
                    "json"
                  );
    	}
    	function addTask()
    	{
    		$("#add_dialog .error-message").html("");
            var name = $("#add_dialog input[name='name']").val();
            var time = $("#add_dialog input[name='time']").val();
            var program = $("#add_dialog input[name='program']").val();
            var enable = $("#add_dialog select[name='enable']").val();
            //$("#modify_dialog select[name='enable']").val(json.ReceiveTemplate.TemplateGroup);
            if (name == "") {
            	$("#add_dialog .error-message").html("名称不能为空!");
            	return;
	        } else if (time == "") {
	            $("#add_dialog .error-message").html("时间周期不能为空!");
	            return;
	        } else if (program == "") {
	            $("#add_dialog .error-message").html("要执行的程序路径不能为空!");
	            return;
	        }
            $.post('code/TaskController.php',
                    { type: 1, name: name, time: time, program: program,enable:enable },
                    function(json) {
                    	if (json.Success == true) {
                            alert(json.MessageInfo);
                            window.location.reload();
                        } else {
                            alert(json.MessageInfo);
                        }
                    },
                    "json"
                  );
    	}
    	$(document).ready(function() {
    		$('#add_dialog').dialog({
                bgiframe: true,
                title: '添加计划任务',
                autoOpen: false,
                width: 400,
                height: 300,
                modal: true,
                show: 'clip',
                hide: 'blind',
                closeOnEscape: true,
                buttons: {
                    "取消": function() {
                        $(this).dialog("close");
                    },
                    "添加": function() {
                        addTask();
                    }
                }
            });
    		$('#modify_dialog').dialog({
                bgiframe: true,
                title: '修改计划任务',
                autoOpen: false,
                width: 400,
                height: 300,
                modal: true,
                show: 'clip',
                hide: 'blind',
                closeOnEscape: true,
                buttons: {
                    "取消": function() {
                        $(this).dialog("close");
                    },
                    "修改": function() {
                        modifyTask();
                    }
                }
            });
            
    	});
    </script>
</body>
</html>