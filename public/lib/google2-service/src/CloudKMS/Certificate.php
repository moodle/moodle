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

namespace Google\Service\CloudKMS;

class Certificate extends \Google\Collection
{
  protected $collection_key = 'subjectAlternativeDnsNames';
  /**
   * Output only. The issuer distinguished name in RFC 2253 format. Only present
   * if parsed is true.
   *
   * @var string
   */
  public $issuer;
  /**
   * Output only. The certificate is not valid after this time. Only present if
   * parsed is true.
   *
   * @var string
   */
  public $notAfterTime;
  /**
   * Output only. The certificate is not valid before this time. Only present if
   * parsed is true.
   *
   * @var string
   */
  public $notBeforeTime;
  /**
   * Output only. True if the certificate was parsed successfully.
   *
   * @var bool
   */
  public $parsed;
  /**
   * Required. The raw certificate bytes in DER format.
   *
   * @var string
   */
  public $rawDer;
  /**
   * Output only. The certificate serial number as a hex string. Only present if
   * parsed is true.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Output only. The SHA-256 certificate fingerprint as a hex string. Only
   * present if parsed is true.
   *
   * @var string
   */
  public $sha256Fingerprint;
  /**
   * Output only. The subject distinguished name in RFC 2253 format. Only
   * present if parsed is true.
   *
   * @var string
   */
  public $subject;
  /**
   * Output only. The subject Alternative DNS names. Only present if parsed is
   * true.
   *
   * @var string[]
   */
  public $subjectAlternativeDnsNames;

  /**
   * Output only. The issuer distinguished name in RFC 2253 format. Only present
   * if parsed is true.
   *
   * @param string $issuer
   */
  public function setIssuer($issuer)
  {
    $this->issuer = $issuer;
  }
  /**
   * @return string
   */
  public function getIssuer()
  {
    return $this->issuer;
  }
  /**
   * Output only. The certificate is not valid after this time. Only present if
   * parsed is true.
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
   * Output only. The certificate is not valid before this time. Only present if
   * parsed is true.
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
   * Output only. True if the certificate was parsed successfully.
   *
   * @param bool $parsed
   */
  public function setParsed($parsed)
  {
    $this->parsed = $parsed;
  }
  /**
   * @return bool
   */
  public function getParsed()
  {
    return $this->parsed;
  }
  /**
   * Required. The raw certificate bytes in DER format.
   *
   * @param string $rawDer
   */
  public function setRawDer($rawDer)
  {
    $this->rawDer = $rawDer;
  }
  /**
   * @return string
   */
  public function getRawDer()
  {
    return $this->rawDer;
  }
  /**
   * Output only. The certificate serial number as a hex string. Only present if
   * parsed is true.
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
   * Output only. The SHA-256 certificate fingerprint as a hex string. Only
   * present if parsed is true.
   *
   * @param string $sha256Fingerprint
   */
  public function setSha256Fingerprint($sha256Fingerprint)
  {
    $this->sha256Fingerprint = $sha256Fingerprint;
  }
  /**
   * @return string
   */
  public function getSha256Fingerprint()
  {
    return $this->sha256Fingerprint;
  }
  /**
   * Output only. The subject distinguished name in RFC 2253 format. Only
   * present if parsed is true.
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
   * Output only. The subject Alternative DNS names. Only present if parsed is
   * true.
   *
   * @param string[] $subjectAlternativeDnsNames
   */
  public function setSubjectAlternativeDnsNames($subjectAlternativeDnsNames)
  {
    $this->subjectAlternativeDnsNames = $subjectAlternativeDnsNames;
  }
  /**
   * @return string[]
   */
  public function getSubjectAlternativeDnsNames()
  {
    return $this->subjectAlternativeDnsNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Certificate::class, 'Google_Service_CloudKMS_Certificate');
