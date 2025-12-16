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

class CivicinfoSchemaV2GeographicDivision extends \Google\Collection
{
  protected $collection_key = 'officeIndices';
  /**
   * Any other valid OCD IDs that refer to the same division.\n\nBecause OCD IDs
   * are meant to be human-readable and at least somewhat predictable, there are
   * occasionally several identifiers for a single division. These identifiers
   * are defined to be equivalent to one another, and one is always indicated as
   * the primary identifier. The primary identifier will be returned in ocd_id
   * above, and any other equivalent valid identifiers will be returned in this
   * list.\n\nFor example, if this division's OCD ID is ocd-
   * division/country:us/district:dc, this will contain ocd-
   * division/country:us/state:dc.
   *
   * @var string[]
   */
  public $alsoKnownAs;
  /**
   * The name of the division.
   *
   * @var string
   */
  public $name;
  /**
   * List of indices in the offices array, one for each office elected from this
   * division. Will only be present if includeOffices was true (or absent) in
   * the request.
   *
   * @var string[]
   */
  public $officeIndices;

  /**
   * Any other valid OCD IDs that refer to the same division.\n\nBecause OCD IDs
   * are meant to be human-readable and at least somewhat predictable, there are
   * occasionally several identifiers for a single division. These identifiers
   * are defined to be equivalent to one another, and one is always indicated as
   * the primary identifier. The primary identifier will be returned in ocd_id
   * above, and any other equivalent valid identifiers will be returned in this
   * list.\n\nFor example, if this division's OCD ID is ocd-
   * division/country:us/district:dc, this will contain ocd-
   * division/country:us/state:dc.
   *
   * @param string[] $alsoKnownAs
   */
  public function setAlsoKnownAs($alsoKnownAs)
  {
    $this->alsoKnownAs = $alsoKnownAs;
  }
  /**
   * @return string[]
   */
  public function getAlsoKnownAs()
  {
    return $this->alsoKnownAs;
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
   * List of indices in the offices array, one for each office elected from this
   * division. Will only be present if includeOffices was true (or absent) in
   * the request.
   *
   * @param string[] $officeIndices
   */
  public function setOfficeIndices($officeIndices)
  {
    $this->officeIndices = $officeIndices;
  }
  /**
   * @return string[]
   */
  public function getOfficeIndices()
  {
    return $this->officeIndices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2GeographicDivision::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2GeographicDivision');
