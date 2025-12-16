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

namespace Google\Service\VMMigrationService;

class AppliedLicense extends \Google\Model
{
  /**
   * Unspecified license for the OS.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * No license available for the OS.
   */
  public const TYPE_NONE = 'NONE';
  /**
   * The license type is Pay As You Go license type.
   */
  public const TYPE_PAYG = 'PAYG';
  /**
   * The license type is Bring Your Own License type.
   */
  public const TYPE_BYOL = 'BYOL';
  /**
   * The OS license returned from the adaptation module's report.
   *
   * @var string
   */
  public $osLicense;
  /**
   * The license type that was used in OS adaptation.
   *
   * @var string
   */
  public $type;

  /**
   * The OS license returned from the adaptation module's report.
   *
   * @param string $osLicense
   */
  public function setOsLicense($osLicense)
  {
    $this->osLicense = $osLicense;
  }
  /**
   * @return string
   */
  public function getOsLicense()
  {
    return $this->osLicense;
  }
  /**
   * The license type that was used in OS adaptation.
   *
   * Accepted values: TYPE_UNSPECIFIED, NONE, PAYG, BYOL
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
class_alias(AppliedLicense::class, 'Google_Service_VMMigrationService_AppliedLicense');
