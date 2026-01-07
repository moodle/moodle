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

class GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpec extends \Google\Model
{
  protected $configsType = GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecSliceConfig::class;
  protected $configsDataType = 'map';

  /**
   * Mapping configuration for this SliceSpec. The key is the name of the
   * feature. By default, the key will be prefixed by "instance" as a dictionary
   * prefix for Vertex Batch Predictions output format.
   *
   * @param GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecSliceConfig[] $configs
   */
  public function setConfigs($configs)
  {
    $this->configs = $configs;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecSliceConfig[]
   */
  public function getConfigs()
  {
    return $this->configs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpec');
