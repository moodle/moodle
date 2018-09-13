Adaptable - the most adaptable moodle theme
===========================================

Version 1.7.1 (2018073000)

Adaptable is a highly customisable responsive two column moodle theme based on the popular BCU theme adding:

-Customisable fonts (Google Fonts)
-Fully customisable colors
-Fully customisable block styles (including FA icons)
-Fully customisable buttons
-Additional header navigation
-News Ticker
-Alternative jQuery slider
-Alternative front page course styles
-Additional customizable marketing blocks
-Additional layout settings for width, slider width, padding of
 various elements
-Social icons
-Mobile settings (customise how theme looks on mobile devices)
-Dismissible bootstrap alerts
-Option to add login form in header on front page
-Logo and Favicon uploader
-Modern emojis (thanks to EmojiOne) 
-Front Page layout builder
-Dashboard layout builder
-Course layout builder
-Activities status
-Privacy API (compatible with GDPR)


In addition many fields (menus, news items, alerts and help links) can be targeted using custom profile fields, thus it is possible
to present different users with different nagivation items and notices. It is also possible for individual users to customise where 
they want top menu navigation to appear (disable, home pages only, sitewide) using custom profile fields.

Adaptable has a lot of settings and may seem daunting at first, our advice is to simply install with the default settings and play 
with it afterwards.

With a little time you should be able to setup an attractive Moodle site with a high degree of individuality without without 
knowing any CSS.

This theme has been developed by:
Lead Developers
Jeremy Hopkins (Coventry University)
Fernando Acedo (3bits elearning solutions)

Developers
Manoj Solanki (Coventry University)


NOTE: This Adaptable version is compatible with moodle 3.4.2 and higher ONLY. 
For older versions use Adaptable 1.6 or 1.3 branch.


Change Log
------------------------------------
The change log is available in: https://adaptable.ws/overview/change-log


HTML/CSS sample code for block areas
------------------------------------
Here you will find some code samples to help you to customize the Info Box and the Marketing Blocks.

You can insert any HTML tag to customize the frontpage blocks. Use a <div> tag as a main container and add the height to keep the 
same value in all the blocks.

The Font Awesome icons set is available in
http://fortawesome.github.io/Font-Awesome/icons/.

You can insert any of them following the examples
http://fortawesome.github.io/Font-Awesome/examples/


Front Page Slider Styles
------------------------
Add images with at least 1900x400px. If you want to reduce or increase the height, Adaptable will resize the image automatically.
There are two possible slider styles each with different markup required:


Original BCU Slider Markup:

<div class="span9">
  <h4>Information</h4>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
    et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
  </div>

  <div class="span3">
  <a href="#" class="submit">2016/17 Courses <i class="fa-chevron-right fa"></i></a>
</div>


Coventry Style Slider Markup

<div class="span6 col-sm-6">
<h3>Hand-crafted</h3> <h4>pixels and code for the Moodle community</h4>
<a href="#" class="submit">Check out our custom theme pricing!</a>
</div>


Frontpage Marketing Block HTML structure Coventry

<div><img src="http://somewebsite.com/2.jpg" class="marketimage"></div>
<h4><a href="#">International Courses</a></h4>
<p>Some text below the link....</p>


Front page Info Box and Marketing Blocks
----------------------------------------

There are two Info blocks in the frontpage located above and below the Marketing Blocks. These are just for compatibility with the
old BCU.

It is recommended to use the new marketing blocks builder that allows you to create your own layout and add much more blocks.

There are 8 rows where you can add up to 4 blocks in each with a total of 32 block of different size. See pix/layout.png for 
more information.

You can enter any HTML code to the block, include FA icons, images, videos and apply inline styles.

Some samples:


Block with solid background, FA icon and some text:

<div style="text-align:center; background: #e6e6e6; height: 350px; padding: 7px;">
    <i class="fa fa-paint-brush fa-5x" style="color: #3A454b;"></i>
    <h3>Title </h3>
    <div style="text-align:center;">Add your text here.</div>
</div>


Block with border and transparent background:

<div style="text-align:center; height: 350px; padding: 7px; border: 1px solid #3A454b;">
    <i class="fa fa-list fa-5x" style="color: #3A454b;"></i>
    <h3>Heading</h3>
    <div style="text-align:center; padding: 5px; color: #3A454b;">Add your text here.</div>
</div>


Block with an image:

<div style="height: 350px;">
    <img src="http://yoursite/yourimage.jpg" style="vertical-align:text-bottom; margin: 0 .5em;" height="auto" width="100%">
    <p style="margin-top: 5px; color: #333333; text-align: center;"><strong>Add your text here</strong></p>
</div>


Block with a video:

<div style="background: #606060; height: 350px">
    <center>
    <iframe src="https://www.youtube.com/embed/wop3FMhoLGs" allowfullscreen="" frameborder="0" height="315" width="560"></iframe>
    </center>
</div>


Block using multi-lang filter:

<div style="width: 100%; height: 240px; background-color: #cccccc;">
<h1 style="text-align: center; line-height: 120px;">
      <span class="multilang" lang="en">text in english</span>
      <span class="multilang" lang="es">texto en espaÃ±ol</span>
      <span class="multilang" lang="fr">texte en franÃ§ais</span>
      <span class="multilang" lang="ca">text en catalÃ </span>
</div>


Footer Blocks
-------------
You can apply the same HTML/CSS in the footer blocks.

Some samples:

Contact information

<i class="fa fa-building"></i>  High St. 100<br>
<span style="margin-left: 20px;">123456 City</span><br><br>
<i class="fa fa-phone"></i> +12 (3)456 78 90<br>
<i class="fa fa-envelope"></i> info@mail.com<br>
<i class="fa fa-globe"></i> www.example.com


List with Chevron

<ul class="block-list">
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>Accessibility</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>Moodle Help</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>Moodle Feedback</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>IT Help</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>IT Feedback</span></a></li>
</ul>


Copyright text
--------------
A sample of copyright text using FA icon

Made with <i class="fa fa-heart" style="color: #ff0000;"></i> in Europe


News Ticker
-----------
From version 1.3 the news ticker do not need to create an unordered list. Just add paragraphs using <p> tags:

<p>Configure all the theme colours</p>
<p>Use any Google Font for the content, headings and site title</p>
<p>Display a logo or a configurable title site</p>
<p>Configurable Slideshow</p>
<p>Display up to 12 marketing blocks in the front page</p>


Messages / Notifications
------------------------
From version 3.2 moodle includes a new system to display messages and 
notifications in the screen. 

The new system displays a hardcoded black icons that are difficult to see when using dark background colour in the top header. 
In that case, you can use an alternate icons pack using white colour.

Login the server by FTP or SFTP and open /theme/adaptable/pix_core/i and 
delete notifications.png and rename notifications-white.png to notifications.png

Then open /theme/adaptable/pix_core/t and delete message.png and 
rename message-white.png to message.png


Activities icons
------------------------
From version 1.4, Adaptable includes its own icons pack that replace the default moodle icons.
If you don't want to use the icons just remove adaptable/pix_plugins and adaptable/pix_core/f
You can enable this icons from the administration.

You will find more information, tips and tricks in: https://adaptable.ws


Versioning
----------
Adaptable is maintained under the Semantic Versioning guidelines as much as possible. Releases will be numbered with the 
following format:

major.minor.patch

and following these guidelines: 
- Breaking backward compatibility bumps the major (and resets the minor and patch) 
- New additions without breaking backward compatibility bumps the minor (and resets the patch)
- Bug fixes and misc changes bumps the patch

For more information on SemVer, please visit http://semver.org.


Acknowledgments
---------------
Big thanks to all the volunteers that are collaborating and testing Adaptable continuously. We really appreciate your help and 
support to develop the most adaptable theme for moodle.

Development:
- Justin Hunt
- Leonid Chernyavskiy
- COMETE (Paris Ouest University)
- Marina Glancy
- Nick Phillips
- Björn Bettzüche 
- Michael Milette
- Bas Brands
- Gareth Barnard
- Konrád Lőrinczi

Testing:
- Andrew Walding
- Alexander Goryntsev

Translation:
- Germán Valero (Español - México)
- Jordi Rodilla (Català  - Andorra)


Contributions
-------------
You are welcome to collaborate in the project. You can fix bugs, add new features or help in the translation to your language.
See CONTRIBUTING.txt for more information


Licenses
--------
Adaptable is licensed under:
GPL v3 (GNU General Public License) - http://www.gnu.org/licenses

Google Fonts released under: 
SIL Open Font License v1.1 - http://scripts.sil.org/OFL 
Apache 2 license - https://www.apache.org/licenses/LICENSE-2.0 
The Ubuntu fonts use the Ubuntu Font License v1.0 - http://font.ubuntu.com/ufl/ubuntu-font-licence-1.0.txt

The Font Awesome font (by Dave Gandy) http://fontawesome.io) is licensed under:
SIL Open Font License v1.1 - http://scripts.sil.org/OFL 

Font Awesome CSS, LESS, and SASS files are licensed under:
MIT License - https://opensource.org/licenses/mit-license.html

Emoji icons provided free by EmojiOne (http://emojione.com) released under:
Creative Commons Attribution 4.0 International - https://creativecommons.org/licenses/by/4.0/


Follow Us
---------
Twitter - https://twitter.com/adaptable_theme 
Facebook - https://www.facebook.com/adaptable.theme

Modify it! - Improve it! - Share it!
