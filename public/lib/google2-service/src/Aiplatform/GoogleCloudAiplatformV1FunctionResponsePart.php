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

class GoogleCloudAiplatformV1FunctionResponsePart extends \Google\Model
{
  protected $fileDataType = GoogleCloudAiplatformV1FunctionResponseFileData::class;
  protected $fileDataDataType = '';
  protected $inlineDataType = GoogleCloudAiplatformV1FunctionResponseBlob::class;
  protected $inlineDataDataType = '';

  /**
   * URI based data.
   *
   * @param GoogleCloudAiplatformV1FunctionResponseFileData $fileData
   */
  public function setFileData(GoogleCloudAiplatformV1FunctionResponseFileData $fileData)
  {
    $this->fileData = $fileData;
  }
  /**
   * @return GoogleCloudAiplatformV1FunctionResponseFileData
   */
  public function getFileData()
  {
    return $this->fileData;
  }
  /**
   * Inline media bytes.
   *
   * @param GoogleCloudAiplatformV1FunctionResponseBlob $inlineData
   */
  public function setInlineData(GoogleCloudAiplatformV1FunctionResponseBlob $inlineData)
  {
    $this->inlineData = $inlineData;
  }
  /**
   * @return GoogleCloudAiplatformV1FunctionResponseBlob
   */
  public function getInlineData()
  {
    return $this->inlineData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FunctionResponsePart::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FunctionResponsePart');
