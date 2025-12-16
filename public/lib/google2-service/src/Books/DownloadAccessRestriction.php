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

class DownloadAccessRestriction extends \Google\Model
{
  /**
   * If restricted, whether access is granted for this (user, device, volume).
   *
   * @var bool
   */
  public $deviceAllowed;
  /**
   * If restricted, the number of content download licenses already acquired
   * (including the requesting client, if licensed).
   *
   * @var int
   */
  public $downloadsAcquired;
  /**
   * If deviceAllowed, whether access was just acquired with this request.
   *
   * @var bool
   */
  public $justAcquired;
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * If restricted, the maximum number of content download licenses for this
   * volume.
   *
   * @var int
   */
  public $maxDownloadDevices;
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
   * Error/warning reason code. Additional codes may be added in the future. 0
   * OK 100 ACCESS_DENIED_PUBLISHER_LIMIT 101 ACCESS_DENIED_LIMIT 200
   * WARNING_USED_LAST_ACCESS
   *
   * @var string
   */
  public $reasonCode;
  /**
   * Whether this volume has any download access restrictions.
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
   * Identifies the volume for which this entry applies.
   *
   * @var string
   */
  public $volumeId;

  /**
   * If restricted, whether access is granted for this (user, device, volume).
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
   * If restricted, the number of content download licenses already acquired
   * (including the requesting client, if licensed).
   *
   * @param int $downloadsAcquired
   */
  public function setDownloadsAcquired($downloadsAcquired)
  {
    $this->downloadsAcquired = $downloadsAcquired;
  }
  /**
   * @return int
   */
  public function getDownloadsAcquired()
  {
    return $this->downloadsAcquired;
  }
  /**
   * If deviceAllowed, whether access was just acquired with this request.
   *
   * @param bool $justAcquired
   */
  public function setJustAcquired($justAcquired)
  {
    $this->justAcquired = $justAcquired;
  }
  /**
   * @return bool
   */
  public function getJustAcquired()
  {
    return $this->justAcquired;
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
   * If restricted, the maximum number of content download licenses for this
   * volume.
   *
   * @param int $maxDownloadDevices
   */
  public function setMaxDownloadDevices($maxDownloadDevices)
  {
    $this->maxDownloadDevices = $maxDownloadDevices;
  }
  /**
   * @return int
   */
  public function getMaxDownloadDevices()
  {
    return $this->maxDownloadDevices;
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
   * Error/warning reason code. Additional codes may be added in the future. 0
   * OK 100 ACCESS_DENIED_PUBLISHER_LIMIT 101 ACCESS_DENIED_LIMIT 200
   * WARNING_USED_LAST_ACCESS
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
   * Whether this volume has any download access restrictions.
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
class_alias(DownloadAccessRestriction::class, 'Google_Service_Books_DownloadAccessRestriction');
