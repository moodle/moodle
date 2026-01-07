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

namespace Google\Service\Reports;

class UsageReportParameters extends \Google\Collection
{
  protected $collection_key = 'msgValue';
  /**
   * Output only. Boolean value of the parameter.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * The RFC 3339 formatted value of the parameter, for example
   * 2010-10-28T10:26:35.000Z.
   *
   * @var string
   */
  public $datetimeValue;
  /**
   * Output only. Integer value of the parameter.
   *
   * @var string
   */
  public $intValue;
  /**
   * Output only. Nested message value of the parameter.
   *
   * @var array[]
   */
  public $msgValue;
  /**
   * The name of the parameter. For the User Usage Report parameter names, see
   * the User Usage parameters reference.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. String value of the parameter.
   *
   * @var string
   */
  public $stringValue;

  /**
   * Output only. Boolean value of the parameter.
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
   * The RFC 3339 formatted value of the parameter, for example
   * 2010-10-28T10:26:35.000Z.
   *
   * @param string $datetimeValue
   */
  public function setDatetimeValue($datetimeValue)
  {
    $this->datetimeValue = $datetimeValue;
  }
  /**
   * @return string
   */
  public function getDatetimeValue()
  {
    return $this->datetimeValue;
  }
  /**
   * Output only. Integer value of the parameter.
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
   * Output only. Nested message value of the parameter.
   *
   * @param array[] $msgValue
   */
  public function setMsgValue($msgValue)
  {
    $this->msgValue = $msgValue;
  }
  /**
   * @return array[]
   */
  public function getMsgValue()
  {
    return $this->msgValue;
  }
  /**
   * The name of the parameter. For the User Usage Report parameter names, see
   * the User Usage parameters reference.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. String value of the parameter.
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
class_alias(UsageReportParameters::class, 'Google_Service_Reports_UsageReportParameters');
