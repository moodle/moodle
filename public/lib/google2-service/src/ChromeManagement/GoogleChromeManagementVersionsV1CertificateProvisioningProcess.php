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

class GoogleChromeManagementVersionsV1CertificateProvisioningProcess extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_UNSPECIFIED = 'SIGNATURE_ALGORITHM_UNSPECIFIED';
  /**
   * The server-side builds the PKCS#1 DigestInfo and sends a SHA256 hash of it
   * to the client. The client should sign using RSA with PKCS#1 v1.5 padding.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_RSA_PKCS1_V1_5_SHA256 = 'SIGNATURE_ALGORITHM_RSA_PKCS1_V1_5_SHA256';
  /**
   * The server-side builds the PKCS#1 DigestInfo and sends it unhashed to the
   * client. The client is responsible for signing and hashing using the P-256
   * curve.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_ECDSA_SHA256 = 'SIGNATURE_ALGORITHM_ECDSA_SHA256';
  protected $chromeOsDeviceType = GoogleChromeManagementVersionsV1ChromeOsDevice::class;
  protected $chromeOsDeviceDataType = '';
  protected $chromeOsUserSessionType = GoogleChromeManagementVersionsV1ChromeOsUserSession::class;
  protected $chromeOsUserSessionDataType = '';
  /**
   * Output only. A message describing why this `CertificateProvisioningProcess`
   * has failed. Presence of this field indicates that the
   * `CertificateProvisioningProcess` has failed.
   *
   * @var string
   */
  public $failureMessage;
  protected $genericCaConnectionType = GoogleChromeManagementVersionsV1GenericCaConnection::class;
  protected $genericCaConnectionDataType = '';
  protected $genericProfileType = GoogleChromeManagementVersionsV1GenericProfile::class;
  protected $genericProfileDataType = '';
  /**
   * Output only. The issued certificate for this
   * `CertificateProvisioningProcess` in PEM format.
   *
   * @var string
   */
  public $issuedCertificate;
  /**
   * Identifier. Resource name of the `CertificateProvisioningProcess`. The name
   * pattern is given as `customers/{customer}/certificateProvisioningProcesses/
   * {certificate_provisioning_process}` with `{customer}` being the obfuscated
   * customer id and `{certificate_provisioning_process}` being the certificate
   * provisioning process id.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The ID of the certificate provisioning profile.
   *
   * @var string
   */
  public $provisioningProfileId;
  protected $scepCaConnectionType = GoogleChromeManagementVersionsV1ScepCaConnection::class;
  protected $scepCaConnectionDataType = '';
  protected $scepProfileType = GoogleChromeManagementVersionsV1ScepProfile::class;
  protected $scepProfileDataType = '';
  /**
   * Output only. The data that the client was asked to sign. This field is only
   * present after the `SignData` operation has been initiated.
   *
   * @var string
   */
  public $signData;
  /**
   * Output only. The signature of `signature_algorithm`, generated using the
   * client's private key using `signature_algorithm`. This field is only
   * present after the `SignData` operation has finished.
   *
   * @var string
   */
  public $signature;
  /**
   * Output only. The signature algorithm that the client and backend components
   * use when processing `sign_data`. If the `profile_type` is a
   * `GenericProfile`, this field will only be present after the `SignData`
   * operation was initiated. If the `profile_type` is a `ScepProfile`, the
   * field will always be present.
   *
   * @var string
   */
  public $signatureAlgorithm;
  /**
   * Output only. Server-generated timestamp of when the certificate
   * provisioning process has been created.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The public key for which a certificate should be provisioned.
   * Represented as a DER-encoded X.509 SubjectPublicKeyInfo.
   *
   * @var string
   */
  public $subjectPublicKeyInfo;

  /**
   * Output only. The client certificate is being provisioned for a ChromeOS
   * device. This contains information about the device.
   *
   * @param GoogleChromeManagementVersionsV1ChromeOsDevice $chromeOsDevice
   */
  public function setChromeOsDevice(GoogleChromeManagementVersionsV1ChromeOsDevice $chromeOsDevice)
  {
    $this->chromeOsDevice = $chromeOsDevice;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ChromeOsDevice
   */
  public function getChromeOsDevice()
  {
    return $this->chromeOsDevice;
  }
  /**
   * Output only. The client certificate is being provisioned for a ChromeOS
   * user. This contains information about the current user session.
   *
   * @param GoogleChromeManagementVersionsV1ChromeOsUserSession $chromeOsUserSession
   */
  public function setChromeOsUserSession(GoogleChromeManagementVersionsV1ChromeOsUserSession $chromeOsUserSession)
  {
    $this->chromeOsUserSession = $chromeOsUserSession;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ChromeOsUserSession
   */
  public function getChromeOsUserSession()
  {
    return $this->chromeOsUserSession;
  }
  /**
   * Output only. A message describing why this `CertificateProvisioningProcess`
   * has failed. Presence of this field indicates that the
   * `CertificateProvisioningProcess` has failed.
   *
   * @param string $failureMessage
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
   * Output only. The CA connection is a generic CA connection.
   *
   * @param GoogleChromeManagementVersionsV1GenericCaConnection $genericCaConnection
   */
  public function setGenericCaConnection(GoogleChromeManagementVersionsV1GenericCaConnection $genericCaConnection)
  {
    $this->genericCaConnection = $genericCaConnection;
  }
  /**
   * @return GoogleChromeManagementVersionsV1GenericCaConnection
   */
  public function getGenericCaConnection()
  {
    return $this->genericCaConnection;
  }
  /**
   * Output only. The profile is a generic certificate provisioning profile.
   *
   * @param GoogleChromeManagementVersionsV1GenericProfile $genericProfile
   */
  public function setGenericProfile(GoogleChromeManagementVersionsV1GenericProfile $genericProfile)
  {
    $this->genericProfile = $genericProfile;
  }
  /**
   * @return GoogleChromeManagementVersionsV1GenericProfile
   */
  public function getGenericProfile()
  {
    return $this->genericProfile;
  }
  /**
   * Output only. The issued certificate for this
   * `CertificateProvisioningProcess` in PEM format.
   *
   * @param string $issuedCertificate
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
   * Identifier. Resource name of the `CertificateProvisioningProcess`. The name
   * pattern is given as `customers/{customer}/certificateProvisioningProcesses/
   * {certificate_provisioning_process}` with `{customer}` being the obfuscated
   * customer id and `{certificate_provisioning_process}` being the certificate
   * provisioning process id.
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
   * Output only. The ID of the certificate provisioning profile.
   *
   * @param string $provisioningProfileId
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
   * Output only. The CA connection is a SCEP CA connection.
   *
   * @param GoogleChromeManagementVersionsV1ScepCaConnection $scepCaConnection
   */
  public function setScepCaConnection(GoogleChromeManagementVersionsV1ScepCaConnection $scepCaConnection)
  {
    $this->scepCaConnection = $scepCaConnection;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ScepCaConnection
   */
  public function getScepCaConnection()
  {
    return $this->scepCaConnection;
  }
  /**
   * Output only. The profile is a SCEP certificate provisioning profile.
   *
   * @param GoogleChromeManagementVersionsV1ScepProfile $scepProfile
   */
  public function setScepProfile(GoogleChromeManagementVersionsV1ScepProfile $scepProfile)
  {
    $this->scepProfile = $scepProfile;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ScepProfile
   */
  public function getScepProfile()
  {
    return $this->scepProfile;
  }
  /**
   * Output only. The data that the client was asked to sign. This field is only
   * present after the `SignData` operation has been initiated.
   *
   * @param string $signData
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
   * Output only. The signature of `signature_algorithm`, generated using the
   * client's private key using `signature_algorithm`. This field is only
   * present after the `SignData` operation has finished.
   *
   * @param string $signature
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
   * Output only. The signature algorithm that the client and backend components
   * use when processing `sign_data`. If the `profile_type` is a
   * `GenericProfile`, this field will only be present after the `SignData`
   * operation was initiated. If the `profile_type` is a `ScepProfile`, the
   * field will always be present.
   *
   * Accepted values: SIGNATURE_ALGORITHM_UNSPECIFIED,
   * SIGNATURE_ALGORITHM_RSA_PKCS1_V1_5_SHA256, SIGNATURE_ALGORITHM_ECDSA_SHA256
   *
   * @param self::SIGNATURE_ALGORITHM_* $signatureAlgorithm
   */
  public function setSignatureAlgorithm($signatureAlgorithm)
  {
    $this->signatureAlgorithm = $signatureAlgorithm;
  }
  /**
   * @return self::SIGNATURE_ALGORITHM_*
   */
  public function getSignatureAlgorithm()
  {
    return $this->signatureAlgorithm;
  }
  /**
   * Output only. Server-generated timestamp of when the certificate
   * provisioning process has been created.
   *
   * @param string $startTime
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
   * Output only. The public key for which a certificate should be provisioned.
   * Represented as a DER-encoded X.509 SubjectPublicKeyInfo.
   *
   * @param string $subjectPublicKeyInfo
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
class_alias(GoogleChromeManagementVersionsV1CertificateProvisioningProcess::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1CertificateProvisioningProcess');
