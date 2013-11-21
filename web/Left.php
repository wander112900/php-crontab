<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <link rel="stylesheet" href="css/site.css" />
</head>
<body id="left">
    <div id="Div1">
        <div class="leftit">
            操作面板</div>
        <div class="space">
        </div>
        <ul id="leftMenu">
            <li class="leftselect" onclick="menu_click(this)"><a href="TaskList.php" target="mainFrame">计划任务管理</a></li>
          <!--  <li onclick="menu_click(this)"><a href="CategoryList.php" target="mainFrame">类别管理</a></li>
            <li onclick="menu_click(this)"><a href="StateList.php" target="mainFrame">状态管理</a></li>
            <li onclick="menu_click(this)"><a href="InterfaceList.php" target="mainFrame">数据接口配置管理</a></li>
            <li onclick="menu_click(this)"><a href="ProcessStateList.php" target="mainFrame">进程状态管理</a></li>
            <li onclick="menu_click(this)"><a href="CrawlerTaskList.php" target="mainFrame">抓取任务管理管理</a></li>
            <li onclick="menu_click(this)"><a href="#" target="mainFrame">测试</a></li>
           <li onclick="menu_click(this)"><a href="#" target="mainFrame">测试</a></li>
            <li onclick="menu_click(this)"><a href="#" target="_top">退出系统</a></li> -->
        </ul>
        <div class="space2">
        </div>
    </div>
     <script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        function menu_click(self) {
            $("#left li").attr("class", "");
            $(self).attr("class", "leftselect");
        }
    </script>
</body>
</html>
