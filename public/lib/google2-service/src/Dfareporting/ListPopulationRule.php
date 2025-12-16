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

namespace Google\Service\Dfareporting;

class ListPopulationRule extends \Google\Collection
{
  protected $collection_key = 'listPopulationClauses';
  /**
   * Floodlight activity ID associated with this rule. This field can be left
   * blank.
   *
   * @var string
   */
  public $floodlightActivityId;
  /**
   * Name of floodlight activity associated with this rule. This is a read-only,
   * auto-generated field.
   *
   * @var string
   */
  public $floodlightActivityName;
  protected $listPopulationClausesType = ListPopulationClause::class;
  protected $listPopulationClausesDataType = 'array';

  /**
   * Floodlight activity ID associated with this rule. This field can be left
   * blank.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
  /**
   * Name of floodlight activity associated with this rule. This is a read-only,
   * auto-generated field.
   *
   * @param string $floodlightActivityName
   */
  public function setFloodlightActivityName($floodlightActivityName)
  {
    $this->floodlightActivityName = $floodlightActivityName;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityName()
  {
    return $this->floodlightActivityName;
  }
  /**
   * Clauses that make up this list population rule. Clauses are joined by ANDs,
   * and the clauses themselves are made up of list population terms which are
   * joined by ORs.
   *
   * @param ListPopulationClause[] $listPopulationClauses
   */
  public function setListPopulationClauses($listPopulationClauses)
  {
    $this->listPopulationClauses = $listPopulationClauses;
  }
  /**
   * @return ListPopulationClause[]
   */
  public function getListPopulationClauses()
  {
    return $this->listPopulationClauses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPopulationRule::class, 'Google_Service_Dfareporting_ListPopulationRule');
