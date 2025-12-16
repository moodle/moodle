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

namespace Google\Service\AndroidEnterprise;

class KeyedAppState extends \Google\Model
{
  public const SEVERITY_severityUnknown = 'severityUnknown';
  public const SEVERITY_severityInfo = 'severityInfo';
  public const SEVERITY_severityError = 'severityError';
  /**
   * Additional field intended for machine-readable data. For example, a number
   * or JSON object. To prevent XSS, we recommend removing any HTML from the
   * data before displaying it.
   *
   * @var string
   */
  public $data;
  /**
   * Key indicating what the app is providing a state for. The content of the
   * key is set by the app's developer. To prevent XSS, we recommend removing
   * any HTML from the key before displaying it. This field will always be
   * present.
   *
   * @var string
   */
  public $key;
  /**
   * Free-form, human-readable message describing the app state. For example, an
   * error message. To prevent XSS, we recommend removing any HTML from the
   * message before displaying it.
   *
   * @var string
   */
  public $message;
  /**
   * Severity of the app state. This field will always be present.
   *
   * @var string
   */
  public $severity;
  /**
   * Timestamp of when the app set the state in milliseconds since epoch. This
   * field will always be present.
   *
   * @var string
   */
  public $stateTimestampMillis;

  /**
   * Additional field intended for machine-readable data. For example, a number
   * or JSON object. To prevent XSS, we recommend removing any HTML from the
   * data before displaying it.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Key indicating what the app is providing a state for. The content of the
   * key is set by the app's developer. To prevent XSS, we recommend removing
   * any HTML from the key before displaying it. This field will always be
   * present.
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
   * Free-form, human-readable message describing the app state. For example, an
   * error message. To prevent XSS, we recommend removing any HTML from the
   * message before displaying it.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Severity of the app state. This field will always be present.
   *
   * Accepted values: severityUnknown, severityInfo, severityError
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Timestamp of when the app set the state in milliseconds since epoch. This
   * field will always be present.
   *
   * @param string $stateTimestampMillis
   */
  public function setStateTimestampMillis($stateTimestampMillis)
  {
    $this->stateTimestampMillis = $stateTimestampMillis;
  }
  /**
   * @return string
   */
  public function getStateTimestampMillis()
  {
    return $this->stateTimestampMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyedAppState::class, 'Google_Service_AndroidEnterprise_KeyedAppState');
