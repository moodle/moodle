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

class GoogleCloudAiplatformV1Rubric extends \Google\Model
{
  /**
   * Importance is not specified.
   */
  public const IMPORTANCE_IMPORTANCE_UNSPECIFIED = 'IMPORTANCE_UNSPECIFIED';
  /**
   * High importance.
   */
  public const IMPORTANCE_HIGH = 'HIGH';
  /**
   * Medium importance.
   */
  public const IMPORTANCE_MEDIUM = 'MEDIUM';
  /**
   * Low importance.
   */
  public const IMPORTANCE_LOW = 'LOW';
  protected $contentType = GoogleCloudAiplatformV1RubricContent::class;
  protected $contentDataType = '';
  /**
   * Optional. The relative importance of this rubric.
   *
   * @var string
   */
  public $importance;
  /**
   * Unique identifier for the rubric. This ID is used to refer to this rubric,
   * e.g., in RubricVerdict.
   *
   * @var string
   */
  public $rubricId;
  /**
   * Optional. A type designator for the rubric, which can inform how it's
   * evaluated or interpreted by systems or users. It's recommended to use
   * consistent, well-defined, upper snake_case strings. Examples:
   * "SUMMARIZATION_QUALITY", "SAFETY_HARMFUL_CONTENT", "INSTRUCTION_ADHERENCE".
   *
   * @var string
   */
  public $type;

  /**
   * Required. The actual testable criteria for the rubric.
   *
   * @param GoogleCloudAiplatformV1RubricContent $content
   */
  public function setContent(GoogleCloudAiplatformV1RubricContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricContent
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Optional. The relative importance of this rubric.
   *
   * Accepted values: IMPORTANCE_UNSPECIFIED, HIGH, MEDIUM, LOW
   *
   * @param self::IMPORTANCE_* $importance
   */
  public function setImportance($importance)
  {
    $this->importance = $importance;
  }
  /**
   * @return self::IMPORTANCE_*
   */
  public function getImportance()
  {
    return $this->importance;
  }
  /**
   * Unique identifier for the rubric. This ID is used to refer to this rubric,
   * e.g., in RubricVerdict.
   *
   * @param string $rubricId
   */
  public function setRubricId($rubricId)
  {
    $this->rubricId = $rubricId;
  }
  /**
   * @return string
   */
  public function getRubricId()
  {
    return $this->rubricId;
  }
  /**
   * Optional. A type designator for the rubric, which can inform how it's
   * evaluated or interpreted by systems or users. It's recommended to use
   * consistent, well-defined, upper snake_case strings. Examples:
   * "SUMMARIZATION_QUALITY", "SAFETY_HARMFUL_CONTENT", "INSTRUCTION_ADHERENCE".
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Rubric::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Rubric');
