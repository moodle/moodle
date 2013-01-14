
 --------------------------------------------------------------------------
 March 31, 2012                                                Version 1.74

                  m i m e T e X   R e a d m e   F i l e

 Copyright(c) 2002-2012, John Forkosh Associates, Inc. All rights reserved.
 --------------------------------------------------------------------------

                            by: John Forkosh
                  john@forkosh.com     www.forkosh.com

          This file is part of mimeTeX, which is free software.
          You may redistribute and/or modify it under the terms
          of the GNU General Public License, version 3 or later,
          as published by the Free Software Foundation. See
                   http://www.gnu.org/licenses/gpl.html

          MimeTeX is discussed and illustrated online at
          its homepage
                    http://www.forkosh.com/mimetex.html
          Or you can follow the Quick Start instructions below
          (or the more detailed instructions in Section III)
          to immediately install mimeTeX on your own machine.
          Then point your browser to
                    http://www.yourdomain.com/mimetex.html
          for a demo/tutorial and reference.
               Installation problems?  Point your browser to
          mimeTeX's homepage
                    http://www.forkosh.com/mimetex.html
          then click its "full mimeTeX manual" link and see
          Section II.


I.  QUICK START
------------------------------------------------------------------------
  To compile and install mimeTeX
       * unzip mimetex.zip in any convenient working directory
       * to produce an executable that emits anti-aliased
         gif images (recommended)
              cc -DAA mimetex.c gifsave.c -lm -o mimetex.cgi
         -or- for gif images without anti-aliasing
              cc -DGIF mimetex.c gifsave.c -lm -o mimetex.cgi
         -or- to produce an executable that emits mime xbitmaps
              cc -DXBITMAP mimetex.c -lm -o mimetex.cgi
         (For Windows, see "Compile Notes" in Section III below.)
       * mv mimetex.cgi  to your server's cgi-bin/ directory
       * mv mimetex.html to your server's htdocs/  directory
       * if the relative path from htdocs to cgi-bin isn't
         ../cgi-bin then edit mimetex.html and change the
         few dozen occurrences as necessary.
  Then, to quickly learn more about mimeTeX
       * point your browser to www.yourdomain.com/mimetex.html
  Any problems with the above?
       * read the more detailed instructions below,
         or see http://www.forkosh.com/mimetex.html


II.  INTRODUCTION
------------------------------------------------------------------------
  MimeTeX, licensed under the gpl, lets you easily embed LaTeX math in
  your html pages.  It parses a LaTeX math expression and immediately
  emits the corresponding gif image, rather than the usual TeX dvi.
       And mimeTeX is an entirely separate little program that doesn't
  use TeX or its fonts in any way.  It's just one cgi that you put in
  your site's cgi-bin/ directory, with no other dependencies.
  So mimeTeX is very easy to install.  And it's equally easy to use.
  Just place an html <img> tag in your document wherever you want to
  see the corresponding LaTeX expression.  For example,
    <img src="../cgi-bin/mimetex.cgi?f(x)=\int_{-\infty}^x~e^{-t^2}dt"
     border=0 align=absmiddle>
  generates and displays the corresponding gif image on-the-fly,
  wherever you put that <img> tag.  MimeTeX doesn't need intermediate
  dvi-to-gif conversion, and it doesn't clutter your filesystem with
  separate little gif files for each converted expression.  (Optional
  image caching does store gif files, and subsequently reads them as
  needed, rather than re-rendering the same images every time a page
  is reloaded.)


III.  COMPILATION AND INSTALLATION
------------------------------------------------------------------------
  I've built and run mimeTeX under Linux and NetBSD using gcc.
  The source code is ansi-standard C, and should compile
  and execute under all environments without any change whatsoever.
  Build instructions below are for Unix. Modify them as necessary
  for your particular situation.  Note the -DWINDOWS switch if
  applicable.

  Unzip mimetex.zip in any convenient working directory.
  Your working directory should now contain
       mimetex.zip    your gnu zipped mimeTeX distribution containing...
       README         this file (see mimetex.html for demo/tutorial)
       COPYING        GPL license, under which you may use mimeTeX
       mimetex.c      mimeTeX source program and all required functions
       mimetex.h      header file for mimetex.c (and for gfuntype.c)
       gfuntype.c     parses output from  gftype -i  and writes bitmap data
       texfonts.h     output from several gfuntype runs, needed by mimetex.c
       gifsave.c      gif library by Sverre H. Huseby <sverrehu@online.no>
       mimetex.html   sample html document, mimeTeX demo and tutorial
  Note: all files in mimetex.zip use Unix line termination,
  i.e., linefeeds (without carriage returns) signal line endings.
  Conversion for Windows, Macs, VMS, etc, can usually be accomplished
  with unzip's -a option, i.e.,  unzip -a mimetex.zip

  Now, to compile a mimeTeX executable that emits anti-aliased gif
  images (recommended for most uses), type the command
            cc -DAA mimetex.c gifsave.c -lm -o mimetex.cgi

  Or, for an executable that emits gif images without
  anti-aliasing,
            cc -DGIF mimetex.c gifsave.c -lm -o mimetex.cgi

  Alternatively, to compile a mimeTeX executable that emits
  mime xbitmaps, just type the command
            cc -DXBITMAP mimetex.c -lm -o mimetex.cgi

  Compile Notes:
     * If (and only if) you're compiling a Windows executable
       with the -DAA or -DGIF option (but not -DXBITMAP), then
       add -DWINDOWS also.  For example,
            cc -DAA -DWINDOWS mimetex.c gifsave.c -lm -o mimetex.cgi
       The above Unix-like syntax works with MinGW (http://www.mingw.org)
       and djgpp (http://www.delorie.com/djgpp/) Windows compilers, but
       probably not with most others, where it's only intended as a
       "template".
     * Several additional command-line options that you may find
       useful are discussed in Section IId (href="#options")
       of your mimetex.html page.

  That's all there is to building mimeTeX.  You can now test your
  mimetex.cgi executable from the Unix command line by typing, e.g.,
       ./mimetex.cgi "x^2+y^2"
  which should emit two ascii rasters something like the following
    Ascii dump of bitmap image...     Hex dump of colormap indexes...
    ........**..................**..  .......1**1................1**1.
    .......*..*.....*..........*..*.  .......*23*.....*..........*23*.
    ..........*.....*.............*.  ..........*.....*.............*.
    .***......*.....*....**.*.....*.  .***1....2*.....*....**3*....2*.
    .**.*....*......*....**.*....*..  .**.*...1*......*....**.*...1*..
    ..*.....*.*..******...*.*...*.*.  ..*....2*.*..******...*.*..2*.*.
    **.*...****.....*....*.*...****.  **.*...****.....*....*.*2..****.
    ****............*.....**........  ****............*....1**........
    ................*......*........  ................*......*........
    ................*....**.........  ................*....**1........
                                  The 5 colormap indexes denote rgb...
                                 .-->255 1-->196 2-->186 3-->177 *-->0
  The right-hand illustration shows asterisks in the same positions as
  the left-hand one, along with anti-aliased grayscale colormap indexes
  assigned to neighboring pixels, and with the rgb value for each
  index.  Just typing ./mimetex.cgi without an argument should produce
  ascii rasters for the default expression f(x)=x^2.  If you see the
  two ascii rasters then your binary's good, so mv it to your server's
  cgi-bin/ directory and set permissions as necessary.

  Once mimetex.cgi is working, mv it to your server's cgi-bin/ directory
  (wherever cgi programs are expected), and chmod/chown it as necessary.
  Then mv mimetex.html to your server's htdocs/ directory.  Now point
  your browser to www.yourdomain.com/mimetex.html and you should see
  your mimeTeX user's manual reference page.

  Install Notes:
     * These two directories are typically of the form
       somewhere/www/cgi-bin/  and  somewhere/www/htdocs/
       so I set up mimtex.html to access mimetex.cgi from
       the relative path ../cgi-bin/   If your directories
       are non-conforming, you may have to edit the few dozen
       occurrences of ../cgi-bin/mimetex.cgi in mimetex.html
       Sometimes a suitable symlink works.  If not, you'll
       have to edit.  In that case, globally changing
       ../cgi-bin/mimetex.cgi  often works.
     * Either way, once mimetex.html displays properly, you can
       assume everything is working, and can begin authoring html
       documents using mimetex.cgi to render your own math.


IV.  REVISION HISTORY
------------------------------------------------------------------------
  A more detailed account of mimeTeX's revision history
  is maintained at  http://www.forkosh.com/mimetexchangelog.html
  ---
  03/31/12  J.Forkosh      version 1.74 released.
  08/24/11  J.Forkosh      version 1.72 released.
  09/06/08  J.Forkosh      version 1.70 released.
  11/30/04  J.Forkosh      version 1.60 released
  10/02/04  J.Forkosh      version 1.50 released on CTAN with various new
                           features and fixes, and updated documentation.
  07/18/04  J.Forkosh      version 1.40 re-released on CTAN with minor
                           changes, e.g., \mathbb font and nested \array's
                           now supported.
  03/21/04  J.Forkosh      version 1.40 released on CTAN, with improved
                           LaTeX compatibility, various new features and
                           fixes, including fix to work under Windows.
  12/21/03  J.Forkosh      version 1.30 released on CTAN, with improved
                           LaTeX compatibility and anti-aliasing, various new
                           features, and thoroughly updated documentation.
  10/17/03  J.Forkosh      version 1.20 released on CTAN, adding picture
                           environment and various other changes (e.g.,
                           more delimiters arbitrarily sized) and fixes.
  07/29/03  J.Forkosh      version 1.10 released on CTAN, completely replacing
                           mimeTeX's original built-in fonts with thinner and
                           more pleasing fonts, and adding one larger size.
  06/27/03  J.Forkosh      version 1.01 released on CTAN, adding lowpass
                           anti-aliasing for gifs, and http_referer checks,
                           and fixing a few very obscure bugs.
  12/11/02  J.Forkosh      version 1.00 released on CTAN, fixing \array bug
                           and adding various new features.
  10/31/02  J.Forkosh      version 0.99 released on CTAN
  09/18/02  J.Forkosh      internal beta test release


V.  CONCLUDING REMARKS
------------------------------------------------------------------------
  I hope you find mimeTeX useful.  If so, a contribution to your
  country's TeX Users Group, or to the GNU project, is suggested,
  especially if you're a company that's currently profitable.
========================= END-OF-FILE README ===========================

