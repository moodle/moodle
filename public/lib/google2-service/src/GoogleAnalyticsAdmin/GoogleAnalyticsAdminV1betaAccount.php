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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccount extends \Google\Model
{
  /**
   * Output only. Time when this account was originally created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Indicates whether this Account is soft-deleted or not. Deleted
   * accounts are excluded from List results unless specifically requested.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Required. Human-readable display name for this account.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The URI for a Google Marketing Platform organization resource.
   * Only set when this account is connected to a GMP organization. Format:
   * marketingplatformadmin.googleapis.com/organizations/{org_id}
   *
   * @var string
   */
  public $gmpOrganization;
  /**
   * Output only. Resource name of this account. Format: accounts/{account}
   * Example: "accounts/100"
   *
   * @var string
   */
  public $name;
  /**
   * Country of business. Must be a Unicode CLDR region code.
   *
   * @var string
   */
  public $regionCode;
  /**
   * Output only. Time when account payload fields were last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when this account was originally created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Indicates whether this Account is soft-deleted or not. Deleted
   * accounts are excluded from List results unless specifically requested.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Required. Human-readable display name for this account.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The URI for a Google Marketing Platform organization resource.
   * Only set when this account is connected to a GMP organization. Format:
   * marketingplatformadmin.googleapis.com/organizations/{org_id}
   *
   * @param string $gmpOrganization
   */
  public function setGmpOrganization($gmpOrganization)
  {
    $this->gmpOrganization = $gmpOrganization;
  }
  /**
   * @return string
   */
  public function getGmpOrganization()
  {
    return $this->gmpOrganization;
  }
  /**
   * Output only. Resource name of this account. Format: accounts/{account}
   * Example: "accounts/100"
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
   * Country of business. Must be a Unicode CLDR region code.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * Output only. Time when account payload fields were last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccount::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccount');
