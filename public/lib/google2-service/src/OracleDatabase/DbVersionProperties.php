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

namespace Google\Service\OracleDatabase;

class DbVersionProperties extends \Google\Model
{
  /**
   * Output only. True if this version of the Oracle Database software is the
   * latest version for a release.
   *
   * @var bool
   */
  public $isLatestForMajorVersion;
  /**
   * Output only. True if this version of the Oracle Database software is the
   * preview version.
   *
   * @var bool
   */
  public $isPreviewDbVersion;
  /**
   * Output only. True if this version of the Oracle Database software is
   * supported for Upgrade.
   *
   * @var bool
   */
  public $isUpgradeSupported;
  /**
   * Output only. True if this version of the Oracle Database software supports
   * pluggable databases.
   *
   * @var bool
   */
  public $supportsPdb;
  /**
   * Output only. A valid Oracle Database version.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. True if this version of the Oracle Database software is the
   * latest version for a release.
   *
   * @param bool $isLatestForMajorVersion
   */
  public function setIsLatestForMajorVersion($isLatestForMajorVersion)
  {
    $this->isLatestForMajorVersion = $isLatestForMajorVersion;
  }
  /**
   * @return bool
   */
  public function getIsLatestForMajorVersion()
  {
    return $this->isLatestForMajorVersion;
  }
  /**
   * Output only. True if this version of the Oracle Database software is the
   * preview version.
   *
   * @param bool $isPreviewDbVersion
   */
  public function setIsPreviewDbVersion($isPreviewDbVersion)
  {
    $this->isPreviewDbVersion = $isPreviewDbVersion;
  }
  /**
   * @return bool
   */
  public function getIsPreviewDbVersion()
  {
    return $this->isPreviewDbVersion;
  }
  /**
   * Output only. True if this version of the Oracle Database software is
   * supported for Upgrade.
   *
   * @param bool $isUpgradeSupported
   */
  public function setIsUpgradeSupported($isUpgradeSupported)
  {
    $this->isUpgradeSupported = $isUpgradeSupported;
  }
  /**
   * @return bool
   */
  public function getIsUpgradeSupported()
  {
    return $this->isUpgradeSupported;
  }
  /**
   * Output only. True if this version of the Oracle Database software supports
   * pluggable databases.
   *
   * @param bool $supportsPdb
   */
  public function setSupportsPdb($supportsPdb)
  {
    $this->supportsPdb = $supportsPdb;
  }
  /**
   * @return bool
   */
  public function getSupportsPdb()
  {
    return $this->supportsPdb;
  }
  /**
   * Output only. A valid Oracle Database version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbVersionProperties::class, 'Google_Service_OracleDatabase_DbVersionProperties');
