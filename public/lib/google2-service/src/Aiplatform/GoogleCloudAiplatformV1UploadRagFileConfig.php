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

class GoogleCloudAiplatformV1UploadRagFileConfig extends \Google\Model
{
  protected $ragFileTransformationConfigType = GoogleCloudAiplatformV1RagFileTransformationConfig::class;
  protected $ragFileTransformationConfigDataType = '';

  /**
   * Specifies the transformation config for RagFiles.
   *
   * @param GoogleCloudAiplatformV1RagFileTransformationConfig $ragFileTransformationConfig
   */
  public function setRagFileTransformationConfig(GoogleCloudAiplatformV1RagFileTransformationConfig $ragFileTransformationConfig)
  {
    $this->ragFileTransformationConfig = $ragFileTransformationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagFileTransformationConfig
   */
  public function getRagFileTransformationConfig()
  {
    return $this->ragFileTransformationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UploadRagFileConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UploadRagFileConfig');
