<?php

namespace Matahari;

/**
 * Class Matahari
 */
class Matahari
{

    protected static $_instance = null;
    protected static $_result = [];
    protected static $_stack = [];
    protected static $start = '';
    protected static $end = '';

    /**
     * Instantiation
     *
     * @return object
     */
    public static function init()
    {
        if ( ! static::instance()) {
            static::$_instance = new static;
            static::$start = microtime(true);
        }

        return static::$_instance;
    }

    /**
     * Sets a time marker
     *
     * @param string $markerName
     */
    public static function setTimeMarker($markerName = '')
    {
        if ( ! static::instance()) static::init();

        static::$_stack[] = [
            'type' => 'timeMarker',
            'time' => microtime(true),
            'memory' => memory_get_usage(),
            'markerName' => $markerName,
        ];
    }

    /**
     * Spies on an element
     *
     * @param mixed $element
     * @param string $markerName
     */
    public static function setSpyPointMarker($element, $markerName = '')
    {
        if ( ! static::instance()) static::init();

        static::$_stack[] = [
            'type' => 'spyPointMarker',
            'markerName' => $markerName,
            'content' => print_r($element, true),
        ];
    }

    /**
     * Shows an actual information, mostly differences, to a marker
     *
     * @param string $markerName
     * @return bool
     */
    public static function setLookPointMarker($markerName = '')
    {
        if ( ! static::instance()) static::init();

        $currentTime = microtime(true);
        $currentMemory = memory_get_usage();

        try {
            $marker = static::findMarker($markerName);
        } catch (\Exception $e) {
            // could be changed to write to log file or so...
            return false;
        }

        $timeDiff = 0;
        $memoryDiff = 0;
        if (is_int($marker)) {
            $markerValues = static::$_stack[$marker];
            $timeDiff = $currentTime - $markerValues['time'];
            $memoryDiff = round(($currentMemory - $markerValues['memory']) / pow(1024, 2), 3);

            if (substr($memoryDiff, 0, 1) != '-') {
                $memoryDiff = "+" . $memoryDiff;
            }
        }

        static::$_stack[] = [
            'type' => 'lookPointMarker',
            'current_memory' => $currentMemory,
            'time_diff' => round($timeDiff, 4),
            'memory_diff' => $memoryDiff,
            'markerName' => $markerName,
        ];
    }

    /**
     * Checks if a marker has already been set and returns the latest key of it
     *
     * @param string $markerName
     * @return int $key
     * @throws Exception
     */
    private static function findMarker($markerName)
    {
        $return = false;
        foreach (static::$_stack as $key => $item) {
            if ($item['type'] == 'timeMarker' and $item['name'] == $markerName) {
                // we cannot return the first matched key here as we wish to
                // always get the latest key of the marker returned.
                // Marker can repeat themselves but should be displayed
                // as if they have been reset!
                $return = $key;
            }
        }

        if ($return === false) {
            throw new \Exception("Marker name is eiter empty or cannot be found!");
        }

        return $return;
    }

    /**
     * Forges the output
     *
     * @return object    // for method chaining
     */
    public static function flush()
    {
        static::$end = microtime(true);

        if ( ! static::instance()) static::init();

        static::$_result['total_time'] = round((static::$end - static::$start), 4);
        static::$_result['total_memory'] = round(memory_get_usage() / pow(1024, 2), 3);
        static::$_result['markers'] = static::$_stack;

        return static::$_result;
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