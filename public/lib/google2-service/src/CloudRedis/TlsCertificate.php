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

namespace Google\Service\CloudRedis;

class TlsCertificate extends \Google\Model
{
  /**
   * PEM representation.
   *
   * @var string
   */
  public $cert;
  /**
   * Output only. The time when the certificate was created in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2020-05-18T00:00:00.094Z`.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the certificate expires in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2020-05-18T00:00:00.094Z`.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Serial number, as extracted from the certificate.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Sha1 Fingerprint of the certificate.
   *
   * @var string
   */
  public $sha1Fingerprint;

  /**
   * PEM representation.
   *
   * @param string $cert
   */
  public function setCert($cert)
  {
    $this->cert = $cert;
  }
  /**
   * @return string
   */
  public function getCert()
  {
    return $this->cert;
  }
  /**
   * Output only. The time when the certificate was created in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2020-05-18T00:00:00.094Z`.
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
   * Output only. The time when the certificate expires in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2020-05-18T00:00:00.094Z`.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Serial number, as extracted from the certificate.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * Sha1 Fingerprint of the certificate.
   *
   * @param string $sha1Fingerprint
   */
  public function setSha1Fingerprint($sha1Fingerprint)
  {
    $this->sha1Fingerprint = $sha1Fingerprint;
  }
  /**
   * @return string
   */
  public function getSha1Fingerprint()
  {
    return $this->sha1Fingerprint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TlsCertificate::class, 'Google_Service_CloudRedis_TlsCertificate');
