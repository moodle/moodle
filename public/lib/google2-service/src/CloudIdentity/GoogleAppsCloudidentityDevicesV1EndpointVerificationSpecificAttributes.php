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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes extends \Google\Collection
{
  protected $collection_key = 'certificateAttributes';
  /**
   * [Additional signals](https://cloud.google.com/endpoint-
   * verification/docs/device-information) reported by Endpoint Verification. It
   * includes the following attributes: * Non-configurable attributes: hotfixes,
   * av_installed, av_enabled, windows_domain_name,
   * is_os_native_firewall_enabled, and is_secure_boot_enabled. * [Configurable
   * attributes](https://cloud.google.com/endpoint-verification/docs/collect-
   * config-attributes): file, folder, and binary attributes; registry entries;
   * and properties in a plist.
   *
   * @var array[]
   */
  public $additionalSignals;
  protected $browserAttributesType = GoogleAppsCloudidentityDevicesV1BrowserAttributes::class;
  protected $browserAttributesDataType = 'array';
  protected $certificateAttributesType = GoogleAppsCloudidentityDevicesV1CertificateAttributes::class;
  protected $certificateAttributesDataType = 'array';

  /**
   * [Additional signals](https://cloud.google.com/endpoint-
   * verification/docs/device-information) reported by Endpoint Verification. It
   * includes the following attributes: * Non-configurable attributes: hotfixes,
   * av_installed, av_enabled, windows_domain_name,
   * is_os_native_firewall_enabled, and is_secure_boot_enabled. * [Configurable
   * attributes](https://cloud.google.com/endpoint-verification/docs/collect-
   * config-attributes): file, folder, and binary attributes; registry entries;
   * and properties in a plist.
   *
   * @param array[] $additionalSignals
   */
  public function setAdditionalSignals($additionalSignals)
  {
    $this->additionalSignals = $additionalSignals;
  }
  /**
   * @return array[]
   */
  public function getAdditionalSignals()
  {
    return $this->additionalSignals;
  }
  /**
   * Details of browser profiles reported by Endpoint Verification.
   *
   * @param GoogleAppsCloudidentityDevicesV1BrowserAttributes[] $browserAttributes
   */
  public function setBrowserAttributes($browserAttributes)
  {
    $this->browserAttributes = $browserAttributes;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1BrowserAttributes[]
   */
  public function getBrowserAttributes()
  {
    return $this->browserAttributes;
  }
  /**
   * Details of certificates.
   *
   * @param GoogleAppsCloudidentityDevicesV1CertificateAttributes[] $certificateAttributes
   */
  public function setCertificateAttributes($certificateAttributes)
  {
    $this->certificateAttributes = $certificateAttributes;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1CertificateAttributes[]
   */
  public function getCertificateAttributes()
  {
    return $this->certificateAttributes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes');
