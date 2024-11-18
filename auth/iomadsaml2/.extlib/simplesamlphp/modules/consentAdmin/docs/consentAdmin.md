Installing and configuring the consentAdmin module
==================================================

The consentAdmin module is an addon to the consent module. This means that
you can not use consentAdmin without the consent module. If you have not
installed and configured the consent module, please do.

  * [How to install and configure the consent module](./consent:consent)
 
The consentAdmin module only works when the consent module is using a 
database to store the consents.

Setting up the consentAdmin module
----------------------------------

The first thing you need to do is to enable the consentAdmin module:

    touch modules/consentAdmin/enable

Next you need to copy the module config file to the config directory:

    cp modules/consentAdmin/config-templates/module_config.php config

Then you will need to set up the database. The consentAdmin uses the same
table as the consent module, but you still need the set the correct
credentials in the config file. Example:

	'consentadmin'  => array(
		'consent:Database',
		'dsn'		=>	'mysql:host=sql.uninett.no;dbname=andreas_consent',
		'username'	=>	'simplesaml', 
		'password'	=>	'sdfsdf',
	),

Notice that credentials usualy is the same as for the consent module, but
can be different.

Go to the frontpage of your SimpleSAMLphp installation. A link to the
consentAdmin module has been added to the frontpage.

Setting optional parameters
---------------------------

In order to make the consentAdmin module work together with the consent
module correctly, you need to set the configuration 'attributes.hash'
according to the value of 'includeValues' configuration in the consent
module. Likewise, if you've used the 'attributes.exclude' configuration
option in the consent module, you should also set the 'attributes.exclude'
configuration option here to match.

You should also set the 'returnURL' configuration in order to pass on your
users when the press the 'Logout' link.

What does consentAdmin do
-------------------------

When logging into the consentAdmin module, you will be presented with a list
of all services connected to the IdP together with at checkbox indicating
whether the user has given consent to the given service. By clicking the
'Show attributes', you will be presented with a list of attributes that are
released to the service, when the user is accessing that service.
consentAdmin are running the processing filters that have been defined for
each service.
ConsentAdmin will not show services that consent has been disabled for in
the consent module.

Processing filters
------------------

The call to these filters are made with an isPassive request, with means that
no filter is allowed to make userinteraction. 

It is up to the developers of the filters to respect the isPassive request.
The prefered thing to do is to make your setup so that only filters that
modify attributes is run. Othervise it is recommended that developers of
filters to throw a 'NoPassive' exception, if the filter can not run without
userinteraction.
