<?php

/**
 * 
 * Crontab计划任务解析器
 * 秒　     （0-59） 
 * 分钟　（0-59） 
 * 小時　（0-23） 
 * 日期　（1-31） 
 * 月份　（1-12） 
 * 星期　（0-6）//0代表星期天 
 * @version 1.0
 * @example 
 * 秒  分　时　日　月　周　
 * 0 0 2 * * *     每天2点0分0秒执行一次
 * 0 0 1-10 * * *  每天1-10各执行一次
 * 0 0 5,6 * * *   每天5点和6点各执行一次
 * 0 0 *\/2 * * *  每隔两小时执行一次（实际使用中"\"要去掉）
 * 例：
 * $cron = new CronParser('10 * * * * *'); 
 * $cron->getLastRan();//取下一次执行时间返回Y-m-d H:i:s
 * $cron->getLastRanUnix();//取unix时间戳
 * 
 */
class CronParser{
     private $input;
     private $elements = array();
     private $isValid = true;
     private $_elementsRanges;
     private $_elementPattern;
     private $_elementRangePattern;
     private $_elementRangeStepPattern;
     private $_now;
     public $debug;     

     function __construct($str, $debug = 1) {
	  $this->_initialize();
	  $this->debug = new Debug($debug);
	  $this->input = $str;
	  $this->_preprocessInput();
     }
     
     /*! Initialize some variables
      */
     private function _initialize() {
	  $this->isValid = false;
	  $this->_elementsRanges = array (
	  	   "seconds" => array (
		    "min" => 0,
		    "max" => 59),
	       "minutes" => array (
		    "min" => 0,
		    "max" => 59),
	       "hours" => array (
		    "min" => 0,
		    "max" => 23),
	       "days" => array (
		    "min" => 1,
		    "max" => 31),
	       "months" => array (
		    "min" => 1,
		    "max" => 12),
	       "weekdays" => array(
		    "min" => 0,
		    "max" => 6));
	  // This patter may add experimental support to compound elements
	  //$this->_elementPattern = "/^\d+((\d+)?(,\d)?(\-\d+($|\/\d+$)?)?)+$/";
	  $this->_elementPattern = "/^\d+(,\d+)*$/";
	  $this->_elementRangeStepPattern = "/^(\*|\d+-\d+)\/\d+$/";
	  $this->_elementRangePattern = "/^\d+-\d+$/";
     }

     /*! Preprocess the input so it can be used
      *
      * This preprocessing involves: triming, eliminating useless
      * blank spaces between elements of the entry, validation,
      * ranges expansion, step calculation.
      */
     private function _preprocessInput() {
	  $this->debug->addDebugMessage("Entering function", "preprocessInput");

	  // Trim extra space from the beginning and end of $input
	  $tmp = trim($this->input);
	  $output= "";
	  $this->debug->addDebugMessage("Finished triming : " . $tmp);

	  // Eliminate extra spaces inside $input
	  $jump = false;
	  for ($i=0; $i<strlen($tmp); $i++) {
	       if ($tmp[$i] != ' ') {
		    $output = $output . $tmp[$i];
		    $jump = false;
	       }
	       else
		    if (!$jump) {
			 $output = $output . $tmp[$i];
			 $jump = true;
		    }
	  }
	  $this->debug->addDebugMessage("Cleaning finished : " . $output);

	  // Split $input into its elements
	  $tmp = explode(" ", $output);
	  if (count($tmp) != 6) 
	       throw new CronException("Wrong number of parameters." . $output);
	  $this->elements = array (
	  				'seconds' =>$tmp[0], 
	  				"minutes" => $tmp[1],
				    "hours" => $tmp[2],
				    "days" => $tmp[3],
				    "months" => $tmp[4],
				    "weekdays" => $tmp[5]);
	  $this->debug->addDebugMessage("Input splitted : ");
	  $this->debug->addDebugMessage($this->elements);

	  // Validate input and expand values
	  foreach (array("seconds","minutes", "hours", "days", "months", "weekdays") as $elementName) { 
	       $currentElement =& $this->elements[$elementName];
	       $currentRange =& $this->_elementsRanges[$elementName];
	       // It is the whole range?
	       if ($currentElement == "*") 
	       {
		    // Days calculation is different
		    if (in_array($elementName, array("days", "weekdays"))) 
			 continue;
		    
		    $currentElement = range($currentRange["min"],
					    $currentRange["max"]);
	       }
	       // It is a range with a step?
	       else if (preg_match($this->_elementRangeStepPattern, $currentElement)) {
		    $pieces = explode("/", $currentElement);
		    // if an asterix range
		    if ($pieces[0] == "*") {
			 $totalRange = range($currentRange["min"],
					     $currentRange["max"]);
		    } 
		    // it *has* to be an numeric range
		    else {
			 $atoms = explode("-", $pieces[0]);
			 $atoms[0] = (int)$atoms[0];
			 $atoms[1] = (int)$atoms[1];
			 if ($atoms[0] > $atoms[1] ||
			     $atoms[0] < $currentRange["min"] || $atoms[0] > $currentRange["max"] ||
			     $atoms[1] < $currentRange["min"] || $atoms[1] > $currentRange["max"]) {
			      throw new CronException("Bad formatted entry(range): " . $currentElement);
			 }
			 $totalRange = range($atoms[0], $atoms[1]);
		    }
		    $sol = array();
		    // now use the step to filter the range
		    foreach (range(0,count($totalRange),(int)$pieces[1]) as $i) { 
			 if (isset($totalRange[$i]))			      
			      $sol[] = $totalRange[$i];		    
		    }		    
		    $currentElement = $sol;
	       }
	       // it is a range withouth step
	       else if (preg_match($this->_elementRangePattern, $currentElement)) {
		    $atoms = explode("-", $currentElement);
		    $atoms[0] = (int)$atoms[0];
		    $atoms[1] = (int)$atoms[1];
		    if ($atoms[0] > $atoms[1] ||
			$atoms[0] < $currentRange["min"] || $atoms[0] > $currentRange["max"] ||
			$atoms[1] < $currentRange["min"] || $atoms[1] > $currentRange["max"]) {
			 throw new CronException("Bad formatted entry(range): " . $currentElement);
		    }
		    $currentElement = range($atoms[0], $atoms[1]);
	       }
	       // It is an element
	       else if (preg_match($this->_elementPattern, $currentElement)){
		    $sol = array();
 		    if (preg_match("/^\d+$/", $currentElement)) {
			 if ((int)$currentElement > $currentRange["max"] || (int)$currentElement < $currentRange["min"]){
			      throw new CronException("Parameter out of range: " . $atom . " of " . $this->input);
			 }
			 $currentElement = array((int)$currentElement);
 		    }
		    else {
			 foreach (explode(",",$currentElement) as $atom) {
			      if ((int)$atom > $currentRange["max"] || (int)$atom < $currentRange["min"]){
				   throw new CronException("Parameter out of range: " . $atom . " of " . $this->input);
			      }
			      $sol[] = (int)$atom;
			 }
			 $currentElement = $sol;
		    }
	       }
	       else {
		    throw new CronException("Bad formatted entry (element): " . $currentElement);
	       }
	  }
	  $this->isValid = true;
     }

     /*! Defines the $now of the class
      *
      * The only pourpuse of this function is to let the user define
      * what time should the class take as reference to calculate Next
      * and Prev
      */
     public function setNow(&$now) {
	  $this->_now = explode(",", $now);
     }

     /*! Returns the reference NOW time
      *
      * This function returns the reference NOW that is used by the
      * class to make its computations. If the var is not set by the
      * getNow method, it return time() properly formatted;
      */
     public function getNow() {
	  if (isset($this->_now)) {
	       return $this->_now;
	  }
	  else {
	       $t = strftime("%S,%M,%H,%d,%m,%w,%Y", time()); //Get the values for now in a format we can use
	       return  explode(",", $t); //Make this an array
	  }
     }

     
     private function getWeekDays($month, $year){
	  $ret = array();
	  
	  $days = range($this->_elementsRanges["days"]["min"],
			$this->daysInMonth($month, $year));		

	  foreach ($days as $day){
//	       if (in_array(jddayofweek(gregoriantojd($month, $day, $year),0), $this->elements["weekdays"])){
//			 $ret[] = $day;
//	       }
		   $week = intval(date('w', strtotime("$year-$month-$day")));
	  	   if (in_array($week, $this->elements["weekdays"])){
			 $ret[] = $day;
	       }
	  }

	  return $ret;		
     }

     /*! Computes the last day of a Month
      *
      * Given a month and a year, this function calculates the last
      * day of the month. It supports leap years (february 29)
      */
     private function daysInMonth($month, $year){
	  if(checkdate($month, 31, $year)) return 31;
	  if(checkdate($month, 30, $year)) return 30;
	  if(checkdate($month, 29, $year)) return 29;
	  if(checkdate($month, 28, $year)) return 28;
	  return 0; // error
     }	

     /*! Computes the Days of a Month
      *
      * Given a month, it calculates the days that fulfill the
      * requirements of the cron string
      */
     private function getDaysArray($month, $year = 0) {
	  $now = $this->getNow();
	  if ($year == 0) {
	       $year = $now[6];
	  }
//	  $this->debug("Getting days for $month");
	  $days = array();
   		
	  if (is_array($this->elements["weekdays"])) {
	       $days = $this->getWeekDays($month, $year);
//	       $this->debug("Weekdays:");
//	       $this->debug($days);
	       if (is_array($this->elements["days"])) {
		    $days += $this->elements["days"];
	       }
	  }
	  else {
	       if (is_array($this->elements["days"]))
		    $days = $this->elements["days"];
	       else
		    $days = range($this->_elementsRanges["days"]["min"],
				  $this->daysInMonth($month, $year));
	  }
//	  $this->debug($days);
	  return $days;
     }

     /*! Retrieves the next element of the array
      *
      * Given an array and an element, it calculates the next element
      * of the array and returns it, false otherwise
      */
     private function getNextArray($arr, $current) {
	  if (is_array($arr)) {
	       foreach ($arr as $v) { 
		    if ($v >= $current)
			 return $v;
	       }
	  }
	  return false;
     }

     /*! Retrieves the next element of the array
      *
      * Given an array and an element, it calculates the previous element
      * of the array and returns it, false otherwise
      */
     private function getPrevArray($arr, $current) {
	  if (is_array($arr)) {
	       foreach ($arr as $v) { 
		    if ($v <= $current)
			 return $v;
	       }
	  }
	  return false;
     }

     private function getNextMonth(&$sol){
	  //month
	  $tmp = $this->getNextArray($this->elements["months"], $sol["month"]);
	  if ($tmp === false) {
	       $days = $this->getDaysArray($this->elements["months"][0], $sol["year"]+1);
	       return array(
	       		$this->elements["seconds"][0],
	       		$this->elements["minutes"][0],
			    $this->elements["hours"][0],
			    $days[0],
			    $this->elements["months"][0],
			    $sol["year"]+1);
	  }
	  else if ($tmp != $sol["month"]) {
	       $days = $this->getDaysArray($tmp, $sol["year"]);
	       return array(
	       		$this->elements["seconds"][0],
	       		$this->elements["minutes"][0],
			    $this->elements["hours"][0],
			    $days[0],
			    $tmp,
			    $sol["year"]);
	  }
	  $sol["month"] = $tmp;
	  return $this->getNextDay($sol);
     }

     private function getNextDay(&$sol) {
	  $tmp = $this->getNextArray($this->getDaysArray($sol["month"], $sol["year"]), $sol["day"]);
	  if ($tmp === false) {
	       $sol["month"] += 1;
	       $sol["day"] = $this->_elementsRanges["days"]["min"];
	       return $this->getNextMonth($sol);
	  }
	  else if ($tmp != $sol["day"]) {
	       return array(
	       		$this->elements["seconds"][0],
	       		$this->elements["minutes"][0],
			    $this->elements["hours"][0],
			    $tmp,
			    $sol["month"],
			    $sol["year"]);
	  }
	  $sol["day"] = $tmp;
	  return $this->getNextHour($sol);

     }

     private function getNextHour(&$sol) {
	  $tmp = $this->getNextArray($this->elements["hours"], $sol["hour"]);
	  if ($tmp === false) {
	       $sol["day"] += 1;
	       $sol["hour"] = $this->_elementsRanges["hours"]["min"];
	       return $this->getNextDay($sol);
	  }
	  else if ($tmp != $sol["hour"]) {
	       return array(
	       		$this->elements["seconds"][0],
	       		$this->elements["minutes"][0],
			    $tmp,
			    $sol["day"],
			    $sol["month"],
			    $sol["year"]);
	  }
	  $sol["hour"] = $tmp;
	  return $this->getNextMinute($sol);
     }

     private function getNextMinute(&$sol)
     {
		  $tmp = $this->getNextArray($this->elements["minutes"], $sol["minute"]);
		  if ($tmp === false) {
		       $sol["hour"] += 1;
		       $sol["minute"] = $this->_elementsRanges["minutes"]["min"];
		       return $this->getNextHour($sol);
		  }
		  else if ($tmp!=$sol["minute"])
		  {
		  	 return array(
		  		   $this->elements["seconds"][0],
		  		   $tmp,
			       $sol["hour"],
			       $sol["day"],
			       $sol["month"],
			       $sol["year"]);
		  }
		  $sol["minute"] = $tmp;
		  return $this->getNextSeconds($sol);
     }
	 private function getNextSeconds(&$sol) 
	 {
	   $tmp = $this->getNextArray($this->elements["seconds"], $sol["second"]);	  
	   if ($tmp === false) {
	       $sol["minute"] += 1;
	       $sol["second"] = $this->_elementsRanges["seconds"]["min"];
	       return $this->getNextMinute($sol);
	   }
	   return array(
	  		   $tmp,
	  		   $sol["minute"],
		       $sol["hour"],
		       $sol["day"],
		       $sol["month"],
		       $sol["year"]);	  
     }

     public function calculateNextRun()
     {
	    if (!$this->isValid) 
	    {
	       throw new CronException("Invalid input");
	    }
	    $tmp = $this->getNow();
	    $sol = array (
	  	   "second"=>$tmp[0],
	       "minute" => $tmp[1],
	       "hour" => $tmp[2],
	       "day" => $tmp[3],
	       "month" => $tmp[4],
	       "weekday" => $tmp[5],
	       "year" => $tmp[6]);
	    return $this->getNextMonth($sol);
     }
     /**
      * 获取下一次执行时间
      */
	 function getLastRan()
	 {
	 	return date('Y-m-d H:i:s',$this->getLastRanUnix());
     }
     /**
      * 
      * 获取下一次执行unix时间戳
      */
	 function getLastRanUnix()
	 {
	 	$lastTime = $this->calculateNextRun();
	 	$lastRan = mktime($lastTime[2], $lastTime[1], $lastTime[0], $lastTime[4], $lastTime[3], $lastTime[5]);
	 	return $lastRan;
     }
}

/**
 * 
 * 测试
 */
//function testCronParser()
//{
//	$t = new CronParser('*/5 * * * * *');//每隔5秒执行一次
//	echo $t->getLastRanUnix();
//}
?>