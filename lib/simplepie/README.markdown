# SimplePie

## Authors and contributors
### Current
* [Ryan McCue](http://ryanmccue.info) (Maintainer, support)

### Alumni
* [Ryan Parman](http://ryanparman.com) (Creator, developer, evangelism, support)
* [Geoffrey Sneddon](http://gsnedders.com) (Lead developer)
* [Michael Shipley](http://michaelpshipley.com) (Submitter of patches, support)
* [Steve Minutillo](http://minutillo.com/steve/) (Submitter of patches)

### Contributors
For a complete list of contributors:

1. Pull down the latest SimplePie code
2. In the `simplepie` directory, run `git shortlog -ns`


## Requirements
* PHP 5.1.4 or newer
* libxml2 (certain 2.7.x releases are too buggy for words, and will crash)
* Either the iconv or mbstring extension
* cURL or fsockopen()
* PCRE support

If you're looking for PHP 4.x support, pull the "1.2" tag, as that's the last version to support PHP 4.x.


## License
[New BSD license](http://www.opensource.org/licenses/bsd-license.php)


## Project status
SimplePie is currently maintained by Ryan McCue.

SimplePie is currently in "low-power mode." If the community decides that SimplePie is a valuable tool, then the community will come together to maintain it into the future.

If you're interested in getting involved with SimplePie, please get in touch with Ryan McCue.


## Roadmap
SimplePie 1.3 should be a thoughtful reduction of features. Remove some bloat, slim it down, and break it into smaller, more manageable chunks.

Removing PHP 4.x support will certainly help with the slimming. It will also help avoid certain issues that frequently crop up with PHP 4.x. The PHP5-only migration is underway, but there is still quite a bit of work before it's "clean."


## What comes in the package?
1. `simplepie.inc` - The SimplePie library.  This is all that's required for your pages.
2. `README.markdown` - This document.
3. `LICENSE.txt` - A copy of the BSD license.
4. `compatibility_test/` - The SimplePie compatibility test that checks your server for required settings.
5. `demo/` - A basic feed reader demo that shows off some of SimplePie's more noticable features.
6. `idn/` - A third-party library that SimplePie can optionally use to understand Internationalized Domain Names (IDNs).
7. `test/` - SimplePie's unit test suite.


## To start the demo
1. Upload this package to your webserver.
2. Make sure that the cache folder inside of the demo folder is server-writable.
3. Navigate your browser to the demo folder.


## Need support?
For further setup and install documentation, function references, etc., visit:
[http://simplepie.org/wiki/](http://simplepie.org/wiki/)

For bug reports and feature requests, visit:
[http://github.com/rmccue/simplepie/issues](http://github.com/rmccue/simplepie/issues)

Support mailing list -- powered by users, for users.
[http://tech.groups.yahoo.com/group/simplepie-support/](http://tech.groups.yahoo.com/group/simplepie-support/)


## Recently removed
The following have recently been removed:

* Parameters for SimplePie::__construct()
* add_to_*
* display_cached_file
* enable_xml_dump
* get_favicon
* set_favicon_handler
* subscribe_* (except subscribe_url)
* utf8_bad_replace
