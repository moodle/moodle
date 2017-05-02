LTI Tool Provider Library PHP
=============================

Some changes from the upstream version have been made:
* Define consumer profile member variable for ToolConsumer class
* Added context type property for Context class
* Set context type if 'context_type' parameter was submitted through POST
These changes can be reverted once the following pull requests have been integrated upstream:
* https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/pull/10/commits/a9a1641f1a593eba4638133245c21d9ad47d8680
* https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/pull/11/commits/0bae60389bd020a02be5554516b86336e651e237

It is recommended by upstream to install depdencies via composer - but the composer installation is bundled
with an autoloader so it's better to do it manually.

Information
-----------

URL: https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/wiki
License: Apache License, Version 2.0

Installation
------------

1) Download the latest version of the provider library
wget https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/archive/3.0.3.zip

2) Unzip the archive
unzip 3.0.3.zip

3) Move the source code directory into place
mv LTI-Tool-Provider-Library-PHP-3.0.3/* lib/ltiprovider/

4) Updates
Check that the following pull request is included in the release.
Then remove this step from this file.
https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/pull/13
If not, apply manually.

5) Run unit tests on enrol_lti_testsuite

Upgrading Notes
---------------

Check if there are any new changes to the database schema. To do this, view the logs
since the last release for the data connector base class and the mysql data connector.

https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/compare/3.0.2...3.0.3

src/ToolProvider/DataConnector/DataConnector.php
src/ToolProvider/DataConnector/DataConnector_mysql.php

In case of any changes we may need to update

enrol/lti/classes/data_connector.php
enrol/lti/db/install.xml
enrol/lti/db/upgrade.php
