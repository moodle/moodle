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

namespace Google\Service\AIPlatformNotebooks;

class ConfidentialInstanceConfig extends \Google\Model
{
  /**
   * No type specified. Do not use this value.
   */
  public const CONFIDENTIAL_INSTANCE_TYPE_CONFIDENTIAL_INSTANCE_TYPE_UNSPECIFIED = 'CONFIDENTIAL_INSTANCE_TYPE_UNSPECIFIED';
  /**
   * AMD Secure Encrypted Virtualization.
   */
  public const CONFIDENTIAL_INSTANCE_TYPE_SEV = 'SEV';
  /**
   * Optional. Defines the type of technology used by the confidential instance.
   *
   * @var string
   */
  public $confidentialInstanceType;

  /**
   * Optional. Defines the type of technology used by the confidential instance.
   *
   * Accepted values: CONFIDENTIAL_INSTANCE_TYPE_UNSPECIFIED, SEV
   *
   * @param self::CONFIDENTIAL_INSTANCE_TYPE_* $confidentialInstanceType
   */
  public function setConfidentialInstanceType($confidentialInstanceType)
  {
    $this->confidentialInstanceType = $confidentialInstanceType;
  }
  /**
   * @return self::CONFIDENTIAL_INSTANCE_TYPE_*
   */
  public function getConfidentialInstanceType()
  {
    return $this->confidentialInstanceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfidentialInstanceConfig::class, 'Google_Service_AIPlatformNotebooks_ConfidentialInstanceConfig');
