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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1DefineCertificateRequest extends \Google\Collection
{
  protected $collection_key = 'settings';
  /**
   * Optional. The optional name of the certificate. If not specified, the
   * certificate issuer will be used as the name.
   *
   * @var string
   */
  public $ceritificateName;
  /**
   * Required. The raw contents of the .PEM, .CRT, or .CER file.
   *
   * @var string
   */
  public $certificate;
  protected $settingsType = GoogleChromePolicyVersionsV1NetworkSetting::class;
  protected $settingsDataType = 'array';
  /**
   * Required. The target resource on which this certificate is applied. The
   * following resources are supported: * Organizational Unit
   * ("orgunits/{orgunit_id}")
   *
   * @var string
   */
  public $targetResource;

  /**
   * Optional. The optional name of the certificate. If not specified, the
   * certificate issuer will be used as the name.
   *
   * @param string $ceritificateName
   */
  public function setCeritificateName($ceritificateName)
  {
    $this->ceritificateName = $ceritificateName;
  }
  /**
   * @return string
   */
  public function getCeritificateName()
  {
    return $this->ceritificateName;
  }
  /**
   * Required. The raw contents of the .PEM, .CRT, or .CER file.
   *
   * @param string $certificate
   */
  public function setCertificate($certificate)
  {
    $this->certificate = $certificate;
  }
  /**
   * @return string
   */
  public function getCertificate()
  {
    return $this->certificate;
  }
  /**
   * Optional. Certificate settings within the chrome.networks.certificates
   * namespace.
   *
   * @param GoogleChromePolicyVersionsV1NetworkSetting[] $settings
   */
  public function setSettings($settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return GoogleChromePolicyVersionsV1NetworkSetting[]
   */
  public function getSettings()
  {
    return $this->settings;
  }
  /**
   * Required. The target resource on which this certificate is applied. The
   * following resources are supported: * Organizational Unit
   * ("orgunits/{orgunit_id}")
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1DefineCertificateRequest::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1DefineCertificateRequest');
