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

class StringException extends \Exception {}

class String {
    
    protected static $config;
	
	/**
	 * @var  object  PHPSecLib hash object
	 */
	protected static $hasher;

	
    protected static function get_config()
    {
        if ( !static::$config ):
            $config = \Config::load('string', true);
            static::$config = $config[ $config['active'] ];
        endif;
    
        return static::$config;
        
    }
    
    
    /**
     * Encode a string using the core encode function
     *
     *  This will retrieve the salt and use that for the encoding.
     *
     *  @param string $string The string to be encoded
     *  @param null | string $salt If the salt is null, the config will be used instead.
     *  @return string
     */
    public static function encode( $string, $salt = null )
    {
        
        if ( ! $salt) {
            $config = self::get_config();
            
            $salt = $config['salt'];            
            
        }
        
        return \Crypt::encode( $string, $salt );
    }
    
    
    /**
     * Decode a string using the core decode function
     *
     *  This will retrieve the salt and use that for the decoding
     *
     *  @param string $string The string to be decoded
     *  @param null | string $salt If the salt is null, the config will be used instead.
     *  @return string
     */
    public static function decode( $string, $salt = null )
    {
        if ( ! $salt) {
            $config = self::get_config();
            
            $salt = $config['salt'];            
            
        }
        
        return \Crypt::decode ( $string, $salt );
    }
	
	
	
	/**
	 * Default password hash method
	 *
	 * @param   string
	 * @return  string
	 */
	public static function hash_password($password)
	{	
		$config = self::get_config();       
        $salt = $config['salt'];
		
		return base64_encode(self::hasher()->pbkdf2($password, $config['salt'], 10000, 32));
	}

	/**
	 * Returns the hash object and creates it if necessary
	 *
	 * @return  PHPSecLib\Crypt_Hash
	 */
	public static function hasher()
	{
		if ( !static::$hasher ):
			if ( ! class_exists('PHPSecLib\\Crypt_Hash', false))
			{
				import('phpseclib/Crypt/Hash', 'vendor');
			}
		
            $hasher = new \PHPSecLib\Crypt_Hash();
            return $hasher;
        endif;
    
        return static::$hasher;
	}
    
	
    /**
	 * lower
	 *
	 * @param   string  $str       required
	 * @param   string  $encoding  default UTF-8
	 * @return  string
	 */
	public static function lower($str, $encoding = null)
	{
		$encoding or $encoding = \Fuel::$encoding;

		return function_exists('mb_strtolower')
			? mb_strtolower($str, $encoding)
			: strtolower($str);
	}

	/**
	 * upper
	 *
	 * @param   string  $str       required
	 * @param   string  $encoding  default UTF-8
	 * @return  string
	 */
	public static function upper($str, $encoding = null)
	{
		$encoding or $encoding = \Fuel::$encoding;

		return function_exists('mb_strtoupper')
			? mb_strtoupper($str, $encoding)
			: strtoupper($str);
	}

	/**
	 * lcfirst
	 *
	 * Does not strtoupper first
	 *
	 * @param   string  $str       required
	 * @param   string  $encoding  default UTF-8
	 * @return  string
	 */
	public static function lcfirst($str, $encoding = null)
	{
		$encoding or $encoding = \Fuel::$encoding;

		return function_exists('mb_strtolower')
			? mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding).
				mb_substr($str, 1, mb_strlen($str, $encoding), $encoding)
			: lcfirst($str);
	}

	/**
	 * ucfirst
	 *
	 * Does not strtolower first
	 *
	 * @param   string $str       required
	 * @param   string $encoding  default UTF-8
	 * @return   string
	 */
	public static function ucfirst($str, $encoding = null)
	{
		$encoding or $encoding = \Fuel::$encoding;

		return function_exists('mb_strtoupper')
			? mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
				mb_substr($str, 1, mb_strlen($str, $encoding), $encoding)
			: ucfirst($str);
	}

	/**
	 * ucwords
	 *
	 * First strtolower then ucwords
	 *
	 * ucwords normally doesn't strtolower first
	 * but MB_CASE_TITLE does, so ucwords now too
	 *
	 * @param   string   $str       required
	 * @param   string   $encoding  default UTF-8
	 * @return  string
	 */
	public static function ucwords($str, $encoding = null)
	{
		$encoding or $encoding = \Fuel::$encoding;

		return function_exists('mb_convert_case')
			? mb_convert_case($str, MB_CASE_TITLE, $encoding)
			: ucwords(strtolower($str));
	}
    
    
    
    public static function sanitize_for_url($title) {
        
    	$title = strip_tags($title);
    	// Preserve escaped octets.
    	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    	// Remove percent signs that are not part of an octet.
    	$title = str_replace('%', '', $title);
    	// Restore octets.
    	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

    	if ( String::seems_utf8( $title ) ) {
    		
            if ( function_exists( 'mb_strtolower' ) ) {
    			$title = mb_strtolower( $title, 'UTF-8' );
    		}
            
    		$title = String::utf8_uri_encode( $title, 200 );
    	}

    	$title = strtolower( $title );
    	$title = preg_replace( '/&.+?;/', '', $title ); // kill entities
    	$title = str_replace( '.', '-', $title );
    	$title = preg_replace( '/[^%a-z0-9 _-]/', '', $title );
    	$title = preg_replace( '/\s+/', '-', $title );
    	$title = preg_replace( '|-+|', '-', $title );
    	$title = trim( $title, '-' );

    	return $title;
    }
    
    
    
    
    public static function seems_utf8($str) {
        
    	$length = strlen($str);
    	
        for ($i=0; $i < $length; $i++) {
    		$c = ord($str[$i]);
    		if ($c < 0x80) $n = 0; # 0bbbbbbb
    		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
    		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
    		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
    		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
    		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
    		else return false; # Does not match any model
    		for ($j=0; $j<$n; $j++): # n bytes matching 10bbbbbb follow ?
    			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)){
    				return false;
    			}
    		endfor;
    	}
    	return true;
    }
    
    
    
    /**
     * Encode the Unicode values to be used in the URI.
     *
     * @since 1.5.0
     *
     * @param string $utf8_string
     * @param int $length Max length of the string
     * @return string String with Unicode encoded for URI.
     */
    public static function utf8_uri_encode( $utf8_string, $length = 0 ) {
    	$unicode = '';
    	$values = array();
    	$num_octets = 1;
    	$unicode_length = 0;
    
    	$string_length = strlen( $utf8_string );
    	for ($i = 0; $i < $string_length; $i++ ) {

    		$value = ord( $utf8_string[ $i ] );

    		if ( $value < 128 ) {
    			if ( $length && ( $unicode_length >= $length ) )
    				break;
    			$unicode .= chr($value);
    			$unicode_length++;
    		} else {
    			if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;
    
    			$values[] = $value;

    			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
    				break;
    			if ( count( $values ) == $num_octets ) {
    				if ($num_octets == 3) {
    					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
    					$unicode_length += 9;
    				} else {
    					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
    					$unicode_length += 6;
    				}
    
    				$values = array();
    				$num_octets = 1;
    			}
    		}
    	}

    	return $unicode;
    }
    
    
    /**
     * Generate a random alpha numeric string
     *
     * @param int $string_length
     * @return string
     */
    static public function generate_random( $string_length = 10 )
    {    
        $characters = 'abcdefghijklmnopqrstuvwxyz023456789';
        
        $string = '';
        for ($i = 0; $i < $string_length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }   
        return $string;
    }
    
}