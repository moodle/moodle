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

class GoogleCloudAiplatformV1UploadRagFileRequest extends \Google\Model
{
  protected $ragFileType = GoogleCloudAiplatformV1RagFile::class;
  protected $ragFileDataType = '';
  protected $uploadRagFileConfigType = GoogleCloudAiplatformV1UploadRagFileConfig::class;
  protected $uploadRagFileConfigDataType = '';

  /**
   * Required. The RagFile to upload.
   *
   * @param GoogleCloudAiplatformV1RagFile $ragFile
   */
  public function setRagFile(GoogleCloudAiplatformV1RagFile $ragFile)
  {
    $this->ragFile = $ragFile;
  }
  /**
   * @return GoogleCloudAiplatformV1RagFile
   */
  public function getRagFile()
  {
    return $this->ragFile;
  }
  /**
   * Required. The config for the RagFiles to be uploaded into the RagCorpus.
   * VertexRagDataService.UploadRagFile.
   *
   * @param GoogleCloudAiplatformV1UploadRagFileConfig $uploadRagFileConfig
   */
  public function setUploadRagFileConfig(GoogleCloudAiplatformV1UploadRagFileConfig $uploadRagFileConfig)
  {
    $this->uploadRagFileConfig = $uploadRagFileConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1UploadRagFileConfig
   */
  public function getUploadRagFileConfig()
  {
    return $this->uploadRagFileConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UploadRagFileRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UploadRagFileRequest');
