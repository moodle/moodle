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

class GoogleCloudIntegrationsV1alphaValueType extends \Google\Model
{
  protected $booleanArrayType = GoogleCloudIntegrationsV1alphaBooleanParameterArray::class;
  protected $booleanArrayDataType = '';
  /**
   * Boolean.
   *
   * @var bool
   */
  public $booleanValue;
  protected $doubleArrayType = GoogleCloudIntegrationsV1alphaDoubleParameterArray::class;
  protected $doubleArrayDataType = '';
  /**
   * Double Number.
   *
   * @var 
   */
  public $doubleValue;
  protected $intArrayType = GoogleCloudIntegrationsV1alphaIntParameterArray::class;
  protected $intArrayDataType = '';
  /**
   * Integer.
   *
   * @var string
   */
  public $intValue;
  /**
   * Json.
   *
   * @var string
   */
  public $jsonValue;
  protected $stringArrayType = GoogleCloudIntegrationsV1alphaStringParameterArray::class;
  protected $stringArrayDataType = '';
  /**
   * String.
   *
   * @var string
   */
  public $stringValue;

  /**
   * Boolean Array.
   *
   * @param GoogleCloudIntegrationsV1alphaBooleanParameterArray $booleanArray
   */
  public function setBooleanArray(GoogleCloudIntegrationsV1alphaBooleanParameterArray $booleanArray)
  {
    $this->booleanArray = $booleanArray;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaBooleanParameterArray
   */
  public function getBooleanArray()
  {
    return $this->booleanArray;
  }
  /**
   * Boolean.
   *
   * @param bool $booleanValue
   */
  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }
  /**
   * @return bool
   */
  public function getBooleanValue()
  {
    return $this->booleanValue;
  }
  /**
   * Double Number Array.
   *
   * @param GoogleCloudIntegrationsV1alphaDoubleParameterArray $doubleArray
   */
  public function setDoubleArray(GoogleCloudIntegrationsV1alphaDoubleParameterArray $doubleArray)
  {
    $this->doubleArray = $doubleArray;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaDoubleParameterArray
   */
  public function getDoubleArray()
  {
    return $this->doubleArray;
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
   * Integer Array.
   *
   * @param GoogleCloudIntegrationsV1alphaIntParameterArray $intArray
   */
  public function setIntArray(GoogleCloudIntegrationsV1alphaIntParameterArray $intArray)
  {
    $this->intArray = $intArray;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntParameterArray
   */
  public function getIntArray()
  {
    return $this->intArray;
  }
  /**
   * Integer.
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
   * Json.
   *
   * @param string $jsonValue
   */
  public function setJsonValue($jsonValue)
  {
    $this->jsonValue = $jsonValue;
  }
  /**
   * @return string
   */
  public function getJsonValue()
  {
    return $this->jsonValue;
  }
  /**
   * String Array.
   *
   * @param GoogleCloudIntegrationsV1alphaStringParameterArray $stringArray
   */
  public function setStringArray(GoogleCloudIntegrationsV1alphaStringParameterArray $stringArray)
  {
    $this->stringArray = $stringArray;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaStringParameterArray
   */
  public function getStringArray()
  {
    return $this->stringArray;
  }
  /**
   * String.
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
class_alias(GoogleCloudIntegrationsV1alphaValueType::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaValueType');
