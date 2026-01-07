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

class CertificateConfig extends \Google\Model
{
  protected $publicKeyType = PublicKey::class;
  protected $publicKeyDataType = '';
  protected $subjectConfigType = SubjectConfig::class;
  protected $subjectConfigDataType = '';
  protected $subjectKeyIdType = CertificateConfigKeyId::class;
  protected $subjectKeyIdDataType = '';
  protected $x509ConfigType = X509Parameters::class;
  protected $x509ConfigDataType = '';

  /**
   * Optional. The public key that corresponds to this config. This is, for
   * example, used when issuing Certificates, but not when creating a self-
   * signed CertificateAuthority or CertificateAuthority CSR.
   *
   * @param PublicKey $publicKey
   */
  public function setPublicKey(PublicKey $publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return PublicKey
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * Required. Specifies some of the values in a certificate that are related to
   * the subject.
   *
   * @param SubjectConfig $subjectConfig
   */
  public function setSubjectConfig(SubjectConfig $subjectConfig)
  {
    $this->subjectConfig = $subjectConfig;
  }
  /**
   * @return SubjectConfig
   */
  public function getSubjectConfig()
  {
    return $this->subjectConfig;
  }
  /**
   * Optional. When specified this provides a custom SKI to be used in the
   * certificate. This should only be used to maintain a SKI of an existing CA
   * originally created outside CA service, which was not generated using method
   * (1) described in RFC 5280 section 4.2.1.2.
   *
   * @param CertificateConfigKeyId $subjectKeyId
   */
  public function setSubjectKeyId(CertificateConfigKeyId $subjectKeyId)
  {
    $this->subjectKeyId = $subjectKeyId;
  }
  /**
   * @return CertificateConfigKeyId
   */
  public function getSubjectKeyId()
  {
    return $this->subjectKeyId;
  }
  /**
   * Required. Describes how some of the technical X.509 fields in a certificate
   * should be populated.
   *
   * @param X509Parameters $x509Config
   */
  public function setX509Config(X509Parameters $x509Config)
  {
    $this->x509Config = $x509Config;
  }
  /**
   * @return X509Parameters
   */
  public function getX509Config()
  {
    return $this->x509Config;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateConfig::class, 'Google_Service_CertificateAuthorityService_CertificateConfig');
