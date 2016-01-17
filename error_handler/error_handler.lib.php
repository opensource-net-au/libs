<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PHP error handler class
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
 * @package    error_handler
 * @version    SVN: $Id$
 * 
 * @author     Bradley Forster <bradley@opensource.net.au>
 * @author     Another Author <another@example.com>
 * @copyright  2016 opensource.net.au
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link       https://github.com/opensource-net-au/libs/error_handler
 */

namespace OpenSourceNetAu\library;

class error_handler
{	
    private static $old_error_handler;
	
	public function __construct($log_file=null) 
	{
		$display = OS_SYS_DEBUG;
		
        //error_reporting(E_ALL);
        ini_set("error_reporting", E_ALL);
        ini_set('log_errors', true);
        ini_set('display_errors', $display);
		if(defined('OS_DIR_LOG') && null!==$log_file)
		{
			ini_set('error_log', OS_DIR_LOG. $log_file);
		}
		
        if (!$display) {
            self::$old_error_handler = call_user_func("set_error_handler", array("\OpenSourceNetAu\library\\error_handler", "myErrorHandler"));
            call_user_func("register_shutdown_function", array("\OpenSourceNetAu\library\\error_handler", "fatal_error_handler"));
        }
        else
        {
            self::$old_error_handler = call_user_func("set_error_handler", array("\OpenSourceNetAu\library\\error_handler", "debug_handler"));
            call_user_func("register_shutdown_function", array("\OpenSourceNetAu\library\\error_handler", "fatal_error_handler_echo"));
        }
    }

    public static function debug_handler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

		ob_start();
		
        switch ($errno) {
            case E_USER_ERROR:
                echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
                exit(1);
                break;

            case E_USER_WARNING:
                echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
                break;

            case E_USER_NOTICE:
                echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
                break;

            default:
                echo "Unknown error type: [$errno] $errstr<br />\n";
                break;
        }
        echo "<PRE>";
        //var_dump(debug_backtrace());
        debug_print_backtrace();
        echo "</PRE>";
		
		$output = ob_get_clean();
		
		echo $output;
		
        /* Don't execute PHP internal error handler */
        return true;
    }

    public static function myErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

        switch ($errno) {
            case E_ERROR:
                $err = "\tE_ERROR\t$errno\tLine: $errline\t";
                self::fatalErrMsg($err, $errno, $errstr, $errfile, $errline);
                break;
            case E_WARNING:
                error_log("\tE_WARNING\t$errno\tLine: $errline\t$errfile\t$errstr");
                break;
            case E_PARSE:
                error_log("\tE_PARSE\t$errno\tLine: $errline\t$errfile\t$errstr");
                break;
            case E_NOTICE:
                error_log("\tE_PARSE\t$errno\tLine: $errline\t$errfile\t$errstr");
                break;

            case E_USER_ERROR:
                $err = "\tMy ERROR\t$errno\t$errstr<br />";
                self::fatalErrMsg($err, $errno, $errstr, $errfile, $errline);
                break;
            case E_USER_WARNING:
                error_log("\tMy WARNING\t$errno\tLine: $errline\t$errfile\t$errstr");
                break;

            case E_USER_NOTICE:
                error_log("\tMy NOTICE\t$errno\tLine: $errline\t$errfile\t$errstr");
                break;

            default:
                error_log("\tUnknown error\t$errno\tLine: $errline\t$errfile\t$errstr");
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

    private static function fatalErrMsg($err, $errno, $errstr, $errfile, $errline) {
        $err .= "$errfile\t$errstr<br />";
        //$err.= "Fatal error on line $errline in file $errfile";
        $err.= "PHP: " . PHP_VERSION . " (" . PHP_OS . ")<br />";
        $err.= "Aborting...";

        error_log($err);
        exit(1);
    }

    public static function fatal_error_handler() {
        if (@is_array($e = @error_get_last())) {
            $code = isset($e['type']) ? $e['type'] : 0;
            $msg = isset($e['message']) ? $e['message'] : '';
            $file = isset($e['file']) ? $e['file'] : '';
            $line = isset($e['line']) ? $e['line'] : '';
            if ($code > 0)
                self::myErrorHandler($code, $msg, $file, $line);
        }
    }
    public static function fatal_error_handler_echo() {
        if (@is_array($e = @error_get_last())) {
            $code = isset($e['type']) ? $e['type'] : 0;
            $msg = isset($e['message']) ? $e['message'] : '';
            $file = isset($e['file']) ? $e['file'] : '';
            $line = isset($e['line']) ? $e['line'] : '';
            if ($code > 0)
                self::debug_handler($code, $msg, $file, $line);
        }
    }

}