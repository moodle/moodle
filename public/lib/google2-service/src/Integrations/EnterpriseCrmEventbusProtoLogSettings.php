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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoLogSettings extends \Google\Model
{
  public const SEED_PERIOD_SEED_PERIOD_UNSPECIFIED = 'SEED_PERIOD_UNSPECIFIED';
  /**
   * Sanitized values remain constant for the day of the event.
   */
  public const SEED_PERIOD_DAY = 'DAY';
  /**
   * Sanitized values remain constant for the week of the event; may cross month
   * boundaries.
   */
  public const SEED_PERIOD_WEEK = 'WEEK';
  /**
   * Sanitized values remain constant for the month of the event.
   */
  public const SEED_PERIOD_MONTH = 'MONTH';
  public const SEED_SCOPE_SEED_SCOPE_UNSPECIFIED = 'SEED_SCOPE_UNSPECIFIED';
  /**
   * Hash computations include the event name.
   */
  public const SEED_SCOPE_EVENT_NAME = 'EVENT_NAME';
  /**
   * Hash computations include a time period.
   */
  public const SEED_SCOPE_TIME_PERIOD = 'TIME_PERIOD';
  /**
   * Hash computations include the param name.
   */
  public const SEED_SCOPE_PARAM_NAME = 'PARAM_NAME';
  /**
   * The name of corresponding logging field of the event property. If omitted,
   * assumes the same name as the event property key.
   *
   * @var string
   */
  public $logFieldName;
  /**
   * @var string
   */
  public $seedPeriod;
  /**
   * @var string
   */
  public $seedScope;

  /**
   * The name of corresponding logging field of the event property. If omitted,
   * assumes the same name as the event property key.
   *
   * @param string $logFieldName
   */
  public function setLogFieldName($logFieldName)
  {
    $this->logFieldName = $logFieldName;
  }
  /**
   * @return string
   */
  public function getLogFieldName()
  {
    return $this->logFieldName;
  }
  /**
   * @param self::SEED_PERIOD_* $seedPeriod
   */
  public function setSeedPeriod($seedPeriod)
  {
    $this->seedPeriod = $seedPeriod;
  }
  /**
   * @return self::SEED_PERIOD_*
   */
  public function getSeedPeriod()
  {
    return $this->seedPeriod;
  }
  /**
   * @param self::SEED_SCOPE_* $seedScope
   */
  public function setSeedScope($seedScope)
  {
    $this->seedScope = $seedScope;
  }
  /**
   * @return self::SEED_SCOPE_*
   */
  public function getSeedScope()
  {
    return $this->seedScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoLogSettings::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoLogSettings');
