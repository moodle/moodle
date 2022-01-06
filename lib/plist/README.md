# CFPropertyList

The PHP implementation of Apple's PropertyList can handle XML PropertyLists as well as binary PropertyLists. It offers functionality to easily convert data between worlds, e.g. recalculating timestamps from unix epoch to apple epoch and vice versa. A feature to automagically create (guess) the plist structure from a normal PHP data structure will help you dump your data to plist in no time.

Note: CFPropertylist was originally hosted on [Google Code](http://code.google.com/p/cfpropertylist/)

## Choose Your Favorite Operating System

CFPropertyList does not rely on any "Apple proprietary" components, like plutil. CFPropertyList runs on any Operating System with PHP and some standard extensions installed.

Although you might want to deliver data to your iPhone application, you might want to run those server side services on your standard Linux (or even Windows) environment, rather than buying an expensive Apple Server. With CFPropertyList you now have the power to provide data from your favorite Operating System.

## Requirements And Limitations

* requires PHP5.3 (as of CFPropertyList 2.0)
* requires either [MBString](http://php.net/mbstring) or [Iconv](http://php.net/iconv)
* requires either [BC](http://php.net/bc) or [GMP](http://php.net/gmp) or [phpseclib](http://phpseclib.sourceforge.net/) (see BigIntegerBug for an explanation) - as of CFPropertyList 1.0.1

## Authors

- Rodney Rehm <rodney.rehm@medialize.de>
- Christian Kruse <cjk@wwwtech.de>
- PSR-0 changes by Jarvis Badgley <https://github.com/ChiperSoft/CFPropertyList>

## License

CFPropertyList is published under the [MIT License](http://www.opensource.org/licenses/mit-license.php).

## Installation

see [Composer / Packagist](http://packagist.org/packages/rodneyrehm/plist).

## Related

* [man(5) plist](http://developer.apple.com/documentation/Darwin/Reference/ManPages/man5/plist.5.html)
* [CFBinaryPList.c](http://www.opensource.apple.com/source/CF/CF-476.15/CFBinaryPList.c)
* [CFPropertyList in Ruby](http://rubyforge.org/projects/cfpropertylist/)
* [CFPropertyList in Python](https://github.com/bencochran/CFPropertyList)
* [plist on Wikipedia](http://en.wikipedia.org/wiki/Plist)
