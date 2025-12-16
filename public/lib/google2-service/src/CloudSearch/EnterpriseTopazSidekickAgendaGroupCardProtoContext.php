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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickAgendaGroupCardProtoContext extends \Google\Model
{
  /**
   * No specific event was requested.
   */
  public const EVENTS_RESTRICT_NONE = 'NONE';
  /**
   * The next meeting was requested.
   */
  public const EVENTS_RESTRICT_NEXT_MEETING = 'NEXT_MEETING';
  /**
   * User friendly free text that describes the context of the card (e.g. "Next
   * meeting with Bob"). This is largely only applicable when the card is
   * generated from a query.
   *
   * @var string
   */
  public $context;
  /**
   * Localized free text that describes the dates represented by the card.
   * Currently, the card will only represent a single day.
   *
   * @var string
   */
  public $date;
  /**
   * Represents restrictions applied to the events requested in the user's
   * query.
   *
   * @var string
   */
  public $eventsRestrict;

  /**
   * User friendly free text that describes the context of the card (e.g. "Next
   * meeting with Bob"). This is largely only applicable when the card is
   * generated from a query.
   *
   * @param string $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return string
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Localized free text that describes the dates represented by the card.
   * Currently, the card will only represent a single day.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Represents restrictions applied to the events requested in the user's
   * query.
   *
   * Accepted values: NONE, NEXT_MEETING
   *
   * @param self::EVENTS_RESTRICT_* $eventsRestrict
   */
  public function setEventsRestrict($eventsRestrict)
  {
    $this->eventsRestrict = $eventsRestrict;
  }
  /**
   * @return self::EVENTS_RESTRICT_*
   */
  public function getEventsRestrict()
  {
    return $this->eventsRestrict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAgendaGroupCardProtoContext::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAgendaGroupCardProtoContext');
