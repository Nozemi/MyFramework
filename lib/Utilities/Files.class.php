<?php
class Files {
    private static $search_results, $search_filters;
    
    public static function ensureDirectory($directory, $permissions=0777) {
        if (!file_exists($directory)) {
            mkdir($directory, $permissions, true);
        }
    }
	
	public static function FindFile($file)
	{
		if(!file_exists($file))
		{
			for($i = 0; $i < 3; $i++)
			{
				if(!file_exists($file))
				{
					$file = '../' . $file;
				}
			}
		}
		
		return $file;
	}
    
    public static function getDirectories($directory) {
        $directories = array();
        $hdir = opendir($directory);
        if ($hdir) {
            while ($file = readdir($hdir)) {
                if ($file == '.' || $file == '..') continue;
                if (is_dir($directory.$file)) {
                    $directories[] = $file;
                }
            }
        }
        closedir($hdir);
        
        return $directories;
    }
    
    public static function getFiles($directory) {
        $files = array();
        $hdir = opendir($directory);
        if ($hdir) {
            while ($file = readdir($hdir)) {
                if ($file == '.' || $file == '..') continue;
                if (is_file($directory.$file)) {
                    $files[] = $file;
                }
            }
        }
        closedir($hdir);
        
        return $files;
    }
    
    public static function urecurse($directory, $callback, $callback_for_dirs=false) {
        assert(is_callable($callback));
        $hdir = opendir($directory);
        if ($hdir) {
            while ($file = readdir($hdir)) {
                if ($file == '.' || $file == '..') continue;
                if (is_dir($directory.$file)) {
                    self::urecurse($directory.$file.'/', $callback, $callback_for_dirs);
                    if ($callback_for_dirs) call_user_func($callback, $file);
                } else {
                    call_user_func($callback, $directory, $file);
                }
            }
        }
        closedir($hdir);
    }
    
    public static function findFiles($directory, $include=null, $exclude=null) {
        self::$search_results = array();
        self::$search_filters = array(
            'include' => $include,
            'exclude' => $exclude
        );
        self::urecurse($directory, array('Files', 'searchCallback'));
        return self::$search_results;
    }
    
    private static function searchCallback($directory, $file) {
        $include = self::$search_filters['include'];
        $exclude = self::$search_filters['exclude'];
        
        $match = false;
        if (!empty($include)) {
            foreach ($include as $wildcard_str) {
                if (Strings::matchesWildcardStr($file, $wildcard_str)) {
                    $match = true;
                    break;
                }
            }
            
            // if we couldn't include it there's no point in checking if we're excluding it
            if (!$match) return;
            
            // however a file that's included now could become excluded later
        }
        
        // either include wasn't specified so we default to included, or we've included it
        // so force match to be true either way
        $match = true;
        if (!empty($exclude)) {
            foreach ($exclude as $wildcard_str) {
                if (Strings::matchesWildcardStr($file, $wildcard_str)) {
                    $match = false;
                    break;
                }
            }
        }
        
        if ($match) {
            self::$search_results[] = $directory.$file;
        }
    }
}
?>