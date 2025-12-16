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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAssistAnswer extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Assist operation is currently in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Assist operation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Assist operation has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Assist operation has been skipped.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  protected $collection_key = 'replies';
  /**
   * Reasons for not answering the assist call.
   *
   * @var string[]
   */
  public $assistSkippedReasons;
  protected $customerPolicyEnforcementResultType = GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult::class;
  protected $customerPolicyEnforcementResultDataType = '';
  /**
   * Immutable. Identifier. Resource name of the `AssistAnswer`. Format: `projec
   * ts/{project}/locations/{location}/collections/{collection}/engines/{engine}
   * /sessions/{session}/assistAnswers/{assist_answer}` This field must be a
   * UTF-8 encoded string with a length limit of 1024 characters.
   *
   * @var string
   */
  public $name;
  protected $repliesType = GoogleCloudDiscoveryengineV1alphaAssistAnswerReply::class;
  protected $repliesDataType = 'array';
  /**
   * State of the answer generation.
   *
   * @var string
   */
  public $state;

  /**
   * Reasons for not answering the assist call.
   *
   * @param string[] $assistSkippedReasons
   */
  public function setAssistSkippedReasons($assistSkippedReasons)
  {
    $this->assistSkippedReasons = $assistSkippedReasons;
  }
  /**
   * @return string[]
   */
  public function getAssistSkippedReasons()
  {
    return $this->assistSkippedReasons;
  }
  /**
   * Optional. The field contains information about the various policy checks'
   * results like the banned phrases or the Model Armor checks. This field is
   * populated only if the assist call was skipped due to a policy violation.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult $customerPolicyEnforcementResult
   */
  public function setCustomerPolicyEnforcementResult(GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult $customerPolicyEnforcementResult)
  {
    $this->customerPolicyEnforcementResult = $customerPolicyEnforcementResult;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult
   */
  public function getCustomerPolicyEnforcementResult()
  {
    return $this->customerPolicyEnforcementResult;
  }
  /**
   * Immutable. Identifier. Resource name of the `AssistAnswer`. Format: `projec
   * ts/{project}/locations/{location}/collections/{collection}/engines/{engine}
   * /sessions/{session}/assistAnswers/{assist_answer}` This field must be a
   * UTF-8 encoded string with a length limit of 1024 characters.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Replies of the assistant.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistAnswerReply[] $replies
   */
  public function setReplies($replies)
  {
    $this->replies = $replies;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistAnswerReply[]
   */
  public function getReplies()
  {
    return $this->replies;
  }
  /**
   * State of the answer generation.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS, FAILED, SUCCEEDED, SKIPPED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAssistAnswer::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAssistAnswer');
