Bennu - An object-oriented iCalendar (RFC2445) implementation in PHP
http://bennu.sourceforge.net

Bennu is copyright (C) 2005 by Ioannis Papaioannou (pj@moodle.org).

=======================================================================
    TABLE OF CONTENTS
=======================================================================

1. About Bennu
    1.1. What is Bennu?
    1.2. What is iCalendar?
    1.3. Bennu license

2. Using Bennu
    2.1. Integrating Bennu in your application
    2.2. Usage examples

3. Bugs and limitations
    3.1. Known limitations

4. Contact information

=======================================================================

-----------------------------------------------------------------------
1. About Bennu
-----------------------------------------------------------------------

1.1. What is Bennu?

    Bennu is a software library written in PHP that implements the 
    functionality of the IETF iCalendar 2.0 specification (RFC 2445). 
    Its purpose is to enable applications which have an interest in 
    this format (e.g. calendaring, scheduler and organizer programs) 
    to support iCalendar in an easy, powerful, and extensible way.

    In other words, Bennu exists so that developers working on such 
    applications don't have to waste painful hours going through the 
    standard and writing the code to implement it. Instead, they can 
    include this library in their application and get to the fun part 
    already: coding THEIR program.

1.2. What is iCalendar?

    The iCalendar specification is a result of the work of the IETF 
    (Internet Engineering Task Force), Calendaring and Scheduling 
    Working Group. It was authored by Frank Dawson of Lotus 
    Development Corporation and Derik Stenerson of Microsoft 
    Corporation. iCalendar is heavily based on the earlier vCalendar 
    industry specification by the Internet Mail Consortium (IMC), 
    which it extends and seeks to replace.
    
    In practical terms, iCalendar is the number one format used today 
    by calendaring and scheduler applications to import and export 
    data. Applications which provide support for iCalendar include:
    
    * Microsoft Outlook
    * Apple iCal
    * Mozilla Calendar (and Mozilla Sunbird)
    * Mulberry
    * Korganizer
    * Ximian Evolution

    Effectively, this means that iCalendar is akin to a "common 
    language" which all these applications speak. If you are writing 
    an application which includes scheduling or calendaring elements, 
    and you want it to be able to synchronize with other such 
    programs, you need to support iCalendar. Bennu is an easy way to 
    do exactly that, as long as you are coding in PHP.

1.3. Bennu license

    Bennu is released under the GNU Lesser General Public License 
    (LGPL). In short, this means that:
    
    * You are allowed to distribute and/or modify the source code of 
      Bennu
    * You are allowed to use Bennu or any modified version of it in a 
      commercial application
    * You do not have to pay any fees to use, modify, or distribute
      Bennu
    * You can charge others for distributing Bennu or derived versions
    * However, in ALL OF THE ABOVE CASES, you MUST provide the source 
      code for Bennu (or any modified version you may have produced), 
      and that source code MUST be provided under the GNU GPL -or- the 
      GNU LGPL license. Furthermore, you MUST include the original 
      copyright notices and credits that you received the source code 
      with when you distribute it INTACT.
    * In any case, the copyright to Bennu is retained by me, 
      Ioannis Papaioannou.

    ##################
    ##  DISCLAIMER  ##
    ##################
    
    Please be advised that the above is a very short and to the point 
    explanation of the GNU LGPL terms, as I understand it, and it is 
    only my personal opinion. IT IS NOT THE ACTUAL LICENSE UNDER WHICH 
    BENNU IS RELEASED. It is STRONGLY RECOMMENDED that you read the 
    full text of the LGPL in order to avoid any misunderstandings 
    which may be caused by reading my interpretation of it. You can 
    find the full text of the LGPL in the file LICENSE.TXT, which you 
    must have received as part of the Bennu distribution. If you have 
    not received such a file, please email me mentioning where you 
    obtained your copy of Bennu.

-----------------------------------------------------------------------
2. Using Bennu
-----------------------------------------------------------------------

2.1. Integrating Bennu in your application

    To include Bennu in your application, you only need to include one 
    PHP file, like this:
    
    <?php
       include($path_to_bennu.'/library/lib/bennu.inc.php');
       
       // the rest of your code goes here
    ?>

2.2. Usage examples

    Please look at the /examples/ directory for ready-to-run examples 
    illustrating how Bennu is to be used. This section will be 
    revisited and properly written when the source code reaches an 
    acceptable level of features and meturity (no, I don't know when 
    that will be).

-----------------------------------------------------------------------
3. Bugs and limitations
-----------------------------------------------------------------------

3.1. Known limitations

    * LANGUAGE property parameters aren't semantically checked.

-----------------------------------------------------------------------
4. Contact information
-----------------------------------------------------------------------

    You can contact me at the email address pj@moodle.org for any 
    suggestions, ideas, or bug reports regarding Bennu.
    
    At some point there will also be a site which you can use to do 
    anything related to Bennu, but sadly not today. If you want to 
    volunteer and give a hand, I 'll be happy to collaborate.
