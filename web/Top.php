<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <link rel="stylesheet" href="css/site.css" />
</head>
<body id="top">
    <div class="topmain">
        <div class="mlogo"></div>
        <div class="toparc">            
        </div>
        <div class="userinfo">
            欢迎您！&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" target="_top">退出</a>
        </div>
        <div class="menu">
            <ul>                
                <li class="topselect" onclick="menu_click(this,'List')">数据中心</li>
            </ul>
        </div>
    </div>
    <script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        function menu_click(self, defaultUrl) {
            $("#top li").attr("class", "");
            $(self).attr("class", "topselect");
        }
    </script>
</body>
</html>
