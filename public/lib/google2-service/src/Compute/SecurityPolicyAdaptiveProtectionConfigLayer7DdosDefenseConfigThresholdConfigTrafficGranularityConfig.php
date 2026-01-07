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

namespace Google\Service\Compute;

class SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfigThresholdConfigTrafficGranularityConfig extends \Google\Model
{
  public const TYPE_HTTP_HEADER_HOST = 'HTTP_HEADER_HOST';
  public const TYPE_HTTP_PATH = 'HTTP_PATH';
  public const TYPE_UNSPECIFIED_TYPE = 'UNSPECIFIED_TYPE';
  /**
   * If enabled, traffic matching each unique value for the specified type
   * constitutes a separate traffic unit. It can only be set to true if `value`
   * is empty.
   *
   * @var bool
   */
  public $enableEachUniqueValue;
  /**
   * Type of this configuration.
   *
   * @var string
   */
  public $type;
  /**
   * Requests that match this value constitute a granular traffic unit.
   *
   * @var string
   */
  public $value;

  /**
   * If enabled, traffic matching each unique value for the specified type
   * constitutes a separate traffic unit. It can only be set to true if `value`
   * is empty.
   *
   * @param bool $enableEachUniqueValue
   */
  public function setEnableEachUniqueValue($enableEachUniqueValue)
  {
    $this->enableEachUniqueValue = $enableEachUniqueValue;
  }
  /**
   * @return bool
   */
  public function getEnableEachUniqueValue()
  {
    return $this->enableEachUniqueValue;
  }
  /**
   * Type of this configuration.
   *
   * Accepted values: HTTP_HEADER_HOST, HTTP_PATH, UNSPECIFIED_TYPE
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
   * Requests that match this value constitute a granular traffic unit.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfigThresholdConfigTrafficGranularityConfig::class, 'Google_Service_Compute_SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfigThresholdConfigTrafficGranularityConfig');
