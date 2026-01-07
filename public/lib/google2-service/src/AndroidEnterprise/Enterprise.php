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

namespace Google\Service\AndroidEnterprise;

class Enterprise extends \Google\Collection
{
  /**
   * This value is not used.
   */
  public const ENTERPRISE_TYPE_enterpriseTypeUnspecified = 'enterpriseTypeUnspecified';
  /**
   * The enterprise belongs to a managed Google domain.
   */
  public const ENTERPRISE_TYPE_managedGoogleDomain = 'managedGoogleDomain';
  /**
   * The enterprise is a managed Google Play Accounts enterprise.
   */
  public const ENTERPRISE_TYPE_managedGooglePlayAccountsEnterprise = 'managedGooglePlayAccountsEnterprise';
  /**
   * The managed Google domain type is not specified.
   */
  public const MANAGED_GOOGLE_DOMAIN_TYPE_managedGoogleDomainTypeUnspecified = 'managedGoogleDomainTypeUnspecified';
  /**
   * The managed Google domain is an email-verified team.
   */
  public const MANAGED_GOOGLE_DOMAIN_TYPE_typeTeam = 'typeTeam';
  /**
   * The managed Google domain is domain-verified.
   */
  public const MANAGED_GOOGLE_DOMAIN_TYPE_typeDomain = 'typeDomain';
  protected $collection_key = 'administrator';
  protected $administratorType = Administrator::class;
  protected $administratorDataType = 'array';
  /**
   * The type of the enterprise.
   *
   * @var string
   */
  public $enterpriseType;
  protected $googleAuthenticationSettingsType = GoogleAuthenticationSettings::class;
  protected $googleAuthenticationSettingsDataType = '';
  /**
   * The unique ID for the enterprise.
   *
   * @var string
   */
  public $id;
  /**
   * The type of managed Google domain
   *
   * @var string
   */
  public $managedGoogleDomainType;
  /**
   * The name of the enterprise, for example, "Example, Inc".
   *
   * @var string
   */
  public $name;
  /**
   * The enterprise's primary domain, such as "example.com".
   *
   * @var string
   */
  public $primaryDomain;

  /**
   * Admins of the enterprise. This is only supported for enterprises created
   * via the EMM-initiated flow.
   *
   * @param Administrator[] $administrator
   */
  public function setAdministrator($administrator)
  {
    $this->administrator = $administrator;
  }
  /**
   * @return Administrator[]
   */
  public function getAdministrator()
  {
    return $this->administrator;
  }
  /**
   * The type of the enterprise.
   *
   * Accepted values: enterpriseTypeUnspecified, managedGoogleDomain,
   * managedGooglePlayAccountsEnterprise
   *
   * @param self::ENTERPRISE_TYPE_* $enterpriseType
   */
  public function setEnterpriseType($enterpriseType)
  {
    $this->enterpriseType = $enterpriseType;
  }
  /**
   * @return self::ENTERPRISE_TYPE_*
   */
  public function getEnterpriseType()
  {
    return $this->enterpriseType;
  }
  /**
   * Output only. Settings for Google-provided user authentication.
   *
   * @param GoogleAuthenticationSettings $googleAuthenticationSettings
   */
  public function setGoogleAuthenticationSettings(GoogleAuthenticationSettings $googleAuthenticationSettings)
  {
    $this->googleAuthenticationSettings = $googleAuthenticationSettings;
  }
  /**
   * @return GoogleAuthenticationSettings
   */
  public function getGoogleAuthenticationSettings()
  {
    return $this->googleAuthenticationSettings;
  }
  /**
   * The unique ID for the enterprise.
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
   * The type of managed Google domain
   *
   * Accepted values: managedGoogleDomainTypeUnspecified, typeTeam, typeDomain
   *
   * @param self::MANAGED_GOOGLE_DOMAIN_TYPE_* $managedGoogleDomainType
   */
  public function setManagedGoogleDomainType($managedGoogleDomainType)
  {
    $this->managedGoogleDomainType = $managedGoogleDomainType;
  }
  /**
   * @return self::MANAGED_GOOGLE_DOMAIN_TYPE_*
   */
  public function getManagedGoogleDomainType()
  {
    return $this->managedGoogleDomainType;
  }
  /**
   * The name of the enterprise, for example, "Example, Inc".
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The enterprise's primary domain, such as "example.com".
   *
   * @param string $primaryDomain
   */
  public function setPrimaryDomain($primaryDomain)
  {
    $this->primaryDomain = $primaryDomain;
  }
  /**
   * @return string
   */
  public function getPrimaryDomain()
  {
    return $this->primaryDomain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Enterprise::class, 'Google_Service_AndroidEnterprise_Enterprise');
