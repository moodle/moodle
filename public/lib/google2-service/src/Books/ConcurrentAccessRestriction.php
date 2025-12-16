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

namespace Google\Service\Books;

class ConcurrentAccessRestriction extends \Google\Model
{
  /**
   * Whether access is granted for this (user, device, volume).
   *
   * @var bool
   */
  public $deviceAllowed;
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * The maximum number of concurrent access licenses for this volume.
   *
   * @var int
   */
  public $maxConcurrentDevices;
  /**
   * Error/warning message.
   *
   * @var string
   */
  public $message;
  /**
   * Client nonce for verification. Download access and client-validation only.
   *
   * @var string
   */
  public $nonce;
  /**
   * Error/warning reason code.
   *
   * @var string
   */
  public $reasonCode;
  /**
   * Whether this volume has any concurrent access restrictions.
   *
   * @var bool
   */
  public $restricted;
  /**
   * Response signature.
   *
   * @var string
   */
  public $signature;
  /**
   * Client app identifier for verification. Download access and client-
   * validation only.
   *
   * @var string
   */
  public $source;
  /**
   * Time in seconds for license auto-expiration.
   *
   * @var int
   */
  public $timeWindowSeconds;
  /**
   * Identifies the volume for which this entry applies.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Whether access is granted for this (user, device, volume).
   *
   * @param bool $deviceAllowed
   */
  public function setDeviceAllowed($deviceAllowed)
  {
    $this->deviceAllowed = $deviceAllowed;
  }
  /**
   * @return bool
   */
  public function getDeviceAllowed()
  {
    return $this->deviceAllowed;
  }
  /**
   * Resource type.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The maximum number of concurrent access licenses for this volume.
   *
   * @param int $maxConcurrentDevices
   */
  public function setMaxConcurrentDevices($maxConcurrentDevices)
  {
    $this->maxConcurrentDevices = $maxConcurrentDevices;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentDevices()
  {
    return $this->maxConcurrentDevices;
  }
  /**
   * Error/warning message.
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
   * Client nonce for verification. Download access and client-validation only.
   *
   * @param string $nonce
   */
  public function setNonce($nonce)
  {
    $this->nonce = $nonce;
  }
  /**
   * @return string
   */
  public function getNonce()
  {
    return $this->nonce;
  }
  /**
   * Error/warning reason code.
   *
   * @param string $reasonCode
   */
  public function setReasonCode($reasonCode)
  {
    $this->reasonCode = $reasonCode;
  }
  /**
   * @return string
   */
  public function getReasonCode()
  {
    return $this->reasonCode;
  }
  /**
   * Whether this volume has any concurrent access restrictions.
   *
   * @param bool $restricted
   */
  public function setRestricted($restricted)
  {
    $this->restricted = $restricted;
  }
  /**
   * @return bool
   */
  public function getRestricted()
  {
    return $this->restricted;
  }
  /**
   * Response signature.
   *
   * @param string $signature
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
  /**
   * Client app identifier for verification. Download access and client-
   * validation only.
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
   * Time in seconds for license auto-expiration.
   *
   * @param int $timeWindowSeconds
   */
  public function setTimeWindowSeconds($timeWindowSeconds)
  {
    $this->timeWindowSeconds = $timeWindowSeconds;
  }
  /**
   * @return int
   */
  public function getTimeWindowSeconds()
  {
    return $this->timeWindowSeconds;
  }
  /**
   * Identifies the volume for which this entry applies.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConcurrentAccessRestriction::class, 'Google_Service_Books_ConcurrentAccessRestriction');
