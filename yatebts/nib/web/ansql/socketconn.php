<?php
/**
 * socketconn.php
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

require_once("debug.php");

/*
 * Class used to open a socket, send and receive information from it
 * After connecting through the socket the header information send by Yate is ignored.
 * The authentication is done only if param $rmanager_pass is set
 */ 

class SocketConn
{
	public $socket;
	public $error = "";
	public $mode;
	
	 /*
	  * @param $mode String. Possible values:
	  *      -> "write_close" - will write command and close the socket
	  *      -> "write_read" - write a command and read the response - this is the default mode that is set in the constructor
	  *      -> "write_read_close" - write a command, wait for the response and then close the socket 
	  *      If multiple_write_read is needed the default mode can be used, by calling the method command() with the commands needed,
	  *      after that be carreful to close the socket.  
	  */       
	function __construct($ip = null, $port = null, $mode="write_read")
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		global $default_ip, $default_port, $rmanager_pass, $socket_timeout;
		global $default_tries;

		$this->mode = $mode;

		$protocol_list = stream_get_transports();

		if (!isset($socket_timeout))
			$socket_timeout = 5;
		$default_tries = $socket_timeout*100;

		if (!$ip)
			$ip = $default_ip;

		if (!$port)
			$port = $default_port;

		if (substr($ip,0,4)=="ssl:" && !in_array("ssl", $protocol_list))
			die("Don't have ssl support.");

		$errno = 0;
		$socket = fsockopen($ip,$port,$errno,$errstr,$socket_timeout);		
		if (!$socket) {
			$this->error = "Can't connect:[$errno]  ".$errstr;
			$this->socket = false;
		} else {
			$this->socket = $socket;
			stream_set_blocking($this->socket,false);
			stream_set_timeout($this->socket,$socket_timeout);
			$line1 = $this->read(); // read and ignore header
			if (isset($rmanager_pass) && strlen($rmanager_pass)) {
				$res = $this->command("auth $rmanager_pass");
				if (substr($res,0,30)!="Authenticated successfully as ") {
					fclose($socket);
					$this->socket = false;
					$this->error = "Can't authenticate: ".$res;
				}
			}
		}
	}

	/**
	 * Write command through the socket 
	 * close the socket
	 */
	private function write_close($command)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		$this->write($command);
		$this->close();
	}
	
	/* 
	 * Write commmand through the socket 
	 * Read from the socket the answer 
	 * Close the socket
	*/	
	private function write_read_close($command, $marker_end, $limited_tries)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		$this->write($command);
		$response = $this->read($marker_end,$limited_tries);

		$this->close();
		return $response;
	}


	function write($str)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		fwrite($this->socket, $str."\r\n");
	}

	/*
	 * @param $marker_end set to null to not limit the reading
	 */ 
	function read($marker_end = "\r\n",$limited_tries=false)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		global $default_tries;
		if (!$limited_tries)
			$limited_tries = $default_tries; 
		$keep_trying = true;
		$line = "";
		$i = 0;
		//print "<br/>limited_tries=$limited_tries: ";
		while ($keep_trying) {
			$line .= fgets($this->socket,8192);
			if ($line === false)
				continue;
			usleep(15000); // sleep 15 miliseconds
			if (substr($line, -strlen($marker_end)) == $marker_end)
				$keep_trying = false;
			$i++;
			if ($limited_tries && $limited_tries<=$i)
				$keep_trying = false;
			if ($i>1500) {  // don't try to read for more than 15 seconds
				//print "<br/>------force stop<br/>";
				$keep_trying = false;
			}
		}
		if ($marker_end != null) 
			$line = str_replace($marker_end, "", $line);
		return $line;
	}

	function close()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		fclose($this->socket);
	}

	/**
		Commands
		status
		uptime
		reload
		restart
		stop
		.... -> will be mapped into an engine.command
	 */
	function command($command, $marker_end = "\r\n", $limited_tries=false)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
	   
		if ($this->mode == "write_read") {
			 // if after sending command to yate,
			 // the page seems to stall it might be because the generated message has not handled or retval was not set
			$this->write($command);
			return $this->read($marker_end,$limited_tries);
		
		} elseif ($this->mode == "write_close") {
			$this->write_close($command);

		} elseif ($this->mode == "write_read_close") {
			return $this->write_read_close($command, $marker_end, $limited_tries);
		}

	}
}

?>
