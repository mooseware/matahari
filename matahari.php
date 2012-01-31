<?php namespace Matahari;

class Matahari {

	const VERSION = '1.0';
	
	private $always_show_request_array = true;
	private $always_show_session_array = false;

	private $markers 	= array();
	private $meta 		= array();
	private $time 		= '';

	public function __construct() {
		$this->time['Script Start'] = microtime(true);
	}
	
	/**
	 * spy on the code to gather some information
	 *
	 * @param string 	$marker_name
	 * @param mixed 	$message
	 * @param string 	$meta
	 */
	public function spy($marker_name, $message = '', $meta = '') {
		$message = '<span class="date_field">set at <em>' . date('Y-m-d H:i:s') . '</em></span><br><span class="message">' . $this->pre(print_r($message, true)) . '</span><br><span class="meta_field">' . $meta . '</span>';
		
		if (is_array($this->markers[$marker_name])) {
			array_push($this->markers[$marker_name], $message);
		} else {
			$this->markers[$marker_name] = array($message);
		}
	}
	
	/**
	 * Shows the gathered results in a nice div
	 *
	 * @return string
	 */
	public function spit() {
		$this->time['Script End'] = microtime(true);

		$script_duration = number_format($this->time['Script End'] - $this->time['Script Start'], 6, ',', '.');
		if ($this->always_show_request_array) $this->meta['$_REQUEST'] = $this->pre(print_r($_REQUEST, true));
		if ($this->always_show_session_array) $this->meta['$_SESSION'] = $this->pre(print_r($_SESSION, true));
		
		$html = '';
		$html.= $this->css();
		$html.= '<div id="mata_hari_debug"><div id="title">Mata Hari - exotic espionage for PHP</div>';
		$html.= '<div id="mata_hari_keys">';
		
		// Spy marker header list
		$html.= '<div class="mata_hari_keys_list"><h1>Markers:</h1><ul>';
		foreach ($this->markers as $key => $value) {
			$html.= '<li><a href="#' . md5($key) . '">' . $key . ' (' . count($value) . ')' . '</a></li>';
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
		foreach ($this->markers as $key => $value) {
			$html.= $this->dump_marker($key, $value).'<br />';
		}
		
		// Meta info
		foreach ($this->meta as $key => $value) {
			$html.= $this->make_meta_list($key, $value);
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
	private function dump_marker($title, $marker) {
		$html = '<div id="' . md5($title) . '" class="spy-marker"><h1>Spy Marker: ' . $title . '</h1><div>';
		
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
	private function make_meta_list($title, $array) {
		$html = '<div id="' . md5($title) . '"><h1>Meta Info: ' . $title . '</h1>';
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
	private function css() {
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
	private function pre($string) {
		return '<pre>' . $string . '</pre>';
	}

}