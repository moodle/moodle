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

class CloudRunRevisionInfo extends \Google\Model
{
  /**
   * Name of a Cloud Run revision.
   *
   * @var string
   */
  public $displayName;
  /**
   * Location in which this revision is deployed.
   *
   * @var string
   */
  public $location;
  /**
   * URI of Cloud Run service this revision belongs to.
   *
   * @var string
   */
  public $serviceUri;
  /**
   * URI of a Cloud Run revision.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a Cloud Run revision.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Location in which this revision is deployed.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * URI of Cloud Run service this revision belongs to.
   *
   * @param string $serviceUri
   */
  public function setServiceUri($serviceUri)
  {
    $this->serviceUri = $serviceUri;
  }
  /**
   * @return string
   */
  public function getServiceUri()
  {
    return $this->serviceUri;
  }
  /**
   * URI of a Cloud Run revision.
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
class_alias(CloudRunRevisionInfo::class, 'Google_Service_NetworkManagement_CloudRunRevisionInfo');
