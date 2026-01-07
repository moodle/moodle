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

class GoogleCloudAiplatformV1FileData extends \Google\Model
{
  /**
   * Optional. The display name of the file. Used to provide a label or filename
   * to distinguish files. This field is only returned in `PromptMessage` for
   * prompt management. It is used in the Gemini calls only when server side
   * tools (`code_execution`, `google_search`, and `url_context`) are enabled.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The URI of the file in Google Cloud Storage.
   *
   * @var string
   */
  public $fileUri;
  /**
   * Required. The IANA standard MIME type of the source data.
   *
   * @var string
   */
  public $mimeType;

  /**
   * Optional. The display name of the file. Used to provide a label or filename
   * to distinguish files. This field is only returned in `PromptMessage` for
   * prompt management. It is used in the Gemini calls only when server side
   * tools (`code_execution`, `google_search`, and `url_context`) are enabled.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The URI of the file in Google Cloud Storage.
   *
   * @param string $fileUri
   */
  public function setFileUri($fileUri)
  {
    $this->fileUri = $fileUri;
  }
  /**
   * @return string
   */
  public function getFileUri()
  {
    return $this->fileUri;
  }
  /**
   * Required. The IANA standard MIME type of the source data.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FileData::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FileData');
