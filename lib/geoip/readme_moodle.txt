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

