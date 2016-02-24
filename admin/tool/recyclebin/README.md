# Moodle Recycle Bin [![Build Status](https://travis-ci.org/unikent/moodle-local_recyclebin.svg)](https://travis-ci.org/unikent/moodle-local_recyclebin)
This plugin adds a "recycle bin" for course modules to Moodle.
It requires a core hack.

See plugin pages here: https://moodle.org/plugins/view/local_recyclebin

See documentation here: https://docs.moodle.org/29/en/local/Recycle_bin

## Installation - course recyclebin
As there is no "pre-cm-deleted" event, you will need to add a line to '/course/lib.php' (function course_delete_module), right after the first "if()".
```
diff --git a/course/lib.php b/course/lib.php
index e49bdf1..5f8d6e6 100644
--- a/course/lib.php
+++ b/course/lib.php
@@ -1654,6 +1654,9 @@ function course_delete_module($cmid) {
         return true;
     }
 
+    // Notify the recycle bin plugin.
+    \local_recyclebin\observer::pre_cm_delete($cm);
+
     // Get the module context.
     $modcontext = context_module::instance($cm->id);
 
```

## Installation - category recyclebin
As there is no "pre-course-deleted" event, you will need to add a line to '/lib/moodlelib.php' (function delete_course), right after the second "if()".
```
diff --git a/lib/moodlelib.php b/lib/moodlelib.php
index 456d0f1..aeb0853 100644
--- a/lib/moodlelib.php
+++ b/lib/moodlelib.php
@@ -4683,6 +4683,9 @@ function delete_course($courseorid, $showfeedback = true) {
         return false;
     }
 
+    // Notify the recycle bin plugin.
+    \local_recyclebin\observer::pre_course_delete($course);
+
     // Make the course completely empty.
     remove_course_contents($courseid, $showfeedback);
 
```
