<?php
	class Config
	{
		public static function C()
		{
			$ConfigDir = Files::FindFile('Config/');
			
			$config = array();
			
			if(!file_exists($ConfigDir)) {
				echo 'Config directory does not exist.'; exit;
			}
			
			foreach(glob($ConfigDir . '*.conf.json') as $file) {
				$config[basename($file,'.conf.json')] = json_decode(file_get_contents($file), true);
			}
			
			return $config;
		}
	}
?>