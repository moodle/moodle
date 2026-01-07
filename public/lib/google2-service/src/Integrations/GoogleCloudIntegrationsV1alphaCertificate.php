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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaCertificate extends \Google\Model
{
  /**
   * Unspecified certificate status
   */
  public const CERTIFICATE_STATUS_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Certificate in active state will be able to use
   */
  public const CERTIFICATE_STATUS_ACTIVE = 'ACTIVE';
  /**
   * Certificate in expired state needs to be updated
   */
  public const CERTIFICATE_STATUS_EXPIRED = 'EXPIRED';
  /**
   * Status of the certificate
   *
   * @var string
   */
  public $certificateStatus;
  /**
   * Immutable. Credential id that will be used to register with trawler
   *
   * @var string
   */
  public $credentialId;
  /**
   * Description of the certificate
   *
   * @var string
   */
  public $description;
  /**
   * Required. Name of the certificate
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Auto generated primary key
   *
   * @var string
   */
  public $name;
  protected $rawCertificateType = GoogleCloudIntegrationsV1alphaClientCertificate::class;
  protected $rawCertificateDataType = '';
  /**
   * Immutable. Requestor ID to be used to register certificate with trawler
   *
   * @var string
   */
  public $requestorId;
  /**
   * Output only. The timestamp after which certificate will expire
   *
   * @var string
   */
  public $validEndTime;
  /**
   * Output only. The timestamp after which certificate will be valid
   *
   * @var string
   */
  public $validStartTime;

  /**
   * Status of the certificate
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, EXPIRED
   *
   * @param self::CERTIFICATE_STATUS_* $certificateStatus
   */
  public function setCertificateStatus($certificateStatus)
  {
    $this->certificateStatus = $certificateStatus;
  }
  /**
   * @return self::CERTIFICATE_STATUS_*
   */
  public function getCertificateStatus()
  {
    return $this->certificateStatus;
  }
  /**
   * Immutable. Credential id that will be used to register with trawler
   *
   * @param string $credentialId
   */
  public function setCredentialId($credentialId)
  {
    $this->credentialId = $credentialId;
  }
  /**
   * @return string
   */
  public function getCredentialId()
  {
    return $this->credentialId;
  }
  /**
   * Description of the certificate
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Name of the certificate
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Auto generated primary key
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
   * Input only. Raw client certificate which would be registered with trawler
   *
   * @param GoogleCloudIntegrationsV1alphaClientCertificate $rawCertificate
   */
  public function setRawCertificate(GoogleCloudIntegrationsV1alphaClientCertificate $rawCertificate)
  {
    $this->rawCertificate = $rawCertificate;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaClientCertificate
   */
  public function getRawCertificate()
  {
    return $this->rawCertificate;
  }
  /**
   * Immutable. Requestor ID to be used to register certificate with trawler
   *
   * @param string $requestorId
   */
  public function setRequestorId($requestorId)
  {
    $this->requestorId = $requestorId;
  }
  /**
   * @return string
   */
  public function getRequestorId()
  {
    return $this->requestorId;
  }
  /**
   * Output only. The timestamp after which certificate will expire
   *
   * @param string $validEndTime
   */
  public function setValidEndTime($validEndTime)
  {
    $this->validEndTime = $validEndTime;
  }
  /**
   * @return string
   */
  public function getValidEndTime()
  {
    return $this->validEndTime;
  }
  /**
   * Output only. The timestamp after which certificate will be valid
   *
   * @param string $validStartTime
   */
  public function setValidStartTime($validStartTime)
  {
    $this->validStartTime = $validStartTime;
  }
  /**
   * @return string
   */
  public function getValidStartTime()
  {
    return $this->validStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaCertificate::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaCertificate');
