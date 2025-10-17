<?php
namespace App\Core\Lib;
class Layout
{
    protected static $sections = [];
    protected static $stacks = [];
    protected static $current = null;
    protected static $isStack = false;

    /**
     * Start a content section or stack
     */
    public static function start($name, $stack = false)
    {
        self::$current = $name;
        self::$isStack = $stack;
        ob_start();
    }

    /**
     * End the current section or stack
     */
    public static function end()
    {
        if (!self::$current)
            return;

        $content = ob_get_clean();

        if (self::$isStack) {
            if (!isset(self::$stacks[self::$current])) {
                self::$stacks[self::$current] = [];
            }
            self::$stacks[self::$current][] = $content;
        } else {
            self::$sections[self::$current] = $content;
        }

        self::$current = null;
        self::$isStack = false;
    }

    /**
     * Render a section or stack
     */
    public static function yield($name)
    {
        // First check section
        if (isset(self::$sections[$name])) {
            echo self::$sections[$name];
        }

        // Then check stack
        if (isset(self::$stacks[$name])) {
            foreach (self::$stacks[$name] as $content) {
                echo $content;
            }
        }
    }

    /**
     * Get section content (raw)
     */
    public static function get($name)
    {
        return self::$sections[$name] ?? '';
    }

    public static function clear()
    {
        self::$sections = [];
        self::$stacks = [];
        self::$current = null;
        self::$isStack = false;
    }
}
