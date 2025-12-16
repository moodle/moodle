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

class GoogleCloudAiplatformV1RubricBasedInstructionFollowingResult extends \Google\Collection
{
  protected $collection_key = 'rubricCritiqueResults';
  protected $rubricCritiqueResultsType = GoogleCloudAiplatformV1RubricCritiqueResult::class;
  protected $rubricCritiqueResultsDataType = 'array';
  /**
   * Output only. Overall score for the instruction following.
   *
   * @var float
   */
  public $score;

  /**
   * Output only. List of per rubric critique results.
   *
   * @param GoogleCloudAiplatformV1RubricCritiqueResult[] $rubricCritiqueResults
   */
  public function setRubricCritiqueResults($rubricCritiqueResults)
  {
    $this->rubricCritiqueResults = $rubricCritiqueResults;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricCritiqueResult[]
   */
  public function getRubricCritiqueResults()
  {
    return $this->rubricCritiqueResults;
  }
  /**
   * Output only. Overall score for the instruction following.
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricBasedInstructionFollowingResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricBasedInstructionFollowingResult');
