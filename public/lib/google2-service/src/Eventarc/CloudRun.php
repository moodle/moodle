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

class CloudRun extends \Google\Model
{
  /**
   * Optional. The relative path on the Cloud Run service the events should be
   * sent to. The value must conform to the definition of a URI path segment
   * (section 3.3 of RFC2396). Examples: "/route", "route", "route/subroute".
   *
   * @var string
   */
  public $path;
  /**
   * Required. The region the Cloud Run service is deployed in.
   *
   * @var string
   */
  public $region;
  /**
   * Required. The name of the Cloud Run service being addressed. See
   * https://cloud.google.com/run/docs/reference/rest/v1/namespaces.services.
   * Only services located in the same project as the trigger object can be
   * addressed.
   *
   * @var string
   */
  public $service;

  /**
   * Optional. The relative path on the Cloud Run service the events should be
   * sent to. The value must conform to the definition of a URI path segment
   * (section 3.3 of RFC2396). Examples: "/route", "route", "route/subroute".
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Required. The region the Cloud Run service is deployed in.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Required. The name of the Cloud Run service being addressed. See
   * https://cloud.google.com/run/docs/reference/rest/v1/namespaces.services.
   * Only services located in the same project as the trigger object can be
   * addressed.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudRun::class, 'Google_Service_Eventarc_CloudRun');
