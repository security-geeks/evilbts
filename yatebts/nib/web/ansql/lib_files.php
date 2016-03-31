<?php

/**
 * Copyright (C) 2014 Null Team
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

require_once("ansql/base_classes.php");

class GenericFile extends GenericStatus
{
	protected $filename;
	protected $read_handler;
	protected $write_handler;

	function __construct($file_name)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->filename = $file_name;
	}

	function openForRead()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		if (isset($this->read_handler))
			return;

		if(!is_file($this->filename)) {
			$this->setError("File doesn't exist.");
		} else {
			$this->read_handler = fopen($this->filename,"r");
			if (!$this->read_handler)
				$this->setError("Could not open file for reading.");
		}
	}

	function openForWrite()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		if (isset($this->write_handler))
			return;

		$this->write_handler = fopen($this->filename,"w");
		if (!$this->write_handler)
			$this->setError("Could not open file for writting.");
	}

	function getHandler($type="r")
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		if ($type == "r")
			$this->openForRead();
		elseif ($type == "w")
			$this->openForWrite();

		if (!$this->status())
			return;

		return ($type=="r") ? $this->read_handler : $this->write_handler;
	}

	function close()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		if (isset($this->read_handler)) {
			fclose($this->read_handler);
			unset($this->read_handler);
		}
		if (isset($this->write_handler)) {
			fclose($this->write_handler);
			unset($this->write_handler);
		}
	}

	function createBackup()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$backup = $this->filename.".tmp";

		if (!file_exists($this->filename))
			return;

		if (!copy($this->filename, $backup))
			$this->setError("Failed to create backup of existing file: ".$this->filename);
	}

	function restoreBackup()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$backup_file = $this->filename.".tmp";

		if (!file_exists($backup_file))
			return;

		if (!copy($backup_file, $this->filename))
			return $this->setError("Failed to restore backup for file: ".$this->filename);

		$this->removeBackup();
	}

	function removeBackup()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$bk_file = $this->filename.".tmp";

		if (file_exists($bk_file))
			if (!unlink($bk_file))
				$this->setError("Failed to remove backup file ".$bk_file);
	}

	function safeSave($content=NULL)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->createBackup();
		if (!$this->status())
			return;

		if (!method_exists($this,"save"))
			return $this->setError("Please implement 'save' method for class ".get_class($this));

		$this->save($content);
		if (!$this->status()) {
			$this->restoreBackup();
			return;
		}

		$this->removeBackup();
	}
}

class CsvFile extends GenericFile
{
	public $file_content = array();
	private $formats = array();
	private $sep;

	function __construct($file_name, $formats, $file_content=array(), $test_header=true, $read=true, $sep=",")
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		parent::__construct($file_name);
		$this->formats = $formats;
		$this->sep = $sep;
		$this->file_content = $file_content;
		$this->test_header = $test_header;

		if ($read)
			$this->read();
	}

	function read() 
	{	
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->openForRead();
		if (!$this->status())
			return;

		$content = fread($this->read_handler,filesize($this->filename));
		$content = preg_replace(array('/"="/',"/'='/",'/"/',"/'/"),"",$content);
		$content = explode("\n",$content);

		$bad_format = false;
		$first_row = explode(',',trim($content[0]));
		foreach ($first_row as $key => $data)
			$first_row[$key] = str_replace(" ","_", strtolower($first_row[$key]));
		for ($i=0;$i<count($this->formats);$i++) {
			$form = str_replace(" ", "_", strtolower($this->formats[$i]));
			if (!in_array($form, $first_row)) {
				$bad_format = true;
				break;
			}
		}

		if ($this->test_header && count($this->formats) && $bad_format)
			return $this->setError("The format of the file is incorrect.");

		for ($i=0; $i<count($content); $i++) {
			$row = explode(',',trim($content[$i]));
			
			if (!is_array($row) || count($row) < count($this->formats))
				continue;

			if (!$this->test_header)  {
				for ($j=0; $j<count($row); $j++) {
					if (count($this->formats) && isset($this->formats[$j]))
						$this->file_content[$i][$this->formats[$j]] = $row[$j];
					elseif (!count($this->formats))
				                $this->file_content[$i][$j] = $row[$j];	
				}
			} else {
				if (count($this->formats) && $i == 0) 
					continue;
				
				if (count($this->formats)) {
					for ($n = 0 ; $n<count($this->formats); $n++) {
						$pos = array_search(str_replace(" ", "_", strtolower($this->formats[$n])), $first_row);
						if ($pos !== false)
							$this->file_content[$i-1][$this->formats[$n]] = $row[$pos];
					}
				} 
			}

			if ($i%10 == 0 && $this->exceeded_script_memory())
				return $this->setError("Your about to reach your php memory_limit allowed for this script. The csv file reading is stopped. Use smaller csv files."); 

		}
		$this->close();
	}

	//The same functionality as write_in_file function from ansql/lib.php
	function write($key_val_arr=true, $col_header=true)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");

		$this->openForWrite();
		if (!$this->status())
			return;

		$col_nr = 0;

		if (!$this->formats && count($this->file_content))
			foreach($this->file_content[0] as $name=>$val)
				$this->formats[$name] = $name;

		if ($col_header && $this->formats!="no") {
			foreach($this->formats as $column_name => $var_name) {
				$exploded = explode(":",$column_name);
				if (count($exploded)>1)
					$name = $exploded[1];
				else {
					$name = $column_name;
					if(substr($column_name, 0, 9) == "function_")
						$name = substr($column_name,9);
					if(is_numeric($column_name))
						$name = $var_name;
				}
				$val = str_replace("_"," ",ucfirst($name));
				$val = str_replace("&nbsp;"," ",$val);
				$val = ($col_nr) ? $this->sep."\"$val\"" : "\"$val\"";
				fwrite($this->write_handler, $val);
				if ($col_nr%10 == 0 && $this->exceeded_script_memory())
					return $this->setError("Your about to reach your php memory_limit allowed for scripts. The writing of the file will be stopped.");
				$col_nr++;
			} 
			fwrite($this->write_handler,"\n");
		}
		for ($i=0; $i<count($this->file_content); $i++) {
			$col_nr = 0;
			if ($key_val_arr) {
				foreach($this->formats as $column_name=>$names_in_array) {
					$use_vars = explode(",", $names_in_array);
					$exploded_col = explode(":", $column_name);
					$column_value = '';

					if (substr($exploded_col[0],0,9) == "function_") {
						$function_name = substr($exploded_col[0],9,strlen($exploded_col[0]));
						if (count($use_vars)) {
							$params = array();
							for($var_nr=0; $var_nr<count($use_vars); $var_nr++)
								array_push($params, $this->file_content[$i][$use_vars[$var_nr]]);
							$column_value = call_user_func_array($function_name,$params);
						}
					} elseif(isset($this->file_content[$i][$names_in_array])) {
						$column_value = $this->file_content[$i][$names_in_array];
					}
					if (!strlen($column_value))
						$column_value = "";
					$column_value = "\"=\"\"".$column_value."\"\"\"";
					if ($col_nr)
						$column_value = $this->sep.$column_value;
					fwrite($this->write_handler,$column_value);
					if ($col_nr%10 == 0 && $this->exceeded_script_memory())
						return $this->setError("Your about to reach your php memory_limit allowed for scripts. The writing of the file will be stopped.");

					$col_nr++;
				}
			} else {
				for ($j=0; $j<count($this->file_content[$i]);$j++) {
					$column_value = "\"".$this->file_content[$i][$j]."\"";
					if ($col_nr)
						$column_value =  $sep.$column_value;
					fwrite($this->write_handler,$column_value);

					if ($col_nr%10 == 0 && $this->exceeded_script_memory())
						return $this->setError("Your about to reach your php memory_limit allowed for scripts. The writing of the file will be stopped.");

					$col_nr++;
				}
			}
			fwrite($this->write_handler,"\n");
		}
		$this->close();	
	}

	function exceeded_script_memory()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$script_memory_limit = $this->return_bytes(ini_get("memory_limit"));
		if ($script_memory_limit - memory_get_usage() < 3000)
			return true;
		return false;
	}

	function return_bytes($size_str)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		switch (substr($size_str, -1)){
		case 'M':
		case 'm': 
			return (int)$size_str * 1048576;
		case 'K':
		case 'k':
			return (int)$size_str * 1024;
		case 'G':
		case 'g':
			return (int)$size_str * 1073741824;
		default:
			return $size_str;
		}
	}
}

class ConfFile extends GenericFile
{
	public $sections = array();
	public $structure = array();
	public $chr_comment = array(";","#");
	public $initial_comment = null;
	public $write_comments = false;
	public $lines = "\n";

	function __construct($file_name,$read=true,$write_comments=true,$lines="\n")
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		parent::__construct($file_name);
		$this->write_comments = $write_comments;
		$this->lines = $lines;

		if ($read)
			$this->read();
	}

	function read($close=true)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->openForRead();
		if (!$this->status())
			return;

		$last_section = "";
		while(!feof($this->read_handler))
		{
			$row = fgets($this->read_handler);
			$row = trim($row);
			if (!strlen($row))
				continue;
			if ($row == "")
				continue;
			// new section started
			// the second paranthesis is kind of weird but i got both cases
			if (substr($row,0,1)=="[" && substr($row,-1,1)) {
				$last_section = substr($row,1,strlen($row)-2);
				$this->sections[$last_section] = array();
				$this->structure[$last_section] = array();
				continue;
			}
			if (in_array(substr($row,0,1),$this->chr_comment)) {
				if ($last_section == "")
					array_push($this->structure, $row);
				else
					array_push($this->structure[$last_section], $row);
				continue;
			}
			// this is not a section (it's part of a section or file does not have sections)
			$params = explode("=", $row, 2);
			if (count($params)>2 || count($params)<2)
				// skip row (wrong format)
				continue;
			if (is_numeric($params[0]))
				// if key is numeric add __ so we don't get this mixed up with comments
				$params[0] = "__".$params[0];
			if ($last_section == ""){
				$this->sections[$params[0]] = trim($params[1]);
				$this->structure[$params[0]] = trim($params[1]);
			} else {
				$this->sections[$last_section][$params[0]] = trim($params[1]);
				$this->structure[$last_section][$params[0]] = trim($params[1]);
			}
		}
		if ($close)
			$this->close();
	}

	public function save()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->openForWrite();
		if (!$this->status())
			return;

		$wrote_something = false;
		if ($this->initial_comment)
			fwrite($this->write_handler, $this->initial_comment."\n");

		foreach($this->structure as $name=>$value)
		{
			// make sure we don't write the initial comment over and over
			if ($this->initial_comment && !$wrote_something && !is_array($value) && in_array(substr($value,0,1),$this->chr_comment) && $this->write_comments)
				continue;
			if (!is_array($value)) {
				if(in_array(substr($value,0,1),$this->chr_comment) && is_numeric($name)) {

					//writing a comment
					if ($this->write_comments)
						fwrite($this->write_handler, $value."\n");
					continue;
				}
				$wrote_something = true;
				fwrite($this->write_handler, "$name=".ltrim($value).$this->lines);
				continue;
			}else
				fwrite($this->write_handler, "[".$name."]\n");
			$section = $value;
			foreach($section as $param=>$value)
			{
				// if key was numeric __ was added when reading
				if (substr($param,0,2)=="__")
					$param = substr($param,2);

				if (is_array($value)) {
					foreach($value as $key => $val)
						fwrite($this->write_handler, $param."=".ltrim($val).$this->lines);
				} else {
					//writing a comment
					if (in_array(substr($value,0,1),$this->chr_comment) && is_numeric($param)) {
						if ($this->write_comments)
							fwrite($this->write_handler, $value."\n");
						continue;
					}

					$wrote_something = true;
					fwrite($this->write_handler, "$param=".ltrim($value).$this->lines);
				}
			}
			fwrite($this->write_handler, "\n");
		}

		$this->close();
	}

	function getSection($section)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		if (!strlen($section)) {
			$this->setError("Please specify section name.");
			return;
		}
		if (!isset($this->structure[$section])) {
			$this->setError("Section '$section' was not found.");
			return;
		}
		return $this->structure[$section];
	}
}


class JsObjFile extends GenericFile
{
	protected $block;
	protected $prefix = "";
	protected $suffix = "";

	public function __construct($filename, $prefix="", $suffix="", $block=array())
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		parent::__construct($filename, false);

		$this->block = $block;
		$this->prefix = $prefix;
		$this->suffix = $suffix;
	}

	public function read($close=true)
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->openForRead();
		if (!$this->status())
			return;

		$content = "";
		while (!feof($this->read_handler)) {
			$row = fgets($this->read_handler);
			//$row = trim($row);
			//if (!strlen($row))
			//	continue;
			//if ($row=="" || $row==$this->prefix || $row==$this->suffix)
			//	continue;

			$content .= $row;
		}

		if (substr($content,0,strlen($this->prefix))==$this->prefix)
			$content = substr($content,strlen($this->prefix));

		$len = strlen($this->suffix)+1;
		//if (substr($content,-$len)==$this->suffix)
		$content = substr($content,0,strlen($content)-strlen($this->suffix)-1);

		$content = "{".$content."}";

		
		$this->block = json_decode($content,true);
		if (!$this->block)
			$this->setError("Could not decode file: ".$this->filename);

		if ($close)
			$this->close();
	}

	public function save()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		$this->openForWrite();
		if (!$this->status())
			return;

		if (strlen($this->prefix))
			fwrite($this->write_handler, $this->prefix."\n");

		$arr_index = 0;
		$count = count($this->block);
		foreach ($this->block as $index=>$obj) {
			++$arr_index;
			fwrite($this->write_handler, json_encode($index).":".json_encode($obj));
			if ($count>$arr_index)
				fwrite($this->write_handler,",\n");
			else
				fwrite($this->write_handler,"\n");
		}
		//fwrite($this->write_handler, json_encode($this->block));
		if (strlen($this->suffix))
			fwrite($this->write_handler, $this->suffix."\n");

		$this->close();
	}

	public static function runUnitTests()
	{
		$test_path = "";

		$files = array(
			"Default test" => "test_js1.js"); //, "Defaul2" => "test_js2.js");

		$objects = array(
			"Default test" => array(
				"block" => array(
				    "0010155667788" => array("ki"=>"8383838202fklndsiri", "op"=>NULL, "active"=>1),
				    "0010155667789" => array("ki"=>"8383838202fklndaaai", "op"=>NULL, "active"=>1),
				    "0010155667790" => array("ki"=>"8383838202fklbbbbbbbi", "op"=>NULL, "active"=>0)
				),
				"prefix" => "var subscribers = {",
				"suffix" => "};"
			)
		);

		// write
		foreach ($objects as $test_name=>$obj_arr) {

			$test_file = $test_path.$files[$test_name];
			$output_file = $test_path."write/".str_replace("test_","",$files[$test_name]);

			Debug::Output("Running writing test '$test_name'");
			$js_objs = new JsObjFile($output_file, $obj_arr["prefix"], $obj_arr["suffix"], $obj_arr["block"]);
			$js_objs->safeSave();
			$ok = true;
			if (!$js_objs->status()) {
				Debug::output($js_objs->getError());
				$ok = false;
			} elseif (file_get_contents($test_file)!=file_get_contents($output_file)) {
				Debug::output("Test file $test_file and output file $output_file don't match.");
				$ok = false;
			}
			if ($ok)
				Debug::output("-------------------- OK");
			else
				Debug::output("-------------------- Failed");
		}

		// read
		foreach ($objects as $test_name=>$obj_arr) {

			$test_file = $test_path.$files[$test_name];
			$js_objs = new JsObjFile($test_file, $obj_arr["prefix"], $obj_arr["suffix"]);
			$js_objs->read();

			Debug::Output("Running reading test '$test_name'");

			$ok = true;
			if (!$js_objs->status()) {
				Debug::output($js_objs->getError());
				$ok = false;
			} elseif ($js_objs->block!=$objects[$test_name]["block"]) {
				Debug::output("Parsed block doesn't match test. parsed block=".print_r($js_objs->block,true). ", test block=".print_r($objects[$test_name]["block"],true));
				$ok = false;
			}

			if ($ok)
				Debug::output("-------------------- OK");
			else
				Debug::output("-------------------- Failed");
		}
	}

	public function getObject()
	{
		Debug::func_start(__METHOD__,func_get_args(),"ansql");
		return $this->block;
	}
}

?>
