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

class GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings extends \Google\Collection
{
  protected $collection_key = 'promptTemplates';
  protected $promptTemplatesType = GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettingsPromptTemplate::class;
  protected $promptTemplatesDataType = 'array';
  /**
   * Display name of the selected prompt.
   *
   * @var string
   */
  public $selectedPrompt;

  /**
   * Stored prompts that can be selected, for example default templates like
   * "conservative" or "chatty", or user defined ones.
   *
   * @param GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettingsPromptTemplate[] $promptTemplates
   */
  public function setPromptTemplates($promptTemplates)
  {
    $this->promptTemplates = $promptTemplates;
  }
  /**
   * @return GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettingsPromptTemplate[]
   */
  public function getPromptTemplates()
  {
    return $this->promptTemplates;
  }
  /**
   * Display name of the selected prompt.
   *
   * @param string $selectedPrompt
   */
  public function setSelectedPrompt($selectedPrompt)
  {
    $this->selectedPrompt = $selectedPrompt;
  }
  /**
   * @return string
   */
  public function getSelectedPrompt()
  {
    return $this->selectedPrompt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings');
