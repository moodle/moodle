# My Profile Picture

My Profile Picture is a Moodle block that interfaces with a web service to import profile pictures from the Blackboard eCommerce Server.

## Features

* PHP webservice based
* Imports photos via cron
* Automated
* Users can reprocess their photos from the official pool
* Stops users from uploading their own pictures

## Download

Visit [My Profile Picture's Github page][my_picture_github] to either download a package or clone the git repository.

## Installation

The My Profile Picture block should be installed like any other block. See [the Moodle Docs page on block installation][block_doc].

## Configuration

The My Profile Picture block makes use of an external webservice for profile
picture replacements. Be sure that the url is a valid url, and can be accessed
from your application server via http request. The application hashes the
idnumber with the current time via sha1 hash `($time . $idnumber)`. This value
is injected into the setting as a standard `sprintf` formatted string.

## Contributions

Contributions of any form are welcome. Github pull requests are preferred.

File any bugs, improvements, or feature requiests in our [issue tracker][issues].

## License

My Profile Picture adopts the same license that Moodle does.

[my_picture_github]: https://github.com/lsuits/my_picture
[block_doc]: http://docs.moodle.org/20/en/Installing_contributed_modules_or_plugins#Block_installation
[issues]: https://github.com/lsuits/my_picture/issues
