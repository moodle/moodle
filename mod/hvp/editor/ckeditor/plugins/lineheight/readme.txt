lineheight
==========

CKEDITOR Line-height plugin documentation
----------------------------------------------

Installation Notes:

    Adding plugin into the toolbar:
    Using CKBuilder is a recommended solution, however, if you have plugins developed by yourself or by third parties, you can add plugins to your local installation manually by following the steps described below:
    Extract the plugin .zip archive.
    Copy the plugin files to the plugins folder of your CKEditor installation. Each plugin must be placed in a sub-folder that matches its "technical" name.

    For example, the lineheight plugin would be installed into this folder: <CKEditor folder>/plugins/lineheight.
    Check and resolve plugin dependencies. If a plugin needs others to work, you will need to add these manually as well.
    Enable the plugin. Use the extraPlugins setting to add the plugin to your confiuration:

          config.extraPlugins = 'lineheight';

Refer this link for configuring the toolbar: http://docs.ckeditor.com/#!/guide/dev_toolbar


Customizing the lineheight values in the dropdown
-------------------------------------------------------
In order to have custom values of line-height instead of the default, you can add following line in the config.js file:

        config.line_height="1em;1.1em;1.2em;1.3em;1.4em;1.5em";

        Here, "em" is the unit of line-height which you can change to "px","%" or any other allowed unit for line-height.
        You can also change the numbers based on your requirements. You can have more range or low range , it all depends on your requirements.
