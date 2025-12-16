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

namespace Google\Service\Compute;

class NetworkEndpointGroupAppEngine extends \Google\Model
{
  /**
   * Optional serving service.
   *
   * The service name is case-sensitive and must be 1-63 characters long.
   *
   * Example value: default, my-service.
   *
   * @var string
   */
  public $service;
  /**
   * An URL mask is one of the main components of the Cloud Function.
   *
   * A template to parse service and version fields from a request URL. URL mask
   * allows for routing to multiple App Engine services without having to create
   * multiple Network Endpoint Groups and backend services.
   *
   * For example, the request URLsfoo1-dot-appname.appspot.com/v1 andfoo1-dot-
   * appname.appspot.com/v2 can be backed by the same Serverless NEG with URL
   * mask-dot-appname.appspot.com/. The URL mask will parse them to { service =
   * "foo1", version = "v1" } and { service = "foo1", version = "v2" }
   * respectively.
   *
   * @var string
   */
  public $urlMask;
  /**
   * Optional serving version.
   *
   * The version name is case-sensitive and must be 1-100 characters long.
   *
   * Example value: v1, v2.
   *
   * @var string
   */
  public $version;

  /**
   * Optional serving service.
   *
   * The service name is case-sensitive and must be 1-63 characters long.
   *
   * Example value: default, my-service.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * An URL mask is one of the main components of the Cloud Function.
   *
   * A template to parse service and version fields from a request URL. URL mask
   * allows for routing to multiple App Engine services without having to create
   * multiple Network Endpoint Groups and backend services.
   *
   * For example, the request URLsfoo1-dot-appname.appspot.com/v1 andfoo1-dot-
   * appname.appspot.com/v2 can be backed by the same Serverless NEG with URL
   * mask-dot-appname.appspot.com/. The URL mask will parse them to { service =
   * "foo1", version = "v1" } and { service = "foo1", version = "v2" }
   * respectively.
   *
   * @param string $urlMask
   */
  public function setUrlMask($urlMask)
  {
    $this->urlMask = $urlMask;
  }
  /**
   * @return string
   */
  public function getUrlMask()
  {
    return $this->urlMask;
  }
  /**
   * Optional serving version.
   *
   * The version name is case-sensitive and must be 1-100 characters long.
   *
   * Example value: v1, v2.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointGroupAppEngine::class, 'Google_Service_Compute_NetworkEndpointGroupAppEngine');
