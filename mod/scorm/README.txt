SCORM Module by Roberto "Bobo" Pinna

The current module version seem to work fine but I tried it only with 3/4 SCORM courses (Marine Navigation distribuited with ADL RTE 1.2 and some courses developed by our course team). 

I try to explain how SCORM module works:
SCORM packages come in .zip or .pif (is a zip file with another extension);
Like any other file we must upload the package using the files page.

    * Create an activity:
      When we create a new activity, we can choose from a popup menu the right package.
      After that, on continue, the package is checked and validated (the current version check only if the package contains a imsmanifest.xml file; future versions will check if this file is well formed and other);
      This operation creates a record in the scorm table and a directory containing the unpacked SCORM course.
    * View an activity:
      The first time someone try to view a SCORM activity the module parse the imsmanifest file and insert a record for every manifest item in the scorm_scoes table.
      Then the module show the course summary with two buttons of three, browse and review or enter the course.
      When we click one of them will load an new page that will show the first SCO or the last viewed not completed SCO.
    * Activity report:
      I develop also a begining report page that show the status of every SCO in the SCORM and the time spent in each SCO.

If anyone what to help me to design and develop this module is welcome.

Sorry for my poor English.

Bobo
