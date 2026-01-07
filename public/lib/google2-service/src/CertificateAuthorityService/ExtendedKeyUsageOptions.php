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

namespace Google\Service\CertificateAuthorityService;

class ExtendedKeyUsageOptions extends \Google\Model
{
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.2. Officially described as "TLS WWW
   * client authentication", though regularly used for non-WWW TLS.
   *
   * @var bool
   */
  public $clientAuth;
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.3. Officially described as "Signing of
   * downloadable executable code client authentication".
   *
   * @var bool
   */
  public $codeSigning;
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.4. Officially described as "Email
   * protection".
   *
   * @var bool
   */
  public $emailProtection;
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.9. Officially described as "Signing OCSP
   * responses".
   *
   * @var bool
   */
  public $ocspSigning;
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.1. Officially described as "TLS WWW
   * server authentication", though regularly used for non-WWW TLS.
   *
   * @var bool
   */
  public $serverAuth;
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.8. Officially described as "Binding the
   * hash of an object to a time".
   *
   * @var bool
   */
  public $timeStamping;

  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.2. Officially described as "TLS WWW
   * client authentication", though regularly used for non-WWW TLS.
   *
   * @param bool $clientAuth
   */
  public function setClientAuth($clientAuth)
  {
    $this->clientAuth = $clientAuth;
  }
  /**
   * @return bool
   */
  public function getClientAuth()
  {
    return $this->clientAuth;
  }
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.3. Officially described as "Signing of
   * downloadable executable code client authentication".
   *
   * @param bool $codeSigning
   */
  public function setCodeSigning($codeSigning)
  {
    $this->codeSigning = $codeSigning;
  }
  /**
   * @return bool
   */
  public function getCodeSigning()
  {
    return $this->codeSigning;
  }
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.4. Officially described as "Email
   * protection".
   *
   * @param bool $emailProtection
   */
  public function setEmailProtection($emailProtection)
  {
    $this->emailProtection = $emailProtection;
  }
  /**
   * @return bool
   */
  public function getEmailProtection()
  {
    return $this->emailProtection;
  }
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.9. Officially described as "Signing OCSP
   * responses".
   *
   * @param bool $ocspSigning
   */
  public function setOcspSigning($ocspSigning)
  {
    $this->ocspSigning = $ocspSigning;
  }
  /**
   * @return bool
   */
  public function getOcspSigning()
  {
    return $this->ocspSigning;
  }
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.1. Officially described as "TLS WWW
   * server authentication", though regularly used for non-WWW TLS.
   *
   * @param bool $serverAuth
   */
  public function setServerAuth($serverAuth)
  {
    $this->serverAuth = $serverAuth;
  }
  /**
   * @return bool
   */
  public function getServerAuth()
  {
    return $this->serverAuth;
  }
  /**
   * Corresponds to OID 1.3.6.1.5.5.7.3.8. Officially described as "Binding the
   * hash of an object to a time".
   *
   * @param bool $timeStamping
   */
  public function setTimeStamping($timeStamping)
  {
    $this->timeStamping = $timeStamping;
  }
  /**
   * @return bool
   */
  public function getTimeStamping()
  {
    return $this->timeStamping;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtendedKeyUsageOptions::class, 'Google_Service_CertificateAuthorityService_ExtendedKeyUsageOptions');
