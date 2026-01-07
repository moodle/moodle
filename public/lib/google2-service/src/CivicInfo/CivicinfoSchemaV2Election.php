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

namespace Google\Service\CivicInfo;

class CivicinfoSchemaV2Election extends \Google\Model
{
  public const SHAPE_LOOKUP_BEHAVIOR_shapeLookupDefault = 'shapeLookupDefault';
  public const SHAPE_LOOKUP_BEHAVIOR_shapeLookupDisabled = 'shapeLookupDisabled';
  public const SHAPE_LOOKUP_BEHAVIOR_shapeLookupEnabled = 'shapeLookupEnabled';
  /**
   * Day of the election in YYYY-MM-DD format.
   *
   * @var string
   */
  public $electionDay;
  /**
   * The unique ID of this election.
   *
   * @var string
   */
  public $id;
  /**
   * A displayable name for the election.
   *
   * @var string
   */
  public $name;
  /**
   * The political division of the election. Represented as an OCD Division ID.
   * Voters within these political jurisdictions are covered by this election.
   * This is typically a state such as ocd-division/country:us/state:ca or for
   * the midterms or general election the entire US (i.e. ocd-
   * division/country:us).
   *
   * @var string
   */
  public $ocdDivisionId;
  /**
   * @var string
   */
  public $shapeLookupBehavior;

  /**
   * Day of the election in YYYY-MM-DD format.
   *
   * @param string $electionDay
   */
  public function setElectionDay($electionDay)
  {
    $this->electionDay = $electionDay;
  }
  /**
   * @return string
   */
  public function getElectionDay()
  {
    return $this->electionDay;
  }
  /**
   * The unique ID of this election.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A displayable name for the election.
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
   * The political division of the election. Represented as an OCD Division ID.
   * Voters within these political jurisdictions are covered by this election.
   * This is typically a state such as ocd-division/country:us/state:ca or for
   * the midterms or general election the entire US (i.e. ocd-
   * division/country:us).
   *
   * @param string $ocdDivisionId
   */
  public function setOcdDivisionId($ocdDivisionId)
  {
    $this->ocdDivisionId = $ocdDivisionId;
  }
  /**
   * @return string
   */
  public function getOcdDivisionId()
  {
    return $this->ocdDivisionId;
  }
  /**
   * @param self::SHAPE_LOOKUP_BEHAVIOR_* $shapeLookupBehavior
   */
  public function setShapeLookupBehavior($shapeLookupBehavior)
  {
    $this->shapeLookupBehavior = $shapeLookupBehavior;
  }
  /**
   * @return self::SHAPE_LOOKUP_BEHAVIOR_*
   */
  public function getShapeLookupBehavior()
  {
    return $this->shapeLookupBehavior;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2Election::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2Election');
