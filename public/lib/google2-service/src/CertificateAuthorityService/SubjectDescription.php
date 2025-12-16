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

class SubjectDescription extends \Google\Model
{
  /**
   * The serial number encoded in lowercase hexadecimal.
   *
   * @var string
   */
  public $hexSerialNumber;
  /**
   * For convenience, the actual lifetime of an issued certificate.
   *
   * @var string
   */
  public $lifetime;
  /**
   * The time after which the certificate is expired. Per RFC 5280, the validity
   * period for a certificate is the period of time from not_before_time through
   * not_after_time, inclusive. Corresponds to 'not_before_time' + 'lifetime' -
   * 1 second.
   *
   * @var string
   */
  public $notAfterTime;
  /**
   * The time at which the certificate becomes valid.
   *
   * @var string
   */
  public $notBeforeTime;
  protected $subjectType = Subject::class;
  protected $subjectDataType = '';
  protected $subjectAltNameType = SubjectAltNames::class;
  protected $subjectAltNameDataType = '';

  /**
   * The serial number encoded in lowercase hexadecimal.
   *
   * @param string $hexSerialNumber
   */
  public function setHexSerialNumber($hexSerialNumber)
  {
    $this->hexSerialNumber = $hexSerialNumber;
  }
  /**
   * @return string
   */
  public function getHexSerialNumber()
  {
    return $this->hexSerialNumber;
  }
  /**
   * For convenience, the actual lifetime of an issued certificate.
   *
   * @param string $lifetime
   */
  public function setLifetime($lifetime)
  {
    $this->lifetime = $lifetime;
  }
  /**
   * @return string
   */
  public function getLifetime()
  {
    return $this->lifetime;
  }
  /**
   * The time after which the certificate is expired. Per RFC 5280, the validity
   * period for a certificate is the period of time from not_before_time through
   * not_after_time, inclusive. Corresponds to 'not_before_time' + 'lifetime' -
   * 1 second.
   *
   * @param string $notAfterTime
   */
  public function setNotAfterTime($notAfterTime)
  {
    $this->notAfterTime = $notAfterTime;
  }
  /**
   * @return string
   */
  public function getNotAfterTime()
  {
    return $this->notAfterTime;
  }
  /**
   * The time at which the certificate becomes valid.
   *
   * @param string $notBeforeTime
   */
  public function setNotBeforeTime($notBeforeTime)
  {
    $this->notBeforeTime = $notBeforeTime;
  }
  /**
   * @return string
   */
  public function getNotBeforeTime()
  {
    return $this->notBeforeTime;
  }
  /**
   * Contains distinguished name fields such as the common name, location and /
   * organization.
   *
   * @param Subject $subject
   */
  public function setSubject(Subject $subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return Subject
   */
  public function getSubject()
  {
    return $this->subject;
  }
  /**
   * The subject alternative name fields.
   *
   * @param SubjectAltNames $subjectAltName
   */
  public function setSubjectAltName(SubjectAltNames $subjectAltName)
  {
    $this->subjectAltName = $subjectAltName;
  }
  /**
   * @return SubjectAltNames
   */
  public function getSubjectAltName()
  {
    return $this->subjectAltName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubjectDescription::class, 'Google_Service_CertificateAuthorityService_SubjectDescription');
