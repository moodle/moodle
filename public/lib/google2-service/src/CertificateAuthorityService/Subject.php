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

class Subject extends \Google\Collection
{
  protected $collection_key = 'rdnSequence';
  /**
   * The "common name" of the subject.
   *
   * @var string
   */
  public $commonName;
  /**
   * The country code of the subject.
   *
   * @var string
   */
  public $countryCode;
  /**
   * The locality or city of the subject.
   *
   * @var string
   */
  public $locality;
  /**
   * The organization of the subject.
   *
   * @var string
   */
  public $organization;
  /**
   * The organizational_unit of the subject.
   *
   * @var string
   */
  public $organizationalUnit;
  /**
   * The postal code of the subject.
   *
   * @var string
   */
  public $postalCode;
  /**
   * The province, territory, or regional state of the subject.
   *
   * @var string
   */
  public $province;
  protected $rdnSequenceType = RelativeDistinguishedName::class;
  protected $rdnSequenceDataType = 'array';
  /**
   * The street address of the subject.
   *
   * @var string
   */
  public $streetAddress;

  /**
   * The "common name" of the subject.
   *
   * @param string $commonName
   */
  public function setCommonName($commonName)
  {
    $this->commonName = $commonName;
  }
  /**
   * @return string
   */
  public function getCommonName()
  {
    return $this->commonName;
  }
  /**
   * The country code of the subject.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * The locality or city of the subject.
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
   * The organization of the subject.
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
   * The organizational_unit of the subject.
   *
   * @param string $organizationalUnit
   */
  public function setOrganizationalUnit($organizationalUnit)
  {
    $this->organizationalUnit = $organizationalUnit;
  }
  /**
   * @return string
   */
  public function getOrganizationalUnit()
  {
    return $this->organizationalUnit;
  }
  /**
   * The postal code of the subject.
   *
   * @param string $postalCode
   */
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  /**
   * @return string
   */
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  /**
   * The province, territory, or regional state of the subject.
   *
   * @param string $province
   */
  public function setProvince($province)
  {
    $this->province = $province;
  }
  /**
   * @return string
   */
  public function getProvince()
  {
    return $this->province;
  }
  /**
   * This field can be used in place of the named subject fields.
   *
   * @param RelativeDistinguishedName[] $rdnSequence
   */
  public function setRdnSequence($rdnSequence)
  {
    $this->rdnSequence = $rdnSequence;
  }
  /**
   * @return RelativeDistinguishedName[]
   */
  public function getRdnSequence()
  {
    return $this->rdnSequence;
  }
  /**
   * The street address of the subject.
   *
   * @param string $streetAddress
   */
  public function setStreetAddress($streetAddress)
  {
    $this->streetAddress = $streetAddress;
  }
  /**
   * @return string
   */
  public function getStreetAddress()
  {
    return $this->streetAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subject::class, 'Google_Service_CertificateAuthorityService_Subject');
