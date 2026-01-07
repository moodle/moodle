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

namespace Google\Service\Dataflow;

class DisplayData extends \Google\Model
{
  /**
   * Contains value if the data is of a boolean type.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Contains value if the data is of duration type.
   *
   * @var string
   */
  public $durationValue;
  /**
   * Contains value if the data is of float type.
   *
   * @var float
   */
  public $floatValue;
  /**
   * Contains value if the data is of int64 type.
   *
   * @var string
   */
  public $int64Value;
  /**
   * Contains value if the data is of java class type.
   *
   * @var string
   */
  public $javaClassValue;
  /**
   * The key identifying the display data. This is intended to be used as a
   * label for the display data when viewed in a dax monitoring system.
   *
   * @var string
   */
  public $key;
  /**
   * An optional label to display in a dax UI for the element.
   *
   * @var string
   */
  public $label;
  /**
   * The namespace for the key. This is usually a class name or programming
   * language namespace (i.e. python module) which defines the display data.
   * This allows a dax monitoring system to specially handle the data and
   * perform custom rendering.
   *
   * @var string
   */
  public $namespace;
  /**
   * A possible additional shorter value to display. For example a
   * java_class_name_value of com.mypackage.MyDoFn will be stored with MyDoFn as
   * the short_str_value and com.mypackage.MyDoFn as the java_class_name value.
   * short_str_value can be displayed and java_class_name_value will be
   * displayed as a tooltip.
   *
   * @var string
   */
  public $shortStrValue;
  /**
   * Contains value if the data is of string type.
   *
   * @var string
   */
  public $strValue;
  /**
   * Contains value if the data is of timestamp type.
   *
   * @var string
   */
  public $timestampValue;
  /**
   * An optional full URL.
   *
   * @var string
   */
  public $url;

  /**
   * Contains value if the data is of a boolean type.
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
   * Contains value if the data is of duration type.
   *
   * @param string $durationValue
   */
  public function setDurationValue($durationValue)
  {
    $this->durationValue = $durationValue;
  }
  /**
   * @return string
   */
  public function getDurationValue()
  {
    return $this->durationValue;
  }
  /**
   * Contains value if the data is of float type.
   *
   * @param float $floatValue
   */
  public function setFloatValue($floatValue)
  {
    $this->floatValue = $floatValue;
  }
  /**
   * @return float
   */
  public function getFloatValue()
  {
    return $this->floatValue;
  }
  /**
   * Contains value if the data is of int64 type.
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
   * Contains value if the data is of java class type.
   *
   * @param string $javaClassValue
   */
  public function setJavaClassValue($javaClassValue)
  {
    $this->javaClassValue = $javaClassValue;
  }
  /**
   * @return string
   */
  public function getJavaClassValue()
  {
    return $this->javaClassValue;
  }
  /**
   * The key identifying the display data. This is intended to be used as a
   * label for the display data when viewed in a dax monitoring system.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * An optional label to display in a dax UI for the element.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The namespace for the key. This is usually a class name or programming
   * language namespace (i.e. python module) which defines the display data.
   * This allows a dax monitoring system to specially handle the data and
   * perform custom rendering.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
  /**
   * A possible additional shorter value to display. For example a
   * java_class_name_value of com.mypackage.MyDoFn will be stored with MyDoFn as
   * the short_str_value and com.mypackage.MyDoFn as the java_class_name value.
   * short_str_value can be displayed and java_class_name_value will be
   * displayed as a tooltip.
   *
   * @param string $shortStrValue
   */
  public function setShortStrValue($shortStrValue)
  {
    $this->shortStrValue = $shortStrValue;
  }
  /**
   * @return string
   */
  public function getShortStrValue()
  {
    return $this->shortStrValue;
  }
  /**
   * Contains value if the data is of string type.
   *
   * @param string $strValue
   */
  public function setStrValue($strValue)
  {
    $this->strValue = $strValue;
  }
  /**
   * @return string
   */
  public function getStrValue()
  {
    return $this->strValue;
  }
  /**
   * Contains value if the data is of timestamp type.
   *
   * @param string $timestampValue
   */
  public function setTimestampValue($timestampValue)
  {
    $this->timestampValue = $timestampValue;
  }
  /**
   * @return string
   */
  public function getTimestampValue()
  {
    return $this->timestampValue;
  }
  /**
   * An optional full URL.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DisplayData::class, 'Google_Service_Dataflow_DisplayData');
