Athabasca University (upload) assignment type
-----------------------------------------

INFORMATION
-----------

This module has been successfully tested for Moodle versions:

- 1.5.3

- 1.5.4 (yes)

- 1.6 (yes, but not by me)

Information about this type can be found here http://webunit.cs.athabascau.ca/moodle/au_adb_functionality/

Zipped code can be found here
http://webunit.cs.athabascau.ca/moodle/moodle_files_folder/upload_type.zip/file_view
and on Moodle CVS

Additional thanks to the guys who developed "Upload&Review" and "Upload files" assignment types. Their code I was using as a base code for my assignment type. 


INSTALLATION:
------------

After downloading and unpacking the archive /*, or checking out the files via CVS,*/ you will be left with a directory called "upload", containing a number of files.

Place the whole folder in the directory your_moodle_folder/mod/assignment

Copy content of assignment.php file into 

- your_moodle_folder/lang/en/assignment.php for version 1.5.3 or 1.5.4

- your_moodle_folder/lang/en-UTF8/assignment.php for version 1.6


NOTE:
-----

The temporary name of this type is "upload assignment type". It will be changed in the future to something more appropriate.

If you would like to change the name you may do it by changing the value of 
$string['typeupload'] = 'upload assignment type'; 
at your_moodle_folder/lang/en/assignment.php

