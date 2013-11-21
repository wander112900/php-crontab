<?php
/**
 * mysql PDO操作类
 * @author Wander
 */
class Mysql{
	/**
	 * 当前SQL指令
	 */
	private $sql = '';
	
	/**
	 * 当前使用的连接
	 */
	private $link_id = null;
	/**
	 * 主库连接
	 */
	private $m_link_id = null;
	/**
	 * 从库连接
	 */
	private $s_link_id = null;
	
	// 当前查询ID
	private $query_id = null;
	
	/**
	 * 数据库连接参数配置
	 */
	private $config = array ();
	
	/**
	 * 是否多库
	 */
	private $multi_server = false;
	
	/**
	 * 编码
	 */
	private $charset = 'utf8';
	
	/**
	 * 架构函数
	 * @param array $config 数据库连接参数 主库master,从库slave
	 */
	public function __construct() {
		$config = array();
		$config['master']['host'] = MASTER_DB_HOST;
		$config['master']['port'] = MASTER_DB_PORT;
		$config['master']['dbname'] = MASTER_DB_DBNAME;
		$config['master']['username'] = MASTER_DB_USERNAME;
		$config['master']['password'] = MASTER_DB_PWD;
		
		$config['slave']['host'] = SLAVE_DB_HOST;
		$config['slave']['port'] = SLAVE_DB_PORT;
		$config['slave']['dbname'] = SLAVE_DB_DBNAME;
		$config['slave']['username'] = SLAVE_DB_USERNAME;
		$config['slave']['password'] = SLAVE_DB_PWD;
		
		$this->config = $config;
		$this->multi_server = empty ( $this->config ['slave'] ) ? false : true;
		$this->charset = empty ( $this->config ['charset'] ) ? 'utf8' : $this->config ['charset'];
	}
	
	/**
	 * 执行sql
	 * @param string $sql 执行的SQL语句
	 * @param array $params 参数
	 * @param boolen $master 是否主库
	 */
	public function query($sql,$params = array(),$master = true) {
		$this->sql = $sql;
		try {
			$this->initConnect ( $master );
			$this->query_id = $this->link_id->prepare ( $sql );
			$this->query_id->execute ( $params );
			return $this->query_id;
		} catch ( Exception $e ) {
			$errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
			$message = $e->getMessage ();
			$this->show ( $message );
		}
	
	}
	/**
	 * 获取单个数据
	 * @param string $sql
	 * @param array $params
	 * @param boolen $master
	 */
	public function getOne($sql, $params = array(),$master = true) {
		return $this->query ( $sql, $params, $master )->fetchColumn ();
	}
	/**
	 * 获取一行数据
	 * @param varchar $sql SQL
	 * @param boolen $master 是否主库
	 * @param array $params 参数
	 */
	public function getRow($sql, $params = array(),$master = true) {
		return $this->query ( $sql, $params, $master )->fetch ();
	}
	/**
	 * 获取所有数据
	 * @param string $sql
	 * @param array $params
	 * @param boolen $master
	 */
	public function getList($sql, $params = array(),$master = true) {
		return $this->query ( $sql, $params, $master )->fetchAll ();
	}
	/**
	 * 获取所有数据
	 * @param string $sql
	 * @param array $params
	 * @param boolen $master
	 */
	public function getAll($sql, $params = array(),$master = true) {
		return $this->query ( $sql, $params, $master )->fetchAll ();
	}
	
	/**
	 * 分页数据(sql语句后面自动加上limit)
	 * @param string $sql
	 * @param array $params
	 * @param int $page_size 默认是10
	 * @param int $page 默认1
	 * @param boolen $master
	 */
	public function getLimitList($sql,$params = array(), $page_size = 10, $page = 1,$master = true) {
		$sql .= ' LIMIT ' . ($page - 1) * $page_size . ',' . $page_size;
		return $this->getList ( $sql, $params, $master );
	}
	
	//影响行数
	public function getRowCount(){
		return $this->link_id->rowCount();
	}
	
	/**
	 * 开始事务
	 * 主-从库要分清楚
	 */
	public function begin($master = TRUE)
	{
		$this->initConnect();
		$this->link_id->beginTransaction();
	}
	/**
	 * 提交事务
	 */
	public function commit(){
		if($this->link_id){
			$this->link_id->commit();
		}
	}
	/**
	 * 事务回滚
	 */
	public function rollback(){
		if($this->link_id)
		{
			$this->link_id->rollBack();
		}
		else{
			$this->show('rollback error');
		}
	}
	
	
	/**
	 * 初始化数据库连接
	 * @param boolen $master 是否主库(默认TRUE)
	 */
	private function initConnect($master = TRUE) {
		if ($master || ! $this->multi_server) {
			if ($this->m_link_id) {
				$this->link_id = $this->m_link_id;
			} else {
				$this->connect ( $master );
				$this->m_link_id = $this->link_id;
			}
		} else {
			if ($this->s_link_id) {
				$this->link_id = $this->s_link_id;
			} else {
				$this->connect ( $master );
				$this->s_link_id = $this->link_id;
			}
		}
	}
	/**
	 * 连接数据库
	 * @param boolen $master
	 */
	private function connect($master = TRUE) {
		try {
			$this->link_id = $this->initPDO ( $master );
			if ($this->charset !== null) {
				$this->link_id->exec ( 'set names ' . $this->charset );
			}
		} catch ( PDOException $e ) {
			$errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
			$message = $e->getMessage ();
			$this->show ( $message );
		}
	}
	/**
	 * 强制重新连接数据库方法
	 */
	public function reConnect($master = true) {
		if ($master || ! $this->multi_server) {
			$this->connect ( $master );
			$this->m_link_id = $this->link_id;
		} else {
			$this->connect ( $master );
			$this->s_link_id = $this->link_id;
		}
	}
	
	/**
	 * 实例化pdo连接对象
	 * @param boolen $master 是否主库
	 */
	private function initPDO($master = TRUE) {
		$dsn = "mysql:host=%s;dbname=%s;port=%s";
		$username = '';
		$password = '';
		if ($master) {
			$dsn = sprintf ( $dsn, $this->config ['master'] ['host'], $this->config['master']['dbname'], $this->config['master']['port'] );
			$username = $this->config ['master'] ['username'];
			$password = $this->config ['master'] ['password'];
		} else {
			$dsn = sprintf ( $dsn, $this->config ['slave'] ['host'], $this->config['slave']['dbname'], $this->config['slave']['port'] );
			$username = $this->config ['slave'] ['username'];
			$password = $this->config ['slave'] ['password'];
		}
		return new PDO ( $dsn, $username, $password, 
						array (PDO::ATTR_PERSISTENT => 0, 
								PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
								PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
								PDO::ATTR_CASE => PDO::CASE_LOWER, 
								PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE, 
								PDO::ATTR_AUTOCOMMIT => TRUE ) );
	}
	/**
	 * 打印错误
	 * @param string $message
	 */
	protected function show($message) {
		if (DEBUG_MODE) {
			echo $message;
			exit ();
		}
		return FALSE;
	}
	//析构函数是在对象销毁时调用的代码
	public function __destruct(){
		$this->link_id = null;
		$this->s_link_id = null;
		$this->m_link_id = null;
		$this->query_id = null;
	}
}