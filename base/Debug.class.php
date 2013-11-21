<?php
class Debug{
     private $debugLevel;
     private $debugMessage = "";
     private $functionScope = "main";

     public function __construct($debugLevel = 0) {
	  if ($debugLevel <= 0) {
	       $this->debugLevel = 0;
	  }
	  else if ($debugLevel >= 2) {
	       $this->debugLevel = 2;
	  }
	  else
	       $this->debugLevel = 1;
     }

     public function addDebugMessage($message, $function = "") {
	  // If we change of scope we need to record the new scope
	  if ($function != "") 
	       $this->functionScope = $function;
	  // What level of debugging are we in?
	  if ($this->debugLevel == 0)
	       return;
	  if ($this->debugLevel >= 2) {
	       print $this->functionScope;
	       print "------------";
	       if (is_string($message)) 
		    print $message;
	       else
		    echo var_dump($message);
	       print "------------";
	  }

	  $this->debugMessage .= "\n". $this->functionScope . ": ";
	  if (is_array($message)) {
	       ob_start();var_dump($message);$output=ob_get_contents();ob_end_clean();
	       $this->debugMessage .= $output;
	  } else
	       $this->debugMessage .= $message;
     }
     
     public function __toString() {
	  echo $this->debugMessage;
     }
}