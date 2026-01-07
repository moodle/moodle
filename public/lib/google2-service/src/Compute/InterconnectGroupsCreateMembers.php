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

namespace Google\Service\Compute;

class InterconnectGroupsCreateMembers extends \Google\Collection
{
  public const INTENT_MISMATCH_BEHAVIOR_CREATE = 'CREATE';
  public const INTENT_MISMATCH_BEHAVIOR_REJECT = 'REJECT';
  public const INTENT_MISMATCH_BEHAVIOR_UNSPECIFIED = 'UNSPECIFIED';
  protected $collection_key = 'interconnects';
  /**
   * How to behave when configured.topologyCapability.supportedSLA would not
   * equal intent.topologyCapability after this call.
   *
   * @var string
   */
  public $intentMismatchBehavior;
  protected $interconnectsType = InterconnectGroupsCreateMembersInterconnectInput::class;
  protected $interconnectsDataType = 'array';
  protected $templateInterconnectType = InterconnectGroupsCreateMembersInterconnectInput::class;
  protected $templateInterconnectDataType = '';

  /**
   * How to behave when configured.topologyCapability.supportedSLA would not
   * equal intent.topologyCapability after this call.
   *
   * Accepted values: CREATE, REJECT, UNSPECIFIED
   *
   * @param self::INTENT_MISMATCH_BEHAVIOR_* $intentMismatchBehavior
   */
  public function setIntentMismatchBehavior($intentMismatchBehavior)
  {
    $this->intentMismatchBehavior = $intentMismatchBehavior;
  }
  /**
   * @return self::INTENT_MISMATCH_BEHAVIOR_*
   */
  public function getIntentMismatchBehavior()
  {
    return $this->intentMismatchBehavior;
  }
  /**
   * @param InterconnectGroupsCreateMembersInterconnectInput[] $interconnects
   */
  public function setInterconnects($interconnects)
  {
    $this->interconnects = $interconnects;
  }
  /**
   * @return InterconnectGroupsCreateMembersInterconnectInput[]
   */
  public function getInterconnects()
  {
    return $this->interconnects;
  }
  /**
   * Parameters for the Interconnects to create.
   *
   * @param InterconnectGroupsCreateMembersInterconnectInput $templateInterconnect
   */
  public function setTemplateInterconnect(InterconnectGroupsCreateMembersInterconnectInput $templateInterconnect)
  {
    $this->templateInterconnect = $templateInterconnect;
  }
  /**
   * @return InterconnectGroupsCreateMembersInterconnectInput
   */
  public function getTemplateInterconnect()
  {
    return $this->templateInterconnect;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupsCreateMembers::class, 'Google_Service_Compute_InterconnectGroupsCreateMembers');
