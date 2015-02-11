## Table of contents

- [\Gckabir\AwesomeCache\CacheException](#class-gckabirawesomecachecacheexception)
- [\Gckabir\AwesomeCache\Cache](#class-gckabirawesomecachecache)
- [\Gckabir\AwesomeCache\ConfigurableObject (abstract)](#class-gckabirawesomecacheconfigurableobject-abstract)

<hr /> 
### Class: \Gckabir\AwesomeCache\CacheException
| Visibility | Function |
|:-----------|:---------|

*This class extends \Exception*

<hr /> 
### Class: \Gckabir\AwesomeCache\Cache
| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$key</strong>)</strong> : <em>void</em><br /><em>Cache Constructor</em> |
| public | <strong>cachedData()</strong> : <em>mixed</em><br /><em>Retrieves the cached data that is refered by this object</em> |
| public static | <strong>clear(</strong><em>string</em> <strong>$key</strong>)</strong> : <em>void</em><br /><em>Clear/Delete a specified data by its key</em> |
| public static | <strong>clearAll()</strong> : <em>void</em><br /><em>Clear all the cache data stored in the cache directory</em> |
| public static | <strong>countAll()</strong> : <em>int</em><br /><em>Returns the total number of unique cached data</em> |
| public | <strong>duration()</strong> : <em>int</em><br /><em>Returns the time elapsed since the last modified date of the cached file</em> |
| public | <strong>filePath()</strong> : <em>string</em><br /><em>Returns the cache filepath of this data</em> |
| public | <strong>isCached()</strong> : <em>bool</em><br /><em>Checks if any data is stored for this key or not</em> |
| public | <strong>isCachedAndUsable()</strong> : <em>bool</em><br /><em>Checks if the data is cached & usable (AND of isCached() & isUsable())</em> |
| public | <strong>isUsable()</strong> : <em>bool</em><br /><em>Checks if data stored is usable(valid & not expired) or not</em> |
| public | <strong>key()</strong> : <em>string</em><br /><em>Returns the unique key of this data</em> |
| protected | <strong>lastModified()</strong> : <em>int</em><br /><em>Returns the last modified date of the cached data file</em> |
| public | <strong>purge()</strong> : <em>void</em><br /><em>Clear/Delete the cache data refered by this object</em> |
| public | <strong>putInCache(</strong><em>mixed</em> <strong>$data</strong>)</strong> : <em>void</em><br /><em>Stores the data in the cache Note: Repeatedly calling this will overwrite the existing data</em> |

*This class extends [\Gckabir\AwesomeCache\ConfigurableObject](#class-gckabirawesomecacheconfigurableobject-abstract)*

<hr /> 
### Class: \Gckabir\AwesomeCache\ConfigurableObject (abstract)
| Visibility | Function |
|:-----------|:---------|
| public static | <strong>config(</strong><em>mixed</em> <strong>$config=null</strong>)</strong> : <em>mixed</em><br /><em>Gets/Sets the Caching configurations  Getting:  $allConfig = Cache::config(); $configValue = Cache::config('configName');  Setting:  $config = array(); Cache::config($config);</em> |

