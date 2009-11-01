Ziba Scott <ziba@linuxbox.com> 06/23/05

This is a utility to convert Blackboard Course export zip files into
Moodle course export zip files. It has been successfully tested with
Blackboard 5.5 and Moodle 1.4.1 and Moodle CVS. There is minimal
Blackboard 6 support.  It will convert:

    * Course Name/Title
    * Forum Topics
    * Course Documents
    * Assignments
    * External Links


AUTOMATED OPERATION:

  REQUIREMENTS FOR WEB INTERFACE:
    *Moodle 1.4.1 or greater
    *PHP 4.3 compiled with --enable-xslt  --with-xslt-sablot options
    (Check php.net for instructions on enabling xslt for your platform)
    *Alternatively, PHP 5 compiled with xml and xsl support (-with-xsl)

  INSTALLATION:
    *Unpack this file into the "backup" directory



MANUAL OPERATION:

  REQUIREMENTS:

    *An XSLT 1.0 processor (like Sablotron)
    *A zipping utility

  REQUIREMENTS FOR COMMAND LINE INTERFACE:

    *Linux/Unix
    *PHP 4 compiled with --enable-xslt  --with-xslt-sablot options.
    *PHP 5 compiled with -with-xsl and the default xml support left in.
    *Apache with write access to /tmp
    *A commandline zipping utility

  COMMAND LINE INSTRUCTIONS:

    1) Download and uncompress the Blackboard export into a directory.

    2) Copy bb2moodle.xslt into the Blackboard course directory.

    3) Run your XSLT processor on imsmanifest.xml with bb2moodle.xslt
       as the input and moodle.xml as the output.  If you are using
       Sablotron on linux, this command will look like this:
          sabcmd bb2moodle.xslt imsmanifest.xml > moodle.xml

    4) Create a moodle zip file with this structure:
       moodle.xml
       user_files/
       course_files/

    5) Copy every subdirectory and its contents from the Blackboard
       export directory into the course_files directory.  This does
       not include the Blackboard XML files, only the course
       documents.  Your moodle zip file will now look similar to
       this:
       moodle.xml
       user_files/
       course_files/res0009/myfile.doc
       course_files/res0010/myotherfile.doc
       course_files/res0010/mypicture.jpg

    6) Upload and restore your moodle zip file
