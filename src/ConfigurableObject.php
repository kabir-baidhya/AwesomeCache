<?php
/*
 * This file is part of the AwesomeCache package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\AwesomeCache;

abstract class ConfigurableObject
{
	protected static $config = array();

	/**
     * Gets/Sets the Caching configurations
     * 
     * Getting:
     * 
     *      $allConfig = Cache::config();
     *      $configValue = Cache::config('configName');
     * 
     * Setting:
     * 
     *      $config = array();
     *      Cache::config($config);
     * 
     * @param array $config (optional)
     * @return mixed
     */
	public static function config($config = null)
	{
		if(is_array($config)) {
			
            # Setting Configurations
			static::$config = $config + static::$config;
			$pathWithoutTrailingSlash = rtrim(static::$config['directory'], '/');
			static::$config['directory'] = $pathWithoutTrailingSlash.'/';

		} elseif(is_string($config)) {

            # Getting Single Config item
			return isset(static::$config[$config]) ? static::$config[$config] : null;

		} elseif(!$config) {

            # Getting All configurations
			return static::$config;
		} else {
			throw new CacheException('Invalid parameter provided for Cache::config()');
		}
	}
}