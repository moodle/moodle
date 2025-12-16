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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1ScepProfile extends \Google\Collection
{
  protected $collection_key = 'subjectAltNames';
  /**
   * Output only. The certificate template name as defined by the admin on their
   * on-prem infrastructure. The Certificate Authority uses this name to
   * identify the certificate template.
   *
   * @var string
   */
  public $certificateTemplateName;
  /**
   * Output only. The country of the subject.
   *
   * @var string
   */
  public $country;
  /**
   * Output only. The allowed key usages for certificate's key.
   *
   * @var string[]
   */
  public $keyUsages;
  /**
   * Output only. The locality of the subject.
   *
   * @var string
   */
  public $locality;
  /**
   * Output only. The name of the organization the subject belongs to.
   *
   * @var string
   */
  public $organization;
  /**
   * Output only. The organizational units of the subject.
   *
   * @var string[]
   */
  public $organizationalUnits;
  /**
   * Output only. The state of the subject.
   *
   * @var string
   */
  public $state;
  protected $subjectAltNamesType = GoogleChromeManagementVersionsV1SubjectAltName::class;
  protected $subjectAltNamesDataType = 'array';
  /**
   * Output only. The common name of the subject.
   *
   * @var string
   */
  public $subjectCommonName;

  /**
   * Output only. The certificate template name as defined by the admin on their
   * on-prem infrastructure. The Certificate Authority uses this name to
   * identify the certificate template.
   *
   * @param string $certificateTemplateName
   */
  public function setCertificateTemplateName($certificateTemplateName)
  {
    $this->certificateTemplateName = $certificateTemplateName;
  }
  /**
   * @return string
   */
  public function getCertificateTemplateName()
  {
    return $this->certificateTemplateName;
  }
  /**
   * Output only. The country of the subject.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * Output only. The allowed key usages for certificate's key.
   *
   * @param string[] $keyUsages
   */
  public function setKeyUsages($keyUsages)
  {
    $this->keyUsages = $keyUsages;
  }
  /**
   * @return string[]
   */
  public function getKeyUsages()
  {
    return $this->keyUsages;
  }
  /**
   * Output only. The locality of the subject.
   *
   * @param string $locality
   */
  public function setLocality($locality)
  {
    $this->locality = $locality;
  }
  /**
   * @return string
   */
  public function getLocality()
  {
    return $this->locality;
  }
  /**
   * Output only. The name of the organization the subject belongs to.
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * Output only. The organizational units of the subject.
   *
   * @param string[] $organizationalUnits
   */
  public function setOrganizationalUnits($organizationalUnits)
  {
    $this->organizationalUnits = $organizationalUnits;
  }
  /**
   * @return string[]
   */
  public function getOrganizationalUnits()
  {
    return $this->organizationalUnits;
  }
  /**
   * Output only. The state of the subject.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The subject alternative names.
   *
   * @param GoogleChromeManagementVersionsV1SubjectAltName[] $subjectAltNames
   */
  public function setSubjectAltNames($subjectAltNames)
  {
    $this->subjectAltNames = $subjectAltNames;
  }
  /**
   * @return GoogleChromeManagementVersionsV1SubjectAltName[]
   */
  public function getSubjectAltNames()
  {
    return $this->subjectAltNames;
  }
  /**
   * Output only. The common name of the subject.
   *
   * @param string $subjectCommonName
   */
  public function setSubjectCommonName($subjectCommonName)
  {
    $this->subjectCommonName = $subjectCommonName;
  }
  /**
   * @return string
   */
  public function getSubjectCommonName()
  {
    return $this->subjectCommonName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ScepProfile::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ScepProfile');
