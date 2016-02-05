<?php

/**
 * String Utils class file
 * @author Peter Belm <peterbelm@gmail.com>
 * @copyright Copyright 2010 Peter Belm
 * @package notacake
 * @subpackage Utils
 */

/**
 * Date holds dates, parses/formats dates, and allows operations to be applied to them
 */
class Strings {

    // TODO: improve this to actually validate URLs, and support relative URLs
    //       perhaps move into a URL class
    public static function isUrl($str) {
        $info = parse_url($str);
        return ($info['scheme'] == 'http' || $info['scheme'] == 'https') && $info['host'] != "";
    }

    /**
     * Tests with a string begins with another string
     * 
     * @param string $str The overall string
     * @param string $substring The substring to test
     * @return bool
     */
    public static function startsWith($str, $substring) {
        return (substr($str, 0, strlen($substring)) == $substring);
    }

    /**
     * Tests with a string ends with another string
     * 
     * @param string $str The overall string
     * @param string $substring The substring to test
     * @return bool
     */
    public static function endsWith($str, $substring) {
        return (substr($str, -strlen($substring)) == $substring);
    }

    /**
     * Shorten a string on a word boundary (' '), backtracking by a given amount to try and shorten
     * on a sentence boundary ('.'). Strings shorter than the given length won't be shortened
     * or ellipsised. Only word boundary shortened strings will be ellipsised.
     * 
     * @param string $str The string to shorten
     * @param int $max_length The maximum length of the resulting string
     * @param int $backtrace_max The number of characters to backtrace to find a word boundary
     * @param string $ellipsis A string to use as an ellipsis
     * @return string
     */
    public static function shorten($str, $max_length, $backtrace_max = 20, $ellipsis='...') {
        if (strlen($str) <= $max_length)
            return $str;
        
        if ($max_length <= $backtrace_max) {
            $backtrace_max = $max_length - 1;
        }

        $pos = strpos($str, '.', ($max_length - $backtrace_max) - 1);

        if ($pos === false || $pos > $max_length) {
            $pos = $max_length;
            while ($str[$pos] != ' ') {
                $pos--;
            }

            return substr($str, 0, $pos) . $ellipsis;
        }

        return substr($str, 0, $pos + 1);
    }

    /**
     * Checks a string against a wildcard string to see if it matches.
     * A wildcard string can contain '*' symbols, for instance: '*.jpg'
     * 
     * @param string $str The string to check
     * @param string $wildcard_str The wildcard string to check against
     * @return bool
     * @todo Allow escaped '*'
     */
    public static function matchesWildcardStr($str, $wildcard_str) {
        $regexp = '/' . str_replace('\*', '(?:.+)?', self::escapeForRegex($wildcard_str)) . '/';
        return (preg_match($regexp, $str) > 0);
    }

    /**
     * Escape all instances of Regexp special characters in a string
     * 
     * @param string $str String to escape
     * @return string
     */
    public static function escapeForRegex($str) {
        $patterns = array('/\//', '/\^/', '/\./', '/\$/', '/\|/', '/\(/', '/\)/', '/\[/', '/\]/',
            '/\*/', '/\+/', '/\?/', '/\{/', '/\}/', '/\,/');
        $replace = array('\/', '\^', '\.', '\$', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,');
        return preg_replace($patterns, $replace, $str);
    }

    /**
     * Returns the text between two given substrings in a string. This will find the least
     * amount of text to extract, for instance: substrBetween('./test/example.txt', '/', '.')
     * will return 'example'
     * 
     * @param string $str The string to sample
     * @param string $start The text to the left of the substring
     * @param string $end The text to the right of the substring
     * @return string
     */
    public static function substrBetween($str, $start, $end) {
        $start_pos = 0;
        while (($start_pos = strpos($str, $start, $start_pos)) !== false) {
            // we've found the start, but let's check there's not another 'start' before the 'end'
            $next_start_pos = strpos($str, $start, $start_pos + 1);
            $end_pos = strpos($str, $end, $start_pos + 1);
            if ($end_pos === false) {
                break;
            }

            if ($next_start_pos !== false && $next_start_pos < $end_pos) {
                $start_pos++;
                continue;
            }

            $start = $start_pos + strlen($start);
            return substr($str, $start, $end_pos - $start);
        }
        return false;
    }

    /**
     * Splits a string into chunks given by the lengths array
     * @param string $str The string to split
     * @param array $lengths The lengths of each string to split
     * @return array An array of split strings
     */
    static function chunk($str, $lengths) {
        if (array_sum($lengths) < strlen($str))
            return FALSE;

        $pointer = 0;
        $chunks = array();

        foreach ($lengths as $length) {
            $chunks[] = substr($str, $pointer, $length);
            $pointer += $length;
        }

        return $chunks;
    }

    public static function parseBool($str, $true_values=array('1', 'y', 'true', 'yes')) {
        if (in_array(strtolower($str), $true_values)) return true;
        return false;
    }
    
    public static function randomBinary($length) {
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= chr(mt_rand(0, 255));
        }
        return $str;
    }

    public static function ordinal_suffix($number, $ss=0)
    {
        if ($number % 100 > 10 && $number %100 < 14)
        {
            $os = 'th';
        }
        elseif($number == 0)
        {
            $os = '';
        }
        else
        {
            $last = substr($number, -1, 1);
            switch($last)
            {
                case "1":
                    $os = 'st';
                    break;
                case "2":
                    $os = 'nd';
                    break;

                case "3":
                    $os = 'rd';
                    break;
                default:
                    $os = 'th';
            }
        }
        $os = $ss==0 ? $os : '<sup>'.$os.'</sup>';
        return $number.$os;
    } 
}
?>