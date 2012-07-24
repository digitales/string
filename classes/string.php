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
    
    
}