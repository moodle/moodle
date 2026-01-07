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

class GoogleCloudAiplatformV1LogprobsResult extends \Google\Collection
{
  protected $collection_key = 'topCandidates';
  protected $chosenCandidatesType = GoogleCloudAiplatformV1LogprobsResultCandidate::class;
  protected $chosenCandidatesDataType = 'array';
  protected $topCandidatesType = GoogleCloudAiplatformV1LogprobsResultTopCandidates::class;
  protected $topCandidatesDataType = 'array';

  /**
   * A list of the chosen candidate tokens at each decoding step. The length of
   * this list is equal to the total number of decoding steps. Note that the
   * chosen candidate might not be in `top_candidates`.
   *
   * @param GoogleCloudAiplatformV1LogprobsResultCandidate[] $chosenCandidates
   */
  public function setChosenCandidates($chosenCandidates)
  {
    $this->chosenCandidates = $chosenCandidates;
  }
  /**
   * @return GoogleCloudAiplatformV1LogprobsResultCandidate[]
   */
  public function getChosenCandidates()
  {
    return $this->chosenCandidates;
  }
  /**
   * A list of the top candidate tokens at each decoding step. The length of
   * this list is equal to the total number of decoding steps.
   *
   * @param GoogleCloudAiplatformV1LogprobsResultTopCandidates[] $topCandidates
   */
  public function setTopCandidates($topCandidates)
  {
    $this->topCandidates = $topCandidates;
  }
  /**
   * @return GoogleCloudAiplatformV1LogprobsResultTopCandidates[]
   */
  public function getTopCandidates()
  {
    return $this->topCandidates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1LogprobsResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1LogprobsResult');
