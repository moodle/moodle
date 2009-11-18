18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: geoip.inc
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/geoip/geoip.inc,v
retrieving revision 1.1.2.2
diff -u -r1.1.2.2 geoip.inc
--- geoip.inc	2 Jan 2008 16:49:05 -0000	1.1.2.2
+++ geoip.inc	18 Nov 2009 06:34:59 -0000
@@ -490,7 +490,7 @@
   $r->nameservers = array("ws1.maxmind.com");
   $p = $r->search($l."." . $ip .".s.maxmind.com","TXT","IN");
   $str = is_object($p->answer[0])?$p->answer[0]->string():'';
-  ereg("\"(.*)\"",$str,$regs);
+  preg_match("/\"(.*)\"/",$str,$regs);
   $str = $regs[1];
   return $str;
 }
