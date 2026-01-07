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

class GoogleCloudAiplatformV1ExportFeatureValuesRequest extends \Google\Collection
{
  protected $collection_key = 'settings';
  protected $destinationType = GoogleCloudAiplatformV1FeatureValueDestination::class;
  protected $destinationDataType = '';
  protected $featureSelectorType = GoogleCloudAiplatformV1FeatureSelector::class;
  protected $featureSelectorDataType = '';
  protected $fullExportType = GoogleCloudAiplatformV1ExportFeatureValuesRequestFullExport::class;
  protected $fullExportDataType = '';
  protected $settingsType = GoogleCloudAiplatformV1DestinationFeatureSetting::class;
  protected $settingsDataType = 'array';
  protected $snapshotExportType = GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport::class;
  protected $snapshotExportDataType = '';

  /**
   * Required. Specifies destination location and format.
   *
   * @param GoogleCloudAiplatformV1FeatureValueDestination $destination
   */
  public function setDestination(GoogleCloudAiplatformV1FeatureValueDestination $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureValueDestination
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Required. Selects Features to export values of.
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
   * Exports all historical values of all entities of the EntityType within a
   * time range
   *
   * @param GoogleCloudAiplatformV1ExportFeatureValuesRequestFullExport $fullExport
   */
  public function setFullExport(GoogleCloudAiplatformV1ExportFeatureValuesRequestFullExport $fullExport)
  {
    $this->fullExport = $fullExport;
  }
  /**
   * @return GoogleCloudAiplatformV1ExportFeatureValuesRequestFullExport
   */
  public function getFullExport()
  {
    return $this->fullExport;
  }
  /**
   * Per-Feature export settings.
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
  /**
   * Exports the latest Feature values of all entities of the EntityType within
   * a time range.
   *
   * @param GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport $snapshotExport
   */
  public function setSnapshotExport(GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport $snapshotExport)
  {
    $this->snapshotExport = $snapshotExport;
  }
  /**
   * @return GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport
   */
  public function getSnapshotExport()
  {
    return $this->snapshotExport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportFeatureValuesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportFeatureValuesRequest');
