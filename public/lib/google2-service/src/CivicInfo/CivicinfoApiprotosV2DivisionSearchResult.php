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

class CivicinfoApiprotosV2DivisionSearchResult extends \Google\Collection
{
  protected $collection_key = 'aliases';
  /**
   * Other Open Civic Data identifiers that refer to the same division -- for
   * example, those that refer to other political divisions whose boundaries are
   * defined to be coterminous with this one. For example, ocd-
   * division/country:us/state:wy will include an alias of ocd-
   * division/country:us/state:wy/cd:1, since Wyoming has only one Congressional
   * district.
   *
   * @var string[]
   */
  public $aliases;
  /**
   * The name of the division.
   *
   * @var string
   */
  public $name;
  /**
   * The unique Open Civic Data identifier for this division
   *
   * @var string
   */
  public $ocdId;

  /**
   * Other Open Civic Data identifiers that refer to the same division -- for
   * example, those that refer to other political divisions whose boundaries are
   * defined to be coterminous with this one. For example, ocd-
   * division/country:us/state:wy will include an alias of ocd-
   * division/country:us/state:wy/cd:1, since Wyoming has only one Congressional
   * district.
   *
   * @param string[] $aliases
   */
  public function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  /**
   * @return string[]
   */
  public function getAliases()
  {
    return $this->aliases;
  }
  /**
   * The name of the division.
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
   * The unique Open Civic Data identifier for this division
   *
   * @param string $ocdId
   */
  public function setOcdId($ocdId)
  {
    $this->ocdId = $ocdId;
  }
  /**
   * @return string
   */
  public function getOcdId()
  {
    return $this->ocdId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoApiprotosV2DivisionSearchResult::class, 'Google_Service_CivicInfo_CivicinfoApiprotosV2DivisionSearchResult');
