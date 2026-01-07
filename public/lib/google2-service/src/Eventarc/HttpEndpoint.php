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

namespace Google\Service\Eventarc;

class HttpEndpoint extends \Google\Model
{
  /**
   * Required. The URI of the HTTP endpoint. The value must be a RFC2396 URI
   * string. Examples: `http://10.10.10.8:80/route`, `http://svc.us-
   * central1.p.local:8080/`. Only HTTP and HTTPS protocols are supported. The
   * host can be either a static IP addressable from the VPC specified by the
   * network config, or an internal DNS hostname of the service resolvable via
   * Cloud DNS.
   *
   * @var string
   */
  public $uri;

  /**
   * Required. The URI of the HTTP endpoint. The value must be a RFC2396 URI
   * string. Examples: `http://10.10.10.8:80/route`, `http://svc.us-
   * central1.p.local:8080/`. Only HTTP and HTTPS protocols are supported. The
   * host can be either a static IP addressable from the VPC specified by the
   * network config, or an internal DNS hostname of the service resolvable via
   * Cloud DNS.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpEndpoint::class, 'Google_Service_Eventarc_HttpEndpoint');
