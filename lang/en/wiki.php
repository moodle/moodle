<?PHP // $Id$
      // wiki.php - created with Moodle 1.2 development (2004010900)


$string['modulename'] = 'Wiki';
$string['modulenameplural'] = 'Wikis';

$string['wikiname'] = 'Page Name';
$string['wikitype'] = 'Type';
$string['ewikiprinttitle'] = 'Print the wiki name on every page.';
$string['htmlmode'] = 'HTML Mode';
$string['ewikiacceptbinary'] = 'Allow binary files';
$string['wikilinkoptions'] = 'Wiki auto-linking options';
$string['studentadminoptions'] = 'Student admin options';
$string['initialcontent'] = 'Choose an Initial Page';
$string['chooseafile'] = 'Choose/upload initial page';
$string['pagenamechoice'] = '- or -';

$string['wikidefaultpagename'] = 'WikiIndex';
$string['wikistartederror'] = 'Wiki already has entries - can\'t change.';
$string['nowikicreated'] = 'No entries have been created for this wiki.';
$string['wikiusage'] = 'Wiki usage';

$string['nohtml'] = 'No HTML';
$string['safehtml'] = 'Safe HTML';
$string['htmlonly'] = 'HTML only';

$string['searchwiki'] = 'Search Wiki';
$string['wikilinks'] = 'Wiki Links';
$string['choosewikilinks'] = '-- Choose Wiki Links --';
$string['viewpage'] = 'View Page';
$string['sitemap'] = 'Sitemap';
$string['pageindex'] = 'Page Index';
$string['newestpages'] = 'Newest pages';
$string['mostvisitedpages'] = 'Most visited pages';
$string['mostoftenchangedpages'] = 'Most often changed pages';
$string['updatedpages'] = 'Updated pages';
$string['orphanedpages'] = 'Orphaned pages';
$string['orphanedpage'] = 'Orphaned page';
$string['wantedpages'] = 'Wanted pages';
$string['filedownload'] = 'File Download';
$string['for'] = 'for';
$string['groups'] = 'Groups';

$string['action'] = '-- Action --';
$string['otherwikis'] = 'Other Wikis';
$string['pageactions'] = 'Page actions';
$string['editthispage'] = 'Edit this page';
$string['backlinks'] = 'Referring links';
$string['pageinfo'] = 'Page information';
$string['attachments'] = 'Page attachments';
$string['howtowiki'] = 'How to wiki';

$string['chooseadministration'] = '-- Administration --';
$string['administration'] = 'Administration';
$string['notadministratewiki'] = 'You are not allowed to administrate this wiki !';
$string['noadministrationaction'] = 'No administration action given.';
$string['setpageflags'] = 'Set page flags';
$string['nocandidatestoremove'] = 'No candidate pages to remove, choose \'$a\' to show all pages.';
$string['removepages'] = 'Remove pages';
$string['pagesremoved'] = 'Pages removed.';
$string['checklinkscheck'] = 'Are you sure that you want to check the links on this page:';
$string['checklinks'] = 'Check links';
$string['linkschecked'] = 'Links checked';
$string['checklinksnotice'] = 'Please be patient when this page is working.';
$string['removenotice'] = 'Note that only unreferenced pages will be listed here. And because the ewiki engine itself does only limited testing if a page is referenced it may miss some of them here.<br>If you however empty a page first, it will get listed here too. Various other database diagnostics are made as well.';
$string['removepagecheck'] = 'Are you sure that you want to delete these pages ?';
$string['pagename'] = 'Page name';
$string['errororreason'] = 'Error or reason';
$string['listall'] = 'List all';
$string['listcandidates'] = 'List candidates';
$string['removeselectedpages'] = 'Remove selected pages';
$string['disabledpage'] = 'Disabled page';
$string['errorbinandtxt'] = 'Flag error: Page of type BIN and TXT';
$string['errornotype'] = 'Flag error: Neither BIN nor TXT';
$string['errorhtml'] = 'Page of type HTML';
$string['readonly'] = 'Read only page';
$string['ownerunknown'] = 'unknown';
$string['errorroandwr'] = 'Flag error: Page is Writeable and Read only';
$string['errorsize'] = 'Page size bigger than 64k';
$string['emptypage'] = 'Empty page';
$string['flags'] = 'Flags';
$string['status'] = 'Status';
$string['flagsset'] = 'Flags changed';
$string['strippages'] = 'Strip pages';
$string['strippagecheck'] = 'Are you sure that you want to strip old versions from these pages:';
$string['nothingtostrip'] = 'There are no pages with more than one version.';
$string['version'] = 'Version';
$string['versions'] = 'Versions';
$string['pagesstripped'] = 'Pages stripped.';
$string['wrongversionrange'] = '$a is not a correct range!';
$string['versionrangetoobig'] = 'You cannot delete all versions of a page! The last version should remain.';
$string['linkok'] = 'OK';
$string['linkdead'] = 'DEAD';
$string['offline'] = 'OFFLINE';
$string['nolinksfound'] = 'No links found on page.';
$string['revertpages'] = 'Revert mass changes';
$string['pagesreverted'] = 'Changes reverted';
$string['revertpagescheck'] = 'Do you really want to revert the following changes:';
$string['revertchanges'] = 'Revert changes';
$string['versionstodelete'] = 'Version(s) to delete';
$string['nochangestorevert'] = 'No changes to revert.';
$string['authorfieldpattern'] = 'Author field pattern';
$string['noregexp'] = 'This must be a fixed string (you cannot use * or regex), at best use the attackers` IP address or host name, but do not include the port number (because it increased with every http access).';
$string['changesfield'] = 'Within how many hours from the last change';
$string['howtooperate'] = 'How to operate';
$string['authorfieldpatternerror'] = 'Please enter an author.';
$string['deleteversionserror'] = 'Please enter a correct version count.';
$string['changesfielderror'] = 'Please enter a correct hour count.';
$string['revertlastonly'] = 'Only, if it was the last change';
$string['revertallsince'] = 'Version diving, also delete changes made after';
$string['revertthe'] = 'Version diving, but only purge the affected one';
$string['deleteversions'] = 'Delete how many last versions';
$string['deletepage'] = 'Delete page';

# Filter Name
$string['filtername'] = 'Wiki Page Auto-linking';


# Flags, please be careful when translating
$string['flagtxt'] = 'TXT';
$string['flagbin'] = 'BIN';
$string['flagoff'] = 'OFF';
$string['flaghtm'] = 'HTM';
$string['flagro'] = 'RO';
$string['flagwr'] = 'WR';

# This one has to be a WikiWord !!!
$string['deletemewikiword'] = 'DeleteMe';
$string['deletemewikiwordfound'] = '$a found on page';

$string['submit'] = 'Submit';

# Ewiki
$string['editform1']='Try not to worry too much about formatting, it can always be improved later.';
$string['editform2']='Please write sensibly, and remember that all editing is logged.';
$string['save']='Save';
$string['preview']='Preview';
$string['canceledit']='Cancel';
$string['uploadpicturebutton']='Upload';
$string['lastchanged']="Last changed on \$a";
$string['hits'] = "\$a hits";
$string['changes'] = "\$a changes";
$string['upload0'] = 'Use this form to upload an arbitrary binary file into the wiki:';
$string['uplnewnam'] = 'Save with different filename';
$string['uplok'] = 'Your file was uploaded correctly.';
$string['uplerror'] = 'We are sorry, but something went wrong during the file upload.';
$string['dwnlnofiles'] = 'No files uploaded yet.';
$string['file'] = 'File';
$string['uploadedon'] = 'Uploaded on';
$string['fileisoftype'] = 'File is of type';
$string['downloadtimes'] = "Downloaded \$a times";
$string['of'] = 'of';
$string['comment'] = 'Comment';
$string['dwnlsection'] = 'Download section';
$string['infoaboutpage']='Information about the page';
$string['thanksforcontribution']='Thank you for your contribution.';
$string['disabledpage']='This page is currently not available.';
$string['doesnotexist']='This page does not yet exist, please click on the edit Button if you would like to create it.';
$string['errversionsave']='Sorry, while you edited this page someone else did already save a changed version. Please go back to the previous screen and copy your changes to your computers clipboard to insert it again after you reload the edit screen.';
$string['forbidden']='You are not authorized to access this page.';
$string['binimgtoolarge']='Image file is too large!';
$string['binnoimg']='This is an inacceptable file format!';
$string['browse']='Browse';
$string['fetchback']='Fetch-back';
$string['differences']="Differences between version \$a->new_ver and \$a->old_ver of \$a->pagename.";
$string['diff']='Diff';
$string['author']='Author';
$string['created']='Created';
$string['lastmodified']='Last modification';
$string['meta']='Meta data';
$string['refs']='References';
$string['contentsize']='Content size';
$string['pageslinkingto']="Pages linking to this page";
$string['viewsmfor']="View sitemap for";
$string['smfor']="Sitemap for";
$string['cannotchangepage']="This page cannot be changed.";
$string['errversionsave']="Sorry, while you edited this page someone else did already save a changed version. Please go back to the previous screen and copy your changes to your computers clipboard to insert it again after you reload the edit screen.";
$string['uplinsect']="Upload into";
$string['invalidroot']="You are not authorized to access the current root page so no sitemap can be created.";
$string['thispageisntlinkedfromanywhereelse'] = 'This page isn\'t linked from anywhere else.';

$string['wikiexportcomment']='Here you can configure the export to your needs.';
$string['wikiexport']='Export pages';
$string['exportformats']='Export formats';
$string['export']='Export';
$string['withbinaries']='Include binary content';
$string['withvirtualpages']='Include Wiki-Links';
$string['plaintext']='Plain Text';
$string['html']='HTML-Format';
$string['downloadaszip']='Downloadable zip archive';
$string['moduledirectory']='Module Directory';
$string['exportto']='Export to';
$string['exportsuccessful']='Export successful.';
$string['index']='Index';
?>