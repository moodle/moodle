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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1WorkspaceConfig extends \Google\Model
{
  /**
   * Defaults to an unspecified Workspace type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Workspace Data Store contains Drive data
   */
  public const TYPE_GOOGLE_DRIVE = 'GOOGLE_DRIVE';
  /**
   * Workspace Data Store contains Mail data
   */
  public const TYPE_GOOGLE_MAIL = 'GOOGLE_MAIL';
  /**
   * Workspace Data Store contains Sites data
   */
  public const TYPE_GOOGLE_SITES = 'GOOGLE_SITES';
  /**
   * Workspace Data Store contains Calendar data
   */
  public const TYPE_GOOGLE_CALENDAR = 'GOOGLE_CALENDAR';
  /**
   * Workspace Data Store contains Chat data
   */
  public const TYPE_GOOGLE_CHAT = 'GOOGLE_CHAT';
  /**
   * Workspace Data Store contains Groups data
   */
  public const TYPE_GOOGLE_GROUPS = 'GOOGLE_GROUPS';
  /**
   * Workspace Data Store contains Keep data
   */
  public const TYPE_GOOGLE_KEEP = 'GOOGLE_KEEP';
  /**
   * Workspace Data Store contains People data
   */
  public const TYPE_GOOGLE_PEOPLE = 'GOOGLE_PEOPLE';
  /**
   * Obfuscated Dasher customer ID.
   *
   * @var string
   */
  public $dasherCustomerId;
  /**
   * Optional. The super admin email address for the workspace that will be used
   * for access token generation. For now we only use it for Native Google Drive
   * connector data ingestion.
   *
   * @var string
   */
  public $superAdminEmailAddress;
  /**
   * Optional. The super admin service account for the workspace that will be
   * used for access token generation. For now we only use it for Native Google
   * Drive connector data ingestion.
   *
   * @var string
   */
  public $superAdminServiceAccount;
  /**
   * The Google Workspace data source.
   *
   * @var string
   */
  public $type;

  /**
   * Obfuscated Dasher customer ID.
   *
   * @param string $dasherCustomerId
   */
  public function setDasherCustomerId($dasherCustomerId)
  {
    $this->dasherCustomerId = $dasherCustomerId;
  }
  /**
   * @return string
   */
  public function getDasherCustomerId()
  {
    return $this->dasherCustomerId;
  }
  /**
   * Optional. The super admin email address for the workspace that will be used
   * for access token generation. For now we only use it for Native Google Drive
   * connector data ingestion.
   *
   * @param string $superAdminEmailAddress
   */
  public function setSuperAdminEmailAddress($superAdminEmailAddress)
  {
    $this->superAdminEmailAddress = $superAdminEmailAddress;
  }
  /**
   * @return string
   */
  public function getSuperAdminEmailAddress()
  {
    return $this->superAdminEmailAddress;
  }
  /**
   * Optional. The super admin service account for the workspace that will be
   * used for access token generation. For now we only use it for Native Google
   * Drive connector data ingestion.
   *
   * @param string $superAdminServiceAccount
   */
  public function setSuperAdminServiceAccount($superAdminServiceAccount)
  {
    $this->superAdminServiceAccount = $superAdminServiceAccount;
  }
  /**
   * @return string
   */
  public function getSuperAdminServiceAccount()
  {
    return $this->superAdminServiceAccount;
  }
  /**
   * The Google Workspace data source.
   *
   * Accepted values: TYPE_UNSPECIFIED, GOOGLE_DRIVE, GOOGLE_MAIL, GOOGLE_SITES,
   * GOOGLE_CALENDAR, GOOGLE_CHAT, GOOGLE_GROUPS, GOOGLE_KEEP, GOOGLE_PEOPLE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WorkspaceConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WorkspaceConfig');
