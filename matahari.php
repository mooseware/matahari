<?php namespace Matahari;

class Matahari
{

	const VERSION = '0.4.0';
	
	private static $_instance = null;
	private static $_result = '';
	private static $_stack = array();
	private static $_config = array();
	private static $start = '';
	private static $end = '';

	/**
	 * Instantiation
	 *
	 * @param array 	$config 	// not yet used!
	 * @return object
	 */
	public static function init($config = array())
	{
		if ( ! static::instance())
		{
			static::$_instance = new static;
			static::$start = microtime(true);

			return static::$_instance;
		}
	}

	/**
	 * Sets a time marker
	 *
	 * @param string	$name
	 */
	public static function mark($name = '')
	{
		if ( ! static::instance()) static::init();

		$item = array(
			'type' => 'marker',
			'time' => microtime(true),
			'memory' => memory_get_usage(),
			'name' => $name
		);
		
		static::$_stack[] = $item;
	}

	/**
	 * Sets a memory marker
	 *
	 * @param string	$name
	 */
	public static function memory($name = '')
	{
		if ( ! static::instance()) static::init();

		$item = array(
			'type' => 'memory',
			'memory' => memory_get_usage(),
			'name' => $name
		);

		static::$_stack[] = $item;
	}

	/**
	 * Spies on an element
	 * 
	 * @param mixed 	$element
	 * @param string 	$name
	 */
	public static function spy($element, $name = '')
	{
		if ( ! static::instance()) static::init();

		$item = array(
			'type' => 'spy',
			'name' => $name,
			'content' => print_r($element, true),
		);

		static::$_stack[] = $item;
	}

	/**
	 * Shows an actual information, mostly differences, to a marker
	 * 
	 * @param string	$marker_name
	 */
	public static function look($marker_name = '')
	{
		if ( ! static::instance()) static::init();

		$current_time = microtime(true);
		$current_memory = memory_get_usage();
		$marker = static::find_marker($marker_name);

		if (is_int($marker))
		{
			$marker_values = static::$_stack[$marker];
			$time_diff = $current_time - $marker_values['time'];
			$memory_diff = round(($current_memory - $marker_values['memory']) / pow(1024, 2), 3);

			if (substr($memory_diff, 0, 1) != '-')
			{
				$memory_diff = "+".$memory_diff;
			}
		}

		$item = array(
			'type' => 'look',
			'current_memory' => $current_memory,
			'time_diff' => round($time_diff, 4),
			'memory_diff' => $memory_diff,
			'name' => $marker_name,
		);

		static::$_stack[] = $item;
	}

	/**
	 * Checks if a marker has already been set and returns the latest key of it
	 * 
	 * @param string	$marker_name
	 * @return integer	$key
	 */
	private static function find_marker($marker_name)
	{
		foreach (static::$_stack as $key => $item)
		{
			if ($item['type'] == 'marker' and $item['name'] == $marker_name)
			{
				// we cannot return the first matched key here as we wish to
				// always get the latest key of the marker returned.
				// Marker can repeat themselves but should be displayed
				// as if they have been reset!
				$return = $key;
			}
		}

		return $return;
	}

	/**
	 * Forges the output
	 * 
	 * @return object 	// for method chaining
	 */
	public static function spit()
	{
		static::$end = microtime(true);

		if ( ! static::instance()) static::init();

		static::$_result = static::html('header');
		$time_diff = static::$end - static::$start;
		static::$_result.= '<div class="meta-info">';
		static::$_result.= 'Total Execution Time: <span class="time">'.round($time_diff, 4).'s</span>';
		static::$_result.= '<br />Total Consumed Memory: <span class="memory">'.round(memory_get_usage() / pow(1024, 2), 3).'Mb</span>';
		static::$_result.= '</div>';

		$i = 1;
		$odd_even = 'even';
		foreach (static::$_stack as $item)
		{
			if ($item['name'] == '')
			{
				$item['name'] = '#'.$i;
			}

			static::$_result.=  '<div class="spy-marker-time '.$odd_even.'">';

			switch ($item['type'])
			{
				case 'marker':
					static::$_result.= sprintf(
						'Marked <span class="marker-name">"%s</span> [ <span class="time">%ss</span> <span class="memory">%sMb</span> ]', 
						$item['name'], 
						round(($item['time'] - static::$start), 4), 
						round($item['memory'] / pow(1024, 2), 3)
					);
					break;

				case 'look':
					static::$_result.= sprintf(
						'Look at <span class="marker-name">%s</span> [ <span class="time">%ss</span> <span class="memory">%sMb</span> (<span class="memory">%sMb</span>) ]',
						$item['name'],
						$item['time_diff'],
						round($item['current_memory'] / pow(1024, 2), 3),
						$item['memory_diff']
					);
					break;
				
				case 'spy':
					static::$_result.= sprintf(
						'Spying on <span class="marker-name">%s</span>%s',
						$item['name'],
						static::pre($item['content'])
					);
					break;

				case 'memory':
					static::$_result.= sprintf(
						'Memory consumed at marker <span class="marker-name">%s</span></em>: <span class="time">%sMb</span>',
						$item['name'],
						round($item['memory'] / pow(1024, 2), 3)
					);
					break;
			}

			static::$_result.= '</div>';

			$i++;
			($odd_even == 'even') ? $odd_even = 'odd' : $odd_even = 'even';
		}
		static::$_result.= static::html('footer');

		return static::$_instance;
	}

	/**
	 * Returns the result for later use
	 *
	 * @return string
	 */
	public function to_board()
	{
		return static::$_result;
	}

	/**
	 * Streams result into a file
	 * 
	 * @todo: write method!
	 */
	public function to_file($path) {}

	/**
	 * Checks if instance has been created
	 *
	 * @return bool
	 */
	private static function instance()
	{
		return ( ! is_null(static::$_instance));
	}

	/**
	 * Returns some HTML structures
	 * 
	 * @param string 	$type
	 * @return string
	 */
	private static function html($type)
	{
		switch ($type)
		{
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
	 * Returns CSS for output
	 *
	 * @return string
	 */
	private static function css()
	{
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
			.memory {
				color: #89af62;
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
	private static function pre($string)
	{
		return '<pre>'.$string.'</pre>';
	}

}