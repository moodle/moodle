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

namespace Google\Service\Container;

class ConfidentialNodes extends \Google\Model
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
   * AMD Secure Encrypted Virtualization - Secure Nested Paging.
   */
  public const CONFIDENTIAL_INSTANCE_TYPE_SEV_SNP = 'SEV_SNP';
  /**
   * Intel Trust Domain eXtension.
   */
  public const CONFIDENTIAL_INSTANCE_TYPE_TDX = 'TDX';
  /**
   * Defines the type of technology used by the confidential node.
   *
   * @var string
   */
  public $confidentialInstanceType;
  /**
   * Whether Confidential Nodes feature is enabled.
   *
   * @var bool
   */
  public $enabled;

  /**
   * Defines the type of technology used by the confidential node.
   *
   * Accepted values: CONFIDENTIAL_INSTANCE_TYPE_UNSPECIFIED, SEV, SEV_SNP, TDX
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
  /**
   * Whether Confidential Nodes feature is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfidentialNodes::class, 'Google_Service_Container_ConfidentialNodes');
