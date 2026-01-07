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

namespace Google\Service\CCAIPlatform;

class Quota extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_INSTANCE_SIZE_UNSPECIFIED = 'INSTANCE_SIZE_UNSPECIFIED';
  /**
   * Instance Size STANDARD_SMALL.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_STANDARD_SMALL = 'STANDARD_SMALL';
  /**
   * Instance Size STANDARD_MEDIUM.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_STANDARD_MEDIUM = 'STANDARD_MEDIUM';
  /**
   * Instance Size STANDARD_LARGE.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_STANDARD_LARGE = 'STANDARD_LARGE';
  /**
   * Instance Size STANDARD_XLARGE.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_STANDARD_XLARGE = 'STANDARD_XLARGE';
  /**
   * Instance Size STANDARD_2XLARGE.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_STANDARD_2XLARGE = 'STANDARD_2XLARGE';
  /**
   * Instance Size STANDARD_3XLARGE.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_STANDARD_3XLARGE = 'STANDARD_3XLARGE';
  /**
   * Instance Size MULTIREGION_SMALL
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_MULTIREGION_SMALL = 'MULTIREGION_SMALL';
  /**
   * Instance Size MULTIREGION_MEDIUM
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_MULTIREGION_MEDIUM = 'MULTIREGION_MEDIUM';
  /**
   * Instance Size MULTIREGION_LARGE
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_MULTIREGION_LARGE = 'MULTIREGION_LARGE';
  /**
   * Instance Size MULTIREGION_XLARGE
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_MULTIREGION_XLARGE = 'MULTIREGION_XLARGE';
  /**
   * Instance Size MULTIREGION_2XLARGE.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_MULTIREGION_2XLARGE = 'MULTIREGION_2XLARGE';
  /**
   * Instance Size MULTIREGION_3XLARGE.
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_MULTIREGION_3XLARGE = 'MULTIREGION_3XLARGE';
  /**
   * Instance Size DEV_SMALL
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_DEV_SMALL = 'DEV_SMALL';
  /**
   * Instance Size SANDBOX_SMALL
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_SANDBOX_SMALL = 'SANDBOX_SMALL';
  /**
   * Instance Size TRIAL_SMALL
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_TRIAL_SMALL = 'TRIAL_SMALL';
  /**
   * Instance Size TIME_LIMITED_TRIAL_SMALL
   */
  public const CONTACT_CENTER_INSTANCE_SIZE_TIME_LIMITED_TRIAL_SMALL = 'TIME_LIMITED_TRIAL_SMALL';
  /**
   * Reflects the count limit of contact centers on a billing account.
   *
   * @var int
   */
  public $contactCenterCountLimit;
  /**
   * Reflects the count sum of contact centers on a billing account.
   *
   * @var int
   */
  public $contactCenterCountSum;
  /**
   * Contact center instance type.
   *
   * @var string
   */
  public $contactCenterInstanceSize;

  /**
   * Reflects the count limit of contact centers on a billing account.
   *
   * @param int $contactCenterCountLimit
   */
  public function setContactCenterCountLimit($contactCenterCountLimit)
  {
    $this->contactCenterCountLimit = $contactCenterCountLimit;
  }
  /**
   * @return int
   */
  public function getContactCenterCountLimit()
  {
    return $this->contactCenterCountLimit;
  }
  /**
   * Reflects the count sum of contact centers on a billing account.
   *
   * @param int $contactCenterCountSum
   */
  public function setContactCenterCountSum($contactCenterCountSum)
  {
    $this->contactCenterCountSum = $contactCenterCountSum;
  }
  /**
   * @return int
   */
  public function getContactCenterCountSum()
  {
    return $this->contactCenterCountSum;
  }
  /**
   * Contact center instance type.
   *
   * Accepted values: INSTANCE_SIZE_UNSPECIFIED, STANDARD_SMALL,
   * STANDARD_MEDIUM, STANDARD_LARGE, STANDARD_XLARGE, STANDARD_2XLARGE,
   * STANDARD_3XLARGE, MULTIREGION_SMALL, MULTIREGION_MEDIUM, MULTIREGION_LARGE,
   * MULTIREGION_XLARGE, MULTIREGION_2XLARGE, MULTIREGION_3XLARGE, DEV_SMALL,
   * SANDBOX_SMALL, TRIAL_SMALL, TIME_LIMITED_TRIAL_SMALL
   *
   * @param self::CONTACT_CENTER_INSTANCE_SIZE_* $contactCenterInstanceSize
   */
  public function setContactCenterInstanceSize($contactCenterInstanceSize)
  {
    $this->contactCenterInstanceSize = $contactCenterInstanceSize;
  }
  /**
   * @return self::CONTACT_CENTER_INSTANCE_SIZE_*
   */
  public function getContactCenterInstanceSize()
  {
    return $this->contactCenterInstanceSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Quota::class, 'Google_Service_CCAIPlatform_Quota');
