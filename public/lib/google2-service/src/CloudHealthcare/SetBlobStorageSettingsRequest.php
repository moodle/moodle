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

namespace Google\Service\CloudHealthcare;

class SetBlobStorageSettingsRequest extends \Google\Model
{
  protected $blobStorageSettingsType = BlobStorageSettings::class;
  protected $blobStorageSettingsDataType = '';
  protected $filterConfigType = DicomFilterConfig::class;
  protected $filterConfigDataType = '';

  /**
   * The blob storage settings to update for the specified resources. Only
   * fields listed in `update_mask` are applied.
   *
   * @param BlobStorageSettings $blobStorageSettings
   */
  public function setBlobStorageSettings(BlobStorageSettings $blobStorageSettings)
  {
    $this->blobStorageSettings = $blobStorageSettings;
  }
  /**
   * @return BlobStorageSettings
   */
  public function getBlobStorageSettings()
  {
    return $this->blobStorageSettings;
  }
  /**
   * Optional. A filter configuration. If `filter_config` is specified, set the
   * value of `resource` to the resource name of a DICOM store in the format `pr
   * ojects/{projectID}/locations/{locationID}/datasets/{datasetID}/dicomStores/
   * {dicomStoreID}`.
   *
   * @param DicomFilterConfig $filterConfig
   */
  public function setFilterConfig(DicomFilterConfig $filterConfig)
  {
    $this->filterConfig = $filterConfig;
  }
  /**
   * @return DicomFilterConfig
   */
  public function getFilterConfig()
  {
    return $this->filterConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetBlobStorageSettingsRequest::class, 'Google_Service_CloudHealthcare_SetBlobStorageSettingsRequest');
