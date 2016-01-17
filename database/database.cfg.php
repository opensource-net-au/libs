<?php
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

class exception_db_con extends \Exception {}
class exception_db_con_nodbase extends \Exception {}

define('OS_SQL_SUCCESS',			1000);
define('OS_SQL_ERROR_CON_UNKNOWN',	1001);
define('OS_SQL_ERROR_CON_NODBASE',	1002);
define('OS_SQL_ERROR_CON_NOAUTH',	1003);
define('OS_SQL_ERROR_CON_NOHOST',	1004);
define('OS_SQL_ERROR_EXECUTE',		1005);