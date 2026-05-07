H5P Editor PHP Library
==========

A general library that is supposed to be used in most PHP implementations of H5P.

## License

All code is licensed under MIT License

Open Sans font is licensed under Apache license, Version 2.0

## Compiling scss to css

You first need to install ruby and compass:
- `sudo apt update`
- `sudo apt install ruby-full`
- `sudo apt install build-essential`
- `sudo gem install compass`

Then cd to `h5p-editor-php-library/styles` and compile the scss files:
- `compass watch` to continuely compile changes
- or `compass clean && compass compile` to delete the css files and compile new ones
