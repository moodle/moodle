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

class NetworkEndpointGroupCloudRun extends \Google\Model
{
  /**
   * Cloud Run service is the main resource of Cloud Run.
   *
   * The service must be 1-63 characters long, and comply withRFC1035.
   *
   * Example value: "run-service".
   *
   * @var string
   */
  public $service;
  /**
   * Optional Cloud Run tag represents the "named-revision" to provide
   * additional fine-grained traffic routing information.
   *
   * The tag must be 1-63 characters long, and comply withRFC1035.
   *
   * Example value: "revision-0010".
   *
   * @var string
   */
  public $tag;
  /**
   * An URL mask is one of the main components of the Cloud Function.
   *
   * A template to parse  and fields from a request URL. URL mask allows for
   * routing to multiple Run services without having to create multiple network
   * endpoint groups and backend services.
   *
   * For example, request URLs foo1.domain.com/bar1 andfoo1.domain.com/bar2 can
   * be backed by the same Serverless Network Endpoint Group (NEG) with URL
   * mask.domain.com/. The URL mask will parse them to { service="bar1",
   * tag="foo1" } and { service="bar2", tag="foo2" } respectively.
   *
   * @var string
   */
  public $urlMask;

  /**
   * Cloud Run service is the main resource of Cloud Run.
   *
   * The service must be 1-63 characters long, and comply withRFC1035.
   *
   * Example value: "run-service".
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
   * Optional Cloud Run tag represents the "named-revision" to provide
   * additional fine-grained traffic routing information.
   *
   * The tag must be 1-63 characters long, and comply withRFC1035.
   *
   * Example value: "revision-0010".
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * An URL mask is one of the main components of the Cloud Function.
   *
   * A template to parse  and fields from a request URL. URL mask allows for
   * routing to multiple Run services without having to create multiple network
   * endpoint groups and backend services.
   *
   * For example, request URLs foo1.domain.com/bar1 andfoo1.domain.com/bar2 can
   * be backed by the same Serverless Network Endpoint Group (NEG) with URL
   * mask.domain.com/. The URL mask will parse them to { service="bar1",
   * tag="foo1" } and { service="bar2", tag="foo2" } respectively.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointGroupCloudRun::class, 'Google_Service_Compute_NetworkEndpointGroupCloudRun');
