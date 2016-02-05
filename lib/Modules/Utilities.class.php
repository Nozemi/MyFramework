<?php
	class Utilities
	{
		public static function FindKey($aKey, $array) {
			if(is_array($array)) {
				foreach($array as $key => $item) {
					if($key == $aKey) {
						return $item;
					} else {
						$result = self::FindKey($aKey, $item);
						if($result != false) {
							return $result;
						}
					}
				}
			}
			return false;
		}
	}
?>