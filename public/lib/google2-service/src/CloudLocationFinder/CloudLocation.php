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

namespace Google\Service\CloudLocationFinder;

class CloudLocation extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const CLOUD_LOCATION_TYPE_CLOUD_LOCATION_TYPE_UNSPECIFIED = 'CLOUD_LOCATION_TYPE_UNSPECIFIED';
  /**
   * CloudLocation type for region.
   */
  public const CLOUD_LOCATION_TYPE_CLOUD_LOCATION_TYPE_REGION = 'CLOUD_LOCATION_TYPE_REGION';
  /**
   * CloudLocation type for zone.
   */
  public const CLOUD_LOCATION_TYPE_CLOUD_LOCATION_TYPE_ZONE = 'CLOUD_LOCATION_TYPE_ZONE';
  /**
   * CloudLocation type for region extension.
   */
  public const CLOUD_LOCATION_TYPE_CLOUD_LOCATION_TYPE_REGION_EXTENSION = 'CLOUD_LOCATION_TYPE_REGION_EXTENSION';
  /**
   * CloudLocation type for Google Distributed Cloud Connected Zone.
   */
  public const CLOUD_LOCATION_TYPE_CLOUD_LOCATION_TYPE_GDCC_ZONE = 'CLOUD_LOCATION_TYPE_GDCC_ZONE';
  /**
   * Unspecified type.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_UNSPECIFIED = 'CLOUD_PROVIDER_UNSPECIFIED';
  /**
   * Cloud provider type for Google Cloud.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_GCP = 'CLOUD_PROVIDER_GCP';
  /**
   * Cloud provider type for AWS.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_AWS = 'CLOUD_PROVIDER_AWS';
  /**
   * Cloud provider type for Azure.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_AZURE = 'CLOUD_PROVIDER_AZURE';
  /**
   * Cloud provider type for OCI.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_OCI = 'CLOUD_PROVIDER_OCI';
  /**
   * Optional. The carbon free energy percentage of the cloud location. This
   * represents the average percentage of time customers' application will be
   * running on carbon-free energy. See
   * https://cloud.google.com/sustainability/region-carbon for more details.
   * There is a difference between default value 0 and unset value. 0 means the
   * carbon free energy percentage is 0%, while unset value means the carbon
   * footprint data is not available.
   *
   * @var float
   */
  public $carbonFreeEnergyPercentage;
  /**
   * Optional. The type of the cloud location.
   *
   * @var string
   */
  public $cloudLocationType;
  /**
   * Optional. The provider of the cloud location. Values can be Google Cloud or
   * third-party providers, including AWS, Azure, or Oracle Cloud
   * Infrastructure.
   *
   * @var string
   */
  public $cloudProvider;
  /**
   * Output only. The containing cloud location in the strict nesting hierarchy.
   * For example, the containing cloud location of a zone is a region.
   *
   * @var string
   */
  public $containingCloudLocation;
  /**
   * Optional. The human-readable name of the cloud location. Example: us-
   * east-2, us-east1.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. Name of the cloud location. Unique name of the cloud location
   * including project and location using the form: `projects/{project_id}/locat
   * ions/{location}/cloudLocations/{cloud_location}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The two-letter ISO 3166-1 alpha-2 code of the cloud location.
   * Examples: US, JP, KR.
   *
   * @var string
   */
  public $territoryCode;

  /**
   * Optional. The carbon free energy percentage of the cloud location. This
   * represents the average percentage of time customers' application will be
   * running on carbon-free energy. See
   * https://cloud.google.com/sustainability/region-carbon for more details.
   * There is a difference between default value 0 and unset value. 0 means the
   * carbon free energy percentage is 0%, while unset value means the carbon
   * footprint data is not available.
   *
   * @param float $carbonFreeEnergyPercentage
   */
  public function setCarbonFreeEnergyPercentage($carbonFreeEnergyPercentage)
  {
    $this->carbonFreeEnergyPercentage = $carbonFreeEnergyPercentage;
  }
  /**
   * @return float
   */
  public function getCarbonFreeEnergyPercentage()
  {
    return $this->carbonFreeEnergyPercentage;
  }
  /**
   * Optional. The type of the cloud location.
   *
   * Accepted values: CLOUD_LOCATION_TYPE_UNSPECIFIED,
   * CLOUD_LOCATION_TYPE_REGION, CLOUD_LOCATION_TYPE_ZONE,
   * CLOUD_LOCATION_TYPE_REGION_EXTENSION, CLOUD_LOCATION_TYPE_GDCC_ZONE
   *
   * @param self::CLOUD_LOCATION_TYPE_* $cloudLocationType
   */
  public function setCloudLocationType($cloudLocationType)
  {
    $this->cloudLocationType = $cloudLocationType;
  }
  /**
   * @return self::CLOUD_LOCATION_TYPE_*
   */
  public function getCloudLocationType()
  {
    return $this->cloudLocationType;
  }
  /**
   * Optional. The provider of the cloud location. Values can be Google Cloud or
   * third-party providers, including AWS, Azure, or Oracle Cloud
   * Infrastructure.
   *
   * Accepted values: CLOUD_PROVIDER_UNSPECIFIED, CLOUD_PROVIDER_GCP,
   * CLOUD_PROVIDER_AWS, CLOUD_PROVIDER_AZURE, CLOUD_PROVIDER_OCI
   *
   * @param self::CLOUD_PROVIDER_* $cloudProvider
   */
  public function setCloudProvider($cloudProvider)
  {
    $this->cloudProvider = $cloudProvider;
  }
  /**
   * @return self::CLOUD_PROVIDER_*
   */
  public function getCloudProvider()
  {
    return $this->cloudProvider;
  }
  /**
   * Output only. The containing cloud location in the strict nesting hierarchy.
   * For example, the containing cloud location of a zone is a region.
   *
   * @param string $containingCloudLocation
   */
  public function setContainingCloudLocation($containingCloudLocation)
  {
    $this->containingCloudLocation = $containingCloudLocation;
  }
  /**
   * @return string
   */
  public function getContainingCloudLocation()
  {
    return $this->containingCloudLocation;
  }
  /**
   * Optional. The human-readable name of the cloud location. Example: us-
   * east-2, us-east1.
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
   * Identifier. Name of the cloud location. Unique name of the cloud location
   * including project and location using the form: `projects/{project_id}/locat
   * ions/{location}/cloudLocations/{cloud_location}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The two-letter ISO 3166-1 alpha-2 code of the cloud location.
   * Examples: US, JP, KR.
   *
   * @param string $territoryCode
   */
  public function setTerritoryCode($territoryCode)
  {
    $this->territoryCode = $territoryCode;
  }
  /**
   * @return string
   */
  public function getTerritoryCode()
  {
    return $this->territoryCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudLocation::class, 'Google_Service_CloudLocationFinder_CloudLocation');
