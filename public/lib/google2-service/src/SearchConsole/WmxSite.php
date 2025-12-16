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

namespace Google\Service\SearchConsole;

class WmxSite extends \Google\Model
{
  public const PERMISSION_LEVEL_SITE_PERMISSION_LEVEL_UNSPECIFIED = 'SITE_PERMISSION_LEVEL_UNSPECIFIED';
  /**
   * Owner has complete access to the site.
   */
  public const PERMISSION_LEVEL_SITE_OWNER = 'SITE_OWNER';
  /**
   * Full users can access all data, and perform most of the operations.
   */
  public const PERMISSION_LEVEL_SITE_FULL_USER = 'SITE_FULL_USER';
  /**
   * Restricted users can access most of the data, and perform some operations.
   */
  public const PERMISSION_LEVEL_SITE_RESTRICTED_USER = 'SITE_RESTRICTED_USER';
  /**
   * Unverified user has no access to site's data.
   */
  public const PERMISSION_LEVEL_SITE_UNVERIFIED_USER = 'SITE_UNVERIFIED_USER';
  /**
   * The user's permission level for the site.
   *
   * @var string
   */
  public $permissionLevel;
  /**
   * The URL of the site.
   *
   * @var string
   */
  public $siteUrl;

  /**
   * The user's permission level for the site.
   *
   * Accepted values: SITE_PERMISSION_LEVEL_UNSPECIFIED, SITE_OWNER,
   * SITE_FULL_USER, SITE_RESTRICTED_USER, SITE_UNVERIFIED_USER
   *
   * @param self::PERMISSION_LEVEL_* $permissionLevel
   */
  public function setPermissionLevel($permissionLevel)
  {
    $this->permissionLevel = $permissionLevel;
  }
  /**
   * @return self::PERMISSION_LEVEL_*
   */
  public function getPermissionLevel()
  {
    return $this->permissionLevel;
  }
  /**
   * The URL of the site.
   *
   * @param string $siteUrl
   */
  public function setSiteUrl($siteUrl)
  {
    $this->siteUrl = $siteUrl;
  }
  /**
   * @return string
   */
  public function getSiteUrl()
  {
    return $this->siteUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WmxSite::class, 'Google_Service_SearchConsole_WmxSite');
