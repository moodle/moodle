SCORM Module by Roberto "Bobo" Pinna

This module is a SCORM player that import SCORM packages in .zip or .pif format
(they are the same thing).
At this time the SCORM module import packages in SCORM1.2, SCORM1.3 (aka SCORM2004) and AICC.
The SCORM 1.3 support still under development use it carefully.

Moodle SCORM Module is SCORM Version 1.2 Run-Time Environment Conformant -
Minimum with All Optional Data Model Elements (LMS-RTE3)

SCORM MODULE IS JAVA FREE.

================================================================

FIX TO DO:
Autocontinue & nav.event call

================================================================

ROAD MAP

Moodle 1.6
A popup window display mode.             DONE
New Moodle course format: SCORM.         DONE
Add prerequisites support to SCORM 1.2.  DONE
Customizable player page.                DONE
Multiple attempt management.             DONE
Complete AICC conformance.


Moodle 1.7
Customizable detailed report page.
Complete conformity to SCORM 2004 RTE.
Support of SCORM 2004's sequencing and navigation.
New package validation subsystem.

Moodle 2.0
The BIG Boh?!

================================================================

SCORM MODULE Schema:

Insert and Update an activity:

mod_edit.php <-- onsubmit --> validate.php (-- include --> validatordomxml.php)
   ||   \
   ||    \
 submit  load
   ||      \
   ||       \
   \/     request.js
view.php

----------------------------------------------------------------

View an activity:

view.php
   ||
   ||
 submit
   ||
   ||
   \/
player.php -- load --> request.js
   ||                     api.php -- include --> datamodels/(SCORM1_2.js.php || SCORM1_3.js.php || AICC.js.php)
   ||                       /\
 iframe                     ||
 "main"<-----             XMLHTTP
  load      |             request
   ||       |               ||
   ||       |               \/
   \/       |          datamodel.php
loadSCO.php |
    |       |
    |       |
    ---------
    reload itself
    to the right sco


================================================================
Updated January 9th 2006
