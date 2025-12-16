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

namespace Google\Service\NetworkManagement;

class GoogleServiceInfo extends \Google\Model
{
  /**
   * Unspecified Google Service.
   */
  public const GOOGLE_SERVICE_TYPE_GOOGLE_SERVICE_TYPE_UNSPECIFIED = 'GOOGLE_SERVICE_TYPE_UNSPECIFIED';
  /**
   * Identity aware proxy. https://cloud.google.com/iap/docs/using-tcp-
   * forwarding
   */
  public const GOOGLE_SERVICE_TYPE_IAP = 'IAP';
  /**
   * One of two services sharing IP ranges: * Load Balancer proxy * Centralized
   * Health Check prober https://cloud.google.com/load-balancing/docs/firewall-
   * rules
   */
  public const GOOGLE_SERVICE_TYPE_GFE_PROXY_OR_HEALTH_CHECK_PROBER = 'GFE_PROXY_OR_HEALTH_CHECK_PROBER';
  /**
   * Connectivity from Cloud DNS to forwarding targets or alternate name servers
   * that use private routing.
   * https://cloud.google.com/dns/docs/zones/forwarding-zones#firewall-rules
   * https://cloud.google.com/dns/docs/policies#firewall-rules
   */
  public const GOOGLE_SERVICE_TYPE_CLOUD_DNS = 'CLOUD_DNS';
  /**
   * private.googleapis.com and restricted.googleapis.com
   */
  public const GOOGLE_SERVICE_TYPE_GOOGLE_API = 'GOOGLE_API';
  /**
   * Google API via Private Service Connect.
   * https://cloud.google.com/vpc/docs/configure-private-service-connect-apis
   */
  public const GOOGLE_SERVICE_TYPE_GOOGLE_API_PSC = 'GOOGLE_API_PSC';
  /**
   * Google API via VPC Service Controls.
   * https://cloud.google.com/vpc/docs/configure-private-service-connect-apis
   */
  public const GOOGLE_SERVICE_TYPE_GOOGLE_API_VPC_SC = 'GOOGLE_API_VPC_SC';
  /**
   * Google API via Serverless VPC Access.
   * https://cloud.google.com/vpc/docs/serverless-vpc-access
   */
  public const GOOGLE_SERVICE_TYPE_SERVERLESS_VPC_ACCESS = 'SERVERLESS_VPC_ACCESS';
  /**
   * Recognized type of a Google Service.
   *
   * @var string
   */
  public $googleServiceType;
  /**
   * Source IP address.
   *
   * @var string
   */
  public $sourceIp;

  /**
   * Recognized type of a Google Service.
   *
   * Accepted values: GOOGLE_SERVICE_TYPE_UNSPECIFIED, IAP,
   * GFE_PROXY_OR_HEALTH_CHECK_PROBER, CLOUD_DNS, GOOGLE_API, GOOGLE_API_PSC,
   * GOOGLE_API_VPC_SC, SERVERLESS_VPC_ACCESS
   *
   * @param self::GOOGLE_SERVICE_TYPE_* $googleServiceType
   */
  public function setGoogleServiceType($googleServiceType)
  {
    $this->googleServiceType = $googleServiceType;
  }
  /**
   * @return self::GOOGLE_SERVICE_TYPE_*
   */
  public function getGoogleServiceType()
  {
    return $this->googleServiceType;
  }
  /**
   * Source IP address.
   *
   * @param string $sourceIp
   */
  public function setSourceIp($sourceIp)
  {
    $this->sourceIp = $sourceIp;
  }
  /**
   * @return string
   */
  public function getSourceIp()
  {
    return $this->sourceIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleServiceInfo::class, 'Google_Service_NetworkManagement_GoogleServiceInfo');
