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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource extends \Google\Collection
{
  protected $collection_key = 'featureGroups';
  protected $featureGroupsType = GoogleCloudAiplatformV1FeatureViewFeatureRegistrySourceFeatureGroup::class;
  protected $featureGroupsDataType = 'array';
  /**
   * Optional. The project number of the parent project of the Feature Groups.
   *
   * @var string
   */
  public $projectNumber;

  /**
   * Required. List of features that need to be synced to Online Store.
   *
   * @param GoogleCloudAiplatformV1FeatureViewFeatureRegistrySourceFeatureGroup[] $featureGroups
   */
  public function setFeatureGroups($featureGroups)
  {
    $this->featureGroups = $featureGroups;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewFeatureRegistrySourceFeatureGroup[]
   */
  public function getFeatureGroups()
  {
    return $this->featureGroups;
  }
  /**
   * Optional. The project number of the parent project of the Feature Groups.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource');
