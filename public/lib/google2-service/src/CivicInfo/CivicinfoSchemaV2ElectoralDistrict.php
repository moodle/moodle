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

class CivicinfoSchemaV2ElectoralDistrict extends \Google\Model
{
  public const SCOPE_statewide = 'statewide';
  public const SCOPE_congressional = 'congressional';
  public const SCOPE_stateUpper = 'stateUpper';
  public const SCOPE_stateLower = 'stateLower';
  public const SCOPE_countywide = 'countywide';
  public const SCOPE_judicial = 'judicial';
  public const SCOPE_schoolBoard = 'schoolBoard';
  public const SCOPE_citywide = 'citywide';
  public const SCOPE_special = 'special';
  public const SCOPE_countyCouncil = 'countyCouncil';
  public const SCOPE_township = 'township';
  public const SCOPE_ward = 'ward';
  public const SCOPE_cityCouncil = 'cityCouncil';
  public const SCOPE_national = 'national';
  /**
   * An identifier for this district, relative to its scope. For example, the
   * 34th State Senate district would have id "34" and a scope of stateUpper.
   *
   * @var string
   */
  public $id;
  /**
   * The name of the district.
   *
   * @var string
   */
  public $name;
  /**
   * The geographic scope of this district. If unspecified the district's
   * geography is not known. One of: national, statewide, congressional,
   * stateUpper, stateLower, countywide, judicial, schoolBoard, cityWide,
   * township, countyCouncil, cityCouncil, ward, special
   *
   * @var string
   */
  public $scope;

  /**
   * An identifier for this district, relative to its scope. For example, the
   * 34th State Senate district would have id "34" and a scope of stateUpper.
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
   * The name of the district.
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
   * The geographic scope of this district. If unspecified the district's
   * geography is not known. One of: national, statewide, congressional,
   * stateUpper, stateLower, countywide, judicial, schoolBoard, cityWide,
   * township, countyCouncil, cityCouncil, ward, special
   *
   * Accepted values: statewide, congressional, stateUpper, stateLower,
   * countywide, judicial, schoolBoard, citywide, special, countyCouncil,
   * township, ward, cityCouncil, national
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2ElectoralDistrict::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2ElectoralDistrict');
