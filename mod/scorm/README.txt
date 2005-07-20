SCORM Module by Roberto "Bobo" Pinna

This module is a SCORM player that import SCORM packages in .zip or .pif format
(they are the same thing).
At this time the SCORM module import packages in SCORM1.2, SCORM1.3 (aka SCORM2004) and AICC.
It plays SCORM1.2 and launch AICC packages.

Moodle SCORM Module is SCORM Version 1.2 Run-Time Environment Conformant -
Minimum with All Optional Data Model Elements (LMS-RTE3)

SCORM MODULE IS JAVA FREE.

================================================================

ROAD MAP

Moodle 1.6
A popup window display mode.             DONE (needs some javascript fixing)
New Moodle course format: SCORM.         DONE (needs to check CSS)
Complete AICC conformance.
Add prerequisites support to SCORM 1.2.  DONE

Moodle 1.7
Complete conformity to SCORM 2004 RTE.
Support of SCORM 2004's sequencing and navigation.
New package validation subsystem.

Moodle 2.0
Many GREAT new features that NOW we can't think about.

================================================================

SCORM MODULE Schema:

Insert and Update an activity:

mod.html <-- onsubmit --> validate.php (-- include --> validatordomxml.php)
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
playscorm.php -- load --> request.js
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
Updated to April 19 2005
