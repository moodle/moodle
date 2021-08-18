# Microsoft Word file import plugin for the Moodle book module

This plugin is one of two associated plugins for the Moodle book module:

*   The "wordimport" plugin provides functionality to import
    content from Microsoft Word files into book module books.

*   The "wordexport" plugin provides functionality to export
    book module books as Microsoft Word files.


## Requirements

The book module is included in Moodle 2.3 and later, and by
default these plugins can only be installed in these versions
of Moodle. If you use Moodle 2.0-2.2 and have manually installed
the book module and want to use these plugins, you must remove
the line "$plugin->requires = 2012062500;" from the files
"wordimport/version.php" and "wordexport/version.php" before
they can be installed.


## Installation

The import and export plugins can be installed independently
of each other, so if you only want one of them you do not
need to install the other.

In Moodle 2.5 and later you can install plugins from the
"Site administration" view. In older versions of Moodle
you need to install them manually.

Note that the "Administration" block (or "Settings" block
in some versions of Moodle) must be visible in the
user interface for the plugins to work properly. It is
visible in default configurations, but can be configured
to not be displayed, in which case users will not see the
user interface controls for the plugins.

### Installation from the "Site administration" view

This is only possible with Moodle 2.5 and later.

1.  Login as admin and visit the Moodle
    "Site administration" view, and click on
    "Site administration" > "Plugins" > "Install plugins"
    on the left.

2.  Choose "Book / Book tool (booktool)" as the Plugin type.

3.  Select the package you want to install.

4.  Check the "Acknowledgement" box, if present.

5.  Click on the "Install plugin from the ZIP file" button.

6.  Click on the "Install plugin!" button.

### Manual installation

This is possible with Moodle 2.0 and later.

1.  Unzip the Word import or export ZIP package(s) to get the folder(s)
    "wordimport" and/or "wordexport".

2.  Upload or copy the "wordimport" and/or "wordexport"
    folder(s) into the "mod/book/tool/" folder of your
    Moodle installation.

3.  Login as admin and visit the Moodle
    "Site administration" view, and click on
    "Site administration" > "Notifications" on the left
    and follow the instructions to finish the installation.

General plugin installation instructions are available at
http://docs.moodle.org/27/en/Installing_plugins

### Upgrading from an older to a newer version

The plugins do not store any plugin specific data in the
Moodle database. This means that you do not lose any data if you
uninstall them, and you can upgrade to another version of the
plugins simply by uninstalling the old version and then
install the new version.


## Configuration

The export plugin has a few settings that can be changed by
editing the file "config.php".


## Usage

### Exporting a book as a Microsoft Word file

1.  Display the book you want to export.

2.  Click on the "Download as ebook" link under
    "Administration" > "Book administration" on the left.
    (In some versions of Moodle it is instead
    located under "Settings" > "Book administration")

### Importing chapters from a Microsoft Word file into an existing book

1.  Display the book you want to import chapters into.

2.  Click on the "Turn editing on" link under
    "Administration" > "Book administration" on the left.
    (In some versions of Moodle it is instead
    located under "Settings" > "Book administration")

3.  Click on the "Import chapters from ebook" link under
    "Administration" > "Book administration" on the left.

4.  Select the Microsoft Word file and click on "Import".

### Create new books from Microsoft Word files

This functionality is only available with Moodle 2.5 and later.

1.  Click on the "Turn editing on" link under
    "Administration" > "Book administration" on the left.
    (In some versions of Moodle it is instead
    located under "Settings" > "Book administration")

2.  Create a new (temporary) book in the section where
    you want to import the new book(s). To do this you
    click on the "Add an activity or resource" link and
    select "Book", and then fill in a title etc.

3.  Display the temporary book. (As the book is empty,
    an editing form will be displayed.)

4.  Click on the "Import ebook as new book" link under
    "Administration" > "Book administration" on the left.

5.  Either:

    a) Select the Microsoft Word file(s) you want to import,
       and click on "Import".

    or:

    b) Enter URL:s for the Microsoft Word file(s) you want to import,
       one on each line in the textbox, and click on
       "Import from URL:s".

6.  Delete the temporary book you created in step 1.

    (Steps 2 and 6 are only necessary if the section
    does not already contain any books. You must display
    a book to see the "Import ebook as new book" link,
    but it can be any book. Unfortunately Moodle does
    not provide any better place to put such a link.)


## Credits

This code is based on the Lucimoo Moodle plugin by Mikael Ylikoski (cf. https://moodle.org/plugins/booktool_importepub).




## Contact information

Web site: http://www.moodle2word.net/
