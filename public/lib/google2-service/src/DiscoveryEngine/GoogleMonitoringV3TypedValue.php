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

class GoogleMonitoringV3TypedValue extends \Google\Model
{
  /**
   * A Boolean value: `true` or `false`.
   *
   * @var bool
   */
  public $boolValue;
  protected $distributionValueType = GoogleApiDistribution::class;
  protected $distributionValueDataType = '';
  /**
   * A 64-bit double-precision floating-point number. Its magnitude is
   * approximately ±10±300 and it has 16 significant digits of precision.
   *
   * @var 
   */
  public $doubleValue;
  /**
   * A 64-bit integer. Its range is approximately ±9.2x1018.
   *
   * @var string
   */
  public $int64Value;
  /**
   * A variable-length string value.
   *
   * @var string
   */
  public $stringValue;

  /**
   * A Boolean value: `true` or `false`.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  /**
   * A distribution value.
   *
   * @param GoogleApiDistribution $distributionValue
   */
  public function setDistributionValue(GoogleApiDistribution $distributionValue)
  {
    $this->distributionValue = $distributionValue;
  }
  /**
   * @return GoogleApiDistribution
   */
  public function getDistributionValue()
  {
    return $this->distributionValue;
  }
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * A 64-bit integer. Its range is approximately ±9.2x1018.
   *
   * @param string $int64Value
   */
  public function setInt64Value($int64Value)
  {
    $this->int64Value = $int64Value;
  }
  /**
   * @return string
   */
  public function getInt64Value()
  {
    return $this->int64Value;
  }
  /**
   * A variable-length string value.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMonitoringV3TypedValue::class, 'Google_Service_DiscoveryEngine_GoogleMonitoringV3TypedValue');
