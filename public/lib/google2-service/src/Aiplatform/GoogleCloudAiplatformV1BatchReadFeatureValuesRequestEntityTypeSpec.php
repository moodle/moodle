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

class GoogleCloudAiplatformV1BatchReadFeatureValuesRequestEntityTypeSpec extends \Google\Collection
{
  protected $collection_key = 'settings';
  /**
   * Required. ID of the EntityType to select Features. The EntityType id is the
   * entity_type_id specified during EntityType creation.
   *
   * @var string
   */
  public $entityTypeId;
  protected $featureSelectorType = GoogleCloudAiplatformV1FeatureSelector::class;
  protected $featureSelectorDataType = '';
  protected $settingsType = GoogleCloudAiplatformV1DestinationFeatureSetting::class;
  protected $settingsDataType = 'array';

  /**
   * Required. ID of the EntityType to select Features. The EntityType id is the
   * entity_type_id specified during EntityType creation.
   *
   * @param string $entityTypeId
   */
  public function setEntityTypeId($entityTypeId)
  {
    $this->entityTypeId = $entityTypeId;
  }
  /**
   * @return string
   */
  public function getEntityTypeId()
  {
    return $this->entityTypeId;
  }
  /**
   * Required. Selectors choosing which Feature values to read from the
   * EntityType.
   *
   * @param GoogleCloudAiplatformV1FeatureSelector $featureSelector
   */
  public function setFeatureSelector(GoogleCloudAiplatformV1FeatureSelector $featureSelector)
  {
    $this->featureSelector = $featureSelector;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureSelector
   */
  public function getFeatureSelector()
  {
    return $this->featureSelector;
  }
  /**
   * Per-Feature settings for the batch read.
   *
   * @param GoogleCloudAiplatformV1DestinationFeatureSetting[] $settings
   */
  public function setSettings($settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return GoogleCloudAiplatformV1DestinationFeatureSetting[]
   */
  public function getSettings()
  {
    return $this->settings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BatchReadFeatureValuesRequestEntityTypeSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BatchReadFeatureValuesRequestEntityTypeSpec');
