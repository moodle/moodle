Moodle - FirstClass authentication module
-----------------------------------------
This module uses the FirstClass Flexible Provisining Protocol (FPP) to communicate between the FirstClass server
and the Moodle host.

Installation
------------

1. Enable FPP on the FirstClass server
FPP is not doumented in the FirstClass documentation and is not enable by default.
To enable the protocol you need to edit the file \FCPO\Server\Netinfo. Open the file and insert the
following lines.

// TCP port for Flexible Provisioning Protocol (FPP).
TCPFPPPORT = 3333


2. Create an account on the FirstClass server with privilege "Subadministrator".
Using the FPP protocoll this module logs in to the FirstClass server and issuess batch admin commands.
Batch admin command can only be issued in the context of a user with subadministrative privileges.

Default account name is "fcMoodle".


3. Check that the FPP protocoll is working by running a Telnet session. If everyting is working you
should get a "+0" answer from the server.

> telnet yourhost.domain.com 3333
+0

Check that the "fcMoodle" is working by entering the following sequens of commands:

> telnet yourhost.domain.com 3333
+0
fcMoodle
+0

the_password_you_gave_fcmoodle
+0

Get user some_user_id 1201

1201 0 some_user_id
+0



4. On the Moodle host go to the directory where you have installed Moodle.
Open the folder "auth", where all other authentication modules are installed,
 and create a new directory with the name "fc".

Copy the files "config.html", "fcFPP.php" and "lib.php" to the "auth" directory.

Now you need to add som strings to the language file. This distribution contains
string for the English (en) and Swedish (sv) translation.

Open the file "auth.php" in the folder "lang/sv" and paste the text from the file
"auth.php - sv.txt" at the end of the file above the line "?>"

Open the file "auth.php" in the folder "lang/en" and paste the text from the file
"auth.php - en.txt" at the end of the file above the line "?>"










