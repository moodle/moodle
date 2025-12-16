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

class GoogleCloudAiplatformV1RubricCritiqueResult extends \Google\Model
{
  /**
   * Output only. Rubric to be evaluated.
   *
   * @var string
   */
  public $rubric;
  /**
   * Output only. Verdict for the rubric - true if the rubric is met, false
   * otherwise.
   *
   * @var bool
   */
  public $verdict;

  /**
   * Output only. Rubric to be evaluated.
   *
   * @param string $rubric
   */
  public function setRubric($rubric)
  {
    $this->rubric = $rubric;
  }
  /**
   * @return string
   */
  public function getRubric()
  {
    return $this->rubric;
  }
  /**
   * Output only. Verdict for the rubric - true if the rubric is met, false
   * otherwise.
   *
   * @param bool $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return bool
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricCritiqueResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricCritiqueResult');
