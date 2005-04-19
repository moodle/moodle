SCORM Module by Roberto "Bobo" Pinna

This module is a SCORM player that import SCORM packages in .zip or .pif format 
(they are the same thing).
At this time the SCORM module import packages in SCORM1.2, SCORM1.3 (aka SCORM2004) and AICC.
It plays SCORM1.2 and AICC packages.

THIS MODULE IS JAVA FREE.

================================================================

Still in development (you can see this as a road map):
- support for playing SCORM 1.3;
- LMS store for all datamodels;
- navigation/sequencing (prerequisites, objective,etc...);
- SCORM packages validation (I disabled the old one);

================================================================

SCORM MODULE Schema:

Insert and Update an activity:

mod.html <-- onsubmit --> validate.php (-- include --> validatordomxml.php)
   ||   \
   ||    \
 submit  load
   ||      \
   ||       \
   \/	  request.js 
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
   ||                     api.php -- include --> datamodels/((SCORM1_2.js.php &| SCORM1_3.js.php) || AICC.js.php)
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
