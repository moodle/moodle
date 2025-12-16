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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig extends \Google\Model
{
  /**
   * The type of the predefined question is unspecified.
   */
  public const TYPE_PREDEFINED_QUESTION_TYPE_UNSPECIFIED = 'PREDEFINED_QUESTION_TYPE_UNSPECIFIED';
  /**
   * A prebuilt classifier classfying the outcome of the conversation. For
   * example, if the customer issue mentioned in a conversation has been
   * resolved or not.
   */
  public const TYPE_CONVERSATION_OUTCOME = 'CONVERSATION_OUTCOME';
  /**
   * A prebuilt classifier classfying the initiator of the conversation
   * escalation. For example, if it was initiated by the customer or the agent.
   */
  public const TYPE_CONVERSATION_OUTCOME_ESCALATION_INITIATOR_ROLE = 'CONVERSATION_OUTCOME_ESCALATION_INITIATOR_ROLE';
  /**
   * The type of the predefined question.
   *
   * @var string
   */
  public $type;

  /**
   * The type of the predefined question.
   *
   * Accepted values: PREDEFINED_QUESTION_TYPE_UNSPECIFIED,
   * CONVERSATION_OUTCOME, CONVERSATION_OUTCOME_ESCALATION_INITIATOR_ROLE
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
class_alias(GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig');
