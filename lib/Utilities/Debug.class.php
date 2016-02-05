<?php
define('DBGLVL_NONE', 0);
define('DBGLVL_VERBOSE', 1);
define('DBGLVL_TRACE', 2);
define('DBGLVL_WARNING', 4);
define('DBGLVL_ERROR', 8);
define('DBGLVL_ALL', 0xFF);

class Debug {
    private static $logLevel = DBGLVL_NONE;
    
    public static function setLogLevel($level) {
        self::$logLevel = $level;
    }
    
    public static function setDebugUser() {
        Session::set('debug_user', true);
    }
    
    public static function isDebugUser() {
        if (!Session::isStarted()) return false;
        return Session::get('debug_user', true); // TODO: change this back to default to false
    }
    
    public static function getDebugLevelLabel($level) {
        switch ($level) {
            case DBGLVL_VERBOSE:
                return "Verbose";
            case DBGLVL_WARNING:
                return "Warning";
            case DBGLVL_TRACE:
                return "Trace";
            case DBGLVL_ERROR:
                return "Error";
        }
        
        return "Unknown: {$level}";
    }
    
    public static function log($origin_component, $level, $message, $data_label="", $data=null) {
        if (defined('NO_DEBUG') || !BitMask::contains(self::$logLevel, $level)) {
            return;
        }
        
        $parts = array(
            0,
            $_SERVER['REMOTE_ADDR'],
            session_id(),
            time(),
            $origin_component,
            $level,
            $message,
            $data_label,
            serialize($data),
            serialize(isset($_SESSION) ? $_SESSION : null),
            serialize(debug_backtrace())
        );
        
        if (!isset($GLOBALS['request_key'])) {
            $GLOBALS['request_key'] = uniqid("", true);
            
            // only log this stuff once, keeps log filesize a lot smaller
            $parts = array_merge($parts, array(
                serialize($_GET),
                serialize($_POST),
                serialize(isset($_COOKIE) ? $_COOKIE : null),
                serialize($_SERVER)
            ));
        }
        
        $parts[0] = $GLOBALS['request_key'];
        
        $handle = fopen(ROOT_PATH.'/debug_log.psv', 'a');
        fputcsv($handle, $parts, '|');
        fclose($handle);
    }
    
    public static function logError($error_msg, $data) {
        $output = '<tr class="error"><td valign="top>'.time().'</td><td valign="top">'.$error_msg.'</td><td valign="top" colspan="2">'.Debug::dump($data, 'Attached Data').'</td></tr>';
        
        $output .= '<tr class="trace"><td colspan="3">';
        $trace = debug_backtrace();
        
        $lines = array();
        foreach ($trace as $index => $line) {
            $lines[] = '#'.((count($trace) - 1) - $index).' '.$line['function'].'('.Debug::serializeArgs($line['args']).');';
        }
        $output .= join('<br>', $lines).'</td><td>';
        
        $lines = array();
        foreach ($trace as $index => $line) {
            $lines[] = 'in '.Arrays::get($line, 'file', '[Unknown]').':'.Arrays::get($line, 'line', '[Unknown]');
        }
        
        $output .= join('<br>', $lines).'</td></tr>';
        file_put_contents(dirname(__FILE__).'/../../internal_errors.inc', $output, FILE_APPEND);
    }
    
    public static function serializeArgs($args, $key_format='none') {
        $serialized_args = array();
        foreach ($args as $key => $arg) {
            if (is_string($key)) {
                $key = "'{$key}'";
            }
            switch ($key_format) {
                case 'array':
                    $serialized = '['.$key.'] => ';
                    break;
                case 'object':
                    $serialized = $key.' => ';
                    break;
                default:
                    $serialized = '';
            }

            if (is_array($arg)) {
                $serialized.= 'array(';
                $serialized .= Debug::serializeArgs($arg, 'array');
                $serialized .= ')';
            } elseif (is_object($arg)) {
                $serialized.= get_class($arg).'(';
                $serialized .= Debug::serializeArgs($arg, 'object');
                $serialized .= ')';
            } elseif (is_string($arg)) {
                $serialized .= "'{$arg}'";
            } elseif (is_bool($arg)) {
                $serialized .= ($arg ? 'true' : 'false');
            } else {
                $serialized .= $arg;
            }

            $serialized_args[] = $serialized;
        }

        return join(', ', $serialized_args);
    }
    
    public static function dump($value, $name) {
        if (!self::isDebugUser()) return;
        
        $text = var_export($value, true);
        if (php_sapi_name() == 'cli') { return $text; }

        $text = preg_replace('/=(>?) \n */', '=\1 ', $text);
        $html = HTML::encode($text);
    
        $expandable_html = self::getExpandButtonHtml('this.nextSibling');
        $expandable_html .= '<span style="display: none">';

        $html = str_replace('{', '{' . $expandable_html, $html);
        $html = str_replace('}', '</span>}', $html);

        $html = str_replace('::__set_state(array(', ':((' . $expandable_html, $html);
        $html = str_replace('))', '</span>))', $html);

        $html = str_replace('array (', 'array (' . $expandable_html, $html);
        $html = preg_replace('/^(\s*)\)/m', '$1</span>)', $html);
        
        return HTML::tag('div', array('class' => 'debug'), 
            HTML::tag('b', null, $name . self::getExpandButtonHtml('this.parentNode.nextSibling')) .
            HTML::tag('pre', array('style' => 'display: none;'), $html)
        );
    }
    
    private static function getExpandButtonHtml($path) {
        return HTML::tag('button', array(
            'type' => 'button',
            'style' => 'font-size: smaller; border-width: 1px;',
            'onclick' =>
                    "var contentStyle = {$path}.style;"
                    . "var wasOpen = (contentStyle.display == '');"
                    . "contentStyle.display = wasOpen ? 'none' : '';"
                    . "this.firstChild.data = wasOpen ? '+' : '\u2212'"
        ), '+');
    }
}
?>