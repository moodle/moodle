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

class GoogleCloudAiplatformV1EventActions extends \Google\Model
{
  /**
   * Optional. Indicates that the event is updating an artifact. key is the
   * filename, value is the version.
   *
   * @var int[]
   */
  public $artifactDelta;
  /**
   * Optional. The agent is escalating to a higher level agent.
   *
   * @var bool
   */
  public $escalate;
  /**
   * Optional. Will only be set by a tool response indicating tool request euc.
   * Struct key is the function call id since one function call response (from
   * model) could correspond to multiple function calls. Struct value is the
   * required auth config, which can be another struct.
   *
   * @var array[]
   */
  public $requestedAuthConfigs;
  /**
   * Optional. If true, it won't call model to summarize function response. Only
   * used for function_response event.
   *
   * @var bool
   */
  public $skipSummarization;
  /**
   * Optional. Indicates that the event is updating the state with the given
   * delta.
   *
   * @var array[]
   */
  public $stateDelta;
  /**
   * Optional. If set, the event transfers to the specified agent.
   *
   * @var string
   */
  public $transferAgent;

  /**
   * Optional. Indicates that the event is updating an artifact. key is the
   * filename, value is the version.
   *
   * @param int[] $artifactDelta
   */
  public function setArtifactDelta($artifactDelta)
  {
    $this->artifactDelta = $artifactDelta;
  }
  /**
   * @return int[]
   */
  public function getArtifactDelta()
  {
    return $this->artifactDelta;
  }
  /**
   * Optional. The agent is escalating to a higher level agent.
   *
   * @param bool $escalate
   */
  public function setEscalate($escalate)
  {
    $this->escalate = $escalate;
  }
  /**
   * @return bool
   */
  public function getEscalate()
  {
    return $this->escalate;
  }
  /**
   * Optional. Will only be set by a tool response indicating tool request euc.
   * Struct key is the function call id since one function call response (from
   * model) could correspond to multiple function calls. Struct value is the
   * required auth config, which can be another struct.
   *
   * @param array[] $requestedAuthConfigs
   */
  public function setRequestedAuthConfigs($requestedAuthConfigs)
  {
    $this->requestedAuthConfigs = $requestedAuthConfigs;
  }
  /**
   * @return array[]
   */
  public function getRequestedAuthConfigs()
  {
    return $this->requestedAuthConfigs;
  }
  /**
   * Optional. If true, it won't call model to summarize function response. Only
   * used for function_response event.
   *
   * @param bool $skipSummarization
   */
  public function setSkipSummarization($skipSummarization)
  {
    $this->skipSummarization = $skipSummarization;
  }
  /**
   * @return bool
   */
  public function getSkipSummarization()
  {
    return $this->skipSummarization;
  }
  /**
   * Optional. Indicates that the event is updating the state with the given
   * delta.
   *
   * @param array[] $stateDelta
   */
  public function setStateDelta($stateDelta)
  {
    $this->stateDelta = $stateDelta;
  }
  /**
   * @return array[]
   */
  public function getStateDelta()
  {
    return $this->stateDelta;
  }
  /**
   * Optional. If set, the event transfers to the specified agent.
   *
   * @param string $transferAgent
   */
  public function setTransferAgent($transferAgent)
  {
    $this->transferAgent = $transferAgent;
  }
  /**
   * @return string
   */
  public function getTransferAgent()
  {
    return $this->transferAgent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EventActions::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EventActions');
