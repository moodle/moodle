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

class GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfo extends \Google\Model
{
  protected $customGenAiModelInfoType = GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoCustomGenAiModelInfo::class;
  protected $customGenAiModelInfoDataType = '';
  protected $foundationGenAiModelInfoType = GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo::class;
  protected $foundationGenAiModelInfoDataType = '';

  /**
   * Information for a custom Generative AI model created by the user.
   *
   * @param GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoCustomGenAiModelInfo $customGenAiModelInfo
   */
  public function setCustomGenAiModelInfo(GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoCustomGenAiModelInfo $customGenAiModelInfo)
  {
    $this->customGenAiModelInfo = $customGenAiModelInfo;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoCustomGenAiModelInfo
   */
  public function getCustomGenAiModelInfo()
  {
    return $this->customGenAiModelInfo;
  }
  /**
   * Information for a pretrained Google-managed foundation model.
   *
   * @param GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo $foundationGenAiModelInfo
   */
  public function setFoundationGenAiModelInfo(GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo $foundationGenAiModelInfo)
  {
    $this->foundationGenAiModelInfo = $foundationGenAiModelInfo;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo
   */
  public function getFoundationGenAiModelInfo()
  {
    return $this->foundationGenAiModelInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfo::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfo');
