
  IMS Enterprise 1.1 file enrolment module for Moodle
    (also reported to work with v1.01 and v1.0 data)

  (c) 2005-2006 Dan Stowell
  Released under the Gnu Public Licence (GPL)

INSTALLATION

Please see INSTALL.txt.


DESCRIPTION

This enrolment script will repeatedly read an XML file from a 
specified location. The XML file should conform to the IMS Enterprise
1.1 specification, containing <person>, <group>, and <membership> 
elements to specify which students/teachers should be added/removed 
from the course. User accounts and/or Moodle courses can be created 
by the script if they aren't yet registered (this is an option which 
can be turned on/off).

(The IMS 1.0 specification is significantly different from the 1.1
spec. This code has been made flexible so it should in theory be 
able to handle IMS 1.0 as well, but I haven't directly tested it
with v1.0 Enterprise data.
The one restriction that may be important is that the plugin assumes
that the <membership> elements come after the others. The 1.1 spec
demands this, but the 1.0 spec does not make this restriction.)


HOW USERS/COURSES ARE MATCHED AGAINST MOODLE'S DATABASE

IMS Enterprise data typically contains a "sourcedid" for each person 
or group (course) record, which represents the canonical identifier 
used by the source system. This is separate from the "userid" for a 
person, which is also present in the data and should represent the 
login userid which a person is intended to use in Moodle. (In some 
systems these may have the same value.)

This script uses the "sourcedid" as the lookup to determine if the 
user/course exists in the database, in both cases looking at the 
"idnumber" field. This "idnumber" is not typically displayed in 
Moodle. When creating a user, the "userid" field must not be blank, 
because it is stored as the user's Moodle login ID.


TECHNICAL NOTE

The script uses an optimised pattern-matching (regex) method for 
processing the XML, rather than any built-in XML handling. This is for
two reasons: firstly, because some systems produce very sloppy 
(even invalid) XML and we'd like to be able to process it anyway; and 
secondly, because PHP 4 and PHP 5 handle XML differently, and we'd 
like to be independent of that changeover.



FOR MORE INFO / HELP

Please visit the community forums at www.moodle.org and search to see
if any relevant help has already been posted. If not, ask away!

