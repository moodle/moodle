Authentication Processing Filters in SimpleSAMLphp
==================================================

<!-- 
	This file is written in Markdown syntax. 
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->


In SimpleSAMLphp, there is an API where you can *do stuff* at the IdP after authentication is complete, and just before you are sent back to the SP. The same API is available on the SP, after you have received a successful Authentication Response from the IdP and before you are sent back to the SP application.

Authentication processing filters postprocess authentication information received from authentication sources. It is possible to use this for additional authentication checks, requesting the user's consent before delivering attributes about the user, modifying the user's attributes, and other things which should be performed before returning the user to the service provider he came from.

Examples of neat things to do using Authentication Processing Filters:

  * Filter out a subset of available attributes that are sent to a SP.
  * Modify the name of attributes.
  * Generate new attributes that are composed of others, for example eduPersonTargetedID.
  * Ask the user for consent, before the user is sent back to a service.
  * Implement basic Access Control on the IdP (not neccessarily a good idea), limiting access for some users to some SPs.

Be aware that Authentication Proccessing Filters do replace some of the previous features in SimpleSAMLphp, named:

  * `attributemap`
  * `attributealter`
  * `attribute filter`

Later in this document, we will desribe in detail the alternative Authentication Proccessing Filters that will replicate these functionalities.

How to configure Auth Proc Filters
----------------------------------

*Auth Proc Filters* can be set globally, or to be specific for only one SP or one IdP. That means there are five locations where you can configure *Auth Proc Filters*:

  * Globally in `config.php`
  * On the SP: Specific for only the SP in `authsources.php`
  * On the SP: Specific for only one remote IdP in `saml20-idp-remote` or `shib13-idp-remote`
  * On the IdP: Specific for only one hosted IdP in `saml20-idp-hosted` or `shib13-idp-hosted`
  * On the IdP: Specific for only one remote SP in `saml20-sp-remote` or `shib13-sp-remote`

The configuration of *Auth Proc Filters* is a list of filters with priority as *index*. Here is an example of *Auth Proc Filters* configured in `config.php`:

	'authproc.idp' => [
		10 => [
			'class' => 'core:AttributeMap', 
			'addurnprefix'
		],
		20 => 'core:TargetedID',
		50 => 'core:AttributeLimit',
		90 => [
			'class' 	=> 'consent:Consent', 
			'store' 	=> 'consent:Cookie', 
			'focus' 	=> 'yes', 
			'checked' 	=> TRUE
		],
	],

This configuration will execute *Auth Proc Filters* one by one, with the priority value in increasing order. When *Auth Proc Filters* is configured in multiple places, in example both globally, in the hosted IdP and remote SP metadata, then the list is interleaved sorted by priority.

The most important parameter of each item on the list is the *class* of the *Auth Proc Filter*. The syntax of the class is `modulename:classname`. As an example the class definition `core:AttributeLimit` will be expanded to look for the class `\SimpleSAML\Module\core\Auth\Process\AttributeLimit`. The location of this class file *must* then be: `modules/core/lib/Auth/Process/AttributeLimit.php`.

You will see that a bunch of useful filters is included in the `core` module. In addition the `consent` module that is included in the SimpleSAMLphp distribution implements a filter. Beyond that, you are encouraged to create your own filters and share with the community. If you have created a cool *Auth Proc Filter* that does something useful, let us know, and we may share it on the [SimpleSAMLphp web site][].

[SimpleSAMLphp web site]: http://simplesamlphp.org

When you know the class definition of a filter, and the priority, the simple way to configure the filter is:

	20 => 'core:TargetedID',

This is analogous to:

	20 => [
		'class' => 'core:TargetedID'
	],

Some *Auth Proc Filters* have optional or required *parameters*. To send parameters to *Auth Proc Filters*, you need to choose the second of the two alernatives above. Here is an example of provided parameters to the consent module:

	90 => [
		'class' 	=> 'consent:Consent', 
		'store' 	=> 'consent:Cookie', 
		'focus' 	=> 'yes', 
		'checked' 	=> TRUE
	],


### Filters in `config.php`

Global *Auth Proc Filters* are configured in the `config.php` file. You will see that the config template already includes an example configuration.

There are two config parameters:

  * `authproc.idp` and
  * `authproc.sp`

The filters in `authproc.idp` will be executed at the IdP side regardless of which IdP and SP entity that is involved.

The filters in `authproc.sp` will be executed at the SP side regardless of which SP and IdP entity that is involved.


### Filters in metadata

Filters can be added both in `hosted` and `remote` metadata. Here is an example of a filter added in a metadata file:

	'__DYNAMIC:1__' => [
		'host'				=>	'__DEFAULT_',
		'privatekey'		=>	'example.org.pem',
		'certificate'		=>	'example.org.crt',
		'auth'				=>	'feide',
		'authproc' => [
			40 => 'core:TargetedID',
		],
	]

The example above is in `saml20-idp-hosted`.



Auth Proc Filters included in the SimpleSAMLphp distribution
------------------------------------------------------------

The following filters are included in the SimpleSAMLphp distribution:

- [`core:AttributeAdd`](./core:authproc_attributeadd): Add attributes to the response.
- [`core:AttributeCopy`](./core:authproc_attributecopy): Copy existing attributes to the response.
- [`core:AttributeAlter`](./core:authproc_attributealter): Do search-and-replace on attributevalues.
- [`core:AttributeLimit`](./core:authproc_attributelimit): Limit the attributes in the response.
- [`core:AttributeMap`](./core:authproc_attributemap): Change the name of the attributes.
- [`core:AttributeRealm`](./core:authproc_attributerealm): (deprecated) Create an attribute with the realm of the user.
- [`core:AttributeValueMap`](./core:authproc_attributevaluemap): Map attribute values to new values and attribute name.
- [`core:Cardinality`](./core:authproc_cardinality): Ensure the number of attribute values is within the specified multiplicity.
- [`core:CardinalitySingle`](./core:authproc_cardinalitysingle): Ensure the correct cardinality of single-valued attributes.
- [`core:GenerateGroups`](./core:authproc_generategroups): Generate a `group` attribute for the user.
- [`core:LanguageAdaptor`](./core:authproc_languageadaptor): Transfering language setting from IdP to SP.
- [`core:PHP`](./core:authproc_php): Modify attributes with custom PHP code.
- [`core:ScopeAttribute`](./core:authproc_scopeattribute): Add scope to attribute.
- [`core:ScopeFromAttribute`](./core:authproc_scopefromattribute): Create a new attribute based on the scope on a different attribute.
- [`core:StatisticsWithAttribute`](./core:authproc_statisticswithattribute): Create a statistics logentry.
- [`core:TargetedID`](./core:authproc_targetedid): Generate the `eduPersonTargetedID` attribute.
- [`core:WarnShortSSOInterval`](./core:authproc_warnshortssointerval): Give a warning if the user logs into the same SP twice within a few seconds.
- [`saml:AttributeNameID`](./saml:nameid): Generate custom NameID with the value of an attribute.
- [`saml:AuthnContextClassRef`](./saml:authproc_authncontextclassref): Set the authentication context in the response.
- [`saml:ExpectedAuthnContextClassRef`](./saml:authproc_expectedauthncontextclassref): Verify the user's authentication context.
- [`saml:FilterScopes`](./saml:filterscopes): Filter attribute values with scopes forbidden for an IdP.
- [`saml:NameIDAttribute`](./saml:nameidattribute): Create an attribute based on the NameID we receive from the IdP.
- [`saml:PersistentNameID`](./saml:nameid): Generate persistent NameID from an attribute.
- [`saml:PersistentNameID2TargetedID`](./saml:nameid): Store persistent NameID as eduPersonTargetedID.
- [`saml:TransientNameID`](./saml:nameid): Generate transient NameID.

See the [Third-party modules](https://simplesamlphp.org/modules) page on the SimpleSAMLphp website
for externally hosted modules that may provide a processing filter.


Writing your own Auth Proc Filter
---------------------------------

Look at the included *Auth Proc Filters* as examples. Copy the classes into your own module and start playing around.

Authentication processing filters are created by creating a class under `Auth/Process/` in a module. This class is expected to subclass `\SimpleSAML\Auth\ProcessingFilter`. A filter must implement at least one function - the `process(&$request)`-function. This function can access the `$request`-array to add, delete and modify attributes, and can also do more advanced processing based on the SP/IdP metadata (which is also included in the `$request`-array). When this function returns, it is assumed that the filter has finished processing.

If a filter for some reason needs to redirect the user, for example to show a web page, it should save the current request. Upon completion it should retrieve the request, update it with the changes it is going to make, and call `\SimpleSAML\Auth\ProcessingChain::resumeProcessing`. This function will continue processing the next configured filter.

Requirements for authentication processing filters:

 - Must be derived from the `\SimpleSAML\Auth\ProcessingFilter`-class.
 - If a constructor is implemented, it must first call the parent constructor, passing along all parameters, before accessing any of the parameters. In general, only the $config parameter should be accessed.
 - The `process(&$request)`-function must be implemented. If this function completes, it is assumed that processing is completed, and that the $request array has been updated.
 - If the `process`-function does not return, it must at a later time call `\SimpleSAML\Auth\ProcessingChain::resumeProcessing` with the new request state. The request state must be an update of the array passed to the `process`-function.
 - No pages may be shown to the user from the `process`-function. Instead, the request state should be saved, and the user should be redirected to a new page. This must be done to prevent unpredictable events if the user for example reloads the page.
 - No state information should be stored in the filter object. It must instead be stored in the request state array. Any changes to variables in the filter object may be lost.
 - The filter object must be serializable. It may be serialized between being constructed and the call to the `process`-function. This means that, for example, no database connections should be created in the constructor and later used in the `process`-function.

*Note*: An Auth Proc Filter will not work in the "Test authentication sources" option in the web UI of a SimpleSAMLphp IdP. It will only be triggered in conjunction with an actual SP. So you need to set up an IdP *and* and SP when testing your filter.

Don't hestitate to ask on the SimpleSAMLphp mailinglist if you have problems or questions, or want to share your *Auth Proc Filter* with others.
