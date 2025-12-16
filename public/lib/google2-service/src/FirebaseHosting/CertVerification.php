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

namespace Google\Service\FirebaseHosting;

class CertVerification extends \Google\Model
{
  protected $dnsType = DnsUpdates::class;
  protected $dnsDataType = '';
  protected $httpType = HttpUpdate::class;
  protected $httpDataType = '';

  /**
   * Output only. A `TXT` record to add to your DNS records that confirms your
   * intent to let Hosting create an SSL cert for your domain name.
   *
   * @param DnsUpdates $dns
   */
  public function setDns(DnsUpdates $dns)
  {
    $this->dns = $dns;
  }
  /**
   * @return DnsUpdates
   */
  public function getDns()
  {
    return $this->dns;
  }
  /**
   * Output only. A file to add to your existing, non-Hosting hosting service
   * that confirms your intent to let Hosting create an SSL cert for your domain
   * name.
   *
   * @param HttpUpdate $http
   */
  public function setHttp(HttpUpdate $http)
  {
    $this->http = $http;
  }
  /**
   * @return HttpUpdate
   */
  public function getHttp()
  {
    return $this->http;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertVerification::class, 'Google_Service_FirebaseHosting_CertVerification');
