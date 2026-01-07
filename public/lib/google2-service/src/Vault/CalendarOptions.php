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

namespace Google\Service\Vault;

class CalendarOptions extends \Google\Collection
{
  protected $collection_key = 'responseStatuses';
  /**
   * Matches only those events whose location contains all of the words in the
   * given set. If the string contains quoted phrases, this method only matches
   * those events whose location contain the exact phrase. Entries in the set
   * are considered in "and". Word splitting example: ["New Zealand"] vs
   * ["New","Zealand"] "New Zealand": matched by both "New and better Zealand":
   * only matched by the later
   *
   * @var string[]
   */
  public $locationQuery;
  /**
   * Matches only those events that do not contain any of the words in the given
   * set in title, description, location, or attendees. Entries in the set are
   * considered in "or".
   *
   * @var string[]
   */
  public $minusWords;
  /**
   * Matches only those events whose attendees contain all of the words in the
   * given set. Entries in the set are considered in "and".
   *
   * @var string[]
   */
  public $peopleQuery;
  /**
   * Matches only events for which the custodian gave one of these responses. If
   * the set is empty or contains ATTENDEE_RESPONSE_UNSPECIFIED there will be no
   * filtering on responses.
   *
   * @var string[]
   */
  public $responseStatuses;
  /**
   * Search the current version of the Calendar event, but export the contents
   * of the last version saved before 12:00 AM UTC on the specified date. Enter
   * the date in UTC.
   *
   * @var string
   */
  public $versionDate;

  /**
   * Matches only those events whose location contains all of the words in the
   * given set. If the string contains quoted phrases, this method only matches
   * those events whose location contain the exact phrase. Entries in the set
   * are considered in "and". Word splitting example: ["New Zealand"] vs
   * ["New","Zealand"] "New Zealand": matched by both "New and better Zealand":
   * only matched by the later
   *
   * @param string[] $locationQuery
   */
  public function setLocationQuery($locationQuery)
  {
    $this->locationQuery = $locationQuery;
  }
  /**
   * @return string[]
   */
  public function getLocationQuery()
  {
    return $this->locationQuery;
  }
  /**
   * Matches only those events that do not contain any of the words in the given
   * set in title, description, location, or attendees. Entries in the set are
   * considered in "or".
   *
   * @param string[] $minusWords
   */
  public function setMinusWords($minusWords)
  {
    $this->minusWords = $minusWords;
  }
  /**
   * @return string[]
   */
  public function getMinusWords()
  {
    return $this->minusWords;
  }
  /**
   * Matches only those events whose attendees contain all of the words in the
   * given set. Entries in the set are considered in "and".
   *
   * @param string[] $peopleQuery
   */
  public function setPeopleQuery($peopleQuery)
  {
    $this->peopleQuery = $peopleQuery;
  }
  /**
   * @return string[]
   */
  public function getPeopleQuery()
  {
    return $this->peopleQuery;
  }
  /**
   * Matches only events for which the custodian gave one of these responses. If
   * the set is empty or contains ATTENDEE_RESPONSE_UNSPECIFIED there will be no
   * filtering on responses.
   *
   * @param string[] $responseStatuses
   */
  public function setResponseStatuses($responseStatuses)
  {
    $this->responseStatuses = $responseStatuses;
  }
  /**
   * @return string[]
   */
  public function getResponseStatuses()
  {
    return $this->responseStatuses;
  }
  /**
   * Search the current version of the Calendar event, but export the contents
   * of the last version saved before 12:00 AM UTC on the specified date. Enter
   * the date in UTC.
   *
   * @param string $versionDate
   */
  public function setVersionDate($versionDate)
  {
    $this->versionDate = $versionDate;
  }
  /**
   * @return string
   */
  public function getVersionDate()
  {
    return $this->versionDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarOptions::class, 'Google_Service_Vault_CalendarOptions');
