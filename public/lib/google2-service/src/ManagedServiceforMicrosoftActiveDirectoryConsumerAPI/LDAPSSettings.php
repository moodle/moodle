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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class LDAPSSettings extends \Google\Model
{
  /**
   * Not Set
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The LDAPS setting is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The LDAPS setting is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The LDAPS setting is not applied correctly.
   */
  public const STATE_FAILED = 'FAILED';
  protected $certificateType = Certificate::class;
  protected $certificateDataType = '';
  /**
   * Input only. The password used to encrypt the uploaded PFX certificate.
   *
   * @var string
   */
  public $certificatePassword;
  /**
   * Input only. The uploaded PKCS12-formatted certificate to configure LDAPS
   * with. It will enable the domain controllers in this domain to accept LDAPS
   * connections (either LDAP over SSL/TLS or the StartTLS operation). A valid
   * certificate chain must form a valid x.509 certificate chain (or be
   * comprised of a single self-signed certificate. It must be encrypted with
   * either: 1) PBES2 + PBKDF2 + AES256 encryption and SHA256 PRF; or 2)
   * pbeWithSHA1And3-KeyTripleDES-CBC Private key must be included for the leaf
   * / single self-signed certificate. Note: For a fqdn your-example-domain.com,
   * the wildcard fqdn is *.your-example-domain.com. Specifically the leaf
   * certificate must have: - Either a blank subject or a subject with CN
   * matching the wildcard fqdn. - Exactly two SANs - the fqdn and wildcard
   * fqdn. - Encipherment and digital key signature key usages. - Server
   * authentication extended key usage (OID=1.3.6.1.5.5.7.3.1) - Private key
   * must be in one of the following formats: RSA, ECDSA, ED25519. - Private key
   * must have appropriate key length: 2048 for RSA, 256 for ECDSA - Signature
   * algorithm of the leaf certificate cannot be MD2, MD5 or SHA1.
   *
   * @var string
   */
  public $certificatePfx;
  /**
   * The resource name of the LDAPS settings. Uses the form:
   * `projects/{project}/locations/{location}/domains/{domain}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of this LDAPS settings.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Last update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The certificate used to configure LDAPS. Certificates can be
   * chained with a maximum length of 15.
   *
   * @param Certificate $certificate
   */
  public function setCertificate(Certificate $certificate)
  {
    $this->certificate = $certificate;
  }
  /**
   * @return Certificate
   */
  public function getCertificate()
  {
    return $this->certificate;
  }
  /**
   * Input only. The password used to encrypt the uploaded PFX certificate.
   *
   * @param string $certificatePassword
   */
  public function setCertificatePassword($certificatePassword)
  {
    $this->certificatePassword = $certificatePassword;
  }
  /**
   * @return string
   */
  public function getCertificatePassword()
  {
    return $this->certificatePassword;
  }
  /**
   * Input only. The uploaded PKCS12-formatted certificate to configure LDAPS
   * with. It will enable the domain controllers in this domain to accept LDAPS
   * connections (either LDAP over SSL/TLS or the StartTLS operation). A valid
   * certificate chain must form a valid x.509 certificate chain (or be
   * comprised of a single self-signed certificate. It must be encrypted with
   * either: 1) PBES2 + PBKDF2 + AES256 encryption and SHA256 PRF; or 2)
   * pbeWithSHA1And3-KeyTripleDES-CBC Private key must be included for the leaf
   * / single self-signed certificate. Note: For a fqdn your-example-domain.com,
   * the wildcard fqdn is *.your-example-domain.com. Specifically the leaf
   * certificate must have: - Either a blank subject or a subject with CN
   * matching the wildcard fqdn. - Exactly two SANs - the fqdn and wildcard
   * fqdn. - Encipherment and digital key signature key usages. - Server
   * authentication extended key usage (OID=1.3.6.1.5.5.7.3.1) - Private key
   * must be in one of the following formats: RSA, ECDSA, ED25519. - Private key
   * must have appropriate key length: 2048 for RSA, 256 for ECDSA - Signature
   * algorithm of the leaf certificate cannot be MD2, MD5 or SHA1.
   *
   * @param string $certificatePfx
   */
  public function setCertificatePfx($certificatePfx)
  {
    $this->certificatePfx = $certificatePfx;
  }
  /**
   * @return string
   */
  public function getCertificatePfx()
  {
    return $this->certificatePfx;
  }
  /**
   * The resource name of the LDAPS settings. Uses the form:
   * `projects/{project}/locations/{location}/domains/{domain}`.
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
  /**
   * Output only. The current state of this LDAPS settings.
   *
   * Accepted values: STATE_UNSPECIFIED, UPDATING, ACTIVE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Last update time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LDAPSSettings::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_LDAPSSettings');
