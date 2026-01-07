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

namespace Google\Service\CloudTalentSolution;

class CompletionResult extends \Google\Model
{
  /**
   * Default value.
   */
  public const TYPE_COMPLETION_TYPE_UNSPECIFIED = 'COMPLETION_TYPE_UNSPECIFIED';
  /**
   * Suggest job titles for jobs autocomplete. For CompletionType.JOB_TITLE
   * type, only open jobs with the same language_codes are returned.
   */
  public const TYPE_JOB_TITLE = 'JOB_TITLE';
  /**
   * Suggest company names for jobs autocomplete. For
   * CompletionType.COMPANY_NAME type, only companies having open jobs with the
   * same language_codes are returned.
   */
  public const TYPE_COMPANY_NAME = 'COMPANY_NAME';
  /**
   * Suggest both job titles and company names for jobs autocomplete. For
   * CompletionType.COMBINED type, only open jobs with the same language_codes
   * or companies having open jobs with the same language_codes are returned.
   */
  public const TYPE_COMBINED = 'COMBINED';
  /**
   * The URI of the company image for COMPANY_NAME.
   *
   * @var string
   */
  public $imageUri;
  /**
   * The suggestion for the query.
   *
   * @var string
   */
  public $suggestion;
  /**
   * The completion topic.
   *
   * @var string
   */
  public $type;

  /**
   * The URI of the company image for COMPANY_NAME.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * The suggestion for the query.
   *
   * @param string $suggestion
   */
  public function setSuggestion($suggestion)
  {
    $this->suggestion = $suggestion;
  }
  /**
   * @return string
   */
  public function getSuggestion()
  {
    return $this->suggestion;
  }
  /**
   * The completion topic.
   *
   * Accepted values: COMPLETION_TYPE_UNSPECIFIED, JOB_TITLE, COMPANY_NAME,
   * COMBINED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompletionResult::class, 'Google_Service_CloudTalentSolution_CompletionResult');
