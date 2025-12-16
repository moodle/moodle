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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1PartialArg extends \Google\Model
{
  /**
   * Null value.
   */
  public const NULL_VALUE_NULL_VALUE = 'NULL_VALUE';
  /**
   * Optional. Represents a boolean value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Required. A JSON Path (RFC 9535) to the argument being streamed.
   * https://datatracker.ietf.org/doc/html/rfc9535. e.g. "$.foo.bar[0].data".
   *
   * @var string
   */
  public $jsonPath;
  /**
   * Optional. Represents a null value.
   *
   * @var string
   */
  public $nullValue;
  /**
   * Optional. Represents a double value.
   *
   * @var 
   */
  public $numberValue;
  /**
   * Optional. Represents a string value.
   *
   * @var string
   */
  public $stringValue;
  /**
   * Optional. Whether this is not the last part of the same json_path. If true,
   * another PartialArg message for the current json_path is expected to follow.
   *
   * @var bool
   */
  public $willContinue;

  /**
   * Optional. Represents a boolean value.
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
   * Required. A JSON Path (RFC 9535) to the argument being streamed.
   * https://datatracker.ietf.org/doc/html/rfc9535. e.g. "$.foo.bar[0].data".
   *
   * @param string $jsonPath
   */
  public function setJsonPath($jsonPath)
  {
    $this->jsonPath = $jsonPath;
  }
  /**
   * @return string
   */
  public function getJsonPath()
  {
    return $this->jsonPath;
  }
  /**
   * Optional. Represents a null value.
   *
   * Accepted values: NULL_VALUE
   *
   * @param self::NULL_VALUE_* $nullValue
   */
  public function setNullValue($nullValue)
  {
    $this->nullValue = $nullValue;
  }
  /**
   * @return self::NULL_VALUE_*
   */
  public function getNullValue()
  {
    return $this->nullValue;
  }
  public function setNumberValue($numberValue)
  {
    $this->numberValue = $numberValue;
  }
  public function getNumberValue()
  {
    return $this->numberValue;
  }
  /**
   * Optional. Represents a string value.
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
   * Optional. Whether this is not the last part of the same json_path. If true,
   * another PartialArg message for the current json_path is expected to follow.
   *
   * @param bool $willContinue
   */
  public function setWillContinue($willContinue)
  {
    $this->willContinue = $willContinue;
  }
  /**
   * @return bool
   */
  public function getWillContinue()
  {
    return $this->willContinue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PartialArg::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PartialArg');
