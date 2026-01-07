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

namespace Google\Service\NetAppFiles;

class LocationData extends \Google\Model
{
  protected $blobstoreLocationType = BlobstoreLocation::class;
  protected $blobstoreLocationDataType = '';
  protected $childAssetLocationType = CloudAssetComposition::class;
  protected $childAssetLocationDataType = '';
  protected $directLocationType = DirectLocationAssignment::class;
  protected $directLocationDataType = '';
  protected $gcpProjectProxyType = TenantProjectProxy::class;
  protected $gcpProjectProxyDataType = '';
  protected $placerLocationType = PlacerLocation::class;
  protected $placerLocationDataType = '';
  protected $spannerLocationType = SpannerLocation::class;
  protected $spannerLocationDataType = '';

  /**
   * @param BlobstoreLocation
   */
  public function setBlobstoreLocation(BlobstoreLocation $blobstoreLocation)
  {
    $this->blobstoreLocation = $blobstoreLocation;
  }
  /**
   * @return BlobstoreLocation
   */
  public function getBlobstoreLocation()
  {
    return $this->blobstoreLocation;
  }
  /**
   * @param CloudAssetComposition
   */
  public function setChildAssetLocation(CloudAssetComposition $childAssetLocation)
  {
    $this->childAssetLocation = $childAssetLocation;
  }
  /**
   * @return CloudAssetComposition
   */
  public function getChildAssetLocation()
  {
    return $this->childAssetLocation;
  }
  /**
   * @param DirectLocationAssignment
   */
  public function setDirectLocation(DirectLocationAssignment $directLocation)
  {
    $this->directLocation = $directLocation;
  }
  /**
   * @return DirectLocationAssignment
   */
  public function getDirectLocation()
  {
    return $this->directLocation;
  }
  /**
   * @param TenantProjectProxy
   */
  public function setGcpProjectProxy(TenantProjectProxy $gcpProjectProxy)
  {
    $this->gcpProjectProxy = $gcpProjectProxy;
  }
  /**
   * @return TenantProjectProxy
   */
  public function getGcpProjectProxy()
  {
    return $this->gcpProjectProxy;
  }
  /**
   * @param PlacerLocation
   */
  public function setPlacerLocation(PlacerLocation $placerLocation)
  {
    $this->placerLocation = $placerLocation;
  }
  /**
   * @return PlacerLocation
   */
  public function getPlacerLocation()
  {
    return $this->placerLocation;
  }
  /**
   * @param SpannerLocation
   */
  public function setSpannerLocation(SpannerLocation $spannerLocation)
  {
    $this->spannerLocation = $spannerLocation;
  }
  /**
   * @return SpannerLocation
   */
  public function getSpannerLocation()
  {
    return $this->spannerLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationData::class, 'Google_Service_NetAppFiles_LocationData');
