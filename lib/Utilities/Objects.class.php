<?php
class Objects {
    public static function get($object, $property, $default = null) {
        if (property_exists($object, $property)) return $object->$property;
        return $default;
    }
    
    public static function without($object, $skipped_properties) {
        $new = (object)array();
        
        foreach ($object as $property => $value) {
            if (in_array($property, $skipped_properties)) continue;
            $new->$property = $value;
        }
        
        return $new;
    }
}
?>