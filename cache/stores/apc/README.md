Alternative PHP cache (APC)
===========================

The alternative PHP cache (APC) is an opcode cache for PHP that provides a persistent application data store cache to PHP applications.
This plugin allows the use of the APC data store as a Moodle cache store. In turn allowing you to use APC within Moodle.
Its also important to note that because the APCu API is backwards compatible this store can also be used for APCu.

Installation of APC
-------------------

It is recommended that you read through the APC documentation http://www.php.net/manual/en/book.apc.php before beginning with this plugin.
The above documentation recommends installing the PECL APC extension that can be found at http://pecl.php.net/package/apc.
http://www.php.net/manual/en/install.pecl.php contains information on installing PECL extensions.

Its also worth noting for this those using Linux that there is usually a php5-apc package that can be installed very easily.
If you have installed PHP under Linux through a package manager then this will be by far the easiest way to proceed.

Once installed ensure you restart your web server before proceeding.

Installation within Moodle
--------------------------

Browse to your site and log in as an administrator.
Moodle should detect that a new plugin has been added and will proceed to prompt you through an upgrade process to install it.
The installation of this plugin is very minimal. Once installed you will need to need to create an APC cache store instance within the Moodle administration interfaces.

Making use of APC within Moodle
-------------------------------

Installing this plugin makes APC available to use within Moodle however it does not put it into use.
The first thing you will need to do is create an APC cache store instance.
This is done through the Cache configuration interface.

1. Log in as an Administrator.
2. In the settings block browse to Site Administration > Plugins > Caching > Configuration.
3. Once the page loads locate the APC row within the Installed cache stores table.
4. You should see an "Add instance" link within that row. If not then the APC extension has not being installed correctly.
5. Click "Add instance".
6. Give the new instance a name and click "Save changes". You should be directed back to the configuration page.
7. Locate the Configured cache store instances table and ensure there is now a row for you APC instance and that it has a green tick in the ready column.

Once done you have an APC instance that is ready to be used. The next step is to map definitions to make use of the APC instance.

Locate the known cache definitions table. This table lists the caches being used within Moodle at the moment.
For each cache you should be able to Edit mappings. Find a cache that you would like to map to the APC instance and click Edit mappings.
One the next screen proceed to select your APC instance as the primary cache and save changes.
Back in the known cache definitions table you should now see your APC instance listed under the store mappings for the cache you had selected.
You can proceed to map as many or as few cache definitions to the APC instance as you see fit.

That is it! you are now using APC within Moodle.

Information and advice on using APC within Moodle
-------------------------------------------------

APC provides a shared application cache that is usually very limited in size but provides excellent performance.
It doesn't provide the ability to configure multiple instances of itself and as such within Moodle you are only able to create a single APC cache store instance.
Because of its incredible performance but very limited size it is strongly suggested that you map only small, crucial caches to the APC store.

Another important thing to understand about the APC store is that it provides no garbage cleaning, or storage reclamation facilities. As such cache data will persist there until APC is restarted or the store is purged.
On top of that once the store is full requests to store information within the cache fail until there is once more sufficient space.
Because of this it is recommended that you regularly purge or restart APC.
Also recommended is to map a secondary application cache instance to any definition with the APC mapped. This ensures that if it does indeed full up that an alternative cache is available.