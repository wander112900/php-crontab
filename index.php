<?php
require ('conf/init.php');
$cron = new CronParser("*/10 * * * * *");//每分钟去取一次，防止频繁去取
$rantime = $cron->getLastRan();
echo $rantime;