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

class GKE extends \Google\Model
{
  /**
   * Required. The name of the cluster the GKE service is running in. The
   * cluster must be running in the same project as the trigger being created.
   *
   * @var string
   */
  public $cluster;
  /**
   * Required. The name of the Google Compute Engine in which the cluster
   * resides, which can either be compute zone (for example, us-central1-a) for
   * the zonal clusters or region (for example, us-central1) for regional
   * clusters.
   *
   * @var string
   */
  public $location;
  /**
   * Required. The namespace the GKE service is running in.
   *
   * @var string
   */
  public $namespace;
  /**
   * Optional. The relative path on the GKE service the events should be sent
   * to. The value must conform to the definition of a URI path segment (section
   * 3.3 of RFC2396). Examples: "/route", "route", "route/subroute".
   *
   * @var string
   */
  public $path;
  /**
   * Required. Name of the GKE service.
   *
   * @var string
   */
  public $service;

  /**
   * Required. The name of the cluster the GKE service is running in. The
   * cluster must be running in the same project as the trigger being created.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Required. The name of the Google Compute Engine in which the cluster
   * resides, which can either be compute zone (for example, us-central1-a) for
   * the zonal clusters or region (for example, us-central1) for regional
   * clusters.
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
   * Required. The namespace the GKE service is running in.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
  /**
   * Optional. The relative path on the GKE service the events should be sent
   * to. The value must conform to the definition of a URI path segment (section
   * 3.3 of RFC2396). Examples: "/route", "route", "route/subroute".
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
   * Required. Name of the GKE service.
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
class_alias(GKE::class, 'Google_Service_Eventarc_GKE');
