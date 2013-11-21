<?php
/**
 * Autoloader 类
 */
class Autoloader {
	
	private static $loader;
	/**
	 * 构造函数
	 */
	private function __construct() {
		spl_autoload_register ( array ($this, 'inc_class' ) );
	}
	
	private function inc_class($className) {
		$filename = $className . '.class.php';
		$filepath = ROOT_PATH .'base/'. $filename;
		if (file_exists ( $filepath )) {
			return include $filepath;
		} else {
			$this->err_fn ( $className );
		}
	}
	
	public static function init() {
		// 静态化自调用
		if (self::$loader == NULL)
			self::$loader = new self ();
		
		return self::$loader;
	}

	/**
	 * 处理原model和controller文件
	 */
	private function inc_cls($className) {
		$modulesDir = "";
		$classType = "";
		if (substr ( $className, - 5 ) == 'Model') {
			$modulesDir = ROOT_PATH . 'include/model/';
		} elseif (substr ( $className, - 10 ) == 'Controller') {
			$modulesDir = ROOT_PATH . 'include/controller/';
		}
		$classFileName = "";
		$classFileName = $modulesDir . $className . ".php";
		if (file_exists ( $classFileName )) {
			return include $classFileName;
		} else {
			$this->err_fn ( $className );
		}
	}
	// 文件出错提示
	private function err_fn($className) {
		if(DEBUG_MODE){
			echo "class $className files includes err!!";
			exit ();
		}
	}
}
?>