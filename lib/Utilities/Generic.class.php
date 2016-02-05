<?php
class Generic {
    public static function &get($var, $name, $default=null) {
        if (is_array($var)) {
            return Arrays::get($var, $name, $default);
        }
        
        if (is_object($var)) {
            return Objects::get($var, $name, $default);
        }
        
        throw new Exception("\$var must be either an array or object");
    }
    
    public static function set(&$var, $name, $value) {
        if (is_array($var)) {
            $var[$name] = $value;
        } elseif (is_object($var)) {
            $var->$name = $value;
        } else {
            throw new Exception("\$var must be either an array or object");
        }
    }
}
?>