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

class GoogleCloudDialogflowCxV3SafetySettingsRaiSettingsCategoryFilter extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const CATEGORY_SAFETY_CATEGORY_UNSPECIFIED = 'SAFETY_CATEGORY_UNSPECIFIED';
  /**
   * Dangerous content.
   */
  public const CATEGORY_DANGEROUS_CONTENT = 'DANGEROUS_CONTENT';
  /**
   * Hate speech.
   */
  public const CATEGORY_HATE_SPEECH = 'HATE_SPEECH';
  /**
   * Harassment.
   */
  public const CATEGORY_HARASSMENT = 'HARASSMENT';
  /**
   * Sexually explicit content.
   */
  public const CATEGORY_SEXUALLY_EXPLICIT_CONTENT = 'SEXUALLY_EXPLICIT_CONTENT';
  /**
   * Unspecified -- uses default sensitivity levels.
   */
  public const FILTER_LEVEL_SAFETY_FILTER_LEVEL_UNSPECIFIED = 'SAFETY_FILTER_LEVEL_UNSPECIFIED';
  /**
   * Block no text -- effectively disables the category.
   */
  public const FILTER_LEVEL_BLOCK_NONE = 'BLOCK_NONE';
  /**
   * Block a few suspicious texts.
   */
  public const FILTER_LEVEL_BLOCK_FEW = 'BLOCK_FEW';
  /**
   * Block some suspicious texts.
   */
  public const FILTER_LEVEL_BLOCK_SOME = 'BLOCK_SOME';
  /**
   * Block most suspicious texts.
   */
  public const FILTER_LEVEL_BLOCK_MOST = 'BLOCK_MOST';
  /**
   * RAI category to configure.
   *
   * @var string
   */
  public $category;
  /**
   * Blocking sensitivity level to configure for the RAI category.
   *
   * @var string
   */
  public $filterLevel;

  /**
   * RAI category to configure.
   *
   * Accepted values: SAFETY_CATEGORY_UNSPECIFIED, DANGEROUS_CONTENT,
   * HATE_SPEECH, HARASSMENT, SEXUALLY_EXPLICIT_CONTENT
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Blocking sensitivity level to configure for the RAI category.
   *
   * Accepted values: SAFETY_FILTER_LEVEL_UNSPECIFIED, BLOCK_NONE, BLOCK_FEW,
   * BLOCK_SOME, BLOCK_MOST
   *
   * @param self::FILTER_LEVEL_* $filterLevel
   */
  public function setFilterLevel($filterLevel)
  {
    $this->filterLevel = $filterLevel;
  }
  /**
   * @return self::FILTER_LEVEL_*
   */
  public function getFilterLevel()
  {
    return $this->filterLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3SafetySettingsRaiSettingsCategoryFilter::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3SafetySettingsRaiSettingsCategoryFilter');
