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

class RetrieveGoogleDomainsForwardingConfigResponse extends \Google\Collection
{
  protected $collection_key = 'emailForwardings';
  protected $domainForwardingsType = DomainForwarding::class;
  protected $domainForwardingsDataType = 'array';
  protected $emailForwardingsType = EmailForwarding::class;
  protected $emailForwardingsDataType = 'array';

  /**
   * The list of domain forwarding configurations. A forwarding configuration
   * might not work correctly if the required DNS records are not present in the
   * domain's authoritative DNS zone.
   *
   * @param DomainForwarding[] $domainForwardings
   */
  public function setDomainForwardings($domainForwardings)
  {
    $this->domainForwardings = $domainForwardings;
  }
  /**
   * @return DomainForwarding[]
   */
  public function getDomainForwardings()
  {
    return $this->domainForwardings;
  }
  /**
   * The list of email forwarding configurations. A forwarding configuration
   * might not work correctly if the required DNS records are not present in the
   * domain's authoritative DNS zone.
   *
   * @param EmailForwarding[] $emailForwardings
   */
  public function setEmailForwardings($emailForwardings)
  {
    $this->emailForwardings = $emailForwardings;
  }
  /**
   * @return EmailForwarding[]
   */
  public function getEmailForwardings()
  {
    return $this->emailForwardings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetrieveGoogleDomainsForwardingConfigResponse::class, 'Google_Service_CloudDomains_RetrieveGoogleDomainsForwardingConfigResponse');
