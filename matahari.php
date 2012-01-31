<?php namespace Matahari;

class Matahari {

	const VERSION = '0.3.0';
	
	private static $dump_request 	= false; 
	private static $dump_session 	= false;
	
	private static $markers 		= array();
	private static $meta 			= array();
	private static $time 			= '';

	final private function __construct() {}
	
	/**
	 * Spy at marker
	 *
	 * @param mixed 	$marker
	 * @param string 	$marker_name
	 * @param string 	$meta
	 */
	public static function spy($marker, $marker_name = '', $meta = '') {
		if (! isset(static::$time['Script Start'])) {
			static::$time['start'] = microtime(true);
		}

		$message = '<span class="date_field">set at <em>'.date('Y-m-d H:i:s').'</em></span><br><span class="message">'.static::pre(print_r($marker, true)).'</span><br><span class="meta_field">'.$meta.'</span>';
		
		if (is_array(static::$markers[$marker_name])) {
			array_push(static::$markers[$marker_name], $marker);
		} else {
			static::$markers[$marker_name] = array($marker);
		}
	}
	
	/**
	 * Shows the gathered results in a nice div
	 *
	 * @return string
	 */
	public static function spit() {
		static::$time['end'] = microtime(true);

		$script_duration = number_format(static::$time['end'] - static::$time['start'], 6, ',', '.');
		if (static::$dump_request) static::$meta['$_REQUEST'] = static::pre(print_r($_REQUEST, true));
		if (static::$dump_session) static::$meta['$_SESSION'] = static::pre(print_r($_SESSION, true));
		
		$html = '';
		$html.= static::css();
		$html.= '<div id="mata_hari_debug"><div id="title">Mata Hari - exotic espionage for PHP</div>';
		$html.= '<div id="mata_hari_keys">';
		
		// Spy marker header list
		$html.= '<div class="mata_hari_keys_list"><h1>Markers:</h1><ul>';
		foreach (static::$markers as $key => $value) {
			$html.= '<li><a href="#'.md5($key).'">'.$key.' ('.count($value).')'.'</a></li>';
		}
		$html.= '</ul></div>';
		
		// General info header list
		$html.= '<div class="mata_hari_keys_list"><h1>General Info</h1><ul>';
		$html.= '<li>File: '.$_SERVER['PHP_SELF'].'</li>';
		$html.= '<li>Script Duration: '.$script_duration.'</li>';
		$html.= '</ul></div>';
		
		// Dump markers and meta info
		$html.= '</div><br style="clear: both;" /><br />';
		$html.= '<div id="mata_hari_values">';
		
		// Markers
		foreach (static::$markers as $key => $value) {
			$html.= static::dump_marker($key, $value).'<br />';
		}
		
		// Meta info
		foreach (static::$meta as $key => $value) {
			$html.= static::make_meta_list($key, $value);
		}
		
		$html.= '</div><br style="clear: both;" />';
		$html.= '</div>';
		
		return $html;
	}

	/**
	 * Dumps a marker
	 *
	 * @param string $title
	 * @param array $marker
	 * @return string
	 */
	private static function dump_marker($title, $marker) {
		$html = '<div id="'.md5($title).'" class="spy-marker"><h1>Spy Marker: '.$title.'</h1><div>';
		
		foreach ($marker as $key => $value) {
			$html.= $value;
		}
		$html.= '</div></div>';
		
		return $html;
	}	
	
	/**
	 * Creates meta info list
	 *
	 * @param string $title
	 * @param mixed $array
	 * @return string
	 */
	private static function make_meta_list($title, $array) {
		$html = '<div id="'.md5($title).'"><h1>Meta Info: '.$title.'</h1>';
		// get the print_r info into the var
		$html.= print_r($array, true);
		$html.= '</div>';
		
		return $html;
	}
	
	/**
	 * Return CSS for output
	 *
	 * @return string
	 */
	private static function css() {
		return '
		<style>
			#mata_hari_debug {
				margin-bottom: 5px;
				font-family: \'Courier New\', sans-serif;
				width: 85%;
				height: 700px;
				margin: auto;
				padding: 10px;
				padding-right: 0px;
				background-color: #000;
				color: #fff;
				border: 5px solid #ccc;
				text-align: left;
				overflow: auto;
			}
			
			#mata_hari_debug #title {
				display: block;
				width: 100%;
				margin: -10px;
				margin-bottom: 20px;
				padding: 5px;
				background-color: #ccc;
				color: #f00;
				font-size: 20px;
				font-weight: bold;
				clear: both;
			}
			
			#mata_hari_debug .date_field, #mata_hari_debug .meta_field {
				font-size: 12px;
				
			}
			
			#mata_hari_debug .date_field {
				color: #6AD0F7;
			}

			#mata_hari_debug .meta_field {
				color: #509129;
			}
			
			pre {
				background-color: #282828;
				font-size: 12px;
				color: #ccc;
			}
			
			#mata_hari_debug a {
				color: #fff;
				text-decoration: none;
			}
			
			#mata_hari_debug h1 {
				font-size: 16px;
				color: #ccc;
			}
			
			#mata_hari_keys {
				clear: both;
			}
			
			#mata_hari_values {
			}
			
			.mata_hari_keys_list {
				float: left;
				margin: 0 15px 0 0;
				width: 250px;
			}
			.spy-marker {
				padding-right: 5px;
			}
			.spy-marker h1 {
				margin-bottom: 0;
				line-height: 16px;
			}
		</style>
		';
	}
	
	/**
	 * Helper function to wrap the string in <pre> tags
	 *
	 * @param string $string
	 * @return string
	 */
	private static function pre($string) {
		return '<pre>'.$string.'</pre>';
	}

}