Generico Filter
===============
Generico is a filter that will allow any number of templates to be registered.
When Moodle encounters a filter string it will use the data in the filter string to fill out the template, and insert it into the page.
This versoion of Generico will only run on Moodle 2.9 and newer. If your version of Moodle is older than that, please visit https://moodle.org/plugins/filter_generico to get a version compatible with your version of Moodle.


Usage
===============
Define templates at 
Site Administration / plugins / filters / Generico

A template consists of a "key," a "template," some "defaults," and optionally JS and CSS urls to include. 
The key is just a one word name, that tells Generico which template to use. 
The template is just passage of text that you want to use, and the parts of the template that you want to mark as variables you surround in @@ marks.
The defaults are a comma delimited list of variablename=value pairs. Here is an example template.

templatekey: wildthings
template: Inside this box are @@thing1@@ and @@thing2@@
template defaults: thing2=Silly

A possible filter string for this "wildthings" template would look like this:
{GENERICO:type=wildthings,thing1=Sally}

Generico would replace the above filter string with:
"Inside this box are Sally and Silly"

The filter string must follow this format,
{GENERICO:type=templatekey,variable1=data1,variable2=data2}

The wildthings example above is trivial of course. Imagine using it to embed YouTube videos by registering the standard iframe code YouTube gives you, as a template. Then it would only be necessary to insert the id of the video in a generico filter string.
{GENERICO:type=youtube,id=ABC12345678}

Pre-Set Variables
===============

It is also possible now to add user profile variables to your templates. Just make the first part of the variable name USER: and the next part the name of the user profile field. It also works with custom profile fields. There are two "special" user profile fields, picurl and pic that respectively output the url of the user's profile pic, and their picture itself.

e.g

User's first name: @@USER:firstname@@
User's ice cream preference (custom profile field): @@USER:icecreampref@@
Users profile pic url: @@USER:picurl@@

User's profile pic:  @@USER:pic@@

One more preset variable is AUTOID. This will generate a long random string that you can use as ids to link different parts of the template together. For example you set the id of a div to @@AUTOID@@ and in JS go looking for the @@AUTOID@@ to swap out the div for a player.


Installation
==============
If you are uploading Generico, first expand the zip file and upload the generico folder into:
[PATH TO MOODLE]/filters.

Then visit your Moodle server's Site Administration -> Notifications page. Moodle will guide you through the installation.
On the final page of the installation you will be able to register templates. You should choose to  skip that and do it later from each template's settings page. (Seeing all the templates on one page is too confusing.)

After installing you will need to enable the Generico filter. You can enable the Generico filter when you visit:
Site Administration / plugins / filters / manage filters

JQuery Configuration
==============
Many templates will require JQuery. This is available by default in Moodle for the most part. But under the odd circumstance it is not available to Generico. We used to suggest that you add a load call to your Moodle site's additional HTML area. Thats not the best way anymore. In the odd case jquery is not working, the template can usually be rewritten to make it able to access jquery.

Theme Developers
==============
It is possible to distribute Generico templates with your theme. Create a folder called "generico" in the root folder of your theme and place your template bundle files in there. Your theme Generico templates will then appear in the drop down list of presets on each blank Generico template settings page.



Enjoy

Justin Hunt
poodllsupport@gmail.com





