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

class Certificate extends \Google\Collection
{
  protected $collection_key = 'subjectAlternativeName';
  /**
   * The certificate expire time.
   *
   * @var string
   */
  public $expireTime;
  protected $issuingCertificateType = Certificate::class;
  protected $issuingCertificateDataType = '';
  /**
   * The certificate subject.
   *
   * @var string
   */
  public $subject;
  /**
   * The additional hostnames for the domain.
   *
   * @var string[]
   */
  public $subjectAlternativeName;
  /**
   * The certificate thumbprint which uniquely identifies the certificate.
   *
   * @var string
   */
  public $thumbprint;

  /**
   * The certificate expire time.
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
   * The issuer of this certificate.
   *
   * @param Certificate $issuingCertificate
   */
  public function setIssuingCertificate(Certificate $issuingCertificate)
  {
    $this->issuingCertificate = $issuingCertificate;
  }
  /**
   * @return Certificate
   */
  public function getIssuingCertificate()
  {
    return $this->issuingCertificate;
  }
  /**
   * The certificate subject.
   *
   * @param string $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }
  /**
   * The additional hostnames for the domain.
   *
   * @param string[] $subjectAlternativeName
   */
  public function setSubjectAlternativeName($subjectAlternativeName)
  {
    $this->subjectAlternativeName = $subjectAlternativeName;
  }
  /**
   * @return string[]
   */
  public function getSubjectAlternativeName()
  {
    return $this->subjectAlternativeName;
  }
  /**
   * The certificate thumbprint which uniquely identifies the certificate.
   *
   * @param string $thumbprint
   */
  public function setThumbprint($thumbprint)
  {
    $this->thumbprint = $thumbprint;
  }
  /**
   * @return string
   */
  public function getThumbprint()
  {
    return $this->thumbprint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Certificate::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_Certificate');
