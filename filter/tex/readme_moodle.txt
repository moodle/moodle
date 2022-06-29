Description of mimeTeX v 1.77 import into Moodle

Compiling mimeTeX:

Windows
=======
1/ get "Automated MinGW-w64 Installer" from https://sourceforge.net/projects/mingw-w64/
2/ install mingw
3/ go into directory with extracted source files
4/ execute "set path=%path%;c:\mingw-w64\bin"
5/ execute "c:\mingw\bin\gcc -DAA -DWINDOWS mimetex.c gifsave.c -lm -o mimetex.exe"

Linux
=====
1/ install gcc
2/ go into directory with extracted source files
3/ execute "cc -DAA mimetex.c gifsave.c -lm -o mimetex.linux"

Linux aarch64
=====
1/ install gcc
2/ go into directory with extracted source files
3/ execute "cc -DAA mimetex.c gifsave.c -lm -o mimetex.linux.aarch64"

FreeBSD
=======
1/ go into directory with extracted source files
2/ execute "cc -DAA mimetex.c gifsave.c -lm -o mimetex.freebsd"

Apple OSX
=========
1/ install XCode and command line tools
2/ go into directory with extracted source files
3/ execute "cc -DAA -arch i386 -arch x86_64 -mmacosx-version-min=10.5 mimetex.c gifsave.c -lm -o mimetex.darwin"


Petr Skoda
Sujith Haridasan
