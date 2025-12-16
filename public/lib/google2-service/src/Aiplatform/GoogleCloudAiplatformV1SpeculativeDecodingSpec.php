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

class GoogleCloudAiplatformV1SpeculativeDecodingSpec extends \Google\Model
{
  protected $draftModelSpeculationType = GoogleCloudAiplatformV1SpeculativeDecodingSpecDraftModelSpeculation::class;
  protected $draftModelSpeculationDataType = '';
  protected $ngramSpeculationType = GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation::class;
  protected $ngramSpeculationDataType = '';
  /**
   * The number of speculative tokens to generate at each step.
   *
   * @var int
   */
  public $speculativeTokenCount;

  /**
   * draft model speculation.
   *
   * @param GoogleCloudAiplatformV1SpeculativeDecodingSpecDraftModelSpeculation $draftModelSpeculation
   */
  public function setDraftModelSpeculation(GoogleCloudAiplatformV1SpeculativeDecodingSpecDraftModelSpeculation $draftModelSpeculation)
  {
    $this->draftModelSpeculation = $draftModelSpeculation;
  }
  /**
   * @return GoogleCloudAiplatformV1SpeculativeDecodingSpecDraftModelSpeculation
   */
  public function getDraftModelSpeculation()
  {
    return $this->draftModelSpeculation;
  }
  /**
   * N-Gram speculation.
   *
   * @param GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation $ngramSpeculation
   */
  public function setNgramSpeculation(GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation $ngramSpeculation)
  {
    $this->ngramSpeculation = $ngramSpeculation;
  }
  /**
   * @return GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation
   */
  public function getNgramSpeculation()
  {
    return $this->ngramSpeculation;
  }
  /**
   * The number of speculative tokens to generate at each step.
   *
   * @param int $speculativeTokenCount
   */
  public function setSpeculativeTokenCount($speculativeTokenCount)
  {
    $this->speculativeTokenCount = $speculativeTokenCount;
  }
  /**
   * @return int
   */
  public function getSpeculativeTokenCount()
  {
    return $this->speculativeTokenCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SpeculativeDecodingSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SpeculativeDecodingSpec');
