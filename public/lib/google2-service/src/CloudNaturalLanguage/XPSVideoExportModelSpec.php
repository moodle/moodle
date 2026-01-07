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

namespace Google\Service\CloudNaturalLanguage;

class XPSVideoExportModelSpec extends \Google\Collection
{
  protected $collection_key = 'exportModelOutputConfig';
  protected $exportModelOutputConfigType = XPSExportModelOutputConfig::class;
  protected $exportModelOutputConfigDataType = 'array';

  /**
   * Contains the model format and internal location of the model files to be
   * exported/downloaded. Use the Google Cloud Storage bucket name which is
   * provided via TrainRequest.gcs_bucket_name to store the model files.
   *
   * @param XPSExportModelOutputConfig[] $exportModelOutputConfig
   */
  public function setExportModelOutputConfig($exportModelOutputConfig)
  {
    $this->exportModelOutputConfig = $exportModelOutputConfig;
  }
  /**
   * @return XPSExportModelOutputConfig[]
   */
  public function getExportModelOutputConfig()
  {
    return $this->exportModelOutputConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVideoExportModelSpec::class, 'Google_Service_CloudNaturalLanguage_XPSVideoExportModelSpec');
