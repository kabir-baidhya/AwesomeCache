<?php
/*
 * This file is part of the AwesomeCache package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\AwesomeCache;

class Cache extends ConfigurableObject
{
    protected static $config = array(
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

    protected $key = null;
    protected $file = null;

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

            // Recursive directory creation
            mkdir($directory, 0777, true);
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
     * @return string
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

        $serializationEnabled = static::config('serialize');

        if(!$serializationEnabled && !is_scalar($data)) {
            throw new CacheException("Trying to store non-scalar data with serialization disabled");
        }

        $data = $serializationEnabled ? serialize($data) : $data;


        if($this->isCached() && !is_writable($this->filePath())) {
            throw new CacheException(sprintf("File '%s' is not writable", $this->filePath()));
        }

        $success = file_put_contents($this->filePath(), $data);

        if ($success === false) {
            throw new CacheException("Couldn't write to file: {$this->file}");
        } 

        return true;
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
        return ($this->duration() < static::config('cacheExpiry'));
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
    protected function lastModified()
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
     * Clear all the cache data stored in the cache directory
     * @return void
     */
    public static function clearAll()
    {
        $dir = new \DirectoryIterator(static::config('directory'));

        foreach ($dir as $fileinfo) {

            if ($fileinfo->isFile()) {
                $cacheKey = $fileinfo->getFilename();
                static::clear($cacheKey);
            }
        }
    }

    /**
     * Returns the total number of unique cached data
     * @return int
     */
    public static function countAll()
    {
        $count = 0;

        $dir = new \DirectoryIterator(static::config('directory'));

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Clear/Delete the cache data refered by this object
     * @return void
     */
    public function purge() {
        static::clear($this->key()) ;
    }

    /**
     * Clear/Delete a specified data by its key
     * @param string $key 
     * @return void
     */
    public static function clear($key)
    {
        $that = new static($key);

        if ($that->isCached()) {

            $deletionSuccess = unlink($that->filePath());
            
            if(!$deletionSuccess) {
                throw new CacheException(sprintf("Could not delete file '%s'", $this->filePath()));
            }
        }
    }
}
