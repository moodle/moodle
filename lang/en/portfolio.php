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
 * Strings for component 'portfolio', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core_portfolio
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activeexport'] = 'Resolve active export';
$string['activeportfolios'] = 'Available portfolios';
$string['addalltoportfolio'] = 'Export all to portfolio';
$string['addnewportfolio'] = 'Add a new portfolio';
$string['addtoportfolio'] = 'Export to portfolio';
$string['alreadyalt'] = 'Already exporting - please click here to resolve this transfer';
$string['alreadyexporting'] = 'You already have an active portfolio export in this session. Before continuing, you must either complete this export, or cancel it.  Would you like to continue it? (No will cancel it)';
$string['availableformats'] = 'Available export formats';
$string['callbackclassinvalid'] = 'Callback class specified was invalid or not part of the portfolio_caller hierarchy';
$string['callercouldnotpackage'] = 'Failed to package up your data for export: original error was {$a}';
$string['cannotsetvisible'] = 'Cannot set this to visible - the plugin has been completely disabled because of a misconfiguration';
$string['commonportfoliosettings'] = 'Common portfolio settings';
$string['commonsettingsdesc'] = '<p>Whether a transfer is considered to take a \'Moderate\' or \'High\' amount of time changes whether the user is able to wait for the transfer to complete or not.</p><p>Sizes up to the \'Moderate\' threshold just happen immediately without the user being asked, and \'Moderate\' and \'High\' transfers mean they are offered the option but warned it might take some time.</p><p>Additionally, some portfolio plugins might ignore this option completely and force all transfers to be queued.</p>';
$string['configexport'] = 'Configure exported data';
$string['configplugin'] = 'Configure portfolio plugin';
$string['configure'] = 'Configure';
$string['confirmcancel'] = 'Are you sure you wish you cancel this export?';
$string['confirmexport'] = 'Please confirm this export';
$string['confirmsummary'] = 'Summary of your export';
$string['continuetoportfolio'] = 'Continue to your portfolio';
$string['deleteportfolio'] = 'Delete portfolio instance';
$string['destination'] = 'Destination';
$string['disabled'] = 'Sorry, but portfolio exports are not enabled in this site';
$string['disabledinstance'] = 'Disabled';
$string['displayarea'] = 'Export area';
$string['displayexpiry'] = 'Transfer expiry time';
$string['displayinfo'] = 'Export info';
$string['dontwait'] = 'Don\'t wait';
$string['enabled'] = 'Enable portfolios';
$string['enableddesc'] = 'If enabled, users can export content, such as forum posts and assignment submissions, to external portfolios or HTML pages.';
$string['err_uniquename'] = 'Portfolio name must be unique (per plugin)';
$string['exportalreadyfinished'] = 'Portfolio export complete!';
$string['exportalreadyfinisheddesc'] = 'Portfolio export complete!';
$string['exportcomplete'] = 'Portfolio export complete!';
$string['exportedpreviously'] = 'Previous exports';
$string['exportexceptionnoexporter'] = 'A portfolio_export_exception was thrown with an active session but no exporter object';
$string['exportexpired'] = 'Portfolio export expired';
$string['exportexpireddesc'] = 'You tried to repeat the export of some information, or start an empty export. To do that properly you should go back to the original location and start again. This sometimes happens if you use the back button after an export has completed, or by bookmarking an invalid url.';
$string['exporting'] = 'Exporting to portfolio';
$string['exportingcontentfrom'] = 'Exporting content from {$a}';
$string['exportingcontentto'] = 'Exporting content to {$a}';
$string['exportqueued'] = 'Portfolio export has been successfully queued for transfer';
$string['exportqueuedforced'] = 'Portfolio export has been successfully queued for transfer (the remote system has enforced queued transfers)';
$string['failedtopackage'] = 'Could not find files to package';
$string['failedtosendpackage'] = 'Failed to send your data to the selected portfolio system: original error was {$a}';
$string['filedenied'] = 'Access denied to this file';
$string['filenotfound'] = 'File not found';
$string['fileoutputnotsupported'] = 'Rewriting file output is not supported for this format';
$string['format_document'] = 'Document';
$string['format_file'] = 'File';
$string['format_image'] = 'Image';
$string['format_leap2a'] = 'Leap2A portfolio format';
$string['format_mbkp'] = 'Moodle backup format';
$string['format_pdf'] = 'PDF';
$string['format_plainhtml'] = 'HTML';
$string['format_presentation'] = 'Presentation';
$string['format_richhtml'] = 'HTML with attachments';
$string['format_spreadsheet'] = 'Spreadsheet';
$string['format_text'] = 'Plain text';
$string['format_video'] = 'Video';
$string['highdbsizethreshold'] = 'High transfer dbsize';
$string['highdbsizethresholddesc'] = 'Number of db records over which will be considered to take a high amount of time to transfer';
$string['highfilesizethreshold'] = 'High transfer filesize';
$string['highfilesizethresholddesc'] = 'Filesizes over this threshold will be considered to take a high amount of time to transfer';
$string['insanebody'] = 'Hi! You are receiving this message as an administrator of {$a->sitename}.

Some portfolio plugin instances have been automatically disabled due to misconfigurations. This means that users can not currently export content to these portfolios.

The list of portfolio plugin instances that have been disabled is:

{$a->textlist}

This should be corrected as soon as possible, by visiting {$a->fixurl}.';
$string['insanebodyhtml'] = '<p>Hi! You are receiving this message as an administrator of {$a->sitename}.</p>
<p>Some portfolio plugin instances have been automatically disabled due to misconfigurations. This means that users can not currently export content to these portfolios.</p>
<p>The list of portfolio plugin instances that have been disabled is:</p>
{$a->htmllist}
<p>This should be corrected as soon as possible, by visiting <a href="{$a->fixurl}">the portfolio configuration pages</a></p>';
$string['insanebodysmall'] = 'Hi! You are receiving this message as an administrator of {$a->sitename}. Some portfolio plugin instances have been automatically disabled due to misconfigurations. This means that users can not currently export content to these portfolios. This should be corrected as soon as possible, by visiting {$a->fixurl}.';
$string['insanesubject'] = 'Some portfolio instances automatically disabled';
$string['instancedeleted'] = 'Portfolio deleted successfully';
$string['instanceismisconfigured'] = 'Portfolio instance is misconfigured, skipping. Error was: {$a}';
$string['instancenotdelete'] = 'Failed to delete portfolio';
$string['instancenotsaved'] = 'Failed to save portfolio';
$string['instancesaved'] = 'Portfolio saved successfully';
$string['intro'] = 'Content which you have created, such as assignment submissions, forum posts and blog entries, can be exported to a portfolio or downloaded.<br>
Any portfolio that you do not wish to use may be hidden so that it is not listed as an option to export content to.';
$string['invalidaddformat'] = 'Invalid add format passed to portfolio_add_button. ({$a}) Must be one of PORTFOLIO_ADD_XXX';
$string['invalidbuttonproperty'] = 'Could not find that property ({$a}) of portfolio_button';
$string['invalidconfigproperty'] = 'Could not find that config property ({$a->property} of {$a->class})';
$string['invalidexportproperty'] = 'Could not find that export config property ({$a->property} of {$a->class})';
$string['invalidfileareaargs'] = 'Invalid file area arguments passed to set_file_and_format_data - must contain contextid, component, filearea and itemid';
$string['invalidformat'] = 'Something is exporting an invalid format, {$a}';
$string['invalidinstance'] = 'Could not find that portfolio instance';
$string['invalidpreparepackagefile'] = 'Invalid call to prepare_package_file - either single or multifiles must be set';
$string['invalidproperty'] = 'Could not find that property ({$a->property} of {$a->class})';
$string['invalidsha1file'] = 'Invalid call to get_sha1_file - either single or multifiles must be set';
$string['invalidtempid'] = 'Invalid export id. Maybe it has expired';
$string['invaliduserproperty'] = 'Could not find that user config property ({$a->property} of {$a->class})';
$string['leap2a_emptyselection'] = 'Required value not selected';
$string['leap2a_entryalreadyexists'] = 'You tried to add a Leap2A entry with an id ({$a}) that already exists in this feed';
$string['leap2a_feedtitle'] = 'Leap2A export from Moodle for {$a}';
$string['leap2a_filecontent'] = 'Tried to set the content of a Leap2A entry to a file, rather than using the file subclass';
$string['leap2a_invalidentryfield'] = 'You tried to set an entry field that didn\'t exist ({$a}) or you can\'t set directly';
$string['leap2a_invalidentryid'] = 'You tried to access an entry by an id that didn\'t exist ({$a})';
$string['leap2a_missingfield'] = 'Required Leap2A entry field {$a} missing';
$string['leap2a_nonexistantlink'] = 'A Leap2A entry ({$a->from}) tried to link to a non existing entry ({$a->to}) with rel {$a->rel}';
$string['leap2a_overwritingselection'] = 'Overwriting the original type of an entry ({$a}) to selection in make_selection';
$string['leap2a_selflink'] = 'A Leap2A entry ({$a->id}) tried to link to itself with rel {$a->rel}';
$string['logs'] = 'Transfer logs';
$string['logsummary'] = 'Previous successful transfers';
$string['manageportfolios'] = 'Manage portfolios';
$string['manageyourportfolios'] = 'Manage your portfolios';
$string['mimecheckfail'] = 'The portfolio plugin {$a->plugin} doesn\'t support that mimetype {$a->mimetype}';
$string['missingcallbackarg'] = 'Missing callback argument {$a->arg} for class {$a->class}';
$string['moderatedbsizethreshold'] = 'Moderate transfer dbsize';
$string['moderatedbsizethresholddesc'] = 'Number of db records over which will be considered to take a moderate amount of time to transfer';
$string['moderatefilesizethreshold'] = 'Moderate transfer filesize';
$string['moderatefilesizethresholddesc'] = 'Filesizes over this threshold will be considered to take a moderate amount of time to transfer';
$string['multipleinstancesdisallowed'] = 'Trying to create another instance of a plugin that has disallowed multiple instances ({$a})';
$string['mustsetcallbackoptions'] = 'You must set the callback options either in the portfolio_add_button constructor or using the set_callback_options method';
$string['noavailableplugins'] = 'Sorry, but there are no available portfolios for you to export to';
$string['nocallbackclass'] = 'Could not find the callback class to use ({$a})';
$string['nocallbackcomponent'] = 'Could not find the component specified {$a}.';
$string['nocallbackfile'] = 'Something in the module you\'re trying to export from is broken - couldn\'t find a required portfolio file';
$string['noclassbeforeformats'] = 'You must set the callback class before calling set_formats in portfolio_button';
$string['nocommonformats'] = 'No common formats between any available portfolio plugin and the calling location {$a->location} (caller supported {$a->formats})';
$string['noinstanceyet'] = 'Not yet selected';
$string['nologs'] = 'There are no logs to display!';
$string['nomultipleexports'] = 'Sorry, but the portfolio destination ({$a->plugin}) doesn\'t support multiple exports at the same time. Please <a href="{$a->link}">finish the current one first</a> and try again';
$string['nonprimative'] = 'A non primitive value was passed as a callback argument to portfolio_add_button. Refusing to continue. The key was {$a->key} and the value was {$a->value}';
$string['nopermissions'] = 'Sorry but you do not have the required permissions to export files from this area';
$string['notexportable'] = 'Sorry, but the type of content you are trying to export is not exportable';
$string['notimplemented'] = 'Sorry, but you are trying to export content in some format that is not yet implemented ({$a})';
$string['notyetselected'] = 'Not yet selected';
$string['notyours'] = 'You are trying to resume a portfolio export that doesn\'t belong to you!';
$string['nouploaddirectory'] = 'Could not create a temporary directory to package your data into';
$string['off'] = 'Enabled but hidden';
$string['on'] = 'Enabled and visible';
$string['plugin'] = 'Portfolio plugin';
$string['plugincouldnotpackage'] = 'Failed to package up your data for export: original error was {$a}';
$string['pluginismisconfigured'] = 'Portfolio plugin is misconfigured, skipping. Error was: {$a}';
$string['portfolio'] = 'Portfolio';
$string['portfolios'] = 'Portfolios';
$string['privacy:metadata'] = 'The portfolio subsystem acts as a channel, passing requests from plugins to the various portfolio plugins.';
$string['privacy:metadata:name'] = 'Name of the preference.';
$string['privacy:metadata:instance'] = 'Identifier for the portfolio.';
$string['privacy:metadata:instancesummary'] = 'This stores portfolio both instances and preferences for the portfolios user is using.';
$string['privacy:metadata:value'] = 'Value for the preference';
$string['privacy:metadata:userid'] = 'The user Identifier.';
$string['privacy:path'] = 'Portfolio instances';
$string['queuesummary'] = 'Currently queued transfers';
$string['returntowhereyouwere'] = 'Return to where you were';
$string['save'] = 'Save';
$string['selectedformat'] = 'Selected export format';
$string['selectedwait'] = 'Selected to wait?';
$string['selectplugin'] = 'Select destination';
$string['showhide'] = 'Show / hide';
$string['singleinstancenomultiallowed'] = 'Only a single portfolio plugin instance is available, it doesn\'t support multiple exports per session, and there\'s already an active export in the session using this plugin!';
$string['somepluginsdisabled'] = 'Some entire portfolio plugins have been disabled because they are either misconfigured or rely on something else that is:';
$string['sure'] = 'Are you sure you want to delete \'{$a}\'? This cannot be undone.';
$string['thirdpartyexception'] = 'A third party exception was thrown during portfolio export ({$a}). Caught and rethrown but this should really be fixed';
$string['transfertime'] = 'Transfer time';
$string['unknownplugin'] = 'Unknown (may have since been removed by an administrator)';
$string['wait'] = 'Wait';
$string['wanttowait_high'] = 'It is not recommended that you wait for this transfer to complete, but you can if you\'re sure and know what you\'re doing';
$string['wanttowait_moderate'] = 'Do you want to wait for this transfer? It might take a few minutes';

