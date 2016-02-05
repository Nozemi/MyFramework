<?php
	class MessageHandler
	{
		function __construct($message, $type)
		{
			$bootstrap = false;
			
			if(!empty(Utilities::FindKey('BootstrapMessages', $GLOBALS['Config']))) {
				$bootstrap = Utilities::FindKey('BootstrapMessages', $GLOBALS['Config']);
			}
			
			switch(strtoupper($type))
			{
				default:
				case 'ERR':
				case 'ERROR':
					$style = 'danger';
					break;
				case 'SUCC':
				case 'SUCCESS':
					$style = 'success';
					break;
				case 'WARN':
				case 'WARNING':
					$style = 'warning';
					break;
				case 'INFO':
				case 'INFORMATION':
					$style = 'info';
					break;
			}
			
			if($bootstrap === true) {
				$message = '<p class="alert alert-' . $style . '" role="alert">' . $message . '</p>';
			}
			
			echo $message;
		}
	}
?>