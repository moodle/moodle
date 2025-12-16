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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1OpportunityEvent extends \Google\Model
{
  /**
   * Not used.
   */
  public const EVENT_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * New opportunity created.
   */
  public const EVENT_TYPE_CREATED = 'CREATED';
  /**
   * Existing opportunity updated.
   */
  public const EVENT_TYPE_UPDATED = 'UPDATED';
  /**
   * Partner has been detached from the opportunity and can no longer access it.
   */
  public const EVENT_TYPE_PARTNER_DETACHED = 'PARTNER_DETACHED';
  /**
   * Type of event which happened for the opportunity.
   *
   * @var string
   */
  public $eventType;
  /**
   * Resource name of the opportunity. Format: opportunities/{opportunity}
   *
   * @var string
   */
  public $opportunity;
  /**
   * Resource name of the partner. Format: partners/{partner}
   *
   * @var string
   */
  public $partner;

  /**
   * Type of event which happened for the opportunity.
   *
   * Accepted values: TYPE_UNSPECIFIED, CREATED, UPDATED, PARTNER_DETACHED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Resource name of the opportunity. Format: opportunities/{opportunity}
   *
   * @param string $opportunity
   */
  public function setOpportunity($opportunity)
  {
    $this->opportunity = $opportunity;
  }
  /**
   * @return string
   */
  public function getOpportunity()
  {
    return $this->opportunity;
  }
  /**
   * Resource name of the partner. Format: partners/{partner}
   *
   * @param string $partner
   */
  public function setPartner($partner)
  {
    $this->partner = $partner;
  }
  /**
   * @return string
   */
  public function getPartner()
  {
    return $this->partner;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1OpportunityEvent::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1OpportunityEvent');
