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

class IoCloudeventsV1CloudEventCloudEventAttributeValue extends \Google\Model
{
  /**
   * @var bool
   */
  public $ceBoolean;
  /**
   * @var string
   */
  public $ceBytes;
  /**
   * @var int
   */
  public $ceInteger;
  /**
   * @var string
   */
  public $ceString;
  /**
   * @var string
   */
  public $ceTimestamp;
  /**
   * @var string
   */
  public $ceUri;
  /**
   * @var string
   */
  public $ceUriRef;

  /**
   * @param bool
   */
  public function setCeBoolean($ceBoolean)
  {
    $this->ceBoolean = $ceBoolean;
  }
  /**
   * @return bool
   */
  public function getCeBoolean()
  {
    return $this->ceBoolean;
  }
  /**
   * @param string
   */
  public function setCeBytes($ceBytes)
  {
    $this->ceBytes = $ceBytes;
  }
  /**
   * @return string
   */
  public function getCeBytes()
  {
    return $this->ceBytes;
  }
  /**
   * @param int
   */
  public function setCeInteger($ceInteger)
  {
    $this->ceInteger = $ceInteger;
  }
  /**
   * @return int
   */
  public function getCeInteger()
  {
    return $this->ceInteger;
  }
  /**
   * @param string
   */
  public function setCeString($ceString)
  {
    $this->ceString = $ceString;
  }
  /**
   * @return string
   */
  public function getCeString()
  {
    return $this->ceString;
  }
  /**
   * @param string
   */
  public function setCeTimestamp($ceTimestamp)
  {
    $this->ceTimestamp = $ceTimestamp;
  }
  /**
   * @return string
   */
  public function getCeTimestamp()
  {
    return $this->ceTimestamp;
  }
  /**
   * @param string
   */
  public function setCeUri($ceUri)
  {
    $this->ceUri = $ceUri;
  }
  /**
   * @return string
   */
  public function getCeUri()
  {
    return $this->ceUri;
  }
  /**
   * @param string
   */
  public function setCeUriRef($ceUriRef)
  {
    $this->ceUriRef = $ceUriRef;
  }
  /**
   * @return string
   */
  public function getCeUriRef()
  {
    return $this->ceUriRef;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IoCloudeventsV1CloudEventCloudEventAttributeValue::class, 'Google_Service_Integrations_IoCloudeventsV1CloudEventCloudEventAttributeValue');
