SmartAttributes module
======================

The SmartAttributes module provides authentication processing filters to add attributes.
The logic in this filter exceeds what is possible with the standard filters such, as [`core:AttributeAdd`], [`core:AttributeAlter`], and [`core:AttributeMap`].



`smartattributes:SmartID`
=========================

A filter to add an identifier attribute, based on the first non-empty attribute from a given list of attribute names.
This is useful when there are multiple SAML IdPs configured, and there is no common identifier among them.
For example some IdPs send eduPersonPrincipalName, while others send eduPersonTargetedID. If any of the social networks are configured as an authsource, they will send yet another identifier.
The filter has the following configuration options:

* `candidates`. An array of attributes names to consider as the identifier attribute. Defaults to:
	* eduPersonTargetedID
	* eduPersonPrincipalName
	* pairwise-id
	* subject-id
	* openid
	* facebook_targetedID
	* twitter_targetedID
	* windowslive_targetedID
	* linkedin_targetedID
* `id_attribute`. A string to use as the name of the newly added attribute. Defaults to `smart_id`.
* `add_authority`. A boolean to indicate whether or not to append the SAML AuthenticatingAuthority to the resulting identifier. This can be useful to indicate what SAML IdP was used, in case the original identifier is not scoped. Defaults to `TRUE`.
* `add_candidate`. A boolean to indicate whether or not to prepend the candidate attribute name to the resulting identifier. This can be useful to indicate the attribute originating the identifier. Defaults to `TRUE`.

The generated identifiers have the following form:

`AttributeName:AttributeValue!AuthenticatingAuthority`

For example:

`eduPersonTargetedID:c4bcbe7ca8eac074f65291fd5524caa88f3115c8!https://login.terena.org/idp/saml2/idp/metadata.php`

Examples
--------

Without any configuration:

	'authproc' => array(
		50 => array(
			'class' => 'smartattributes:SmartID'
		),
	),


This will add an attribute called `smart_id` with a value looking like, for example:

`eduPersonTargetedID:c4bcbe7ca8eac074f65291fd5524caa88f3115c8!https://login.terena.org/idp/saml2/idp/metadata.php`

Custom configuration:

	'authproc' => array(
		50 => array(
			'class' => 'smartattributes:SmartID',
			'candidates' => array('eduPersonTargetedID', 'eduPersonPrincipalName'),
			'id_attribute' => 'FooUniversityLocalID',
			'add_authority' => FALSE,
		),
	),

This will add an attribute called `FooUniversityLocalID` with a value like:

`eduPersonTargetedID:c4bcbe7ca8eac074f65291fd5524caa88f3115c8`

If you also want to remove the name of the originating attribute, you could configure it like this:

	'authproc' => array(
		50 => array(
			'class' => 'smartattributes:SmartID',
			'candidates' => array('eduPersonTargetedID', 'eduPersonPrincipalName'),
			'id_attribute' => 'FooUniversityLocalID',
			'add_authority' => FALSE,
			'add_candidate' => FALSE,
		),
	),

Resulting in:

`c4bcbe7ca8eac074f65291fd5524caa88f3115c8`
