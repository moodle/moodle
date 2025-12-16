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

class DnsSettings extends \Google\Collection
{
  protected $collection_key = 'glueRecords';
  protected $customDnsType = CustomDns::class;
  protected $customDnsDataType = '';
  protected $glueRecordsType = GlueRecord::class;
  protected $glueRecordsDataType = 'array';
  protected $googleDomainsDnsType = GoogleDomainsDns::class;
  protected $googleDomainsDnsDataType = '';
  /**
   * Output only. Indicates if this `Registration` has configured one of the
   * following deprecated Google Domains DNS features: * Domain forwarding (HTTP
   * `301` and `302` response status codes), * Email forwarding. See
   * https://cloud.google.com/domains/docs/deprecations/feature-deprecations for
   * more details. If any of these features is enabled call the
   * `RetrieveGoogleDomainsForwardingConfig` method to get details about the
   * feature's configuration. A forwarding configuration might not work
   * correctly if required DNS records are not present in the domain's
   * authoritative DNS Zone.
   *
   * @var bool
   */
  public $googleDomainsRedirectsDataAvailable;

  /**
   * An arbitrary DNS provider identified by its name servers.
   *
   * @param CustomDns $customDns
   */
  public function setCustomDns(CustomDns $customDns)
  {
    $this->customDns = $customDns;
  }
  /**
   * @return CustomDns
   */
  public function getCustomDns()
  {
    return $this->customDns;
  }
  /**
   * The list of glue records for this `Registration`. Commonly empty.
   *
   * @param GlueRecord[] $glueRecords
   */
  public function setGlueRecords($glueRecords)
  {
    $this->glueRecords = $glueRecords;
  }
  /**
   * @return GlueRecord[]
   */
  public function getGlueRecords()
  {
    return $this->glueRecords;
  }
  /**
   * Deprecated: For more information, see [Cloud Domains feature
   * deprecation](https://cloud.google.com/domains/docs/deprecations/feature-
   * deprecations). The free DNS zone provided by [Google
   * Domains](https://domains.google/).
   *
   * @deprecated
   * @param GoogleDomainsDns $googleDomainsDns
   */
  public function setGoogleDomainsDns(GoogleDomainsDns $googleDomainsDns)
  {
    $this->googleDomainsDns = $googleDomainsDns;
  }
  /**
   * @deprecated
   * @return GoogleDomainsDns
   */
  public function getGoogleDomainsDns()
  {
    return $this->googleDomainsDns;
  }
  /**
   * Output only. Indicates if this `Registration` has configured one of the
   * following deprecated Google Domains DNS features: * Domain forwarding (HTTP
   * `301` and `302` response status codes), * Email forwarding. See
   * https://cloud.google.com/domains/docs/deprecations/feature-deprecations for
   * more details. If any of these features is enabled call the
   * `RetrieveGoogleDomainsForwardingConfig` method to get details about the
   * feature's configuration. A forwarding configuration might not work
   * correctly if required DNS records are not present in the domain's
   * authoritative DNS Zone.
   *
   * @param bool $googleDomainsRedirectsDataAvailable
   */
  public function setGoogleDomainsRedirectsDataAvailable($googleDomainsRedirectsDataAvailable)
  {
    $this->googleDomainsRedirectsDataAvailable = $googleDomainsRedirectsDataAvailable;
  }
  /**
   * @return bool
   */
  public function getGoogleDomainsRedirectsDataAvailable()
  {
    return $this->googleDomainsRedirectsDataAvailable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsSettings::class, 'Google_Service_CloudDomains_DnsSettings');
