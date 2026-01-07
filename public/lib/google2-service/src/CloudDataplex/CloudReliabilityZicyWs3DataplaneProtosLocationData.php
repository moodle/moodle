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

namespace Google\Service\CloudDataplex;

class CloudReliabilityZicyWs3DataplaneProtosLocationData extends \Google\Model
{
  protected $blobstoreLocationType = CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation::class;
  protected $blobstoreLocationDataType = '';
  protected $childAssetLocationType = CloudReliabilityZicyWs3DataplaneProtosCloudAssetComposition::class;
  protected $childAssetLocationDataType = '';
  protected $directLocationType = CloudReliabilityZicyWs3DataplaneProtosDirectLocationAssignment::class;
  protected $directLocationDataType = '';
  protected $gcpProjectProxyType = CloudReliabilityZicyWs3DataplaneProtosTenantProjectProxy::class;
  protected $gcpProjectProxyDataType = '';
  protected $placerLocationType = CloudReliabilityZicyWs3DataplaneProtosPlacerLocation::class;
  protected $placerLocationDataType = '';
  protected $spannerLocationType = CloudReliabilityZicyWs3DataplaneProtosSpannerLocation::class;
  protected $spannerLocationDataType = '';

  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation
   */
  public function setBlobstoreLocation(CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation $blobstoreLocation)
  {
    $this->blobstoreLocation = $blobstoreLocation;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation
   */
  public function getBlobstoreLocation()
  {
    return $this->blobstoreLocation;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosCloudAssetComposition
   */
  public function setChildAssetLocation(CloudReliabilityZicyWs3DataplaneProtosCloudAssetComposition $childAssetLocation)
  {
    $this->childAssetLocation = $childAssetLocation;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosCloudAssetComposition
   */
  public function getChildAssetLocation()
  {
    return $this->childAssetLocation;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosDirectLocationAssignment
   */
  public function setDirectLocation(CloudReliabilityZicyWs3DataplaneProtosDirectLocationAssignment $directLocation)
  {
    $this->directLocation = $directLocation;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosDirectLocationAssignment
   */
  public function getDirectLocation()
  {
    return $this->directLocation;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosTenantProjectProxy
   */
  public function setGcpProjectProxy(CloudReliabilityZicyWs3DataplaneProtosTenantProjectProxy $gcpProjectProxy)
  {
    $this->gcpProjectProxy = $gcpProjectProxy;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosTenantProjectProxy
   */
  public function getGcpProjectProxy()
  {
    return $this->gcpProjectProxy;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosPlacerLocation
   */
  public function setPlacerLocation(CloudReliabilityZicyWs3DataplaneProtosPlacerLocation $placerLocation)
  {
    $this->placerLocation = $placerLocation;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosPlacerLocation
   */
  public function getPlacerLocation()
  {
    return $this->placerLocation;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosSpannerLocation
   */
  public function setSpannerLocation(CloudReliabilityZicyWs3DataplaneProtosSpannerLocation $spannerLocation)
  {
    $this->spannerLocation = $spannerLocation;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosSpannerLocation
   */
  public function getSpannerLocation()
  {
    return $this->spannerLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudReliabilityZicyWs3DataplaneProtosLocationData::class, 'Google_Service_CloudDataplex_CloudReliabilityZicyWs3DataplaneProtosLocationData');
