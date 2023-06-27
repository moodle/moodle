PDO Metadata Storage Handler
=============================

<!-- 
	This file is written in Markdown syntax. 
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

Introduction
------------

If you want to run a clustered SimpleSAMLphp IdP service and you would like to have centralized storage for metadata, you can use the PDO metadata storage handler.

The present document explains how to configure SimpleSAMLphp and your database.



Preparations
------------

You will need to have the appropriate PDO drivers for your database and you will have to configure the database section within the config/config.php file.



Configuring SimpleSAMLphp
-----------------------------

You will first need to configure a PDO metadata source.

	[root@simplesamlphp simplesamlphp]# vi config/config.php

Here is an example of flatfile plus PDO:

	'metadata.sources' => [
		['type' => 'flatfile'],
		['type' => 'pdo'],
	],



Initializing the Database
-------------------------


Once you have configured your metadata sources to include a PDO source, you will need to initialize the database. This process will create tables in the database for each type of metadata set (saml20-idp-hosted, saml20-idp-remote, saml20-sp-remote, etc).

	[root@simplesamlphp simplesamlphp]# php bin/initMDSPdo.php

If you connect to your database, you will see 11 new empty tables; one for each metadata set.


Adding Metadata
---------------

With the PDO metadata storage handler, metadata is stored in the table for the appropriate set and is stored in JSON format.

As an example, here is the saml20_idp_hosted table:

entity_id       | entity_data
----------------|-------------------------------------------------------------------------------------------------------------------------
`__DEFAULT:1__` | {"host":"\_\_DEFAULT\_\_","privatekey":"idp.key","certificate":"idp.crt","auth":"example-ldap","userid.attribute":"uid"}

Another example is the saml20_idp_remote table:

entity_id                | entity_data
-------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
https://openidp.feide.no | {"name":{"en":"Feide OpenIdP - guest users","no":"Feide Gjestebrukere"},"description":"Here you can login with your account on Feide RnD OpenID. If you do not already have an account on this identity provider, you can create a new one by following the create new account link and follow the instructions.","SingleSignOnService":"https:\/\/openidp.feide.no\/simplesaml\/saml2\/idp\/SSOService.php","SingleLogoutService":"https:\/\/openidp.feide.no\/simplesaml\/saml2\/idp\/SingleLogoutService.php","certFingerprint":"c9ed4dfb07caf13fc21e0fec1572047eb8a7a4cb"}

There is an included script in the `bin` directory that will import all flatfile metadata files and store them in the database, but you can use an external tool to maintain the metadata in the database. This document will only cover adding metadata using the included utility, but the tables above should provide enough information if you would like to create a utility to manage your metadata externally.

To import all flatfile metadata files into the PDO database, run the following script

	[root@simplesamlphp simplesamlphp]# php bin/importPdoMetadata.php

In the event that you import a metadata for an entity id that already exists in the database, it will be overwritten.
