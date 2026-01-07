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

namespace Google\Service\DataFusion;

class Version extends \Google\Collection
{
  /**
   * Version does not have availability yet
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Version is under development and not considered stable
   */
  public const TYPE_TYPE_PREVIEW = 'TYPE_PREVIEW';
  /**
   * Version is available for public use
   */
  public const TYPE_TYPE_GENERAL_AVAILABILITY = 'TYPE_GENERAL_AVAILABILITY';
  /**
   * Version is no longer supported.
   */
  public const TYPE_TYPE_DEPRECATED = 'TYPE_DEPRECATED';
  protected $collection_key = 'availableFeatures';
  /**
   * Represents a list of available feature names for a given version.
   *
   * @var string[]
   */
  public $availableFeatures;
  /**
   * Whether this is currently the default version for Cloud Data Fusion
   *
   * @var bool
   */
  public $defaultVersion;
  /**
   * Type represents the release availability of the version
   *
   * @var string
   */
  public $type;
  /**
   * The version number of the Data Fusion instance, such as '6.0.1.0'.
   *
   * @var string
   */
  public $versionNumber;

  /**
   * Represents a list of available feature names for a given version.
   *
   * @param string[] $availableFeatures
   */
  public function setAvailableFeatures($availableFeatures)
  {
    $this->availableFeatures = $availableFeatures;
  }
  /**
   * @return string[]
   */
  public function getAvailableFeatures()
  {
    return $this->availableFeatures;
  }
  /**
   * Whether this is currently the default version for Cloud Data Fusion
   *
   * @param bool $defaultVersion
   */
  public function setDefaultVersion($defaultVersion)
  {
    $this->defaultVersion = $defaultVersion;
  }
  /**
   * @return bool
   */
  public function getDefaultVersion()
  {
    return $this->defaultVersion;
  }
  /**
   * Type represents the release availability of the version
   *
   * Accepted values: TYPE_UNSPECIFIED, TYPE_PREVIEW, TYPE_GENERAL_AVAILABILITY,
   * TYPE_DEPRECATED
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
  /**
   * The version number of the Data Fusion instance, such as '6.0.1.0'.
   *
   * @param string $versionNumber
   */
  public function setVersionNumber($versionNumber)
  {
    $this->versionNumber = $versionNumber;
  }
  /**
   * @return string
   */
  public function getVersionNumber()
  {
    return $this->versionNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Version::class, 'Google_Service_DataFusion_Version');
