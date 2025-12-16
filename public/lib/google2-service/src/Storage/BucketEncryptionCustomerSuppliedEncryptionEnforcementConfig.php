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

namespace Google\Service\Storage;

class BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig extends \Google\Model
{
  /**
   * Creation of new objects with Customer-Supplied Encryption is not
   * restricted.
   */
  public const RESTRICTION_MODE_NotRestricted = 'NotRestricted';
  /**
   * Creation of new objects with Customer-Supplied Encryption is fully
   * restricted.
   */
  public const RESTRICTION_MODE_FullyRestricted = 'FullyRestricted';
  /**
   * Server-determined value that indicates the time from which configuration
   * was enforced and effective. This value is in RFC 3339 format.
   *
   * @var string
   */
  public $effectiveTime;
  /**
   * Restriction mode for Customer-Supplied Encryption Keys. Defaults to
   * NotRestricted.
   *
   * @var string
   */
  public $restrictionMode;

  /**
   * Server-determined value that indicates the time from which configuration
   * was enforced and effective. This value is in RFC 3339 format.
   *
   * @param string $effectiveTime
   */
  public function setEffectiveTime($effectiveTime)
  {
    $this->effectiveTime = $effectiveTime;
  }
  /**
   * @return string
   */
  public function getEffectiveTime()
  {
    return $this->effectiveTime;
  }
  /**
   * Restriction mode for Customer-Supplied Encryption Keys. Defaults to
   * NotRestricted.
   *
   * Accepted values: NotRestricted, FullyRestricted
   *
   * @param self::RESTRICTION_MODE_* $restrictionMode
   */
  public function setRestrictionMode($restrictionMode)
  {
    $this->restrictionMode = $restrictionMode;
  }
  /**
   * @return self::RESTRICTION_MODE_*
   */
  public function getRestrictionMode()
  {
    return $this->restrictionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig::class, 'Google_Service_Storage_BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig');
