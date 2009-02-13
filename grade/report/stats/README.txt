 ____     __              __                ____                                      __
/\  _`\  /\ \__          /\ \__            /\  _`\                                   /\ \__
\ \,\L\_\\ \ ,_\     __  \ \ ,_\    ____   \ \ \L\ \      __   _____     ___    _ __ \ \ ,_\
 \/_\__ \ \ \ \/   /'__`\ \ \ \/   /',__\   \ \ ,  /    /'__`\/\ '__`\  / __`\ /\`'__\\ \ \/
   /\ \L\ \\ \ \_ /\ \L\.\_\ \ \_ /\__, `\   \ \ \\ \  /\  __/\ \ \L\ \/\ \L\ \\ \ \/  \ \ \_
   \ `\____\\ \__\\ \__/.\_\\ \__\\/\____/    \ \_\ \_\\ \____\\ \ ,__/\ \____/ \ \_\   \ \__\
    \/_____/ \/__/ \/__/\/_/ \/__/ \/___/      \/_/\/ / \/____/ \ \ \/  \/___/   \/_/    \/__/
                                                                 \ \_\
                                                                  \/_/
	                                  ,+7$$Z$ZO= 
	                    :+$$Z$$ZOZZZOOOOOOOOD,:,,,
	                 +Z$ZOOO8OO8OOOOOOOOO8+====~~:,
	               7$ZOOOOZOZOOOOOOOOOO8+++++=~:,
	            77$88OZ8MMDZZZOOOOOOOZ+?++==~:,
	         77Z$$Z8MM8OZOZZZZOOOO8ONDN+=~:,
	       I7$Z$NMOOOOOO$$$ZZOOOOOD8D8D8:,
	    II7$7MDZOOZZZZOZ$$ZZ$OOZNDNDDDD8DO         ,IIII777I~ 
	 7?$$ZOMOOZZZ777MMMMMMMMMMMDNDN8DDDD$$7,   =+++++???IIII7777$? 
	   ,,,:Z==++===7NMMMND8DDDMMD8DDDDD777777:?=~~~~==+++???II777$$$, 
	    ,:DM~~~~::,O8ND8OO8Z$$$$DDDDDD7IIIIII+~~~=?I77$$$$77IIII77$$Z+, 
	      ,8,,,,   ONND$ZZ$$777DDDDNDO$77II??+++I7$ZZOOOOZZ$77IIII7$ZZZ:,
	       M,::,   O$D888ZO$II77DDD88OZ$7IIIIIII7$ZOO8OOOOOZ$77III77$ZO=:
	      =M,,,    ?+~~+I$$$ZI778D$I?+=$77IIIII7$ZOO++===~~~$$7IIII7$ZOO~: 
	      OM,,,    I?==?I7$O8D777?+=:,,:77I?III7ZOO++=~:,,,,:77I?II7$ZOO=~,
	      8MI::,   I?=+II7$O8??+=~:,    77I?II7$ZOO+=~,      ?7I??II$ZOO+~: 
	      ~M=~~,   I?++II7ZOO+=~,       II?+?I7$ZOO=~,       ,II+?II7ZOO+~: 
	       M::~:   I?++II7ZOO+=:,       II++?I7$ZOO=~,        II++II7ZOO+~: 
	       M,:~:   I?++II7ZOO+=:        II++?II$ZOO=~,        I?++II7ZOO+=: 
	       M=::,   I?++II7ZOO+=:        II++?II$ZOO=~,        I?++II7ZOO+=:
	       MI:::   I?++?I7ZOO+=:        II++?II$ZOO=~,        I?=+II7ZOO+=: 
	      ,M?:~:   I?++?I7$OO+=:        II+=?II$ZOO=~,        I?=+II7ZOO+=:
	      =M=:~:   I?++?I7$OO+=:        II+=?II$ZOO=~,        I?=+II7ZOO+=:
	       M,:~:   I?++?I7$OO+=:        II+=?II$ZOO=~,        I?=+II7ZOO+~: 
	       M,:~:   I?++?I7$OO+=:        II+=?II$ZOO=~,        I?=+II7ZOO+~: 
	       M,::,   I?++?I7$OO+=:        II+=?II$ZOO=~,        I?=+II7ZOO+~: 
	         ,:,   I?++?I7$OO+=:        II+=?II$ZOO=~,        I?=+II7ZOO+~: 
	          ,    IIII77$ZOO+=:        IIIII77$OOO=~,        IIII77$ZOO+~:
	               77$$ZZZOOO+=:        77$$ZZZOOOO=~,        77$ZZZOOOO+~:
	               $ZOOOOOOOO+=:        $$ZOOOOOOOO=~,        $ZOOOOOOOO+=:


=== About Stats Report ===
The stats report gradebook plug-in was developed as part of a Google Summer of Code 2008
project The goal of the plug-in is to provide a framework for providing text based
statistics for grades in a course. Several statistics such as Highest, Lowest, Mean,
Median, Mode, Percent Pass and Standard Deviation are included and new statistics can be
easily dropped in by developers.


=== About Author ===
This plug-in was oringal created by a computer science student named Daniel Servos as part of
a Google Summer of Code 2008 project.

==== Contact Information ====
Name: Daniel Servos
E-mail: dservos@lakeheadu.ca
Blog: HackerDan.com


=== Copyright ===
Moodle - Modular Object-Oriented Dynamic Learning Environment
http://moodle.org

Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com

This program is free software; you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details:

http://www.gnu.org/copyleft/gpl.html


=== Version / Release ===
This plug-in was developed for Moodle 2.0 dev.

This is the first real release of this plug-in and should be considered an beta version in that it
has not yet had a chance to be extensively tested by users in real life situations and may have
unknown bugs or issues.

Version: 1.0.0b


=== Development Information ===
==== Adding new Statistics ====
To add a new statistic to the plug-in extend the abstract class stats (grade/report/stats/statistics/stats.php) and place you class in a file name stat_yournamehere.php in  grade/report/stats/statistics and it should be automatically loaded in to plug-in.

==== Specification ====
http://docs.moodle.org/en/Student_projects/Animated_grade_statistics_report

==== TO DO =====
* Back port to Moodle 1.9.x
* Add more statistics.
* Add more settings for the report.
* Improve look.
* Add help windows/html.
* Add report defaults page.
* Deal with outcomes better/at all.
* Add export functionality to different formats.
* Improve this readme file.
* Improve documentation.