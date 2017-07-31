============================================
The Ordering question type for Moodle >= 2.1
============================================

The ordering question type displays several items in a random order
which the user then drags into the correct sequential order.

    * an ordering question can display all items or a subset of items

    * items can be plain text or formatted HTML, including text,
      images, audio and video.

    * items can be listed vertically or horizontally

    * several grading methods are available, ranging from a simple
      all-or-nothing grade, to more complex partial grades that
      consider the placement of each item relative to other items

============================================
To INSTALL or UPDATE this plugin
============================================

    1. get the files for this plugin from any one of the following locations:

        (a) GIT: https://github.com/gbateson/moodle-qtype_ordering.git
        (b) zip: the Moodle.org -> Plugins repository (search for Reader)
        (c) zip: http://bateson.kanazawa-gu.ac.jp/zip/plugins_qtype_ordering.zip

       If you are installing from a zip file, unzip the zip file, to create a folder
       called "ordering" and upload or move this folder into the "question/type" folder
       on your Moodle >= 2.1 site, to create a new folder at "question/type/ordering".

    2. log in to Moodle as administrator to initiate install/upgrade

        if install/upgrade does not begin automatically, you can initiate it manually
        by navigating to the following link:
        Settings -> Site administration -> Notifications

============================================
Further information
============================================

    For more information, tutorials and online discussion forums, please visit:
    http://moodlereader.org/

    This plugin uses JQuery framework and plugins:
    http://jqueryui.com/sortable/
    http://touchpunch.furf.com/

============================================
Development history and credits
============================================

This plugin was originally developed by Thomas Robb (Kyoto Sangyo University, Japan)
and Serafim Panov for the ordering questions in the Reader activity module for Moodle 1.x.

Moodle 2.x development was passed to Gordon Bateson (Kochi University of Technology, Japan)
funded by the Moodle Association of Japan, with partial funding from Sapporo Gakuin University.

Coding enhancements and standardization in preparation for future merging into Moodle core
were undertaken by Vadim Dvorovenko (Kemerovo State University of Culture and Arts, Russia).

Development of "relative to correct" grading method commissioned by Anatoliy Markiv and
sponsored by Kings College London, UK.

many thanks to all concerned!
