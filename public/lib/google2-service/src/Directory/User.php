<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Directory;

class User extends \Google\Collection
{
  protected $collection_key = 'nonEditableAliases';
  /**
   * The list of the user's addresses. The maximum allowed data size for this
   * field is 10KB.
   *
   * @var array
   */
  public $addresses;
  /**
   * Output only. This property is `true` if the user has completed an initial
   * login and accepted the Terms of Service agreement.
   *
   * @var bool
   */
  public $agreedToTerms;
  /**
   * Output only. The list of the user's alias email addresses.
   *
   * @var string[]
   */
  public $aliases;
  /**
   * Indicates if user is archived.
   *
   * @var bool
   */
  public $archived;
  /**
   * Indicates if the user is forced to change their password at next login.
   * This setting doesn't apply when [the user signs in via a third-party
   * identity provider](https://support.google.com/a/answer/60224).
   *
   * @var bool
   */
  public $changePasswordAtNextLogin;
  /**
   * User's G Suite account creation time. (Read-only)
   *
   * @var string
   */
  public $creationTime;
  /**
   * Custom fields of the user. The key is a `schema_name` and its values are
   * `'field_name': 'field_value'`.
   *
   * @var array[]
   */
  public $customSchemas;
  /**
   * Output only. The customer ID to [retrieve all account users](https://develo
   * pers.google.com/workspace/admin/directory/v1/guides/manage-
   * users.html#get_all_users). You can use the alias `my_customer` to represent
   * your account's `customerId`. As a reseller administrator, you can use the
   * resold customer account's `customerId`. To get a `customerId`, use the
   * account's primary domain in the `domain` parameter of a [users.list](https:
   * //developers.google.com/workspace/admin/directory/v1/reference/users/list)
   * request.
   *
   * @var string
   */
  public $customerId;
  /**
   * @var string
   */
  public $deletionTime;
  /**
   * The list of the user's email addresses. The maximum allowed data size for
   * this field is 10KB. This excludes `publicKeyEncryptionCertificates`.
   *
   * @var array
   */
  public $emails;
  /**
   * Output only. ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The list of external IDs for the user, such as an employee or network ID.
   * The maximum allowed data size for this field is 2KB.
   *
   * @var array
   */
  public $externalIds;
  /**
   * The user's gender. The maximum allowed data size for this field is 1KB.
   *
   * @var array
   */
  public $gender;
  protected $guestAccountInfoType = GuestAccountInfo::class;
  protected $guestAccountInfoDataType = '';
  /**
   * Stores the hash format of the `password` property. The following
   * `hashFunction` values are allowed: * `MD5` - Accepts simple hex-encoded
   * values. * `SHA-1` - Accepts simple hex-encoded values. * `crypt` -
   * Compliant with the [C crypt
   * library](https://en.wikipedia.org/wiki/Crypt_%28C%29). Supports the DES,
   * MD5 (hash prefix `$1$`), SHA-256 (hash prefix `$5$`), and SHA-512 (hash
   * prefix `$6$`) hash algorithms. If rounds are specified as part of the
   * prefix, they must be 10,000 or fewer.
   *
   * @var string
   */
  public $hashFunction;
  /**
   * The unique ID for the user. A user `id` can be used as a user request URI's
   * `userKey`.
   *
   * @var string
   */
  public $id;
  /**
   * The list of the user's Instant Messenger (IM) accounts. A user account can
   * have multiple ims properties. But, only one of these ims properties can be
   * the primary IM contact. The maximum allowed data size for this field is
   * 2KB.
   *
   * @var array
   */
  public $ims;
  /**
   * Indicates if the user's profile is visible in the Google Workspace global
   * address list when the contact sharing feature is enabled for the domain.
   * For more information about excluding user profiles, see the [administration
   * help center](https://support.google.com/a/answer/1285988).
   *
   * @var bool
   */
  public $includeInGlobalAddressList;
  /**
   * If `true`, the user's IP address is subject to a deprecated IP address
   * [`allowlist`](https://support.google.com/a/answer/60752) configuration.
   *
   * @var bool
   */
  public $ipWhitelisted;
  /**
   * Output only. Indicates a user with super administrator privileges. The
   * `isAdmin` property can only be edited in the [Make a user an administrator]
   * (https://developers.google.com/workspace/admin/directory/v1/guides/manage-
   * users.html#make_admin) operation ( [makeAdmin](https://developers.google.co
   * m/workspace/admin/directory/v1/reference/users/makeAdmin.html) method). If
   * edited in the user [insert](https://developers.google.com/workspace/admin/d
   * irectory/v1/reference/users/insert.html) or [update](https://developers.goo
   * gle.com/workspace/admin/directory/v1/reference/users/update.html) methods,
   * the edit is ignored by the API service.
   *
   * @var bool
   */
  public $isAdmin;
  /**
   * Output only. Indicates if the user is a delegated administrator. Delegated
   * administrators are supported by the API but cannot create or undelete
   * users, or make users administrators. These requests are ignored by the API
   * service. Roles and privileges for administrators are assigned using the
   * [Admin console](https://support.google.com/a/answer/33325).
   *
   * @var bool
   */
  public $isDelegatedAdmin;
  /**
   * Output only. Is 2-step verification enforced (Read-only)
   *
   * @var bool
   */
  public $isEnforcedIn2Sv;
  /**
   * Output only. Is enrolled in 2-step verification (Read-only)
   *
   * @var bool
   */
  public $isEnrolledIn2Sv;
  /**
   * Immutable. Indicates if the inserted user is a guest.
   *
   * @var bool
   */
  public $isGuestUser;
  /**
   * Output only. Indicates if the user's Google mailbox is created. This
   * property is only applicable if the user has been assigned a Gmail license.
   *
   * @var bool
   */
  public $isMailboxSetup;
  /**
   * The list of the user's keywords. The maximum allowed data size for this
   * field is 1KB.
   *
   * @var array
   */
  public $keywords;
  /**
   * Output only. The type of the API resource. For Users resources, the value
   * is `admin#directory#user`.
   *
   * @var string
   */
  public $kind;
  /**
   * The user's languages. The maximum allowed data size for this field is 1KB.
   *
   * @var array
   */
  public $languages;
  /**
   * User's last login time. (Read-only)
   *
   * @var string
   */
  public $lastLoginTime;
  /**
   * The user's locations. The maximum allowed data size for this field is 10KB.
   *
   * @var array
   */
  public $locations;
  protected $nameType = UserName::class;
  protected $nameDataType = '';
  /**
   * Output only. The list of the user's non-editable alias email addresses.
   * These are typically outside the account's primary domain or sub-domain.
   *
   * @var string[]
   */
  public $nonEditableAliases;
  /**
   * Notes for the user.
   *
   * @var array
   */
  public $notes;
  /**
   * The full path of the parent organization associated with the user. If the
   * parent organization is the top-level, it is represented as a forward slash
   * (`/`).
   *
   * @var string
   */
  public $orgUnitPath;
  /**
   * The list of organizations the user belongs to. The maximum allowed data
   * size for this field is 10KB.
   *
   * @var array
   */
  public $organizations;
  /**
   * User's password
   *
   * @var string
   */
  public $password;
  /**
   * The list of the user's phone numbers. The maximum allowed data size for
   * this field is 1KB.
   *
   * @var array
   */
  public $phones;
  /**
   * The list of [POSIX](https://www.opengroup.org/austin/papers/posix_faq.html)
   * account information for the user.
   *
   * @var array
   */
  public $posixAccounts;
  /**
   * The user's primary email address. This property is required in a request to
   * create a user account. The `primaryEmail` must be unique and cannot be an
   * alias of another user.
   *
   * @var string
   */
  public $primaryEmail;
  /**
   * Recovery email of the user.
   *
   * @var string
   */
  public $recoveryEmail;
  /**
   * Recovery phone of the user. The phone number must be in the E.164 format,
   * starting with the plus sign (+). Example: *+16506661212*.
   *
   * @var string
   */
  public $recoveryPhone;
  /**
   * The list of the user's relationships to other users. The maximum allowed
   * data size for this field is 2KB.
   *
   * @var array
   */
  public $relations;
  /**
   * A list of SSH public keys.
   *
   * @var array
   */
  public $sshPublicKeys;
  /**
   * Indicates if user is suspended.
   *
   * @var bool
   */
  public $suspended;
  /**
   * Output only. Has the reason a user account is suspended either by the
   * administrator or by Google at the time of suspension. The property is
   * returned only if the `suspended` property is `true`.
   *
   * @var string
   */
  public $suspensionReason;
  /**
   * Output only. ETag of the user's photo (Read-only)
   *
   * @var string
   */
  public $thumbnailPhotoEtag;
  /**
   * Output only. The URL of the user's profile photo. The URL might be
   * temporary or private.
   *
   * @var string
   */
  public $thumbnailPhotoUrl;
  /**
   * The user's websites. The maximum allowed data size for this field is 2KB.
   *
   * @var array
   */
  public $websites;

  /**
   * The list of the user's addresses. The maximum allowed data size for this
   * field is 10KB.
   *
   * @param array $addresses
   */
  public function setAddresses($addresses)
  {
    $this->addresses = $addresses;
  }
  /**
   * @return array
   */
  public function getAddresses()
  {
    return $this->addresses;
  }
  /**
   * Output only. This property is `true` if the user has completed an initial
   * login and accepted the Terms of Service agreement.
   *
   * @param bool $agreedToTerms
   */
  public function setAgreedToTerms($agreedToTerms)
  {
    $this->agreedToTerms = $agreedToTerms;
  }
  /**
   * @return bool
   */
  public function getAgreedToTerms()
  {
    return $this->agreedToTerms;
  }
  /**
   * Output only. The list of the user's alias email addresses.
   *
   * @param string[] $aliases
   */
  public function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  /**
   * @return string[]
   */
  public function getAliases()
  {
    return $this->aliases;
  }
  /**
   * Indicates if user is archived.
   *
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * Indicates if the user is forced to change their password at next login.
   * This setting doesn't apply when [the user signs in via a third-party
   * identity provider](https://support.google.com/a/answer/60224).
   *
   * @param bool $changePasswordAtNextLogin
   */
  public function setChangePasswordAtNextLogin($changePasswordAtNextLogin)
  {
    $this->changePasswordAtNextLogin = $changePasswordAtNextLogin;
  }
  /**
   * @return bool
   */
  public function getChangePasswordAtNextLogin()
  {
    return $this->changePasswordAtNextLogin;
  }
  /**
   * User's G Suite account creation time. (Read-only)
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Custom fields of the user. The key is a `schema_name` and its values are
   * `'field_name': 'field_value'`.
   *
   * @param array[] $customSchemas
   */
  public function setCustomSchemas($customSchemas)
  {
    $this->customSchemas = $customSchemas;
  }
  /**
   * @return array[]
   */
  public function getCustomSchemas()
  {
    return $this->customSchemas;
  }
  /**
   * Output only. The customer ID to [retrieve all account users](https://develo
   * pers.google.com/workspace/admin/directory/v1/guides/manage-
   * users.html#get_all_users). You can use the alias `my_customer` to represent
   * your account's `customerId`. As a reseller administrator, you can use the
   * resold customer account's `customerId`. To get a `customerId`, use the
   * account's primary domain in the `domain` parameter of a [users.list](https:
   * //developers.google.com/workspace/admin/directory/v1/reference/users/list)
   * request.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * @param string $deletionTime
   */
  public function setDeletionTime($deletionTime)
  {
    $this->deletionTime = $deletionTime;
  }
  /**
   * @return string
   */
  public function getDeletionTime()
  {
    return $this->deletionTime;
  }
  /**
   * The list of the user's email addresses. The maximum allowed data size for
   * this field is 10KB. This excludes `publicKeyEncryptionCertificates`.
   *
   * @param array $emails
   */
  public function setEmails($emails)
  {
    $this->emails = $emails;
  }
  /**
   * @return array
   */
  public function getEmails()
  {
    return $this->emails;
  }
  /**
   * Output only. ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The list of external IDs for the user, such as an employee or network ID.
   * The maximum allowed data size for this field is 2KB.
   *
   * @param array $externalIds
   */
  public function setExternalIds($externalIds)
  {
    $this->externalIds = $externalIds;
  }
  /**
   * @return array
   */
  public function getExternalIds()
  {
    return $this->externalIds;
  }
  /**
   * The user's gender. The maximum allowed data size for this field is 1KB.
   *
   * @param array $gender
   */
  public function setGender($gender)
  {
    $this->gender = $gender;
  }
  /**
   * @return array
   */
  public function getGender()
  {
    return $this->gender;
  }
  /**
   * Immutable. Additional guest-related metadata fields
   *
   * @param GuestAccountInfo $guestAccountInfo
   */
  public function setGuestAccountInfo(GuestAccountInfo $guestAccountInfo)
  {
    $this->guestAccountInfo = $guestAccountInfo;
  }
  /**
   * @return GuestAccountInfo
   */
  public function getGuestAccountInfo()
  {
    return $this->guestAccountInfo;
  }
  /**
   * Stores the hash format of the `password` property. The following
   * `hashFunction` values are allowed: * `MD5` - Accepts simple hex-encoded
   * values. * `SHA-1` - Accepts simple hex-encoded values. * `crypt` -
   * Compliant with the [C crypt
   * library](https://en.wikipedia.org/wiki/Crypt_%28C%29). Supports the DES,
   * MD5 (hash prefix `$1$`), SHA-256 (hash prefix `$5$`), and SHA-512 (hash
   * prefix `$6$`) hash algorithms. If rounds are specified as part of the
   * prefix, they must be 10,000 or fewer.
   *
   * @param string $hashFunction
   */
  public function setHashFunction($hashFunction)
  {
    $this->hashFunction = $hashFunction;
  }
  /**
   * @return string
   */
  public function getHashFunction()
  {
    return $this->hashFunction;
  }
  /**
   * The unique ID for the user. A user `id` can be used as a user request URI's
   * `userKey`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The list of the user's Instant Messenger (IM) accounts. A user account can
   * have multiple ims properties. But, only one of these ims properties can be
   * the primary IM contact. The maximum allowed data size for this field is
   * 2KB.
   *
   * @param array $ims
   */
  public function setIms($ims)
  {
    $this->ims = $ims;
  }
  /**
   * @return array
   */
  public function getIms()
  {
    return $this->ims;
  }
  /**
   * Indicates if the user's profile is visible in the Google Workspace global
   * address list when the contact sharing feature is enabled for the domain.
   * For more information about excluding user profiles, see the [administration
   * help center](https://support.google.com/a/answer/1285988).
   *
   * @param bool $includeInGlobalAddressList
   */
  public function setIncludeInGlobalAddressList($includeInGlobalAddressList)
  {
    $this->includeInGlobalAddressList = $includeInGlobalAddressList;
  }
  /**
   * @return bool
   */
  public function getIncludeInGlobalAddressList()
  {
    return $this->includeInGlobalAddressList;
  }
  /**
   * If `true`, the user's IP address is subject to a deprecated IP address
   * [`allowlist`](https://support.google.com/a/answer/60752) configuration.
   *
   * @param bool $ipWhitelisted
   */
  public function setIpWhitelisted($ipWhitelisted)
  {
    $this->ipWhitelisted = $ipWhitelisted;
  }
  /**
   * @return bool
   */
  public function getIpWhitelisted()
  {
    return $this->ipWhitelisted;
  }
  /**
   * Output only. Indicates a user with super administrator privileges. The
   * `isAdmin` property can only be edited in the [Make a user an administrator]
   * (https://developers.google.com/workspace/admin/directory/v1/guides/manage-
   * users.html#make_admin) operation ( [makeAdmin](https://developers.google.co
   * m/workspace/admin/directory/v1/reference/users/makeAdmin.html) method). If
   * edited in the user [insert](https://developers.google.com/workspace/admin/d
   * irectory/v1/reference/users/insert.html) or [update](https://developers.goo
   * gle.com/workspace/admin/directory/v1/reference/users/update.html) methods,
   * the edit is ignored by the API service.
   *
   * @param bool $isAdmin
   */
  public function setIsAdmin($isAdmin)
  {
    $this->isAdmin = $isAdmin;
  }
  /**
   * @return bool
   */
  public function getIsAdmin()
  {
    return $this->isAdmin;
  }
  /**
   * Output only. Indicates if the user is a delegated administrator. Delegated
   * administrators are supported by the API but cannot create or undelete
   * users, or make users administrators. These requests are ignored by the API
   * service. Roles and privileges for administrators are assigned using the
   * [Admin console](https://support.google.com/a/answer/33325).
   *
   * @param bool $isDelegatedAdmin
   */
  public function setIsDelegatedAdmin($isDelegatedAdmin)
  {
    $this->isDelegatedAdmin = $isDelegatedAdmin;
  }
  /**
   * @return bool
   */
  public function getIsDelegatedAdmin()
  {
    return $this->isDelegatedAdmin;
  }
  /**
   * Output only. Is 2-step verification enforced (Read-only)
   *
   * @param bool $isEnforcedIn2Sv
   */
  public function setIsEnforcedIn2Sv($isEnforcedIn2Sv)
  {
    $this->isEnforcedIn2Sv = $isEnforcedIn2Sv;
  }
  /**
   * @return bool
   */
  public function getIsEnforcedIn2Sv()
  {
    return $this->isEnforcedIn2Sv;
  }
  /**
   * Output only. Is enrolled in 2-step verification (Read-only)
   *
   * @param bool $isEnrolledIn2Sv
   */
  public function setIsEnrolledIn2Sv($isEnrolledIn2Sv)
  {
    $this->isEnrolledIn2Sv = $isEnrolledIn2Sv;
  }
  /**
   * @return bool
   */
  public function getIsEnrolledIn2Sv()
  {
    return $this->isEnrolledIn2Sv;
  }
  /**
   * Immutable. Indicates if the inserted user is a guest.
   *
   * @param bool $isGuestUser
   */
  public function setIsGuestUser($isGuestUser)
  {
    $this->isGuestUser = $isGuestUser;
  }
  /**
   * @return bool
   */
  public function getIsGuestUser()
  {
    return $this->isGuestUser;
  }
  /**
   * Output only. Indicates if the user's Google mailbox is created. This
   * property is only applicable if the user has been assigned a Gmail license.
   *
   * @param bool $isMailboxSetup
   */
  public function setIsMailboxSetup($isMailboxSetup)
  {
    $this->isMailboxSetup = $isMailboxSetup;
  }
  /**
   * @return bool
   */
  public function getIsMailboxSetup()
  {
    return $this->isMailboxSetup;
  }
  /**
   * The list of the user's keywords. The maximum allowed data size for this
   * field is 1KB.
   *
   * @param array $keywords
   */
  public function setKeywords($keywords)
  {
    $this->keywords = $keywords;
  }
  /**
   * @return array
   */
  public function getKeywords()
  {
    return $this->keywords;
  }
  /**
   * Output only. The type of the API resource. For Users resources, the value
   * is `admin#directory#user`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The user's languages. The maximum allowed data size for this field is 1KB.
   *
   * @param array $languages
   */
  public function setLanguages($languages)
  {
    $this->languages = $languages;
  }
  /**
   * @return array
   */
  public function getLanguages()
  {
    return $this->languages;
  }
  /**
   * User's last login time. (Read-only)
   *
   * @param string $lastLoginTime
   */
  public function setLastLoginTime($lastLoginTime)
  {
    $this->lastLoginTime = $lastLoginTime;
  }
  /**
   * @return string
   */
  public function getLastLoginTime()
  {
    return $this->lastLoginTime;
  }
  /**
   * The user's locations. The maximum allowed data size for this field is 10KB.
   *
   * @param array $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return array
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Holds the given and family names of the user, and the read-only `fullName`
   * value. The maximum number of characters in the `givenName` and in the
   * `familyName` values is 60. In addition, name values support unicode/UTF-8
   * characters, and can contain spaces, letters (a-z), numbers (0-9), dashes
   * (-), forward slashes (/), and periods (.). For more information about
   * character usage rules, see the [administration help
   * center](https://support.google.com/a/answer/9193374). Maximum allowed data
   * size for this field is 1KB.
   *
   * @param UserName $name
   */
  public function setName(UserName $name)
  {
    $this->name = $name;
  }
  /**
   * @return UserName
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The list of the user's non-editable alias email addresses.
   * These are typically outside the account's primary domain or sub-domain.
   *
   * @param string[] $nonEditableAliases
   */
  public function setNonEditableAliases($nonEditableAliases)
  {
    $this->nonEditableAliases = $nonEditableAliases;
  }
  /**
   * @return string[]
   */
  public function getNonEditableAliases()
  {
    return $this->nonEditableAliases;
  }
  /**
   * Notes for the user.
   *
   * @param array $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return array
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * The full path of the parent organization associated with the user. If the
   * parent organization is the top-level, it is represented as a forward slash
   * (`/`).
   *
   * @param string $orgUnitPath
   */
  public function setOrgUnitPath($orgUnitPath)
  {
    $this->orgUnitPath = $orgUnitPath;
  }
  /**
   * @return string
   */
  public function getOrgUnitPath()
  {
    return $this->orgUnitPath;
  }
  /**
   * The list of organizations the user belongs to. The maximum allowed data
   * size for this field is 10KB.
   *
   * @param array $organizations
   */
  public function setOrganizations($organizations)
  {
    $this->organizations = $organizations;
  }
  /**
   * @return array
   */
  public function getOrganizations()
  {
    return $this->organizations;
  }
  /**
   * User's password
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * The list of the user's phone numbers. The maximum allowed data size for
   * this field is 1KB.
   *
   * @param array $phones
   */
  public function setPhones($phones)
  {
    $this->phones = $phones;
  }
  /**
   * @return array
   */
  public function getPhones()
  {
    return $this->phones;
  }
  /**
   * The list of [POSIX](https://www.opengroup.org/austin/papers/posix_faq.html)
   * account information for the user.
   *
   * @param array $posixAccounts
   */
  public function setPosixAccounts($posixAccounts)
  {
    $this->posixAccounts = $posixAccounts;
  }
  /**
   * @return array
   */
  public function getPosixAccounts()
  {
    return $this->posixAccounts;
  }
  /**
   * The user's primary email address. This property is required in a request to
   * create a user account. The `primaryEmail` must be unique and cannot be an
   * alias of another user.
   *
   * @param string $primaryEmail
   */
  public function setPrimaryEmail($primaryEmail)
  {
    $this->primaryEmail = $primaryEmail;
  }
  /**
   * @return string
   */
  public function getPrimaryEmail()
  {
    return $this->primaryEmail;
  }
  /**
   * Recovery email of the user.
   *
   * @param string $recoveryEmail
   */
  public function setRecoveryEmail($recoveryEmail)
  {
    $this->recoveryEmail = $recoveryEmail;
  }
  /**
   * @return string
   */
  public function getRecoveryEmail()
  {
    return $this->recoveryEmail;
  }
  /**
   * Recovery phone of the user. The phone number must be in the E.164 format,
   * starting with the plus sign (+). Example: *+16506661212*.
   *
   * @param string $recoveryPhone
   */
  public function setRecoveryPhone($recoveryPhone)
  {
    $this->recoveryPhone = $recoveryPhone;
  }
  /**
   * @return string
   */
  public function getRecoveryPhone()
  {
    return $this->recoveryPhone;
  }
  /**
   * The list of the user's relationships to other users. The maximum allowed
   * data size for this field is 2KB.
   *
   * @param array $relations
   */
  public function setRelations($relations)
  {
    $this->relations = $relations;
  }
  /**
   * @return array
   */
  public function getRelations()
  {
    return $this->relations;
  }
  /**
   * A list of SSH public keys.
   *
   * @param array $sshPublicKeys
   */
  public function setSshPublicKeys($sshPublicKeys)
  {
    $this->sshPublicKeys = $sshPublicKeys;
  }
  /**
   * @return array
   */
  public function getSshPublicKeys()
  {
    return $this->sshPublicKeys;
  }
  /**
   * Indicates if user is suspended.
   *
   * @param bool $suspended
   */
  public function setSuspended($suspended)
  {
    $this->suspended = $suspended;
  }
  /**
   * @return bool
   */
  public function getSuspended()
  {
    return $this->suspended;
  }
  /**
   * Output only. Has the reason a user account is suspended either by the
   * administrator or by Google at the time of suspension. The property is
   * returned only if the `suspended` property is `true`.
   *
   * @param string $suspensionReason
   */
  public function setSuspensionReason($suspensionReason)
  {
    $this->suspensionReason = $suspensionReason;
  }
  /**
   * @return string
   */
  public function getSuspensionReason()
  {
    return $this->suspensionReason;
  }
  /**
   * Output only. ETag of the user's photo (Read-only)
   *
   * @param string $thumbnailPhotoEtag
   */
  public function setThumbnailPhotoEtag($thumbnailPhotoEtag)
  {
    $this->thumbnailPhotoEtag = $thumbnailPhotoEtag;
  }
  /**
   * @return string
   */
  public function getThumbnailPhotoEtag()
  {
    return $this->thumbnailPhotoEtag;
  }
  /**
   * Output only. The URL of the user's profile photo. The URL might be
   * temporary or private.
   *
   * @param string $thumbnailPhotoUrl
   */
  public function setThumbnailPhotoUrl($thumbnailPhotoUrl)
  {
    $this->thumbnailPhotoUrl = $thumbnailPhotoUrl;
  }
  /**
   * @return string
   */
  public function getThumbnailPhotoUrl()
  {
    return $this->thumbnailPhotoUrl;
  }
  /**
   * The user's websites. The maximum allowed data size for this field is 2KB.
   *
   * @param array $websites
   */
  public function setWebsites($websites)
  {
    $this->websites = $websites;
  }
  /**
   * @return array
   */
  public function getWebsites()
  {
    return $this->websites;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_Directory_User');
