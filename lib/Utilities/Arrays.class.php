<?php
class Arrays {
    public static function indexByProperty($objects, $property, $default = '') {
        $output = array();
        foreach ($objects as $object) {
            $output[Objects::get($object, $property, $default)] = $object;
        }
        
        return $output;
    }
    
    public static function groupByProperty($objects, $property, $default = '') {
        $output = array();
        foreach ($objects as $object) {
            self::ensureSet($output, Objects::get($object, $property, $default));
            $output[Objects::get($object, $property, $default)][] = $object;
        }
        
        return $output;
    }
    
    public static function collectProperty($objects, $property, $default = '') {
        return self::collectProperties($objects, array($property), array($property => $default));
    }
    
    public static function collectProperties($objects, $properties, $defaults = null) {
        $output = array();
        foreach ($objects as $object) {
            foreach ($properties as $property) {
                $output[] = Objects::get($object, $property, $defaults[$property]);
            }
        }
        
        return $output;
    }
    
    public static function collectAssoc($arrays, $index, $default = '') {
        return self::collectAssocs($arrays, array($index), array($index => $default));
    }
    
    public static function collectAssocs($arrays, $indexes, $defaults = null) {
        $output = array();
        foreach ($arrays as $array) {
            foreach ($indexes as $index) {
                $output[] = Arrays::get($array, $index, $defaults[$index]);
            }
        }
        
        return $output;
    }
    
    public static function ensureSet(&$array, $key, $default = array()) {
        if (!isset($array[$key])) $array[$key] = $default;
    }
    
    public static function ensureArray($array) {
        if (!is_array($array)) $array = array($array);
        return $array;
    }
    
    public static function get($array, $key, $default = null) {
        if (isset($array[$key])) return $array[$key];
        return $default;
    }
    
    public static function getNext($array, $current_key) {
        $keys = array_keys($array);
        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] == $current_key) {
                if ($i == count($keys) - 1) {
                    return $array[$keys[0]];
                }
                return $array[$keys[$i + 1]];
            }
        }
        
        return null;
    }
    
    public static function index(array $array, $callback) {
        Assert::isCallable($callback);
        $output = array();
        
        foreach ($array as $item) {
            $output[call_user_func($callback, $item)] = $item;
        }
        
        return $output;
    }
    
    public static function isearch($needle, array $haystack) { 
        foreach ($haystack as $key => $val) {
            if (strcasecmp($val, $needle) === 0) {
                return $key;
            }
        }
        return false;
    }
    
    public static function isearchStartsWith($needle, array $haystack) {
        foreach ($haystack as $key => $val) {
            if (Strings::startsWith(strtolower($val), strtolower($needle))) {
                return $key;
            }
        }
        
        return false;
    }

    public static function first(array $array) {
        $keys = array_keys($array);
        return $array[$keys[0]];
    }
    
    public static function last(array $array) {
        if (count($array) == 0) return null;
        $keys = array_keys($array);
        return $array[$keys[count($keys) - 1]];
    }

    public static function rotate(array $array, $rotate_to_value) {
        if (array_search($rotate_to_value, $array) === FALSE) return FALSE;
        $keys = array_keys($array);
        while ($array[$keys[0]] != $rotate_to_value) {
            $array[] = array_shift($array);
        }
        
        return $array;
    }
    
    public static function average(array $array) {
        return array_sum($array) / count($array);
    }
    
    public static function each(array $array, $callback) {
        $output = array();
        Assert::isCallable($callback);
        
        foreach ($array as $index => $item) {
            $output[$index] = call_user_func($callback, $item);
        }
        
        return $output;
    }
    
    public static function castToObjects(array $array) {
        $new_array = array();
        foreach ($array as $key => $value) {
            $new_array[$key] = (object)$value;
        }
        
        return $output;
    }
    
    public static function expand(array $flat_data, array $expansion_data, $name_property='name', $children_property='children', $output_type='keep_type') {
        /*
         * array('TopBox', 'MiddleBox', 'BottomBox' => array('Average'))
        */
        $normalized_expansion_data = array();
        foreach ($expansion_data as $index => $data) {
            if (is_array($data)) {
                $normalized_expansion_data[$index] = $data;
            } elseif (is_string($data)) {
                $normalized_expansion_data[$data] = array();
            } else {
                throw new Exception("Values in expansion data array must be strings or arrays");
            }
        }
        
        $expanded = array();
        foreach ($flat_data as $data) {
            if ($output_type == 'keep_type') {
                if (is_array($data)) {
                    $output_type = 'array';
                } elseif (is_object($data)) {
                    $output_type = 'object';
                } else {
                    throw new Exception("Data must be either an array or object");
                }
            }
            Arrays::placeInExpanded($data, $expanded, $normalized_expansion_data, $name_property, $children_property, $output_type);
        }
    }
    
    private static function placeInExpanded($data, array &$nodes, array $expansion_data, $name_property='name', $children_property='children', $output_type='keep_type') {
        $keys = array_keys($expansion_data);
        $column = $keys[0];
        $data_to_store = array_shift($expansion_data);
        
        $found_node = null;
        
        foreach ($nodes as $node) {
            if (Generic::get($node, $name_property) == Generic::get($data, $column)) {
                $found_node = $node;
            }
        }
        
        if ($found_node == null) {
            $found_node = array();
            $found_node[$name_property] = Generic::get($data, $column);
            
            foreach ($data_to_store as $col) {
                $found_node[$col] = Generic::get($data, $col);
            }
            
            if (count($expansion_data)) {
                $found_node[$children_property] = array();
            }
            
            if ($output_type == 'object') {
                $found_node = (object)$found_node;
            }
            
            $nodes[] = $found_node;
        }
        
        if (count($expansion_data)) {
            Arrays::placeInExpanded($data, Generic::get($found_node, $children_property), $expansion_data, $name_property, $children_property, $output_type);
        }
    }
}
?>