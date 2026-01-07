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

class InterconnectDiagnosticsLinkOpticalPower extends \Google\Model
{
  /**
   * The value has crossed above the high alarm threshold.
   */
  public const STATE_HIGH_ALARM = 'HIGH_ALARM';
  /**
   * The value of the current optical power has crossed above the high warning
   * threshold.
   */
  public const STATE_HIGH_WARNING = 'HIGH_WARNING';
  /**
   * The value of the current optical power has crossed below the low alarm
   * threshold.
   */
  public const STATE_LOW_ALARM = 'LOW_ALARM';
  /**
   * The value of the current optical power has crossed below the low warning
   * threshold.
   */
  public const STATE_LOW_WARNING = 'LOW_WARNING';
  /**
   * The value of the current optical power has not crossed a warning threshold.
   */
  public const STATE_OK = 'OK';
  /**
   * The status of the current value when compared to the warning and alarm
   * levels for the receiving or transmitting transceiver. Possible states
   * include:             - OK: The value has not crossed a warning threshold.
   * - LOW_WARNING: The value has crossed below the low     warning threshold.
   * - HIGH_WARNING: The value has     crossed above the high warning threshold.
   * - LOW_ALARM: The value has crossed below the low alarm     threshold.     -
   * HIGH_ALARM: The value has crossed above the high alarm     threshold.
   *
   * @var string
   */
  public $state;
  /**
   * Value of the current receiving or transmitting optical power, read in dBm.
   * Take a known good optical value, give it a 10% margin and trigger warnings
   * relative to that value. In general, a -7dBm warning and a -11dBm alarm are
   * good optical value estimates for most links.
   *
   * @var float
   */
  public $value;

  /**
   * The status of the current value when compared to the warning and alarm
   * levels for the receiving or transmitting transceiver. Possible states
   * include:             - OK: The value has not crossed a warning threshold.
   * - LOW_WARNING: The value has crossed below the low     warning threshold.
   * - HIGH_WARNING: The value has     crossed above the high warning threshold.
   * - LOW_ALARM: The value has crossed below the low alarm     threshold.     -
   * HIGH_ALARM: The value has crossed above the high alarm     threshold.
   *
   * Accepted values: HIGH_ALARM, HIGH_WARNING, LOW_ALARM, LOW_WARNING, OK
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Value of the current receiving or transmitting optical power, read in dBm.
   * Take a known good optical value, give it a 10% margin and trigger warnings
   * relative to that value. In general, a -7dBm warning and a -11dBm alarm are
   * good optical value estimates for most links.
   *
   * @param float $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectDiagnosticsLinkOpticalPower::class, 'Google_Service_Compute_InterconnectDiagnosticsLinkOpticalPower');
