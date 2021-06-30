<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for filter_generico
 *
 * @package    filter
 * @subpackage generico
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['filtername'] = 'Generico';
$string['pluginname'] = 'Generico';
$string['genericotemplatesadmin'] = 'Generico Templates Admin';
$string['privacy:metadata'] = 'The Generico filter plugin does not store any personal data.';
$string['filterdescription'] = 'Convert filter strings into templates merged with data';
$string['commonpageheading'] = 'General Settings';
$string['templatepageheading'] = 'Template: {$a}';
$string['templateheading'] = 'Settings for Generico Template {$a}';
$string['template'] = 'The body of template {$a}';
$string['template_desc'] = 'Put the template here, define variables by surrounding them with @@ marks at either e. eg @@variable@@';
$string['templatekey'] = 'The key that identifies template {$a}';
$string['templatekey_desc'] = 'The key should be one word and only contain numbers and letters, underscores, hyphens and dots .';
$string['templatename'] = 'Template Name';
$string['templatename_desc'] = 'The name of this template.';
$string['templateinstructions'] = 'Instructions (template {$a})';
$string['templateinstructions_desc'] =
        'Any instructions entered here will be displayed on the Generic atto form for this template is displayed. Keep them short or it will look bad.';
$string['templateend'] = 'End tags(template {$a})';
$string['templateend_desc'] =
        'If your template encloses user content, eg an info box, put the closing tags here. The user will enter something like {GENERICO:mytag_end} to close out the filter.';
$string['templatescript'] = 'Custom JS (template {$a})';
$string['templatescript_desc'] =
        'If your template needs to run custom javascript, enter that here. It will be run once all the elements have loaded on the page.';
$string['templatedefaults'] = 'variable defaults (template {$a})';
$string['templatedefaults_desc'] =
        'Define the defaults in comma delimited sets of name=value pairs. eg width=800,height=900,feeling=joy';
$string['templaterequire_css'] = 'Requires CSS (template {$a})';
$string['templaterequire_js'] = 'Requires JS (template {$a})';
$string['templaterequire_css_desc'] = 'A link(1 only) to an external CSS file that this template requires. optional.';
$string['templaterequire_js_desc'] = 'A link(1 only) to an external JS file that this template requires. optional.';
$string['templatecount'] = 'Template Count';
$string['templatecount_desc'] = 'The number of templates you can have. Default is 20.';
$string['templateheadingjs'] = 'Javascript Settings.';
$string['templateheadingcss'] = 'CSS/Style Settings.';

$string['templatestyle'] = 'Custom CSS (template {$a})';
$string['templatestyle_desc'] =
        'Enter any custom CSS that your template uses here. Template variables will not work here. Just plain old css.';

$string['templaterequire_amd'] = 'Load via AMD';
$string['templaterequire_amd_desc'] =
        'AMD is a javascript loading mechanism. If you upload or link to javascript libraries in your template, you might have to uncheck this. It only applies if on Moodle 2.9 or greater';

$string['templateupdated'] = '{$a} Poodll Templates Updated.';
$string['updatetoversion'] = 'Update to version: {$a}';
$string['updateall'] = 'Update all';
$string['cleartemplate'] = 'Clear template';

$string['uploadjs'] = 'Upload JS (template {$a})';
$string['uploadjs_desc'] = 'You can upload one js library file which will be loaded for your template. Only one.';

$string['uploadcss'] = 'Upload CSS(template {$a})';
$string['uploadcss_desc'] = 'You can upload one CSS file which will be loaded for your template. Only one.';

$string['presets'] = 'Autofill template with a Preset';
$string['presets_desc'] =
        'Generico comes with some default presets you can use out of the box, or to help you get started with your own template. Choose one of those here, or just create your own template from scratch. You can export a template as a bundle by clicking on the green box above. You can import a bundle by dragging it onto the green box.';

$string['dataset'] = 'Dataset';
$string['dataset_desc'] =
        'Generico allows you to pull a dataset from the database for use in your template. This is an advanced feature. Enter the sql portion of a $DB->get_records_sql call here.';
$string['datasetvars'] = 'Dataset Variables';
$string['datasetvars_desc'] =
        'Put a comma separated list of variables that make up the vars for the SQL. You can and probably will want to use variables here.';

$string['bundle'] = 'Bundle';

$string['templateuploadjsshim'] = ' Upload Shim export';
$string['templateuploadjsshim_desc'] = ' Leave blank unless you know what shimming is';
$string['templaterequirejsshim'] = ' Require Shim export';
$string['templaterequirejsshim_desc'] = ' Leave blank unless you know what shimming is';
$string['templateversion'] = 'The version of this template {$a}';
$string['templateversion_desc'] =
        'Use semantic versioning e.g 1.0.0. Generico will show an update button when the preset version is greater than the template version.';;
$string['templatealternate'] = 'Alternate content';
$string['templatealternate_desc'] =
        'Content that can be used when the custom and uploaded CSS and javascript content is not available. Currently this is used when the template is processed by a webservice, probably for content on the mobile app';
$string['templatealternate_end'] = 'Alternate content end (template {$a})';
$string['templatealternate_end_desc'] =
        'Closing alternate content tags for templates that enclose user content with start and end Generico tags';

//Settings tree headings
$string['templates'] = 'Templates';
$string['jumpcat_heading'] = 'Generico filter settings';
$string['jumpcat_explanation'] = 'The full set of Generico filter settings can be found <a href="{$a}">here</a>.';

//cloud poodll settings
$string['cpapi_heading'] = 'Cloud Poodll API Settings';
$string['cpapi_heading_desc'] =
        "Cloud Poodll allows you to embed recorders direct from cloud.poodll.com in widgets. This is optional and you do not need to fill this in.";
$string['cpapiuser'] = 'Cloud Poodll API User';
$string['cpapiuser_details'] = 'This is the same as your username at Poodll.com.';
$string['cpapisecret'] = 'Cloud Poodll API Secret';
$string['cpapisecret_details'] =
        "This is a special secret key that can be generated from the <a href='https://support.poodll.com/support/solutions/articles/19000083076-cloud-poodll-api-secret'>API tab</a> in your members area on Poodll.com. ";

//CLOUD POODLL API summary display info
$string['displaysubs'] = '{$a->subscriptionname} : expires {$a->expiredate}';
$string['noapiuser'] = "No API username entered.";
$string['noapisecret'] = "No API secret entered.";
$string['credentialsinvalid'] = "The API username and secret entered could not be used to get access. Please check them.";
$string['appauthorised'] = "Cloud Poodll is authorised for this site.";
$string['appnotauthorised'] = "Cloud Poodll is not authorised for this site.";
$string['refreshtoken'] = "Refresh Cloud Poodll license information.";
$string['notokenincache'] = "Refresh Cloud Poodll license information to see details.";