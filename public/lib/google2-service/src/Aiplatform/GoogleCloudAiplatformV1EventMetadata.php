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

class GoogleCloudAiplatformV1EventMetadata extends \Google\Collection
{
  protected $collection_key = 'longRunningToolIds';
  /**
   * Optional. The branch of the event. The format is like
   * agent_1.agent_2.agent_3, where agent_1 is the parent of agent_2, and
   * agent_2 is the parent of agent_3. Branch is used when multiple child agents
   * shouldn't see their siblings' conversation history.
   *
   * @var string
   */
  public $branch;
  /**
   * The custom metadata of the LlmResponse.
   *
   * @var array[]
   */
  public $customMetadata;
  protected $groundingMetadataType = GoogleCloudAiplatformV1GroundingMetadata::class;
  protected $groundingMetadataDataType = '';
  /**
   * Optional. Flag indicating that LLM was interrupted when generating the
   * content. Usually it's due to user interruption during a bidi streaming.
   *
   * @var bool
   */
  public $interrupted;
  /**
   * Optional. Set of ids of the long running function calls. Agent client will
   * know from this field about which function call is long running. Only valid
   * for function call event.
   *
   * @var string[]
   */
  public $longRunningToolIds;
  /**
   * Optional. Indicates whether the text content is part of a unfinished text
   * stream. Only used for streaming mode and when the content is plain text.
   *
   * @var bool
   */
  public $partial;
  /**
   * Optional. Indicates whether the response from the model is complete. Only
   * used for streaming mode.
   *
   * @var bool
   */
  public $turnComplete;

  /**
   * Optional. The branch of the event. The format is like
   * agent_1.agent_2.agent_3, where agent_1 is the parent of agent_2, and
   * agent_2 is the parent of agent_3. Branch is used when multiple child agents
   * shouldn't see their siblings' conversation history.
   *
   * @param string $branch
   */
  public function setBranch($branch)
  {
    $this->branch = $branch;
  }
  /**
   * @return string
   */
  public function getBranch()
  {
    return $this->branch;
  }
  /**
   * The custom metadata of the LlmResponse.
   *
   * @param array[] $customMetadata
   */
  public function setCustomMetadata($customMetadata)
  {
    $this->customMetadata = $customMetadata;
  }
  /**
   * @return array[]
   */
  public function getCustomMetadata()
  {
    return $this->customMetadata;
  }
  /**
   * Optional. Metadata returned to client when grounding is enabled.
   *
   * @param GoogleCloudAiplatformV1GroundingMetadata $groundingMetadata
   */
  public function setGroundingMetadata(GoogleCloudAiplatformV1GroundingMetadata $groundingMetadata)
  {
    $this->groundingMetadata = $groundingMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingMetadata
   */
  public function getGroundingMetadata()
  {
    return $this->groundingMetadata;
  }
  /**
   * Optional. Flag indicating that LLM was interrupted when generating the
   * content. Usually it's due to user interruption during a bidi streaming.
   *
   * @param bool $interrupted
   */
  public function setInterrupted($interrupted)
  {
    $this->interrupted = $interrupted;
  }
  /**
   * @return bool
   */
  public function getInterrupted()
  {
    return $this->interrupted;
  }
  /**
   * Optional. Set of ids of the long running function calls. Agent client will
   * know from this field about which function call is long running. Only valid
   * for function call event.
   *
   * @param string[] $longRunningToolIds
   */
  public function setLongRunningToolIds($longRunningToolIds)
  {
    $this->longRunningToolIds = $longRunningToolIds;
  }
  /**
   * @return string[]
   */
  public function getLongRunningToolIds()
  {
    return $this->longRunningToolIds;
  }
  /**
   * Optional. Indicates whether the text content is part of a unfinished text
   * stream. Only used for streaming mode and when the content is plain text.
   *
   * @param bool $partial
   */
  public function setPartial($partial)
  {
    $this->partial = $partial;
  }
  /**
   * @return bool
   */
  public function getPartial()
  {
    return $this->partial;
  }
  /**
   * Optional. Indicates whether the response from the model is complete. Only
   * used for streaming mode.
   *
   * @param bool $turnComplete
   */
  public function setTurnComplete($turnComplete)
  {
    $this->turnComplete = $turnComplete;
  }
  /**
   * @return bool
   */
  public function getTurnComplete()
  {
    return $this->turnComplete;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EventMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EventMetadata');
