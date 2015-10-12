Description of Zend framework 1.10.6 import into Moodle
Please note the zend framework is modified - some packages are removed.

Delete all the files from the Moodle lib/zend/Zend folder.
Copy all the files from the zend/library/Zend folder into the Moodle lib/zend/Zend folder.

Audit the Classes we actually use - and delete libraries that are not used directly or indirectly by any of them.

Libraries I think are safe to remove:

Application/ Tool/ Application.php Barcode/ Barcode.php Captcha/ Form/ Form.php Dojo/ Dojo.php Cloud/
CodeGenerator/ Console/ Test/ Db.php Db/ Paginator.php Paginator/ Session.php Session/ Feed.php Feed/
Auth/Adapter/DbTable.php Queue/Adapter/Db/ Queue/Adapter/Db.php Debug.php Dom/ EventManager/ File/ Ldap.php
Ldap/ Auth/Adapter/Ldap.php Locale/Data Mail.php Mail/ Markup.php Markup/ Measure/ Memory.php Memory/ Pdf.php Pdf/
Mime.php Mime/ Mobile/ OpenId.php OpenId/ Auth/Adapter/OpenId.php ProgressBar.php ProgressBar Queue.php Queue/
Search/ Serializer.php Serializer/ Stdlib/ Tag/ Text/ TimeSync.php TimeSync/ Translate.php Translate/
Log/Writer/Firebug.php Wildfire/ Service/ShortUrl/ Service/WindowsAzure/




Do not use outside of our /webservice/* or mnet !!

Changes:
* Update to 1.12.16 - this is more or less vanilla now except for the above folders removed.
