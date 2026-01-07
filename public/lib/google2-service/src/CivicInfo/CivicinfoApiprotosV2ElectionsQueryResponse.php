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

class CivicinfoApiprotosV2ElectionsQueryResponse extends \Google\Collection
{
  protected $collection_key = 'elections';
  protected $electionsType = CivicinfoSchemaV2Election::class;
  protected $electionsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "civicinfo#electionsQueryResponse".
   *
   * @var string
   */
  public $kind;

  /**
   * A list of available elections
   *
   * @param CivicinfoSchemaV2Election[] $elections
   */
  public function setElections($elections)
  {
    $this->elections = $elections;
  }
  /**
   * @return CivicinfoSchemaV2Election[]
   */
  public function getElections()
  {
    return $this->elections;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "civicinfo#electionsQueryResponse".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoApiprotosV2ElectionsQueryResponse::class, 'Google_Service_CivicInfo_CivicinfoApiprotosV2ElectionsQueryResponse');
