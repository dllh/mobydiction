<?php


/*==========================================================
>> CLASS Template
	 CORE METHODS:
		Template()	  	Constructor
		parse()			Perform substitutions in template text
  ==========================================================*/

class Template{
	//Parses template and returns for display.

	var $file;
	var $unparsed; 
	var $parsed;
	var $keys;
	var $loopdata;
    var $LM="{";
    var $RM="}";
	var $empty = '';


	/*=================================================================
	>> CONSTRUCTOR Template()
	   Take template file and Config object and put contents of file into
	   internal $unparsed variable. 
	  =================================================================*/

	function Template($file){
		//Set object up to use (limited) tiered error output.
		//$this->error=new Error(1);
		$this->file=$file;
		$fp=fopen($this->file,"r") or die("Could not open template file (" . $this->file . ") at " . __LINE__ . ".");
		$this->unparsed=fread($fp,filesize($this->file)) or die("Could not read template file at " . __LINE__ . ".");
		fclose($fp);
        $this->set_markers();
	}

    function set_markers($left="{", $right="}"){
        $this->LM=$left;
        $this->RM=$right;
    }

	//Set the string that replaces markers that don't match a variable.
	function set_empty($str){
		$this->empty = $str;
	}

	/*=================================================================
	>> FUNCTION parse()
	   Take an associative array and replace bracketed upper-case 
	   instances of keys in template file with matching vals.
	  =================================================================*/

	function parse($vars){
		//print "<pre>" . print_r($vars,1) . "</pre>"; exit;
		//Reset to original value so multiple parses can happen (as with items) without having to reopen the template file each time.
		$this->parsed=$this->unparsed; 
		$this->vars=$vars;
		
		$this->parsed=preg_replace_callback("/" . $this->LM . "INCLUDE ([\w\.\/_]*)" . $this->RM . "/Us",array($this,"eval_include"),$this->parsed);
		$this->parsed=preg_replace_callback("/" . $this->LM . "FOR (\w*)" . $this->RM . "(.*)" . $this->LM . "\/FOR \\1" . $this->RM . "/Us",array($this,"eval_for"),$this->parsed);
		$this->parsed=preg_replace_callback("/<format width=\"(\d*)\" align=\"(\w*)\">([^>]+)<\/format>/",array($this,"apply_format"),$this->parsed);
		$this->parsed=preg_replace_callback("/" . $this->LM . "IF (\w*) *([<>=]+) *([\w]*)" . $this->RM . "(.*)" . $this->LM . "\/IF" . $this->RM . "/Us",array($this,"eval_if"),$this->parsed);
		//$this->parsed=preg_replace_callback("/{IF (\w*)\s*([<>=]{1,2})\s*(.*)}(.*){\/IF}/Us",array($this,"eval_if"),$this->parsed);
		$this->parsed=preg_replace_callback("/" . $this->LM . "IF (\w*)" . $this->RM . "(.*)" . $this->LM . "\/IF" . $this->RM . "/Us",array($this,"eval_if_exists"),$this->parsed);
		$this->parsed=preg_replace_callback("/" . $this->LM . "SKIPIF (\w*)" . $this->RM . "(.*)" . $this->LM . "\/SKIPIF" . $this->RM . "/Us",array($this,"eval_skipif"),$this->parsed);
		while(list($k,$v)=each($vars)){
			if(!is_array($v)){
				$this->keys[$this->LM . strtoupper($k) . $this->RM]=$v;
				//error_log($k);
				$this->parsed=preg_replace("/" . $this->LM . strtoupper($k) . $this->RM . "/",$v,$this->parsed);// or $this->error->trigger("Could not parse template key at " . __LINE__ . ".");
			}
		}	
		//$this->parsed=preg_replace_callback("/{FORMAT=([\w*|=*|\|])}/",array($this,"apply_format"),$this->parsed);
		//$this->parsed=preg_replace_callback("/<pad width=\"(.*)\" align=\"(.*)\">(.*)<\/pad>/",array($this,"apply_format"),$this->parsed,1);
		//Catch anything for which there's a template value specified but no value passed. This occurs on dynamic image generation when there's no info in the db yet, and we wind up getting {VARIABLE_CONTENT_0__0_} printed to the screen. There's definite potential for this to screw up and replace stuff it shouldn't, but I wasn't able to make this happen during testing.
		//$this->parsed=preg_replace("/" . $this->LM . ".{1,}" . $this->RM . "/","",$this->parsed);
		$this->parsed=preg_replace("/" . $this->LM . ".*" . $this->RM . "/",$this->empty,$this->parsed);
		/*
			{IF=([\w|\d|_]*)}(.*){/IF}
		*/
		return $this->parsed;	
	}


	function eval_for($matches){
		//print "GOT TO EVAL FOR";
		$this->tempvars=$this->vars;
		$key=$matches[1];
		$content=$matches[2];
		$return="";
		if(is_array($this->vars[$key])){
			$vars=$this->vars[$key];
			$for_count=1;
			foreach($vars as $iteration){
				$content=$matches[2];
				$this->vars=$iteration;
				while(list($k,$v)=@each($this->vars)){
					if(!is_array($v)){
						//$content = preg_replace("/{" . strtoupper($k) . "}/",$v,$content);// or $this->error->trigger("Could not parse template key at " . __LINE__ . ".");
						$content = preg_replace("/" . $this->LM . strtoupper($k) . $this->RM . "/",$v,$content);// or $this->error->trigger("Could not parse template key at " . __LINE__ . ".");
					}
				}

				$content = preg_replace("/" . $this->LM . "FOR_COUNT" . $this->RM . "/",$for_count,$content);
				$return .= $content;
				//$this->vars=$vars;
				$return=preg_replace_callback("/" . $this->LM . "FOR (\w*)" . $this->RM . "(.*)" . $this->LM . "\/FOR \\1" . $this->RM . "/Us",array($this,"eval_for"),$return);
				//$return=preg_replace_callback("/{IF (\w*)\s*([<>=]{1,2})\s*(.*)}(.*){\/IF}/Us",array($this,"eval_if"),$return);
				$return=preg_replace_callback("/" . $this->LM . "IF (\w*) *([<>=]+) *(\w*)" . $this->RM . "(.*)" . $this->LM . "\/IF" . $this->RM ."/Us",array($this,"eval_if"),$return);
				$return=preg_replace_callback("/" . $this->LM . "IF (\w*)\s*==\s*(.*)" . $this->RM . "(.*)" . $this->LM . "\/IF" . $this->RM . "/Us",array($this,"eval_if"),$return);
				$return=preg_replace_callback("/" . $this->LM . "IF (\w*)\s*!=\s*(.*)" . $this->RM . "(.*)" . $this->LM . "\/IF" . $this->RM . "/Us",array($this,"eval_if"),$return);
				$return=preg_replace_callback("/" . $this->LM . "IF (\w*)" . $this->RM . "(.*)" . $this->LM . "\/IF" . $this->RM . "/Us",array($this,"eval_if_exists"),$return);
				$return=preg_replace_callback("/" . $this->LM . "SKIPIF (\w*)" . $this->RM . "(.*)" . $this->LM  . "\/SKIPIF" . $this->RM . "/Us",array($this,"eval_skipif"),$return);
				$return = preg_replace_callback("/" . $this->LM . "GLOBAL (\w*)" . $this->RM . "/Us", array($this, "eval_global"), $return);
				$return=preg_replace("/" . $this->LM . ".*" . $this->RM . "/",$this->empty,$return);
				$for_count++;
			}
			$this->vars=$this->tempvars;
			return $return;
		}
		$this->vars=$this->tempvars;
		return "";	
	}

	function eval_global($matches){
		if($this->tempvars[$matches[1]]){
			return $this->tempvars[$matches[1]];
		}	
		return 'blah';
		return $this->empty;
	}

	//Callback for when there's a block we want to suppress if we send a given condition. It's the result of laziness: I needed to suppress a sidebar block in a template but didn't want to have to go back and edit the variables sent to the template for all pages but the exception.
	function eval_skipif($matches){
		$key=$matches[1];
		$return=$matches[2];
		if(!$this->vars[$key]){
			return $return;
		}
		return "";	
	}

	function eval_include($matches){
		if(file_exists($matches[1])){
			return file_get_contents($matches[1]);
		}
		return '';
	}

	function eval_if($matches){
		$key=$matches[1];
		$return=$matches[4];

		switch($matches[2]){
			case "<":
				if($this->vars[$key] < $matches[3]){
					return $return;
				}
				break;
			case ">":
				if($this->vars[$key] > $matches[3]){
					return $return;
				}
				break;
			case "<=":
				if($this->vars[$key] <= $matches[3]){
					return $return;
				}
				break;
			case ">=":
				if($this->vars[$key] >= $matches[3]){
					return $return;
				}
				break;
			case "==":
				if($this->vars[$key] == $matches[3]){
					return $return;
				}
				break;
			case "!=":
				if($this->vars[$key] != $matches[3]){
					return $return;
				}
				break;
			default:
				return "";
		}
	}

	function origeval_if($matches){
		$key=$matches[1];
		$return=$matches[3];
		if($this->vars[$key]==$matches[2]){
			return $return;
		}
		return "";	
	}

	function eval_if_exists($matches){
		$key=$matches[1];
		$return=$matches[2];
		if(isset($this->vars[$key])){
			return $return;
		}
		return "";	
	}


	function apply_format2($matches){
		return $matches[3];
	}

	function apply_format($matches){
		$align=$matches[2];
		$align_opts=array("left","right","center");
		if(!in_array($align,$align_opts)){
			$align="left";
		}
		$text=$matches[3];
		$width=$matches[1];
		if(!is_numeric($width)){
			$width=15;
		}
		//Return opposite of alignment param so as a template-builder you think in terms of alignment rather than padding.
		if($align=="right"){
			return str_pad($text,$width," ",STR_PAD_LEFT);
		}
		elseif($align=="center"){
			return str_pad($text,$width," ",STR_PAD_BOTH);
		}
		else{
			return str_pad($text,$width," ",STR_PAD_RIGHT);
		}
	}
}
