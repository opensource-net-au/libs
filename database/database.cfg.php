<?php

class exception_db_con extends \Exception {}
class exception_db_con_nodbase extends \Exception {}

define('OS_SQL_SUCCESS',			1000);
define('OS_SQL_ERROR_CON_UNKNOWN',	1001);
define('OS_SQL_ERROR_CON_NODBASE',	1002);
define('OS_SQL_ERROR_CON_NOAUTH',	1003);
define('OS_SQL_ERROR_CON_NOHOST',	1004);
define('OS_SQL_ERROR_EXECUTE',		1005);