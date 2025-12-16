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

namespace Google\Service\Container;

class AdditionalIPRangesConfig extends \Google\Collection
{
  protected $collection_key = 'podIpv4RangeNames';
  /**
   * List of secondary ranges names within this subnetwork that can be used for
   * pod IPs. Example1: gke-pod-range1 Example2: gke-pod-range1,gke-pod-range2
   *
   * @var string[]
   */
  public $podIpv4RangeNames;
  /**
   * Name of the subnetwork. This can be the full path of the subnetwork or just
   * the name. Example1: my-subnet Example2: projects/gke-project/regions/us-
   * central1/subnetworks/my-subnet
   *
   * @var string
   */
  public $subnetwork;

  /**
   * List of secondary ranges names within this subnetwork that can be used for
   * pod IPs. Example1: gke-pod-range1 Example2: gke-pod-range1,gke-pod-range2
   *
   * @param string[] $podIpv4RangeNames
   */
  public function setPodIpv4RangeNames($podIpv4RangeNames)
  {
    $this->podIpv4RangeNames = $podIpv4RangeNames;
  }
  /**
   * @return string[]
   */
  public function getPodIpv4RangeNames()
  {
    return $this->podIpv4RangeNames;
  }
  /**
   * Name of the subnetwork. This can be the full path of the subnetwork or just
   * the name. Example1: my-subnet Example2: projects/gke-project/regions/us-
   * central1/subnetworks/my-subnet
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdditionalIPRangesConfig::class, 'Google_Service_Container_AdditionalIPRangesConfig');
