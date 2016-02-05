<?php
class Assert {
    public static function equals($a, $b, $strict=false) {
        $comparison = ($strict ? $a === $b : $a ==$b);
        self::_assert($comparison, "\"{$a}\" does not equal \"{$b}\"".($strict ? ' (strict)' : ''));
    }
    
    public static function classExists($classname) {
        self::_assert(class_exists($classname), "Class \"{$classname}\" doesn't exist");
    }
    
    public static function fileExists($file) {
        self::_assert(file_exists($file), "File \"{$file}\" doesn't exist");
    }
    
    public static function isSubclassOf($class, $parent_class) {
        if (is_object($class)) {
            $class = get_class($class);
        }
        self::_assert(is_subclass_of($class, $parent_class), "Class \"{$class}\" is not a subclass of \"{$parent_class}\"");
    }
    
    public static function isInstanceOf($object, $class_name) {
        self::_assert($object instanceof $class_name, "Given object is not an instance of \"{$class_name}\"");
    }
    
    public static function isCallable($callback) {
        self::_assert(is_callable($callback), "Proposed callback \"".var_export($callback, true)."\" is not callable");
    }
    
    public static function notNull($thing) {
        self::_assert(!is_null($thing), "Something was null that shouldn't be");
    }
    
    private static function _assert($condition, $message) {
        if (!$condition) {
            echo "<pre>";
            debug_print_backtrace();
            echo "</pre>";
            trigger_error("<b>Assertion failed:</b> {$message}", E_USER_ERROR);
        }
    }
}
?>