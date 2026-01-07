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

namespace Google\Service\MigrationCenterAPI;

class PostgreSqlSetting extends \Google\Model
{
  /**
   * Required. The setting boolean value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Required. The setting int value.
   *
   * @var string
   */
  public $intValue;
  /**
   * Required. The setting real value.
   *
   * @var float
   */
  public $realValue;
  /**
   * Required. The setting name.
   *
   * @var string
   */
  public $setting;
  /**
   * Required. The setting source.
   *
   * @var string
   */
  public $source;
  /**
   * Required. The setting string value. Notice that enum values are stored as
   * strings.
   *
   * @var string
   */
  public $stringValue;
  /**
   * Optional. The setting unit.
   *
   * @var string
   */
  public $unit;

  /**
   * Required. The setting boolean value.
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
   * Required. The setting int value.
   *
   * @param string $intValue
   */
  public function setIntValue($intValue)
  {
    $this->intValue = $intValue;
  }
  /**
   * @return string
   */
  public function getIntValue()
  {
    return $this->intValue;
  }
  /**
   * Required. The setting real value.
   *
   * @param float $realValue
   */
  public function setRealValue($realValue)
  {
    $this->realValue = $realValue;
  }
  /**
   * @return float
   */
  public function getRealValue()
  {
    return $this->realValue;
  }
  /**
   * Required. The setting name.
   *
   * @param string $setting
   */
  public function setSetting($setting)
  {
    $this->setting = $setting;
  }
  /**
   * @return string
   */
  public function getSetting()
  {
    return $this->setting;
  }
  /**
   * Required. The setting source.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Required. The setting string value. Notice that enum values are stored as
   * strings.
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
  /**
   * Optional. The setting unit.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgreSqlSetting::class, 'Google_Service_MigrationCenterAPI_PostgreSqlSetting');
