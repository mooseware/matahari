<?php

class Matahari
{

	const VERSION = '0.6.0';
	
	private static $_instance = null;
	private static $_result = array();
	private static $_stack = array();
	private static $_config = array();
	public static $start = '';
	public static $end = '';

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
		
		try
		{
			$marker = static::find_marker($marker_name);
		}
		catch (\Exception $e)
		{
			// could be changed to write to log file or so...
			return false;
		}

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
		$return = false;
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
		
		if ($return === false)
		{
			throw new \Exception("Marker name is eiter empty or cannot be found!");
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

		static::$_result['total_time'] = round((static::$end - static::$start), 4);
		static::$_result['total_memory'] = round(memory_get_usage() / pow(1024, 2), 3);
		static::$_result['items'] = static::$_stack;

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

	public function to_ff()
	{
		include 'vendor/FirePHP.class.php';

		$firephp = FirePHP::getInstance(true);

		$firephp->log(static::$_result['total_time']."s", 'Total Execution Time:');
		$firephp->log(static::$_result['total_memory']."Mb", 'Total Consumed Memory:');
		
		$i = 1;
		foreach(static::$_result['items'] as $item)
		{
		
			if ($item['name'] == '')
			{
				$item['name'] = '#'.$i;
			}

			switch ($item['type'])
			{
				case 'marker':
					$firephp->log('Marked \''.$item['name'].'\' [ '.round(($item['time'] - Matahari::$start), 4).'s  '.round($item['memory'] / pow(1024, 2), 3).'Mb ]');
					break;

				case 'look':
					$firephp->log('Look at \''.$item['name'].'\' [ '.$item['time_diff'].'s  '.round($item['current_memory'] / pow(1024, 2), 3).'Mb ('.$item['memory_diff'].'Mb) ]');
					break;
				
				case 'spy':
					$firephp->log($item['content'], 'Spying on \''.$item['name'].'\'');
					break;

				case 'memory':
					$firephp->log('Memory consumed at marker \''.$item['name'].'\': '.round($item['memory'] / pow(1024, 2), 3).'Mb');
					break;
			}
		}
	}

	public function to_chrome()
	{
		include 'vendor/ChromePHP.php';
		
		ChromePHP::log('Total Execution Time:', static::$_result['total_time']."s");
		ChromePHP::log('Total Consumed Memory:', static::$_result['total_memory']."Mb");
		
		$i = 1;
		foreach(static::$_result['items'] as $item)
		{
		
			if ($item['name'] == '')
			{
				$item['name'] = '#'.$i;
			}

			switch ($item['type'])
			{
				case 'marker':
					ChromePHP::log('Marked \''.$item['name'].'\' [ '.round(($item['time'] - Matahari::$start), 4).'s  '.round($item['memory'] / pow(1024, 2), 3).'Mb ]');
					break;

				case 'look':
					ChromePHP::log('Look at \''.$item['name'].'\' [ '.$item['time_diff'].'s  '.round($item['current_memory'] / pow(1024, 2), 3).'Mb ('.$item['memory_diff'].'Mb) ]');
					break;
				
				case 'spy':
					ChromePHP::log('Spying on \''.$item['name'].'\': ', $item['content']);
					break;

				case 'memory':
					ChromePHP::log('Memory consumed at marker \''.$item['name'].'\': '.round($item['memory'] / pow(1024, 2), 3).'Mb');
					break;
			}
		}
	}

	/**
	 * Checks if instance has been created
	 *
	 * @return bool
	 */
	private static function instance()
	{
		return ( ! is_null(static::$_instance));
	}

}