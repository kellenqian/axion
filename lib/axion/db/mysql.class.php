<?php
/**
 * @desc MySQL数据库访问系列函数封装封装，适用于MySQL4.0+
 * @author [Alone] alonedistian@gmail.com〗& Nick Wang
 * @version 1.0.1
 * @package PHPDoc
 */

class Axion_db_MySQL extends Axion_db_Database {
	protected static $obj_this;
	
	/**
	 * 数据库地址
	 * @var string
	 */
	protected $str_host;
	
	/**
	 * 数据库连接端口
	 * @var int
	 */
	protected $int_port;
	
	/**
	 * 数据库登录用户名
	 * @var string
	 */
	protected $str_user;
	
	/**
	 * 数据库登录密码
	 * @var string
	 */
	protected $str_password;
	
	/**
	 * 数据库名称
	 * @var string
	 */
	protected $str_db;
	
	/**
	 * 数据库连接句柄
	 * @var handle
	 */
	protected $hd_connect;
	
	/**
	 * 简单事务处理中所要执行的SQL语句构成的数组
	 * @var array-string
	 */
	protected $arr_QueryList;
	
	/**
	 * 信息池实例
	 *
	 * @var Axion_Axion_log
	 */
	protected $obj_Axion_log;
	
	/**
	 * 记录数据库执行结果
	 *
	 * @var boolean
	 */
	protected $bool_OK;
	
	/**
	 * 构造函数，该类被实例化时需依次提供数据库地址、端口、登录用户名、登录密码、数据库名称
	 *
	 */
	function __construct() {
		if (! function_exists ( 'mysql_connect' )) {
			throw new AXION_EXCEPTION ( '没有配置mysql扩展' );
		}
		$this->obj_Axion_log = Axion_log::_init ();
		$this->arr_QueryList = array ();
		$this->bool_OK = true;
		
		$this->getInitConfig ();
	} //function __construct
	

	/**
	 * 析构函数，用于释放数据库连接
	 */
	function __destruct() {
		$this->commitData (); //提交事物处理
		$this->unconnect (); //中断数据库链接
	} //function __destruct
	

	/**
	 * 单件模式创建数据库对象
	 *
	 * @return Axion_db_MySQL
	 */
	static function _init() {
		if (Axion_db_MySQL::$obj_this)
			return Axion_db_MySQL::$obj_this;
		else
			Axion_db_MySQL::$obj_this = new Axion_db_MySQL ( );
		
		return Axion_db_MySQL::$obj_this;
	} //end function _init
	

	/**
	 * 获取数据库连接配置信息
	 *
	 * @return array
	 */
	protected function getInitConfig() {
		$_arr_ini = array ('hostname' => 'localhost', 'port' => '3306', 'user' => 'root', 'password' => 'kellenqian!@#', 'database' => 'cityjiajunew', 'autocommit' => false );
		
		$this->str_host = $_arr_ini ['hostname'];
		$this->int_port = $_arr_ini ['port'];
		$this->str_user = $_arr_ini ['user'];
		$this->str_password = $_arr_ini ['password'];
		$this->str_db = $_arr_ini ['database'];
		$this->setAutoCommit ( $_arr_ini ['autocommit'] );
		return true;
	} //end function getInitArray
	

	/**
	 * 连接MySQL数据库
	 *
	 * @return boolean
	 */
	protected function connect() {
		$this->hd_connect = mysql_connect ( $this->str_host . ":" . $this->int_port, $this->str_user, $this->str_password, true );
		if (! $this->hd_connect) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_ERROR, "与数据库{$this->str_host}连接失败。" );
			return false;
		} //if
		

		if (! @mysql_selectdb ( $this->str_db, $this->hd_connect )) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_ERROR, "打开数据库{$this->str_host}.{$this->str_db}失败。" );
			return false;
		} //if
		

		return true;
	} //function connect
	

	/**
	 * 断开与MySQL数据库的连接
	 *
	 * @return boolean
	 */
	protected function unconnect() {
		if ($this->hd_connect)
			mysql_close ( $this->hd_connect );
		
		return true;
	} //function unconnect
	

	/**
	 * 更换数据库连接配置信息
	 *
	 * @param string $str_databaseName
	 * @param string $str_hostName
	 * @param int $int_port
	 * @param string $str_user
	 * @param string $str_password
	 * 
	 * @return true
	 */
	public function changeDB($str_databaseName = null, $str_user = null, $str_password = null, $bool_autoCommit = null) {
		$this->str_db = $str_databaseName ? $str_databaseName : $this->str_db; //重设数据库名称
		$this->str_user = $str_user ? $str_user : $this->str_user; //重设数据库服务器账号
		$this->str_password = $str_password ? $str_password : $this->str_password; //重设数据库服务器密码
		

		if (! empty ( $this->hd_connect )) {
			$this->unconnect ();
			if (! $this->connect ())
				return false;
		}
		
		$this->unconnect ();
		
		return true;
	} //end function changeDB
	

	/**
	 * 获取数据库中的全部数据表信息
	 *
	 * @return array
	 */
	public function getTablesInfo() {
		$_arr_tablesInfo = array ();
		$_arr_tablesInfo = mysql_list_tables ( $this->str_db );
		
		$_arr_result = array ();
		while ( $_arr_dataLine = mysql_fetch_assoc ( $_arr_tablesInfo ) )
			$_arr_result [] = $_arr_dataLine;
		
		return $_arr_result;
	} //end function getDBInfo
	

	/**
	 * 确认事务的执行结果
	 *
	 * @param boolean $bool_commitData 为true则提交变更，否则回滚操作
	 * @return boolean
	 */
	public function commitData($bool_commitData = true) {
		if ($bool_commitData)
			$_str_sql = "COMMIT";
		else
			$_str_sql = "ROLLBACK";
		
		if (! $this->querySQL ( $_str_sql )) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_ERROR, '在进行数据提交/撤销时发生异常。' );
			return false;
		} //if
		

		return true;
	} //function commitData
	

	/**
	 * 改变当前连接的AutoCommit设置
	 *
	 * @param boolean $_bool_autoCommitSet true为置为自动提交，false为关闭自动提交
	 * @return boolean
	 */
	public function setAutoCommit($bool_autoCommitSet = true) {
		$bool_autoCommitSet ? $_int_autoCommitSet = 1 : $_int_autoCommitSet = 0;
		$_str_sql = "SET AUTOCOMMIT = {$_int_autoCommitSet}";
		if (! $this->querySQL ( $_str_sql )) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_NOTICE, "数据库发生异常，已将连接关闭。" );
			$this->unconnect ();
			return false;
		} //if
		

		return true;
	} //function setAutoCommit
	

	/**
	 * 获得最后一次插入带有auto_increment属性字段的表的记录的auto_increment值，如果获取失败返回false。
	 *
	 * @return int
	 */
	public function getLastInsertID() {
		$_str_sql = "SELECT LAST_INSERT_ID() AS INT_ID";
		$_void_resutl = $this->querySQL ( $_str_sql );
		if (! $_void_resutl) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_NOTICE, '尝试获取最后一次插入的记录的主键值失败。' );
			return false;
		} //if
		

		return $_void_resutl [0] ['INT_ID'];
	} //function getLastInsertID
	

	/**
	 * 获得最后一次选择中符合条件的记录总数。通常的用法为提交一条带有SQL_CALC_FOUND_ROWS属性的SELECT语句，之后使用本方法获得记录总数。
	 *
	 * @return int
	 */
	public function getFoundRows() {
		$_str_sql = "SELECT FOUND_ROWS() AS ROWS";
		$_void_result = $this->querySQL ( $_str_sql );
		if (empty ( $_void_result )) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_NOTICE, '获取所有符合提交的记录总数时发生了错误。' );
			return false;
		} //if
		

		return $_void_result [0] ['ROWS'];
	} //function getFoundRows
	

	/**
	 * 获得最后一次非选择性的数据操作语句所影响到的记录数量，获取失败返回false
	 *
	 * @return int
	 */
	public function getAffectedRows() {
		$_void_result = @mysql_affected_rows ( $this->hd_connect );
		if (! $_void_result) {
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_NOTICE, '获取最后一次数据库操作语句更新的记录数失败。' );
			return false;
		} //if
		

		return $_void_result;
	} //function getAffectedRows
	

	/**
	 * 直接提交一条SQL到数据库执行。如果该语句返回了结果集，那么本方法将结果集总结为一个2维数组的形式返回，否则简单的返回true。如果语句执行失败返回false，同时错误信息被保存到信息池中。
	 *
	 * @param string $_str_sql
	 * @return void
	 */
	public function querySQL($str_sql) {
		//数据库连接检测
		if (empty ( $this->hd_connect )) {
			if (! $this->connect ()) {
				$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_ERROR, '数据库连接错误' );
				return false;
			} //if
		} //if
		

		//记录数据库语句
		$_arr_SQLHistory = &$this->arr_QueryList [];
		$_arr_SQLHistory ['sql'] = $str_sql;
		
		//执行语句
		$_void_result = mysql_query ( $str_sql, $this->hd_connect );
		if ($_void_result === false) {
			$_arr_SQLHistory ['ok'] = false;
			$this->bool_OK = false;
			$this->obj_Axion_log->newMessage ( Axion_log::$INT_ERR_ERROR, @mysql_errno ( $this->hd_connect ) . @mysql_error ( $this->hd_connect ) . ' IN SQL ' . $str_sql );
			return false;
		} else
			$_arr_SQLHistory ['ok'] = true;
			
		//如果返回的不是结果集句柄则直接返回
		if (! is_resource ( $_void_result ))
			return $_void_result;
			
		//对于结果集进行遍历返回2维数组结构的结果
		$_arr_result = array ();
		while ( $_arr_dataLine = mysql_fetch_assoc ( $_void_result ) )
			$_arr_result [] = $_arr_dataLine;
			
		//结果集为空则返回一个空值
		if (empty ( $_arr_result ))
			return '';
		
		return $_arr_result;
	} //function querySQL
} //class Axion_db_MySQL


/**
 * Finish
 * o._.o
 */
?>