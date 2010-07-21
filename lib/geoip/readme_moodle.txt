20 Jul 2010
MDL-20876 - replaced deprecated split() with explode()

diff --git a/lib/geoip/geoipcity.inc b/lib/geoip/geoipcity.inc
index 2297745..4e7b397 100644
--- a/lib/geoip/geoipcity.inc
+++ b/lib/geoip/geoipcity.inc
@@ -67,9 +67,9 @@ class geoipdnsrecord {
 
 function getrecordwithdnsservice($str){
   $record = new geoipdnsrecord;
-  $keyvalue = split(";",$str);
+  $keyvalue = explode(";",$str);
   foreach ($keyvalue as $keyvalue2){
-    list($key,$value) = split("=",$keyvalue2);
+    list($key,$value) = explode("=",$keyvalue2);
     if ($key == "co"){
       $record->country_code = $value;
     }
@@ -214,4 +214,4 @@ function GeoIP_record_by_addr ($gi,$addr){
   return _get_record($gi, $ipnum);
 }
 
-?>
\ No newline at end of file
+?>



18 Nov 2009
Description of geoip modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: geoip.inc
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/geoip/geoip.inc,v
retrieving revision 1.2
diff -u -r1.2 geoip.inc
--- geoip.inc	19 Jul 2009 13:54:11 -0000	1.2
+++ geoip.inc	18 Nov 2009 04:03:48 -0000
@@ -493,7 +493,7 @@
   $r->nameservers = array("ws1.maxmind.com");
   $p = $r->search($l."." . $ip .".s.maxmind.com","TXT","IN");
   $str = is_object($p->answer[0])?$p->answer[0]->string():'';
-  ereg("\"(.*)\"",$str,$regs);
+  preg_match("#\"(.*)\"#",$str,$regs);
   $str = $regs[1];
   return $str;
 }

