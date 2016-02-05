<?php
define('BASE_CLASS_PATH', dirname(__FILE__).'/');

require_once 'Utilities/Arrays.class.php';
require_once 'Utilities/Assert.class.php';
require_once 'Utilities/Files.class.php';
require_once 'Utilities/Strings.class.php';
require_once 'Utilities/Debug.class.php';
require_once 'Utilities/Config.class.php';

$Config = Config::C();

function FindKey($aKey, $array) {
	if(is_array($array)) {
		foreach($array as $key => $item) {
			if($key == $aKey) {
				return $item;
			} else {
				$result = FindKey($aKey, $item);
				if($result != false) {
					return $result;
				}
			}
		}
	}
	return false;
}

if(FindKey('PHPErrors', $Config) === true) {
	error_reporting(-1);
	ini_set('display_errors', 'On');
} else {
	error_reporting(0);
	@ini_set('display_errors', 0);
}

try 
{
    $classDirectories = getClassDirectories();
} 
catch (ErrorException $ex)
{
    scan_lib();
    $classDirectories = getClassDirectories();
}

function getClassDirectories() 
{
    if (file_exists(BASE_CLASS_PATH.'lib_lookup.inc')) return unserialize(file_get_contents(BASE_CLASS_PATH.'lib_lookup.inc'));
    return array();
}

function scan_lib() {
    $paths = Files::findFiles(BASE_CLASS_PATH, array('*.class.php'));
    $paths = Arrays::index($paths, 'get_classname');
    file_put_contents(BASE_CLASS_PATH.'lib_lookup.inc', serialize($paths));
}

function get_classname($path) {
    preg_match("/([A-Za-z0-9_\-]+)\.(?:class|exception|interface)\.php/", $path, $matches);
    return $matches[1];
}

function __autoload($className) {
    global $classDirectories;
    
    if (!array_key_exists($className, $classDirectories) || !file_exists($classDirectories[$className])) {
        scan_lib();
        $classDirectories = getClassDirectories();
        if (!array_key_exists($className, $classDirectories)) throw new Exception("Class not found: {$className}");
    }
    
    $filename = $classDirectories[$className];
    
    try {
        include_once $filename;
    } catch (ErrorException $ex) {
        //throw new FileNotFoundException($filename);
    }
}
?>