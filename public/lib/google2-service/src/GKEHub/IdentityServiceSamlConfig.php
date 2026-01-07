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

namespace Google\Service\GKEHub;

class IdentityServiceSamlConfig extends \Google\Collection
{
  protected $collection_key = 'identityProviderCertificates';
  /**
   * Optional. The mapping of additional user attributes like nickname, birthday
   * and address etc.. `key` is the name of this additional attribute. `value`
   * is a string presenting as CEL(common expression language, go/cel) used for
   * getting the value from the resources. Take nickname as an example, in this
   * case, `key` is "attribute.nickname" and `value` is "assertion.nickname".
   *
   * @var string[]
   */
  public $attributeMapping;
  /**
   * Optional. Prefix to prepend to group name.
   *
   * @var string
   */
  public $groupPrefix;
  /**
   * Optional. The SAML attribute to read groups from. This value is expected to
   * be a string and will be passed along as-is (with the option of being
   * prefixed by the `group_prefix`).
   *
   * @var string
   */
  public $groupsAttribute;
  /**
   * Required. The list of IdP certificates to validate the SAML response
   * against.
   *
   * @var string[]
   */
  public $identityProviderCertificates;
  /**
   * Required. The entity ID of the SAML IdP.
   *
   * @var string
   */
  public $identityProviderId;
  /**
   * Required. The URI where the SAML IdP exposes the SSO service.
   *
   * @var string
   */
  public $identityProviderSsoUri;
  /**
   * Optional. The SAML attribute to read username from. If unspecified, the
   * username will be read from the NameID element of the assertion in SAML
   * response. This value is expected to be a string and will be passed along
   * as-is (with the option of being prefixed by the `user_prefix`).
   *
   * @var string
   */
  public $userAttribute;
  /**
   * Optional. Prefix to prepend to user name.
   *
   * @var string
   */
  public $userPrefix;

  /**
   * Optional. The mapping of additional user attributes like nickname, birthday
   * and address etc.. `key` is the name of this additional attribute. `value`
   * is a string presenting as CEL(common expression language, go/cel) used for
   * getting the value from the resources. Take nickname as an example, in this
   * case, `key` is "attribute.nickname" and `value` is "assertion.nickname".
   *
   * @param string[] $attributeMapping
   */
  public function setAttributeMapping($attributeMapping)
  {
    $this->attributeMapping = $attributeMapping;
  }
  /**
   * @return string[]
   */
  public function getAttributeMapping()
  {
    return $this->attributeMapping;
  }
  /**
   * Optional. Prefix to prepend to group name.
   *
   * @param string $groupPrefix
   */
  public function setGroupPrefix($groupPrefix)
  {
    $this->groupPrefix = $groupPrefix;
  }
  /**
   * @return string
   */
  public function getGroupPrefix()
  {
    return $this->groupPrefix;
  }
  /**
   * Optional. The SAML attribute to read groups from. This value is expected to
   * be a string and will be passed along as-is (with the option of being
   * prefixed by the `group_prefix`).
   *
   * @param string $groupsAttribute
   */
  public function setGroupsAttribute($groupsAttribute)
  {
    $this->groupsAttribute = $groupsAttribute;
  }
  /**
   * @return string
   */
  public function getGroupsAttribute()
  {
    return $this->groupsAttribute;
  }
  /**
   * Required. The list of IdP certificates to validate the SAML response
   * against.
   *
   * @param string[] $identityProviderCertificates
   */
  public function setIdentityProviderCertificates($identityProviderCertificates)
  {
    $this->identityProviderCertificates = $identityProviderCertificates;
  }
  /**
   * @return string[]
   */
  public function getIdentityProviderCertificates()
  {
    return $this->identityProviderCertificates;
  }
  /**
   * Required. The entity ID of the SAML IdP.
   *
   * @param string $identityProviderId
   */
  public function setIdentityProviderId($identityProviderId)
  {
    $this->identityProviderId = $identityProviderId;
  }
  /**
   * @return string
   */
  public function getIdentityProviderId()
  {
    return $this->identityProviderId;
  }
  /**
   * Required. The URI where the SAML IdP exposes the SSO service.
   *
   * @param string $identityProviderSsoUri
   */
  public function setIdentityProviderSsoUri($identityProviderSsoUri)
  {
    $this->identityProviderSsoUri = $identityProviderSsoUri;
  }
  /**
   * @return string
   */
  public function getIdentityProviderSsoUri()
  {
    return $this->identityProviderSsoUri;
  }
  /**
   * Optional. The SAML attribute to read username from. If unspecified, the
   * username will be read from the NameID element of the assertion in SAML
   * response. This value is expected to be a string and will be passed along
   * as-is (with the option of being prefixed by the `user_prefix`).
   *
   * @param string $userAttribute
   */
  public function setUserAttribute($userAttribute)
  {
    $this->userAttribute = $userAttribute;
  }
  /**
   * @return string
   */
  public function getUserAttribute()
  {
    return $this->userAttribute;
  }
  /**
   * Optional. Prefix to prepend to user name.
   *
   * @param string $userPrefix
   */
  public function setUserPrefix($userPrefix)
  {
    $this->userPrefix = $userPrefix;
  }
  /**
   * @return string
   */
  public function getUserPrefix()
  {
    return $this->userPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceSamlConfig::class, 'Google_Service_GKEHub_IdentityServiceSamlConfig');
