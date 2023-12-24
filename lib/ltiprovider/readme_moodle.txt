LTI Tool Provider Library PHP
=============================

Some changes from the upstream version have been made:
* Define consumer profile member variable for ToolConsumer class
* Added context type property for Context class
* Set context type if 'context_type' parameter was submitted through POST
* Do not require tool_consumer_instance_guid
* Prevent modification of the request to the provider
These changes can be reverted once the following pull requests have been integrated upstream:
* https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/pull/10
* https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/pull/11
* https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/pull/47
* https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/pull/48

This local changes can be reverted once it's checked that they are present upstream (note the
LTI-Tool-Provider-Library-PHP repo has been archived so it doesn't accept pull requests anymore):
* MDL-67034 php74 compliance fixes
* MDL-71920: Migrated from curl_exec and friends to use our Moodle curl wrapper,
so we can better handle site security settings

This local change has been made without the accompanying pull request as the upstream library is archived:
* $FULLME is used as the URL for OAuth, to fix reverse proxy support (see MDL-64152)

It is recommended by upstream to install depdencies via composer - but the composer installation is bundled
with an autoloader so it's better to do it manually.

Information
-----------

URL: https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/wiki
License: Apache License, Version 2.0

Installation
------------

1) Download the latest version of the provider library
wget https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/archive/3.0.3.zip

2) Unzip the archive
unzip 3.0.3.zip

3) Move the source code directory into place
mv LTI-Tool-Provider-Library-PHP-3.0.3/* lib/ltiprovider/

4) Updates
Check that the following pull request is included in the release.
Then remove this step from this file.
https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/pull/13
If not, apply manually.

5) Run unit tests on enrol_lti_testsuite

Upgrading Notes
---------------

Check if there are any new changes to the database schema. To do this, view the logs
since the last release for the data connector base class and the mysql data connector.

https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/compare/3.0.2...3.0.3

src/ToolProvider/DataConnector/DataConnector.php
src/ToolProvider/DataConnector/DataConnector_mysql.php

In case of any changes we may need to update

enrol/lti/classes/data_connector.php
enrol/lti/db/install.xml
enrol/lti/db/upgrade.php

* MDL-78144 PHP 8.2 compliance.
  To temporarily prevent the PHP 8.2 warning about the deprecation of dynamic properties,
  the #[\AllowDynamicProperties] attribute was added on top of the classes.
  Below is a handy command to add the attribute above the class line:
  ```
  cd lib/ltiprovider/src
  for file in `find . -name '*.php' `; do sed -i '/^class /i #[\\AllowDynamicProperties]' $file; done
  ```
