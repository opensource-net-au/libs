<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Database connection and error handling class
 *
 * Long description for file (if any)...
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   OpenSourceNetAu\library
 * @package    database
 * @version    SVN: $Id$
 * 
 * @author     Bradley Forster <bradley@opensource.net.au>
 * @author     Another Author <another@example.com>
 * @copyright  2016 opensource.net.au
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link       https://github.com/opensource-net-au/libs/database
 */

namespace OpenSourceNetAu\library;

class database
{
	/**
	 *
	 * @var \mysqli
	 */
	public $mysqli;
	
	/**
	 *
	 * @var int
	 */
	private $status;
	
	
	public function __construct($host,$user,$pass,$dbase=null)
	{
		$this->status = $this->connect($this->mysqli, $host, $user, $pass, $dbase);
//		var_dump($this->status);die;
	}
	
	private function connect(&$con, $host,$user,$pass,$dbase)
	{				
		$status = -1;
		
		try
		{
			@$con = new \mysqli($host,$user,$pass);
			
			if (mysqli_connect_error())
			{
				throw new \exception_db_con( mysqli_connect_errno() );
			}
			
			if($dbase!==null && $con->select_db($dbase) === false)
			{
				throw new \exception_db_con_nodbase( mysqli_connect_errno() );
			}
		}
		catch (\exception_db_con $e)
		{
			//echo "E:".  $e->getMessage()."<BR>";
			
			switch($e->getMessage())
			{
				case 1045:
					$status	= OS_SQL_ERROR_CON_NOAUTH;
					break;
				case 2002:
					$status	= OS_SQL_ERROR_CON_NOHOST;
					break;
				default:
					$status	= OS_SQL_ERROR_CON_UNKNOWN;
			}
		}
		catch (\exception_db_con_nodbase $e)
		{
			$status	= OS_SQL_ERROR_CON_NODBASE;
		}
		return $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	

	
	
	private function call_array($arr)
	{ 
			$refs = array();

			foreach ($arr as $key => $value)
			{
				$refs[$key] = &$arr[$key]; 
			}

			return $refs; 
	}

	/**
	 * 
	 * @todo fix call_user_method_array
	 */
	public function select(&$result, $query, $vars=null)
	{
			$mysqli = $this->mysqli->prepare($query);
			if($vars!==null) {
				call_user_func_array(array($mysqli, "bind_param"), $this->call_array($vars));
	//			@call_user_method_array('bind_param', $mysqli, db_call_user_method_array($vars));
	//			$mysqli->bind_param($types, $sess_ip,$sess_useragent,$sess_hash);
			}
			if(!$mysqli->execute())
			{
				return OS_SQL_ERROR_EXECUTE;
			}
			$res = $mysqli->get_result();
			$mysqli->close();

			$result = array(
				$res->num_rows,
				$res->fetch_all(MYSQLI_ASSOC)
			);
			
			return true;
	}

	/**
	 * 
	 * @todo fix call_user_method_array
	 */
	public function update(&$result, $query, $vars=null)
	{
			$mysqli = $this->mysqli->prepare($query);
			if($vars!==null) {
				call_user_func_array(array($mysqli, "bind_param"), $this->call_array($vars));
	//			call_user_func_array(array($mysqli, "bind_param"), $vars);
			}
				//@call_user_method_array('bind_param', $mysqli, $vars); }
			if(!$mysqli->execute())
			{
				return OS_SQL_ERROR_EXECUTE;
			}
			$result = $mysqli->affected_rows;
			$mysqli->close();
			
			return true;
	}
	
	
	
	/**
	 * Format a date string suitable to insert into a mysql query
	 * 
	 * @todo exception catch invalid date
	 * 
	 * @param string $_date
	 * @param string $_oldFormat
	 * @return string $newDate
	 */
	public static function date_format_sql($_date, $_oldFormat="d-m-Y")
	{
		if($_date==null || $_date=='')
			return null;
		$date = new \DateTime();

	//	$_n = $date->createFromFormat($_format,$_date);
	//	var_dump( $_n );

		return $date->createFromFormat($_oldFormat,$_date)->format("Y-m-d");
	}
	
	/**
	 * 
	 * @return type
	 */
	public static function mysql_version() { 
		$output = shell_exec('mysql -V'); 
		preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
		return $version[0]; 
	}
	
	/**
	 * 
	 * @param type $host
	 * @param type $user
	 * @param type $pass
	 * @return type
	 */
	public static function mysql_version_noshell($host,$user,$pass)
	{
		$link = mysqli_connect($host, $user, $pass);
		$version = ($link) ? mysqli_get_server_info($link) : null;
		mysqli_close($link);
		return $version;
	}
	
}