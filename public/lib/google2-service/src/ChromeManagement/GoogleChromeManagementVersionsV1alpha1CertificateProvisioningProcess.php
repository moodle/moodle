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

class GoogleChromeManagementVersionsV1alpha1CertificateProvisioningProcess extends \Google\Model
{
  /**
   * @var string
   */
  public $caConnectionAdapterConfigReference;
  protected $chromeOsDeviceType = GoogleChromeManagementVersionsV1alpha1ChromeOsDevice::class;
  protected $chromeOsDeviceDataType = '';
  protected $chromeOsUserSessionType = GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession::class;
  protected $chromeOsUserSessionDataType = '';
  /**
   * @var string
   */
  public $failureMessage;
  protected $genericCaConnectionType = GoogleChromeManagementVersionsV1alpha1GenericCaConnection::class;
  protected $genericCaConnectionDataType = '';
  protected $genericProfileType = GoogleChromeManagementVersionsV1alpha1GenericProfile::class;
  protected $genericProfileDataType = '';
  /**
   * @var string
   */
  public $issuedCertificate;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $profileAdapterConfigReference;
  /**
   * @var string
   */
  public $provisioningProfileId;
  /**
   * @var string
   */
  public $signData;
  /**
   * @var string
   */
  public $signature;
  /**
   * @var string
   */
  public $signatureAlgorithm;
  /**
   * @var string
   */
  public $startTime;
  /**
   * @var string
   */
  public $subjectPublicKeyInfo;

  /**
   * @param string
   */
  public function setCaConnectionAdapterConfigReference($caConnectionAdapterConfigReference)
  {
    $this->caConnectionAdapterConfigReference = $caConnectionAdapterConfigReference;
  }
  /**
   * @return string
   */
  public function getCaConnectionAdapterConfigReference()
  {
    return $this->caConnectionAdapterConfigReference;
  }
  /**
   * @param GoogleChromeManagementVersionsV1alpha1ChromeOsDevice
   */
  public function setChromeOsDevice(GoogleChromeManagementVersionsV1alpha1ChromeOsDevice $chromeOsDevice)
  {
    $this->chromeOsDevice = $chromeOsDevice;
  }
  /**
   * @return GoogleChromeManagementVersionsV1alpha1ChromeOsDevice
   */
  public function getChromeOsDevice()
  {
    return $this->chromeOsDevice;
  }
  /**
   * @param GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession
   */
  public function setChromeOsUserSession(GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession $chromeOsUserSession)
  {
    $this->chromeOsUserSession = $chromeOsUserSession;
  }
  /**
   * @return GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession
   */
  public function getChromeOsUserSession()
  {
    return $this->chromeOsUserSession;
  }
  /**
   * @param string
   */
  public function setFailureMessage($failureMessage)
  {
    $this->failureMessage = $failureMessage;
  }
  /**
   * @return string
   */
  public function getFailureMessage()
  {
    return $this->failureMessage;
  }
  /**
   * @param GoogleChromeManagementVersionsV1alpha1GenericCaConnection
   */
  public function setGenericCaConnection(GoogleChromeManagementVersionsV1alpha1GenericCaConnection $genericCaConnection)
  {
    $this->genericCaConnection = $genericCaConnection;
  }
  /**
   * @return GoogleChromeManagementVersionsV1alpha1GenericCaConnection
   */
  public function getGenericCaConnection()
  {
    return $this->genericCaConnection;
  }
  /**
   * @param GoogleChromeManagementVersionsV1alpha1GenericProfile
   */
  public function setGenericProfile(GoogleChromeManagementVersionsV1alpha1GenericProfile $genericProfile)
  {
    $this->genericProfile = $genericProfile;
  }
  /**
   * @return GoogleChromeManagementVersionsV1alpha1GenericProfile
   */
  public function getGenericProfile()
  {
    return $this->genericProfile;
  }
  /**
   * @param string
   */
  public function setIssuedCertificate($issuedCertificate)
  {
    $this->issuedCertificate = $issuedCertificate;
  }
  /**
   * @return string
   */
  public function getIssuedCertificate()
  {
    return $this->issuedCertificate;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setProfileAdapterConfigReference($profileAdapterConfigReference)
  {
    $this->profileAdapterConfigReference = $profileAdapterConfigReference;
  }
  /**
   * @return string
   */
  public function getProfileAdapterConfigReference()
  {
    return $this->profileAdapterConfigReference;
  }
  /**
   * @param string
   */
  public function setProvisioningProfileId($provisioningProfileId)
  {
    $this->provisioningProfileId = $provisioningProfileId;
  }
  /**
   * @return string
   */
  public function getProvisioningProfileId()
  {
    return $this->provisioningProfileId;
  }
  /**
   * @param string
   */
  public function setSignData($signData)
  {
    $this->signData = $signData;
  }
  /**
   * @return string
   */
  public function getSignData()
  {
    return $this->signData;
  }
  /**
   * @param string
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
  /**
   * @param string
   */
  public function setSignatureAlgorithm($signatureAlgorithm)
  {
    $this->signatureAlgorithm = $signatureAlgorithm;
  }
  /**
   * @return string
   */
  public function getSignatureAlgorithm()
  {
    return $this->signatureAlgorithm;
  }
  /**
   * @param string
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * @param string
   */
  public function setSubjectPublicKeyInfo($subjectPublicKeyInfo)
  {
    $this->subjectPublicKeyInfo = $subjectPublicKeyInfo;
  }
  /**
   * @return string
   */
  public function getSubjectPublicKeyInfo()
  {
    return $this->subjectPublicKeyInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1alpha1CertificateProvisioningProcess::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1alpha1CertificateProvisioningProcess');
