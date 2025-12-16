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

namespace Google\Service\AndroidManagement;

class KeyedAppState extends \Google\Model
{
  /**
   * Unspecified severity level.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Information severity level.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Error severity level. This should only be set for genuine error conditions
   * that a management organization needs to take action to fix.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * The creation time of the app state on the device.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optionally, a machine-readable value to be read by the EMM. For example,
   * setting values that the admin can choose to query against in the EMM
   * console (e.g. “notify me if the battery_warning data < 10”).
   *
   * @var string
   */
  public $data;
  /**
   * The key for the app state. Acts as a point of reference for what the app is
   * providing state for. For example, when providing managed configuration
   * feedback, this key could be the managed configuration key.
   *
   * @var string
   */
  public $key;
  /**
   * The time the app state was most recently updated.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * Optionally, a free-form message string to explain the app state. If the
   * state was triggered by a particular value (e.g. a managed configuration
   * value), it should be included in the message.
   *
   * @var string
   */
  public $message;
  /**
   * The severity of the app state.
   *
   * @var string
   */
  public $severity;

  /**
   * The creation time of the app state on the device.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optionally, a machine-readable value to be read by the EMM. For example,
   * setting values that the admin can choose to query against in the EMM
   * console (e.g. “notify me if the battery_warning data < 10”).
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
   * The key for the app state. Acts as a point of reference for what the app is
   * providing state for. For example, when providing managed configuration
   * feedback, this key could be the managed configuration key.
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
   * The time the app state was most recently updated.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * Optionally, a free-form message string to explain the app state. If the
   * state was triggered by a particular value (e.g. a managed configuration
   * value), it should be included in the message.
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
   * The severity of the app state.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFO, ERROR
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyedAppState::class, 'Google_Service_AndroidManagement_KeyedAppState');
