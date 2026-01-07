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

class GoogleCloudAiplatformV1CandidateResponse extends \Google\Model
{
  /**
   * Required. The name of the candidate that produced the response.
   *
   * @var string
   */
  public $candidate;
  /**
   * Text response.
   *
   * @var string
   */
  public $text;
  /**
   * Fields and values that can be used to populate the response template.
   *
   * @var array
   */
  public $value;

  /**
   * Required. The name of the candidate that produced the response.
   *
   * @param string $candidate
   */
  public function setCandidate($candidate)
  {
    $this->candidate = $candidate;
  }
  /**
   * @return string
   */
  public function getCandidate()
  {
    return $this->candidate;
  }
  /**
   * Text response.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Fields and values that can be used to populate the response template.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CandidateResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CandidateResponse');
