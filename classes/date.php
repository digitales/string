<?php
/**
 * String manipulation Package
 *
 * This is a simple set of methods for string manipulation
 *
 * @copyright  2011 Ross Tweedie
 * @license    MIT License
 */

namespace String;

class String_DateException extends \Exception {}

class String_Date {
    
    protected static $string;
	
    /**
     * Set the string
     *
     * @param string $string
     * @return void
     */
    public static function set_string( $string)
    {
        static::$string = $string;
        return null;
    }
    
    
    /**
     * Convert string to timestamp
     *
     * @param string $string
     * @return string
     */
    public static function convert_to_timestamp( $string )
    {
        if ( empty($string ) ){
            return null;
        }
        
        if ( strlen( $string ) && is_numeric( $string) ){
            return $string;
        }
        
        $date_array = $year = $month = $day = null;
        
        if ( strpos( $string, '-' ) !== false ){
            $date_array = explode( '-', $string );
        } elseif ( strpos( $string, '/') !== false ){
            $date_array = explode( '/', $string );
        }
        
        if ( is_array( $date_array ) ){
            // Now lets check the date and convert to timestamp
            if ( strlen( $date_array[2] ) === 4 ){
                $year       = (int) $date_array[2];
                
                $check = self::check_month_and_day( $date_array[1], $date_array[0] );
                
                
            
            } elseif ( strlen( $date_array[0] ) === 4 ) {
                $year = $date_array[0];
                
                $check = self::check_month_and_day( $date_array[1], $date_array[2] );
                
            } else {
                
                $year = $date_array[0];
                
                $check = self::check_month_and_day( $date_array[1], $date_array[2] );
                
            }
            $month = $check['month'];
            $day = $check['day'];
        
            return mktime(0, 0, 0, $month, $day, $year);
        }
        
        // mm, no joy so far, maybe it's a keyword, so lets check
        $keywords = array(
                          'today',
                          'tomorrow',
                          'yesterday',
                          'next week',
                          'next month',
                          'next monday',
                          'next tuesday',
                          'next wednesday',
                          'next thursday',
                          'next friday',
                          'next saturday',
                          'next sunday',
                        );
        
        
        
        
        $date =  getdate();
        echo strlen( $date[0]);
        
        var_dump($date);
        
        echo '$string<pre>'.print_r($string, 1).'</pre>';
        
        
        echo '<hr />';
        
        
        exit;
        
    }
    
    public static function convert_to_date( $string, $format = 'eu' )
    {
        if ( empty( $string ) ) {
            return null;
        }
        
        $date = \Date::forge( $string );   
        return $date->format( $format );
    }
    
    /**
     * Check 2 values to guess which in the month and which is the day
     *
     * @param integer $value1
     * @param integer $value2
     * @return array
     */
    private static function check_month_and_day ( $value1, $value2 )
    {
        $month = $day = null;
        
        if ( $value1 > 12 ){
            $month  = $value2;
            $day    = $value1;
        }else{
            $month  = $value1;
            $day    = $value2;
        }
        
        return array( 'month' => $month, 'day' => $day );
    }
}