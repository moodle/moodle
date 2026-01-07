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

namespace Google\Service\CloudDomains;

class ConfigureDnsSettingsRequest extends \Google\Model
{
  protected $dnsSettingsType = DnsSettings::class;
  protected $dnsSettingsDataType = '';
  /**
   * Required. The field mask describing which fields to update as a comma-
   * separated list. For example, if only the name servers are being updated for
   * an existing Custom DNS configuration, the `update_mask` is
   * `"custom_dns.name_servers"`. When changing the DNS provider from one type
   * to another, pass the new provider's field name as part of the field mask.
   * For example, when changing from a Google Domains DNS configuration to a
   * Custom DNS configuration, the `update_mask` is `"custom_dns"`. //
   *
   * @var string
   */
  public $updateMask;
  /**
   * Validate the request without actually updating the DNS settings.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Fields of the `DnsSettings` to update.
   *
   * @param DnsSettings $dnsSettings
   */
  public function setDnsSettings(DnsSettings $dnsSettings)
  {
    $this->dnsSettings = $dnsSettings;
  }
  /**
   * @return DnsSettings
   */
  public function getDnsSettings()
  {
    return $this->dnsSettings;
  }
  /**
   * Required. The field mask describing which fields to update as a comma-
   * separated list. For example, if only the name servers are being updated for
   * an existing Custom DNS configuration, the `update_mask` is
   * `"custom_dns.name_servers"`. When changing the DNS provider from one type
   * to another, pass the new provider's field name as part of the field mask.
   * For example, when changing from a Google Domains DNS configuration to a
   * Custom DNS configuration, the `update_mask` is `"custom_dns"`. //
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
  /**
   * Validate the request without actually updating the DNS settings.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigureDnsSettingsRequest::class, 'Google_Service_CloudDomains_ConfigureDnsSettingsRequest');
