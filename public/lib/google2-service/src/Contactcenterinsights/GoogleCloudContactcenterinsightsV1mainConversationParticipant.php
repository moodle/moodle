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

class GoogleCloudContactcenterinsightsV1mainConversationParticipant extends \Google\Model
{
  /**
   * Participant's role is not set.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Participant is a human agent.
   */
  public const ROLE_HUMAN_AGENT = 'HUMAN_AGENT';
  /**
   * Participant is an automated agent.
   */
  public const ROLE_AUTOMATED_AGENT = 'AUTOMATED_AGENT';
  /**
   * Participant is an end user who conversed with the contact center.
   */
  public const ROLE_END_USER = 'END_USER';
  /**
   * Participant is either a human or automated agent.
   */
  public const ROLE_ANY_AGENT = 'ANY_AGENT';
  /**
   * Deprecated. Use `dialogflow_participant_name` instead. The name of the
   * Dialogflow participant. Format: projects/{project}/locations/{location}/con
   * versations/{conversation}/participants/{participant}
   *
   * @deprecated
   * @var string
   */
  public $dialogflowParticipant;
  /**
   * The name of the participant provided by Dialogflow. Format: projects/{proje
   * ct}/locations/{location}/conversations/{conversation}/participants/{partici
   * pant}
   *
   * @var string
   */
  public $dialogflowParticipantName;
  /**
   * Obfuscated user ID from Dialogflow.
   *
   * @var string
   */
  public $obfuscatedExternalUserId;
  /**
   * The role of the participant.
   *
   * @var string
   */
  public $role;
  /**
   * A user-specified ID representing the participant.
   *
   * @var string
   */
  public $userId;

  /**
   * Deprecated. Use `dialogflow_participant_name` instead. The name of the
   * Dialogflow participant. Format: projects/{project}/locations/{location}/con
   * versations/{conversation}/participants/{participant}
   *
   * @deprecated
   * @param string $dialogflowParticipant
   */
  public function setDialogflowParticipant($dialogflowParticipant)
  {
    $this->dialogflowParticipant = $dialogflowParticipant;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDialogflowParticipant()
  {
    return $this->dialogflowParticipant;
  }
  /**
   * The name of the participant provided by Dialogflow. Format: projects/{proje
   * ct}/locations/{location}/conversations/{conversation}/participants/{partici
   * pant}
   *
   * @param string $dialogflowParticipantName
   */
  public function setDialogflowParticipantName($dialogflowParticipantName)
  {
    $this->dialogflowParticipantName = $dialogflowParticipantName;
  }
  /**
   * @return string
   */
  public function getDialogflowParticipantName()
  {
    return $this->dialogflowParticipantName;
  }
  /**
   * Obfuscated user ID from Dialogflow.
   *
   * @param string $obfuscatedExternalUserId
   */
  public function setObfuscatedExternalUserId($obfuscatedExternalUserId)
  {
    $this->obfuscatedExternalUserId = $obfuscatedExternalUserId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalUserId()
  {
    return $this->obfuscatedExternalUserId;
  }
  /**
   * The role of the participant.
   *
   * Accepted values: ROLE_UNSPECIFIED, HUMAN_AGENT, AUTOMATED_AGENT, END_USER,
   * ANY_AGENT
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * A user-specified ID representing the participant.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainConversationParticipant::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainConversationParticipant');
