This is a limited import of PEAR's HTTP_WebDAV_Server library.

Important notes:

 - It does _not_ include all the files, as the tests and example
   files are a serious security risk. It includes the minimal files
   needed - updates need to be extremely careful to avoid including
   file.php, FileSystem dir and Server dir. Also removed documentation,
   while AUTHORS and COPYING remain.

 - The code corresponds to the HEAD branch on Jan 28th 2008, you can
   get a checkout matching these files by passing the date to the
   checkout or update command.

 - The license is new BSD license.


~ martin langhoff <martin@catalyst.net.nz> 28-01-2008
