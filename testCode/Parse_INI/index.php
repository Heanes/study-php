<?php
/**
 * @doc 
 * @filesource index.php
 * @copyright heanes.com
 * @author Heanes
 * @time 2015年4月16日下午9:43:33
 */

class parse_ini {
	
	private $ini_file='str';
	
	public function __construct($str) {
		$this->set_ini_file($str);
	}
	
	public function get_ini_file() {
		return $this->ini_file;
	} 
	
	public function set_ini_file($path_str='config.ini'){
		$this->ini_file=$path_str;
	}
	
	public function get_ini_data() {
		return $this->get_parse_ini ( $this->get_ini_file() );
	}
	
	public function toString(){
		$this->print_arr($this->get_ini_data());
	}
	
	public function print_arr($array) {
		if (! is_array ( $array )) {
			return false;
		}
		if (is_array ( $array ) && ! empty ( $array )) {
			echo '<div style="width:800px;margin:0 auto;"><table style="border:1px solid #eee;border-spacing:0;border-collapse:collapse;font-size:12px;">';
			foreach ( $array as $key => $value ) {
				if (is_array ( $value )) {
					echo '<div style="width:800px;margin:0 auto;"><span style="display:block;background-color:#daf3ff;border: 1px solid #d2e8fa;padding:5px 10px;">' . $key . '</span></div>';
					$this->print_arr ( $value );
				} else {
					echo '<tr>' . '<td style="width:300px;border:1px solid #eee;padding:2px;word-break:break-all;">' . $key . '</td>' . '<td style="width:500px;border:1px solid #eee;padding:2px;word-break:break-all;">' . $value . '</td>' . '</tr>';
				}
			}
			echo '</table></div>';
		}
	}
	

	public function get_parse_ini() {
	
		// if cannot open file, return false
		if (! is_file ( $this->ini_file))
			return false;
	
		$ini = file ( $this->ini_file );
	
		// to hold the categories, and within them the entries
		$cats = array ();
	
		foreach ( $ini as $i ) {
			if (@preg_match ( '/\[(.+)\]/', $i, $matches )) {
				$last = $matches [1];
			} elseif (@preg_match ( '/(.+)=(.+)/', $i, $matches )) {
				$cats [$last] [trim ( $matches [1] )] = trim ( $matches [2] );
			}
		}
	
		return $cats;
	}
	/*
	public function get_parse_ini($file) {
	
		// if cannot open file, return false
		if (! is_file ( $file ))
			return false;
	
		$ini = file ( $file );
	
		// to hold the categories, and within them the entries
		$cats = array ();
	
		foreach ( $ini as $i ) {
			if (@preg_match ( '/\[(.+)\]/', $i, $matches )) {
				$last = $matches [1];
			} elseif (@preg_match ( '/(.+)=(.+)/', $i, $matches )) {
				$cats [$last] [trim ( $matches [1] )] = trim ( $matches [2] );
			}
		}
	
		return $cats;
	}
	*/
	
	
	public function put_ini_file($file, $array, $i = 0) {
		$str = "";
		foreach ( $array as $k => $v ) {
			if (is_array ( $v )) {
				$str .= str_repeat ( " ", $i * 2 ) . "[$k]\r\n";
				$str .= put_ini_file ( "", $v, $i + 1 );
			} else
				$str .= str_repeat ( " ", $i * 2 ) . "$k = $v\r\n";
		}
	
		$phpstr = "<?PHP\r\n/*\r\n" . $str . "*/\r\n?>";
	
		if ($file)
			return file_put_contents ( $file, $phpstr );
		else
			return $str;
	}
	
}
/**
$ini=new parse_ini('config.ini');
$ini->toString();
*/
//$inifile = "config.ini";
//$inivalue = get_parse_ini ( $inifile );
//print_r ( $inivalue );
//$inivalue ['config'] ['length'] = 6;
//put_ini_file ( "config.ini", $inivalue, $i = 0 );

