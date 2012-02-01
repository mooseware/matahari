<?php namespace Matahari;

class Matahari {

	const VERSION = '0.3.1';
	
	private static $_instance = null;
	private static $_result = '';
	private static $_stack = array();
	private static $_config = array();
	private static $start = '';
	private static $end = '';


	public static function init($config = array()) {
		if ( ! static::instance()) {
			static::$_instance = new static;
			static::$start = microtime(true);

			return static::$_instance;
		}
	}

	/**
	 * Set a time marker
	 *
	 * @param string	$name
	 */
	public static function mark($name = '') {
		if ( ! static::instance()) static::init();

		$item = array(
			'type' => 'mark',
			'time' => microtime(true),
			'name' => $name
		);
		static::$_stack[] = $item;
	}

	/**
	 * Set a memory marker
	 *
	 * @param string	$name
	 */
	public static function memory($name = '') {
		if ( ! static::instance()) static::init();

		$item = array(
			'type' => 'memory',
			'memory' => memory_get_usage(),
			'name' => $name
		);
		static::$_stack[] = $item;
	}

	public static function spy($element, $name = '') {
		if ( ! static::instance()) static::init();

		$item = array(
			'type' => 'spy',
			'name' => $name,
			'content' => print_r($element, true),
		);
		static::$_stack[] = $item;
	}

	public static function spit() {
		static::$end = microtime(true);

		if ( ! static::instance()) static::init();

		static::$_result = static::html('header');
		$time_diff = static::$end - static::$start;
		static::$_result.= '<div class="meta-info">';
		static::$_result.= 'Total Execution Time: <span class="time">'.round($time_diff, 4).' s</span>';
		static::$_result.= '<br />Total Consumed Memory: <span class="time">'.round(memory_get_usage() / pow(1024, 2), 3).' MB</span>';
		static::$_result.= '</div>';

		$i = 1;
		$odd_even = 'even';
		foreach (static::$_stack as $item) {
			switch ($item['type']) {
				case 'mark':
					if ($item['name'] == '') {
						$item['name'] = '#'.$i;
					}
					$time_diff = $item['time'] - static::$start;
					static::$_result.= '<div class="spy-marker-time '.$odd_even.'">Time from start to marker <span class="marker-name">'.$item['name'].'</span>: <span class="time">'.round($time_diff, 4).' s</span></div>';
					break;
				
				case 'spy':
					if ($item['name'] == '') {
						$item['name'] = $i;
					}
					static::$_result.= '<div class="spy-marker '.$odd_even.'">Dump of marker <span class="marker-name">'.$item['name'].'</span>'.static::pre($item['content']).'</div>';
					break;

				case 'memory':
					if ($item['name'] == '') {
						$item['name'] = '#'.$i;
					}
					static::$_result.= '<div class="spy-marker-time '.$odd_even.'">Memory consumed at marker <span class="marker-name">'.$item['name'].'</span></em>: <span class="time">'.round($item['memory'] / pow(1024, 2), 3).' MB</span></div>';
					break;
			}

			$i++;
			($odd_even == 'even') ? $odd_even = 'odd' : $odd_even = 'even';
		}
		static::$_result.= static::html('footer');

		return static::$_instance;
	}

	public function to_board() {
		return static::$_result;
	}

	public function to_file($path) {

	}

	private static function instance() {
		return ( ! is_null(static::$_instance));
	}

	private static function html($type) {
		switch ($type) {
			case 'header':
				return 
					static::css() . '
					<div id="mata_hari_debug">
						<div id="title">Mata Hari - exotic espionage for PHP</div>
						<div id="mata_hari_values">';
				break;
			
			case 'footer':
				return 
					'</div>
					<br style="clear: both;" />
				</div>';
		}
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
				background-color: #141414;
				color: #e2e2e2;
				border: 5px solid #ccc;
				text-align: left;
				overflow: auto;
			}
			
			#mata_hari_debug #title {
				display: block;
				width: 100%;
				padding: 5px;
				background-color: #ccc;
				color: #f00;
				font-size: 20px;
				font-weight: bold;
				clear: both;
			}
			.spy-marker {
				padding: 5px;
				border-bottom: 1px solid #3d3d3d;
			}
			.spy-marker-time {
				padding: 5px;
				border-bottom: 1px solid #3d3d3d;
				padding-bottom: 10px;
				padding-top: 10px;
			}
			.time {
				color: #ffe13d;
			}
			.marker-name {
				color: #7eaccc;
			}
			.even {

			}
			.odd {
				background-color: #282828;
			}
			.meta-info {
				font-weight: bold;
				padding: 5px;
				padding-top: 10px;
				padding-bottom: 10px;
				margin-bottom: 20px;
				border-bottom: 1px dashed #3d3d3d;
				line-height: 20px;
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