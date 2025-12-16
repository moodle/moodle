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

namespace Google\Service\CloudOSLogin;

class SshPublicKey extends \Google\Model
{
  /**
   * An expiration time in microseconds since epoch.
   *
   * @var string
   */
  public $expirationTimeUsec;
  /**
   * Output only. The SHA-256 fingerprint of the SSH public key.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Required. Public key text in SSH format, defined by
   * [RFC4253](https://www.ietf.org/rfc/rfc4253.txt) section 6.6.
   *
   * @var string
   */
  public $key;
  /**
   * Output only. The canonical resource name.
   *
   * @var string
   */
  public $name;

  /**
   * An expiration time in microseconds since epoch.
   *
   * @param string $expirationTimeUsec
   */
  public function setExpirationTimeUsec($expirationTimeUsec)
  {
    $this->expirationTimeUsec = $expirationTimeUsec;
  }
  /**
   * @return string
   */
  public function getExpirationTimeUsec()
  {
    return $this->expirationTimeUsec;
  }
  /**
   * Output only. The SHA-256 fingerprint of the SSH public key.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Required. Public key text in SSH format, defined by
   * [RFC4253](https://www.ietf.org/rfc/rfc4253.txt) section 6.6.
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
   * Output only. The canonical resource name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SshPublicKey::class, 'Google_Service_CloudOSLogin_SshPublicKey');
