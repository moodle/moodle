//////////////////////////
//                      //
//  Censorship Filter   //
//                      //
//////////////////////////

This is a very simple Text Filter that searches text 
being output to the screen, replacing "bad" words 
with other words.

To activate this filter, add a line like this to your
config.php:

   $CFG->textfilter1 = 'filter/censor/censor.php';


To customise the word list, edit cursor.php.
