<?php
/*
 * This file is part of the AwesomeCache package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\AwesomeCache;

class Cache
{
    private static $config = array(
        /**
         * Cache directory path
         */ 
        'directory'    =>  'cache/',

        /**
         * Cache Expiration time interval
         * default: 86400 = 1 day
         */ 
        'cacheExpiry'    => 86400,

        /**
         * Whether or not to serialize the data before storing in file
         * Note: Storing non-scalar data (array, objects etc) requires serialization to be true
         */
        'serialize'        => true,
    );

    private $key = null;
    private $file = null;

    /**
     * Cache Constructor
     * @param string $key A Unique non-empty key for the data
     */
    public function __construct($key)
    {
        $this->key = trim($key);

        if(!$this->key) {
            throw new CacheException('Key For Cache must not be an empty string');
        }

        $directory = static::config('directory');

        $this->file = $directory.$this->key;

        if (!file_exists($directory) && !is_dir($directory)) {
            mkdir($directory);
        }
    }

    /**
     * Returns the unique key of this data
     * @return string
     */
    public function key() 
    {
        return $this->key;
    }

    /**
     * Returns the cache filepath of this data
     * @return type
     */
    public function filePath() 
    {
        return $this->file;
    }

    /**
     * Retrieves the cached data that is refered by this object
     * @return mixed
     */
    public function cachedData()
    {
        if (!$this->isCached()) {
            return null;
        }

        $contents = file_get_contents($this->file);

        $serializationEnabled = static::config('serialize');

        $data = $serializationEnabled ? unserialize($contents) : $contents;

        return $data;
    }

    /**
     * Stores the data in the cache
     * Note: Repeatedly calling this will overwrite the existing data
     * @param mixed $data 
     */
    public function putInCache($data)
    {
        if (!$data) {
            throw new CacheException("No data provided for storing in the cache");
        }

        $serializationEnabled = static::$config['serialize'];

        if(!$serializationEnabled && !is_scalar($data)) {
            throw new CacheException("Serialization should be set to 'true' for storing non-scalar data");
        }

        $data = $serializationEnabled ? serialize($data) : $data;

        $written = @file_put_contents($this->file, $data);

        if (!$written) {
            throw new CacheException("Couldn't write to file: {$this->file}");
        }
    }

    /**
     * Checks if any data is stored for this key or not
     * @return bool
     */
    public function isCached()
    {
        return file_exists($this->file) && is_file($this->file);
    }

    /**
     * Checks if data stored is usable(valid & not expired) or not 
     * @return bool
     */
    public function isUsable()
    {
        return ($this->duration() < static::$config['cacheExpiry']);
    }

    /**
     * Returns the time elapsed since the last modified date of the cached file 
     * @return int
     */
    public function duration()
    {
        if (!$this->isCached()) {
            return 0;
        }

        $duration = time() - $this->lastModified();

        return $duration;
    }

    /**
     * Returns the last modified date of the cached data file
     * @return int
     */
    public function lastModified()
    {
        clearstatcache();

        return filemtime($this->file);
    }

    /**
     * Checks if the data is cached & usable (AND of isCached() & isUsable())
     * @return bool
     */
    public function isCachedAndUsable()
    {
        return ($this->isCached() && $this->isUsable());
    }

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

    /**
     * Clear all the cache data stored in the cache directory
     * @return void
     */
    public static function clearAll()
    {
        $dir = static::config('directory');

        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if (!is_dir($file)) {
                @unlink($dir.$file);
            }
        }
        closedir($handle);
    }

    /**
     * Returns the total number of unique cached data
     * @return int
     */
    public static function countAll()
    {
        $count = 0;
        $dir = static::config('directory');

        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if (!is_dir($file)) {
                $count++;
            }
        }
        closedir($handle);

        return $count;
    }

    /**
     * Clear/Delete the cache data refered by this object
     * @return void
     */
    public function purge() {
        static::clear($this->key);
    }

    /**
     * Clear/Delete a specified data by its key
     * @param type $key 
     * @return void
     */
    public static function clear($key)
    {
        $that = new static($key);

        if ($that->isCached()) {
            @unlink($that->file);
        }
    }
}
