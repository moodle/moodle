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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3CompareVersionsRequest extends \Google\Model
{
  /**
   * The language to compare the flow versions for. If not specified, the
   * agent's default language is used. [Many
   * languages](https://cloud.google.com/dialogflow/docs/reference/language) are
   * supported. Note: languages must be enabled in the agent before they can be
   * used.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Required. Name of the target flow version to compare with the base version.
   * Use version ID `0` to indicate the draft version of the specified flow.
   * Format: `projects//locations//agents//flows//versions/`.
   *
   * @var string
   */
  public $targetVersion;

  /**
   * The language to compare the flow versions for. If not specified, the
   * agent's default language is used. [Many
   * languages](https://cloud.google.com/dialogflow/docs/reference/language) are
   * supported. Note: languages must be enabled in the agent before they can be
   * used.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Required. Name of the target flow version to compare with the base version.
   * Use version ID `0` to indicate the draft version of the specified flow.
   * Format: `projects//locations//agents//flows//versions/`.
   *
   * @param string $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return string
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3CompareVersionsRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3CompareVersionsRequest');
