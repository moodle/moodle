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

class GoogleCloudAiplatformV1ImportDataRequest extends \Google\Collection
{
  protected $collection_key = 'importConfigs';
  protected $importConfigsType = GoogleCloudAiplatformV1ImportDataConfig::class;
  protected $importConfigsDataType = 'array';

  /**
   * Required. The desired input locations. The contents of all input locations
   * will be imported in one batch.
   *
   * @param GoogleCloudAiplatformV1ImportDataConfig[] $importConfigs
   */
  public function setImportConfigs($importConfigs)
  {
    $this->importConfigs = $importConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1ImportDataConfig[]
   */
  public function getImportConfigs()
  {
    return $this->importConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ImportDataRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ImportDataRequest');
