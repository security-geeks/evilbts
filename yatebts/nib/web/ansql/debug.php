<?php
/**
 * debug.php
 * This file is part of the FreeSentral Project http://freesentral.com
 *
 * FreeSentral - is a Web Graphical User Interface for easy configuration of the Yate PBX software
 * Copyright (C) 2008-2014 Null Team
 *
 * This software is distributed under multiple licenses;
 * see the COPYING file in the main directory for licensing
 * information for this specific distribution.
 *
 * This use of this software may be subject to additional restrictions.
 * See the LEGAL file in the main directory for details.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// true/false
// if true then all debug messages are remembered unless they are set in $debug_tags to be excluded
// if false then only the tags listed in $debug_tags are included
$debug_all = true;

// array of tags that should be included or excluded from debug
// a tag can't contain white spaces
$debug_tags = array("paranoid","in_framework","in_ansql","ansql","framework");

// default tag if tag is not specified
$default_tag = "logic";

// tags that should never be truncated
$critical_tags = array("critical","query","query failed");

// maximum xdebug message length
// set to false or 0 to disable truncking of messages
$max_xdebug_mess = 50;

/*
// options to notify in case a report was triggered
$debug_notify = array(
    "mail" => array("email@domain.com", "email2@domain.com"),
    "web"  => array("notify", "dump")
);
*/
$debug_notify = array("web"=>array("notify"));

// if $enable_debug_buttons is true, then several Debug buttons are displayed at the top of the page
// Ex: Dump $_SESSION, See $_REQUEST
// you can also add additional buttons if you define the callbacks for them
$debug_buttons = array(
	"dump_session"=>'Dump $_SESSION', 
	"dump_request"=>'Dump $_REQUEST',
	// 
	// "custom_debug_opt1" => callback
);

if(is_file("defaults.php"))
	include("defaults.php");
if(is_file("config.php"))
	include("config.php");

if (!isset($enable_debug_buttons) || $enable_debug_buttons!=true)
	$debug_buttons = false;

class Debug
{
	// xdebug_log when running in cli mode
	// this is only used when $_SESSION is not available
	protected static $_xdebug_log;

	/**
	 * Used when entering a function
	 * Usage: Debug::func_start(__FUNCTION__,func_get_args()); -- in functions
	 * Usage: Debug::func_start(__METHOD__,func_get_args(),"framework");  -- in methods
	 * Output: Entered function_name(0 => array (0 => 'val1',1 => 'val2'),1 => NULL,2 => 'param3')
	 * @param $func Function name
	 * @param $args Array of arguments
	 * @param $tag String Optional. If not set $default_tag is used
	 */
	public static function func_start($func,$args,$tag=null,$obj_class=null,$obj_id=null)
	{
		global $default_tag;
		if (!$tag)
			$tag = $default_tag;

		if ($obj_class)
			$mess = "Entered ".$func." for '$obj_class' id='$obj_id' (".Debug::format_args($args).")";
		else
			$mess = "Entered ".$func."(".Debug::format_args($args).")";

		Debug::xdebug($tag,$mess);
	}


	/**
	 * Calling trigger_report() will register this shutdown_function
	 * This will then be called just when program 
	 */
	public static function shutdown_trigger_report()
	{
		global $count_triggered;
		global $log_triggered;

		if ($count_triggered==1)
			$mess = "There was ".$count_triggered." report";
		else
			$mess = "There were ".$count_triggered." reports";
		$mess .= ": $log_triggered";
		return self::trigger_report("shutdown","$mess",true);
	}

	/**
	 * Function triggers the sending of a bug report
	 * Depending on param $shutdown it agregates the received reports and registers shutdown function shutdown_trigger_report()
	 * Current supported methods: mail, web (dump or notify)
	 * Ex:
	 * $debug_notify = array(
	 * 	"mail" => array("email@domain.com", "email2@domain.com"),
	 * 	"web"  => array("notify", "dump")
	 * );
	 * 'mail' send emails with the log file as attachment or the xdebug log directly if logging to file was not configured
         * 'dump' dumps the xdebug log on the web page
	 * 'notify' Sets 'triggered_error' in $_SESSION. Calling method button_trigger_report() will print a notice on the page
	 *
	 * The report can be triggered by the developper directly when detecting an abnormal situation
	 * or by the user that uses the 'Send bug report' button
	 *
	 * @param $tag String associated Used only when triggered from code
	 * @param $message String Used only when triggered from code
	 * If single parameter is provided and string contains " "(spaces) then it's assumed 
	 * the default tag is used. Default tag is 'logic'
	 * @param $shutdown Bool. Default false. If false, it will just register a shutdown function that sends report at the end. 
	 * If true, we assume we are at shutdown and trigger_report is actually performed
	 */
	public static function trigger_report($tag,$message=null,$shutdown=false)
	{
		global $debug_notify;
		global $server_email_address;
		global $logs_in;
		global $proj_title;
		global $module;
		global $count_triggered;
		global $log_triggered;
		global $software_version;

		if ($tag) 
			self::xdebug($tag,$message);

		if (!$shutdown) {
			// find software_version here, not when called from registered shutdown function, because it seems exec() can't be called from there
			if (!isset($software_version) && function_exists("get_version"))
				$software_version = get_version();

			if (!isset($count_triggered)) {
				$count_triggered = 1;
				$log_triggered = ($message) ? $message : $tag;
				self::xdebug("first_trigger","----------------------");
				register_shutdown_function(array('Debug','shutdown_trigger_report'),array());
			} else {
				$count_triggered++;
				$log_triggered .= ($message) ? "; " . $message : "; ".$tag;
				self::xdebug("trigger","----------------------");
			}
			return;
		}

		// save xdebug
		$xdebug = self::get_xdebug();
		self::dump_xdebug();

		foreach($debug_notify as $notification_type=>$notification_options) {
			if (!count($notification_options))
				continue;
			switch ($notification_type) {
				case "mail":
					if (!isset($server_email_address))
						$server_email_address = "bugreport@localhost.lan";

					$subject = "Bug report for '".$proj_title."'";

					if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_NAME'])
						$ip = $_SERVER['SERVER_ADDR']." (".$_SERVER['SERVER_NAME'].")";
					else {
						exec("/sbin/ifconfig",$info);
						$info = implode($info,"\n");
						$pos_ipv4 = strpos($info,"inet addr:");
						$pos_ipv6 = strpos($info,"inet6 addr: ");
						if ($pos_ipv4!==false)
						    $ip = substr($info,$pos_ipv4+10);
						elseif ($pos_ipv6!==false)
						    $ip = substr($info,$pos_ipv6);
						else
						    $ip = "undetected";
						$break = strpos($ip," ");
						$ip = substr($ip,0,$break);
						if (isset($_SERVER["HOSTNAME"]))
							$ip .= " (".$_SERVER["HOSTNAME"].")";
					} 
					
					$body = "Application is running on ".$ip."\n";
					$body .= "Application: '$proj_title'"."\n";
					if (isset($software_version))
						$body .= "Version: $software_version"."\n";

					if (isset($_SESSION["username"])) {
						$user = $_SESSION["username"];
						$reporter = getparam("name");
						if ($reporter)
							$user .= "($reporter)";
					} else {
						exec('echo "$USER"',$user);
						$user = implode($user,"\n");
					}
					$body .= "User: ".$user."\n";

					$description = getparam("bug_description");
					if ($description)
						$body .= "User description: ".$description."\n";

					if ($message)
						$body .= "Error that triggered report: ".$message."\n";

					$logs_file = self::get_log_file();
					if ($logs_file) {
						$dir_arr = explode("/",$logs_file);
						$path = "";
						for ($i=0; $i<count($dir_arr)-1; $i++)
							$path .= $dir_arr[$i]."/";

						$new_file = $path ."log.".date("YmdHis");
						$attach_file = $path ."attach_log.".date("YmdHis");

						exec("tail -n 500 $logs_file > $attach_file");
						rename($logs_file, $new_file);
					}

					$attachment = ($logs_file) ? array(array("file"=>$attach_file,"content_type"=>"text/plain")) : false;
					if (!$attachment)
						// logs are not kept in file, add xdebug to email body
						$body .= "\n\n$xdebug";
					$body = str_replace("\n","<br/>",$body);

					for ($i=0; $i<count($notification_options); $i++) {
						send_mail($notification_options[$i], $server_email_address, $subject, $body, $attachment,null,false);
					}
					exec("rm -f $attach_file");

					break;
				case "web":
					for ($i=0; $i<count($notification_options); $i++) {
						switch ($notification_options[$i]) {
							case "notify":
								$_SESSION["triggered_report"] = true;
								break;
							case "dump":
								print "<pre>";
								if (!in_array("web",$logs_in))
									// of "web" is not already in $logs_in
									// then print directly because otherwise it won't appear on screen
									if ($message)
										print "Error that triggered report: ".$message."\n";
									print $xdebug;
								print "</pre>\n";
								if (isset($_SESSION["main"]))
									print "<a class='llink' href='".$_SESSION["main"]."?module=$module&method=clear_triggered_error'>Clear</a></div>";
								break;
						}
					}
					break;
			}
		}
	}

	/**
	 * Contacts $message to $_SESSION["xdebug"].
	 * The writting of this log can be triggered or not.
	 * Unless users report a bug or code reaches a developer triggered report,
	 * this log is lost when user session is closed.
	 * @param $tag String associated Used only when triggered from code
	 * @param $message String Used only when triggered from code
	 * If single parameter is provided and string contains " "(spaces) then it's assumed 
	 * the default tag is used. Default tag is 'logic'
	 */
	public static function xdebug($tag, $message=null)
	{
		global $default_tag;
		global $debug_all;
		global $debug_tags;
		global $max_xdebug_mess;
		global $critical_tags;

		if (!in_array($tag, $critical_tags) &&  $max_xdebug_mess && strlen($message)>$max_xdebug_mess)
			$message = substr($message,0,$max_xdebug_mess)." - (truncated)";

		if ($message==null && strpos($tag," ")) {
			$message = $tag;
			$tag = $default_tag;
		}
		if (!$message) {
			self::output("critical","Error in Debug::debug() tag=$tag, empty message in .",false);
			return;
		}
		if ( ($debug_all==true && !in_array($tag,$debug_tags)) || 
		     ($debug_all==false && in_array($tag,$debug_tags)) ) {
			$date = gmdate("[D M d H:i:s Y]");
			if (!isset($_SESSION["dump_xdebug"]))
				self::concat_xdebug("\n$date".strtoupper($tag).": ".$message);
			else
				self::output($tag, $message, false);
		}
	}

	/**
	 * Logs/Prints a nice formated output of PHP debug_print_backtrace()
	 */
	public static function trace()
	{
		$trace = self::debug_string_backtrace(__METHOD__);
		$trace = print_r($trace,true);
		self::output("------- Trace\n".$trace);
	}

	/**
	 * Logs/Prints a message
	 * output is controled by $logs_in setting
	 * Ex: $logs_in = array("web", "/var/log/applog.txt", "php://stdout");
	 * 'web' prints messages on web page
	 * If $logs_in is not configured default is $logs_in = array("web")
	 * @param $tag String Tag for the message
	 * @param $msg String Message to pe logged/printed
	 * @param $write_to_xdebug Bool. Default true. Write to xdebug log on not.
	 * Set to false when called from inside xdebug to avoid loop
	 */
	public static function output($tag,$msg=NULL,$write_to_xdebug=true)
	{
		global $logs_in;

		// log output in xdebug as well
		// if xdebug is written then this log will be duplicated
		// but it will help debugging to have it inserted in appropriate place in xdebug log

		// still, skip writting to xdebug if xdebug is currently dumped constantly
		if ($write_to_xdebug && !isset($_SESSION["dump_xdebug"]))
			self::xdebug($tag,$msg);


		if ($msg==null && strpos($tag," ")) {
			$msg = $tag;
			$tag = "output";
		}
		if (!$msg) {
			self::output("error", "Error in Debug::debug() tag=$tag, empty message in .");
			return;
		}

		// In case we didn't configure a log file just throw this in the apache logs
		// The majority of admins/people installing will know to look in apache logs for issues
		if (!isset($logs_in)) {
			trigger_error("$tag: $msg", E_USER_NOTICE);
			return;
		}

		$arr = $logs_in;
		if(!is_array($arr))
			$arr = array($arr);

		for ($i=0; $i<count($arr); $i++) {
			if ($arr[$i] == "web") {
				print "<br/>\n<br/>\n$msg<br/>\n<br/>\n";
			} else {
				$date = gmdate("[D M d H:i:s Y]");
				if (!is_file($arr[$i]))
					$fh = fopen($arr[$i], "w");
				else
					$fh = fopen($arr[$i], "a");
				fwrite($fh, $date.strtoupper($tag).": ".$msg."\n");
				fclose($fh);
			}
		}
	}

	/**
	 * Outputs xdebug log in file of web page depending on $logs_in
	 */
	public static function dump_xdebug()
	{
		$xdebug = "------- XDebug:".self::get_xdebug();
		Debug::output($xdebug);
		// reset debug to make sure we don't dump same information more than once
		self::reset_xdebug();
	}

	/**
	 * Contacts array of arguments and formats then returning a string
	 * @param $args Array of function arguments
	 * @return String with nicely formated arguments
	 */
	public static function format_args($args)
	{
		$res = str_replace("\n","",var_export($args,true));
		$res = str_replace("  ","",$res);
		$res = str_replace(",)",")",$res);
		// exclude 'array ('
		$res = substr($res,7);
		// exclude last ', )'
		$res = substr($res,0,strlen($res)-1);
		return $res;
	}

	/**
	 * Looks in $logs_in and returns a log file if set. It ignores 'web' and entry containing 'stdout'
	 * @return String Log File 
	 */
	public static function get_log_file()
	{
		global $logs_in;
    
		$count_logs = count($logs_in);
		for ($i=0; $i<$count_logs; $i++) {
			if ($logs_in[$i]=="web" || strpos($logs_in[$i],"stdout"))
				continue;
			return $logs_in[$i];
		}
		return false;
	}

	/**
	 * Clears "triggered_report" variable from session
	 */
	public static function clear_triggered_error()
	{
		unset($_SESSION["triggered_report"]);
	}

	/**
	 * Returns output of PHP debug_print_backtrace after stripping out name and name provided in @exclude
	 * @param String Function name to exclude from trace
	 * @return String Trace
	 */
	private static function debug_string_backtrace($exclude=null) 
	{
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_contents();
		ob_end_clean();

		$exclude = ($exclude) ? array(__METHOD__,$exclude) : array(__METHOD__);
		for ($i=0; $i<count($exclude); $i++) {
			// Remove first item from backtrace as it's this function which
			// is redundant.
			$trace = preg_replace ('/^#0\s+' . $exclude[$i] . "[^\n]*\n/", '', $trace, 1);
			// Renumber backtrace items.
			$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
		}

		return $trace;
	}

	// METHODS THAT COME AFTER THIS USE FUNCTIONS FROM lib.php THAT WAS NOT REQUIRED IN THIS FILE
	// IN CASE YOU WANT TO USE THE Debug CLASS SEPARATELY YOU NEED TO REIMPLEMENT THEM

	/**
	 * Displays buttons to trigger bug report, dump_request, dump_session + custom callbacks
	 * This must be called from outside ansql. Ex: get_content() located in menu.php
	 */
	public static function button_trigger_report()
	{
		global $debug_notify;
		global $module;
		global $debug_buttons;
		global $dump_request_params;

		print "<div class='trigger_report'>";
		if (isset($debug_notify["mail"]) && count($debug_notify["mail"]))
			print "<a class='llink' href='main.php?module=".$module."&method=form_bug_report'>Send bug report</a>";
		if (isset($_SESSION["triggered_report"]) && $_SESSION["triggered_report"]==true && isset($debug_notify["web"]) && in_array("notify",$debug_notify["web"]))
			print "<div class='triggered_error'>!ERROR <a class='llink' href='".$_SESSION["main"]."?module=$module&method=clear_triggered_error'>Clear</a></div>";

		//buttons to Dump $_SESSION, See $_REQUEST + custom callback
		if (isset($debug_buttons) && is_array($debug_buttons)) {
			foreach ($debug_buttons as $method=>$button) {
				switch ($method) {
					case "dump_session":
						print "<a class='llink' href='pages.php?method=$method' target='_blank'>$button</a>";
						break;
					case "dump_request":
						$dump_request_params = true;
						print "<a class='llink' onclick='show_hide(\"dumped_request\");'>$button</a>";
						break;
					default:
						call_user_func($button);
				}
			}
		}

		print "</div>";
	}

	/**
	 * Builds and displays form so user can send a bugreport
	 * This must be called from outside ansql from function 
	 * that could be located in menu.php
	 */
	public static function form_bug_report()
	{
		$fields = array(
			"bug_description"=>array("display"=>"textarea", "compulsory"=>true, "comment"=>"Description of the issue. Add any information you might consider relevant or things that seem weird.<br/><br/>This report will be sent by email message."),

			"name"=>array()
		);

		start_form();
		addHidden(null,array("method"=>"send_bug_report"));
		editObject(null,$fields,"Send bug report","Send");
		end_form();
	}

	/**
	 * Triggers a bug report containing the info submitted by the user.
	 * Report was sent from \ref form_bug_report.
	 * This must be called from outside ansql from function 
	 * that could be located in menu.php
	 */ 
	public static function send_bug_report()
	{
		$report = getparam("bug_description");
		$from = "From: ".getparam("name");
		$report = "$from\n\n$report";
		self::trigger_report("REPORT",$report);
	}

	public static function reset_xdebug()
	{
		if (self::$_xdebug_log!==false)
			self::$_xdebug_log = '';
		elseif (isset($_SESSION["xdebug"]))
			$_SESSION["xdebug"] = '';
		// was not initialized. Nothing to reset
	}

	public static function get_xdebug()
	{
		if (self::$_xdebug_log!==false)
			return self::$_xdebug_log;
		elseif (isset($_SESSION["xdebug"]))
			return $_SESSION["xdebug"];
		// was not initialized. Nothing to return
		return '';
	}

	private static function concat_xdebug($mess)
	{
		if (self::$_xdebug_log===NULL) {
			if (session_id()!="") {
				if (!isset($_SESSION["xdebug"])) {
					$_SESSION["xdebug"] = "";
				}
				self::$_xdebug_log = false;
			} else {
				self::$_xdebug_log = '';
			}
		}

		if (self::$_xdebug_log!==false)
			self::$_xdebug_log .= $mess;
		else
			$_SESSION["xdebug"] .= $mess;
	}
}


/**
 * Function called when dump_session is set in $debug_buttons. 
 * Dumps $_SESSION or calls $cb_dump_session if set and is callable
 */
function dump_session()
{
	global $cb_dump_session;

	if (isset($cb_dump_session) && is_callable($cb_dump_session))
		call_user_func($db_dump_session);
	else {
		print "<pre>";
		var_dump($_SESSION);
		print "</pre>";
	}
}
?>
