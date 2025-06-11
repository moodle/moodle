Moodle Universal Cache / Cache API
==================================

Sample code snippets
--------------------

A definition:

     $definitions = array(
        'string' => array(                            // Required, unique to the component
            'mode' => \core_cache\store::MODE_APPLICATION,  // Required
            'simplekeys' => false,                    // Optional
            'simpledata' => false,                    // Optional
            'requireidentifiers' => array(            // Optional
                'lang'
            ),
            'requiredataguarantee' => false,          // Optional
            'requiremultipleidentifiers' => false,    // Optional
            'requirelockingbeforewrite' => false,     // Optional
            'requiresearchable' => false,             // Optional
            'maxsize' => null,                        // Optional
            'overrideclass' => null,                  // Optional
            'overrideclassfile' => null,              // Optional
            'datasource' => null,                     // Optional
            'datasourcefile' => null,                 // Optional
            'staticacceleration' => false,            // Optional
            'staticaccelerationsize' => false,        // Optional
            'ttl' => 0,                               // Optional
            'mappingsonly' => false                   // Optional
            'invalidationevents' => array(            // Optional
                'contextmarkeddirty'
            ),
            'canuselocalstore' => false               // Optional
            'sharingoptions' => null                  // Optional
            'defaultsharing' => null                  // Optional
        )
    );

Getting something from a cache using the definition:

    $cache = \core_cache\cache::make('core', 'string');
    if (!$component = $cache->get('component')) {
        // get returns false if its not there and can't be loaded.
        $component = generate_data();
        $cache->set($component);
    }

The same thing but using params:

    $cache = \core_cache\cache::make_from_params(\core_cache\store::MODE_APPLICATION, 'core', 'string');
    if (!$component = $cache->get('component')) {
        // get returns false if its not there and can't be loaded.
        $component = generate_data();
        $cache->set($component);
    }

If a data source had been specified in the definition, the following would be all that was needed.

    $cache = \core_cache\cache::make('core', 'string');
    $component = $cache->get('component');

Disabling the cache stores.
There are times in code when you will want to disable the cache stores.
While the cache API must still be functional in order for calls to it to work it is possible to disable the use of the cache stores separately so that you can be sure only the cache will function in all circumstances.

    // Disable the cache store at the start of your script with:
    define('CACHE_DISABLE_STORES', true);

    // Disable the cache within your script when you want with:
    \core_cache\factory::disable_stores();
    // If you disabled it using the above means you can re-enable it with:
    \core_cache\factory::reset();

Disabling the cache entirely.
Like above there are times when you want the cache to avoid initialising anything it doesn't absolutely need. Things such as installation and upgrade require this functionality.
When the cache API is disabled it is still functional however special "disabled" classes will be used instead of the regular classes that make the Cache API tick.
These disabled classes do the least work possible and through this means we avoid all manner of intialisation and configuration.
Once disabled it cannot be re-enabled.

    // To disable the cache entirely call the following:
    define('CACHE_DISABLE_ALL', true);

Cache API parts
---------------

There are several parts that make up the Cache API.

### Loader
The loader is central to the whole thing.
It is used by the end developer to get an object that handles caching.
90% of end developers will not need to know or use anything else in the cache API.
In order to get a loader you must use one of two static methods, make or make_from_params.
The loader has been kept as simple as possible, interaction is summarised by the core_cache\loader_interface interface.
Internally there is lots of magic going on. The important parts to know about are:
* There are two ways to get a loader, the first with a definition (discussed below) the second with params. When params are used they are turned into an adhoc definition with default params.
* A loader is passed three things when being constructed, a definition, a store, and another loader or datasource if there is either.
* If a loader is the third arg then requests will be chained to provide redundancy.
* If a data source is provided then requests for an item that is not cached will be passed to the data source and that will be expected to load the data. If it loads data, that data is stored in each store on its way back to the user.
* There are three core loaders. One for each application, session and request.
* A custom loader can be used. It will be provided by the definition (thus cannot be used with ad hoc definitions) and must override the appropriate core loader
* The loader handles ttl (time to live) for stores that don't natively support ttl.
* The application loader handles locking for stores that don't natively support locking.

### Store
The store is the bridge between the cache API and a cache solution.
Cache store plugins exist within moodle/cache/store.
The administrator of a site can configure multiple instances of each plugin, the configuration gets initialised as a store for the loader when required in code (during construction of the loader).
The following points highlight things you should know about stores.
* A \core_cache\store interface is used to define the requirements of a store plugin.
* The store plugin can inherit the \core_cache\lockable_cache_interface interface to handle its own locking.
* The store plugin can inherit the \core_cache\key_aware_cache_interface interface to handle is own has checks.
* Store plugins inform the cache API about the things they support. Features can be required by a definition.
  * Data guarantee - Data is guaranteed to exist in the cache once it is set there. It is never cleaned up to free space or because it has not been recently used.
  * Multiple identifiers - Rather than a single string key, the parts that make up the key are passed as an array.
  * Native TTL support - When required, the store supports native ttl and doesn't require the cache API to manage ttl of things given to the store.
* There are two reserved store names, base and dummy. These are both used internally.

### Definition
_Definitions were not a part of the previous proposal._
Definitions are cache definitions. They will be located within a new file for each component/plugin at **db/caches.php**.
They can be used to set all of the requirements of a cache instance and are used to ensure that a cache can only be interacted with in the same way no matter where it is being used.
It also ensures that caches are easy to use, the config is stored in the definition and the developer using the cache does not need to know anything about its inner workings.
When getting a loader you can either provide a definition name, or a set or params.
* If you provide a definition name then the matching definition is found and used to construct a loader for you.
* If you provide params then an ad hoc definition is created. It will have defaults and will not have any special requirements or options set.

Definitions are designed to be used in situations where things are more than basic.

The following settings are required for a definition:
* name - Identifies the definition and must be unique.
* mode - Application, session or request.

The following optional settings can also be defined:
* simplekeys - Set to true if items will always and only have simple keys. Simple keys may contain a-zA-Z0-9_. If set to true we use the keys as they are without hashing them. Good for performance and possible because we know the keys are safe.
* simpledata - Set to true if you know that you will only be storing scalar values or arrays of scalar values. Avoids costly investigation of data types.
* requireidentifiers - Any identifiers the definition requires. Must be provided when creating the loader.
* requiredataguarantee - If set to true then only stores that support data guarantee will be used.
* requiremultipleidentifiers - If set to true then only stores that support multiple identifiers will be used.
* requirelockingbeforewrite - If set to true the system will throw an error if you write to a cache without having a lock on the relevant key.
* requiresearchable - If set to true only stores that support key searching will be used for this definition. Its not recommended to use this unless absolutely unavoidable.
* maxsize - This gives a cache an indication about the maximum items it should store. Cache stores don't have to use this, it is up to them to decide if its required.
* overrideclass - If provided this class will be used for the loader. It must extend one of the core loader classes (based upon mode).
* overrideclassfile - Included if required when using the overrideclass param.
* datasource - If provided this class will be used as a data source for the definition. It must implement the \core_cache\data_source_interface interface.
* datasourcefile - Included if required when using the datasource param.
* staticacceleration - Any data passing through the cache will be held onto to make subsequent requests for it faster.
* staticaccelerationsize - If set to an int this will be the maximum number of items stored in the static acceleration array.
* ttl - Can be used to set a ttl value for data being set for this cache.
* mappingsonly - This definition can only be used if there is a store mapping for it. More on this later.
* invalidationevents - An array of events that should trigger this cache to invalidate.
* sharingoptions - The sum of the possible sharing options that are applicable to the definition. An advanced setting.
* defaultsharing - The default sharing option to use. It's highly recommended that you don't set this unless there is a very specific reason not to use the system default.
* canuselocalstore - The default is to required a shared cache location for all nodes in a multi webserver environment.  If the cache uses revisions and never updates key data, administrators can use a local storage cache for this cache.
It's important to note that internally the definition is also aware of the component. This is picked up when the definition is read, based upon the location of the caches.php file.

The staticacceleration option.
Data passed to or retrieved from the loader and its chained loaders gets cached by the instance.
Because it caches key=>value data it avoids the need to re-fetch things from stores after the first request. Its good for performance, bad for memory.
Memory use can be controlled by setting the staticaccelerationsize option.
It should be used sparingly.

The mappingsonly option.
The administrator of a site can create mappings between stores and definitions. Allowing them to designate stores for specific definitions (caches).
Setting this option to true means that the definition can only be used if a mapping has been made for it.
Normally if no mappings exist then the default store for the definition mode is used.

Sharing options.
This controls the options available to the user when configuring the sharing of a definitions cached data.
By default all sharing options are available to select. This particular option allows the developer to limit the options available to the admin configuring the cache.

### Data source
Data sources allow cache _misses_ (requests for a key that doesn't exist) to be handled and loaded internally.
The loader gets used as the last resort if provided and means that code using the cache doesn't need to handle the situation that information isn't cached.
They can be specified in a cache definition and must implement the \core_cache\data_source_interface interface.

### How it all chains together.
Consider the following:

Basic request for information (no frills):

    => Code calls get
        => Loader handles get, passes the request to its store
            <= Memcache doesn't have the data. sorry.
        <= Loader returns the result.
    |= Code couldn't get the data from the cache. It must generate it and then ask the loader to cache it.

Advanced initial request for information not already cached (has chained stores and data source):

    => Code calls get
        => Loader handles get, passes the request to its store
            => Memcache handles request, doesn't have it passes it to the chained store
                => File (default store) doesn't have it requests it from the loader
                    => Data source - makes required db calls, processes information
                        ...database calls...
                        ...processing and moulding...
                    <= Data source returns the information
                <= File caches the information on its way back through
            <= Memcache caches the information on its way back through
        <= Loader returns the data to the user.
    |= Code the code now has the data.

Subsequent request for information:

    => Code calls get
        => Loader handles get, passes the request to its store
            <= Store returns the data
        <= Loader returns the data
    |= Code has the data

Other internal magic you should be aware of
-------------------------------------------
The following should fill you in on a bit more of the behind-the-scenes stuff for the cache API.

### Helper class
There is a helper class called \core_cache\helper which is abstract with static methods.
This class handles much of the internal generation and initialisation requirements.
In normal use this class will not be needed outside of the API (mostly internal use only)

### Configuration
There are two configuration classes \core_cache\config and \core_cache\config_writer.
The reader class is used for every request, the writer is only used when modifying the configuration.
Because the cache API is designed to cache database configuration and meta data it must be able to operate prior to database configuration being loaded.
To get around this we store the configuration information in a file in the dataroot.
The configuration file contains information on the configured store instances, definitions collected from definition files, and mappings.
That information is stored and loaded in the same way we work with the lang string files.
This means that we use the cache API as soon as it has been included.

### Invalidation
Cache information can be invalidated in two ways.
1. pass a definition name and the keys to be invalidated (or none to invalidate the whole cache).
2. pass an event and the keys to be invalidated.

The first method is designed to be used when you have a single known definition you want to invalidate entries within.
The second method is a lot more intensive for the system. There are defined invalidation events that definitions can "subscribe" to (through the definitions invalidationevents option).
When you invalidate by event the cache API finds all of the definitions that subscribe to the event, it then loads the stores for each of those definitions and purges the keys from each store.
This is obviously a recursive, and therefore, intense process.

### Testing
Both the cache API and the cache stores have tests.
Please be aware that several of the cache stores require configuration in order to be able operate in the tests.
Tests for stores requiring configuration that haven't been configured will be skipped.
All configuration is done in your sites config.php through definitions.

As of Moodle 2.8 it is also possible to set the default cache stores used when running tests.
You can do this by adding the following define to your config.php file:

    // xxx is one of the installed stored (for example redis) or other cachestore with a test define.
    define('TEST_CACHE_USING_APPLICATION_STORE', 'xxx');

This allows you to run tests against a defined test store. It uses the defined value to identify a store to test against with a matching TEST_CACHESTORE define.
Alternatively you can also run tests against an actual cache config.
To do this you must add the following to your config.php file:

    define('TEST_CACHE_USING_ALT_CACHE_CONFIG_PATH', true');
    $CFG->altcacheconfigpath = '/a/temp/directory/yoursite.php'

This tells Moodle to use the config at $CFG->altcacheconfigpath when running tests.
There are a couple of considerations to using this method:
* By setting $CFG->altcacheconfigpath your site will store the cache config in the specified path, not just the test cache config but your site config as well.
* If you have configured your cache before setting $CFG->altcacheconfigpath you will need to copy it from moodledata/muc/config.php to the destination you specified.
* This allows you to share a cache config between sites.
* It also allows you to use tests to test your sites cache config.

Please be aware that if you are using Memcache or Memcached it is recommended to use dedicated Memcached servers.
When caches get purged the memcached servers you have configured get purged, any data stored within them whether it belongs to Moodle or not will be removed.
If you are using Memcached for sessions as well as caching/testing and caches get purged your sessions will be removed prematurely and users will be need to start again.
