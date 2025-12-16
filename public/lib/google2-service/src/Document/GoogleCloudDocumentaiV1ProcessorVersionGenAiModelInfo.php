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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo extends \Google\Model
{
  protected $customGenAiModelInfoType = GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo::class;
  protected $customGenAiModelInfoDataType = '';
  protected $foundationGenAiModelInfoType = GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo::class;
  protected $foundationGenAiModelInfoDataType = '';

  /**
   * Information for a custom Generative AI model created by the user.
   *
   * @param GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo $customGenAiModelInfo
   */
  public function setCustomGenAiModelInfo(GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo $customGenAiModelInfo)
  {
    $this->customGenAiModelInfo = $customGenAiModelInfo;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo
   */
  public function getCustomGenAiModelInfo()
  {
    return $this->customGenAiModelInfo;
  }
  /**
   * Information for a pretrained Google-managed foundation model.
   *
   * @param GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo $foundationGenAiModelInfo
   */
  public function setFoundationGenAiModelInfo(GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo $foundationGenAiModelInfo)
  {
    $this->foundationGenAiModelInfo = $foundationGenAiModelInfo;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo
   */
  public function getFoundationGenAiModelInfo()
  {
    return $this->foundationGenAiModelInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo');
