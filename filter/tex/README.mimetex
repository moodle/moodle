
 --------------------------------------------------------------------------
 January 11, 2004                                              Version 1.40

                  m i m e T e X   R e a d m e   F i l e

 Copyright(c) 2002-2004, John Forkosh Associates, Inc. All rights reserved.
 --------------------------------------------------------------------------

                            by: John Forkosh
                  john@forkosh.com     www.forkosh.com

          This file is part of mimeTeX, which is free software.
          You may redistribute and/or modify it under the terms
          of the GNU General Public License, version 2 or later,
          as published by the Free Software Foundation. See
                   http://www.gnu.org/licenses/gpl.html

          Follow the Quick Start instructions immediately below
          or the detailed instructions in Section III to install
          mimeTeX on your machine, and then point your browser to
                  http://www.yourdomain.com/mimetex.html
          for a complete discussion, including demo/tutorial and
          reference.  Installation problems?  Point your browser
          to my page at
                    http://www.forkosh.com/mimetex.html
          then click its "full mimeTeX manual" link and see
          Section II.


I.  QUICK START
------------------------------------------------------------------------
  To compile and install mimeTeX
       o unzip mimetex.zip in any convenient working directory
       o to produce an executable that emits anti-aliased gif images
              cc -DAA mimetex.c gifsave.c -lm -o mimetex.cgi
         -or- for gif images without anti-aliasing
              cc -DGIF mimetex.c gifsave.c -lm -o mimetex.cgi
         -or- to produce an executable that emits mime xbitmaps
              cc -DXBITMAP mimetex.c -lm -o mimetex.cgi
       o mv mimetex.cgi  to your server's cgi-bin/ directory
       o mv mimetex.html to your server's htdocs/  directory
       o if the relative path from htdocs to cgi-bin isn't
         ../cgi-bin then edit mimetex.html and change the
         few dozen occurrences as necessary
  To quickly learn about mimeTeX
       o point your browser to www.yourdomain.com/mimetex.html
  Any problems with the above?
       o read the more detailed instructions in Section III below,
         or see www.forkosh.com/mimetex.html


II.  INTRODUCTION
------------------------------------------------------------------------
  MimeTeX is licensed under the gpl.  It parses LaTeX math expressions,
  emitting either gif images or mime xbitmaps of them, rather than the
  usual TeX dvi's.  And mimeTeX is an entirely separate little program
  that doesn't use TeX in any way.  Therefore, mimeTeX images are easily
  inserted directly into html documents using a standard html <img> tag,
       <img src="../cgi-bin/mimetex.cgi?f(x)=\int_{-\infty}^x~e^{-t^2}dt"
        border=0 align=absmiddle>
  without intermediate dvi-to-gif conversion, and without storing lots
  of little gif image files, one file for each converted expression.
  This makes your web site and html documents more easily maintained.

  Thus, mimeTeX is primarily intended to help you write native html
  documents containing math.  In this sense it's a kind of "lightweight"
  alternative to MathML, with the advantage that mimeTeX preserves LaTeX
  syntax, and works with any browser and server.


III.  COMPILATION AND INSTALLATION
------------------------------------------------------------------------
  I've comnpiled and run mimeTeX under Linux and NetBSD using gcc.
  The source code is entirely ansi-standard C, and should compile
  and execute under all environments without any change whatsoever.
  Build instructions below are for Unix.  Modify them as necessary
  for your particular situation.

  Unzip mimetex.zip in any convenient working directory.
  You should now have files
       mimetex.zip    your gnu zipped mimeTeX distribution containing...
       README         this file (see mimetex.html for demo/tutorial)
       LICENSE        GPL license, under which you may use mimeTeX
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

  Now, to produce an executable that emits anti-aliased gif images
       (which is how the "official" www.forkosh.com/mimetex.html page
       is displayed), compile mimetex with the command
          cc -DAA mimetex.c gifsave.c -lm -o mimetex.cgi

       Or, to produce an executable that emits gif images
       without anti-aliasing, compile mimetex with the command
          cc -DGIF mimetex.c gifsave.c -lm -o mimetex.cgi

       Alternatively, for an executable that emits mime xbitmaps,
       just compile mimetex with the command
          cc -DXBITMAP mimetex.c -lm -o mimetex.cgi

       Several additional command-line options that you may find
       useful are discussed in Section IIc (href="#options")
       of mimetex.html .

  That's all there is to building mimeTeX.  You can now test mimetex.cgi
  from the Unix command line by typing, e.g.,
       ./mimetex.cgi x^2+y^2
  which should emit an ascii raster something like the left-hand
  illustration.  And if you've compiled mimeTeX with the anti-aliasing
  -DAA option, then you'll also see the right-hand illustration.
  It shows asterisks in the same positions as the left-hand illustration,
  and anti-aliased grayscale colormap indexes assigned to neighboring
  pixels.  And you'll also be shown the actual rgb value for each index.
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
  If you get much fancier than x^2+y^2, remember that many characters have
  to be escaped from the Unix command line, e.g., x\<y or f\(x\)=x^2, etc.
  Of course, you won't need these extra escapes when running mimetex from
  a browser.

  Once mimetex.cgi is working (and you're done playing with it),
  mv it to your server's cgi-bin/ directory, where cgi programs
  are expected (and chmod/chown it if necessary).  Then mv
  mimetex.html to your server's htdocs/ directory.  Now point
  your browser to www.yourdomain.com/mimetex.html , and it should
  be rendered exactly like my page at www.forkosh.com/mimetex.html .
       One "gotcha":  the two directories are typically of the form
  somewhere/www/cgi-bin/ and somewhere/www/htdocs/ ,  so I set up
  mimtex.html to get mimetex.cgi from the relative path ../cgi-bin/ .
  If your directories are non-conforming, you may have to edit the
  few dozen occurrences of ../cgi-bin/mimetex.cgi in mimetex.html
  (globally changing ../cgi-bin/mimetex.cgi should work).
  Sometimes a suitable symlink works; if not, you'll have to edit.

  Either way, once mimetex.html displays properly, and you've reviewed
  the tutorial it contains, you can begin writing html documents using
  mimetex.cgi to render your math.


IV.  REVISION HISTORY
------------------------------------------------------------------------
  01/11/04  J.Forkosh      version 1.40 beta released on www.forkosh.com
                           LaTeX compatibility and various new features
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
  
  If you also like mimeTeX's source, I'm an independent contractor
  incorporated in the US as John Forkosh Associates, Inc.  A resume
  is at www.forkosh.com or email john@forkosh.com
========================= END-OF-FILE README ===========================

