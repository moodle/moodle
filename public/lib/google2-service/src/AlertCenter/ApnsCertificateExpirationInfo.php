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

namespace Google\Service\AlertCenter;

class ApnsCertificateExpirationInfo extends \Google\Model
{
  /**
   * The Apple ID used to create the certificate. It may be blank if admins
   * didn't enter it.
   *
   * @var string
   */
  public $appleId;
  /**
   * The expiration date of the APNS certificate.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The UID of the certificate.
   *
   * @var string
   */
  public $uid;

  /**
   * The Apple ID used to create the certificate. It may be blank if admins
   * didn't enter it.
   *
   * @param string $appleId
   */
  public function setAppleId($appleId)
  {
    $this->appleId = $appleId;
  }
  /**
   * @return string
   */
  public function getAppleId()
  {
    return $this->appleId;
  }
  /**
   * The expiration date of the APNS certificate.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The UID of the certificate.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApnsCertificateExpirationInfo::class, 'Google_Service_AlertCenter_ApnsCertificateExpirationInfo');
