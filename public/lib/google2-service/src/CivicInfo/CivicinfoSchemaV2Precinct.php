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

class CivicinfoSchemaV2Precinct extends \Google\Collection
{
  protected $collection_key = 'spatialBoundaryId';
  /**
   * ID of the AdministrationRegion message for this precinct. Corresponds to
   * LocalityId xml tag.
   *
   * @var string
   */
  public $administrationRegionId;
  /**
   * ID(s) of the Contest message(s) for this precinct.
   *
   * @var string[]
   */
  public $contestId;
  /**
   * Required. Dataset ID. What datasets our Precincts come from.
   *
   * @var string
   */
  public $datasetId;
  /**
   * ID(s) of the PollingLocation message(s) for this precinct.
   *
   * @var string[]
   */
  public $earlyVoteSiteId;
  /**
   * ID(s) of the ElectoralDistrict message(s) for this precinct.
   *
   * @var string[]
   */
  public $electoralDistrictId;
  /**
   * Required. A unique identifier for this precinct.
   *
   * @var string
   */
  public $id;
  /**
   * Specifies if the precinct runs mail-only elections.
   *
   * @var bool
   */
  public $mailOnly;
  /**
   * Required. The name of the precinct.
   *
   * @var string
   */
  public $name;
  /**
   * The number of the precinct.
   *
   * @var string
   */
  public $number;
  /**
   * Encouraged. The OCD ID of the precinct
   *
   * @var string[]
   */
  public $ocdId;
  /**
   * ID(s) of the PollingLocation message(s) for this precinct.
   *
   * @var string[]
   */
  public $pollingLocationId;
  /**
   * ID(s) of the SpatialBoundary message(s) for this precinct. Used to specify
   * a geometrical boundary of the precinct.
   *
   * @var string[]
   */
  public $spatialBoundaryId;
  /**
   * If present, this proto corresponds to one portion of split precinct. Other
   * portions of this precinct are guaranteed to have the same `name`. If not
   * present, this proto represents a full precicnt.
   *
   * @var string
   */
  public $splitName;
  /**
   * Specifies the ward the precinct is contained within.
   *
   * @var string
   */
  public $ward;

  /**
   * ID of the AdministrationRegion message for this precinct. Corresponds to
   * LocalityId xml tag.
   *
   * @param string $administrationRegionId
   */
  public function setAdministrationRegionId($administrationRegionId)
  {
    $this->administrationRegionId = $administrationRegionId;
  }
  /**
   * @return string
   */
  public function getAdministrationRegionId()
  {
    return $this->administrationRegionId;
  }
  /**
   * ID(s) of the Contest message(s) for this precinct.
   *
   * @param string[] $contestId
   */
  public function setContestId($contestId)
  {
    $this->contestId = $contestId;
  }
  /**
   * @return string[]
   */
  public function getContestId()
  {
    return $this->contestId;
  }
  /**
   * Required. Dataset ID. What datasets our Precincts come from.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * ID(s) of the PollingLocation message(s) for this precinct.
   *
   * @param string[] $earlyVoteSiteId
   */
  public function setEarlyVoteSiteId($earlyVoteSiteId)
  {
    $this->earlyVoteSiteId = $earlyVoteSiteId;
  }
  /**
   * @return string[]
   */
  public function getEarlyVoteSiteId()
  {
    return $this->earlyVoteSiteId;
  }
  /**
   * ID(s) of the ElectoralDistrict message(s) for this precinct.
   *
   * @param string[] $electoralDistrictId
   */
  public function setElectoralDistrictId($electoralDistrictId)
  {
    $this->electoralDistrictId = $electoralDistrictId;
  }
  /**
   * @return string[]
   */
  public function getElectoralDistrictId()
  {
    return $this->electoralDistrictId;
  }
  /**
   * Required. A unique identifier for this precinct.
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
   * Specifies if the precinct runs mail-only elections.
   *
   * @param bool $mailOnly
   */
  public function setMailOnly($mailOnly)
  {
    $this->mailOnly = $mailOnly;
  }
  /**
   * @return bool
   */
  public function getMailOnly()
  {
    return $this->mailOnly;
  }
  /**
   * Required. The name of the precinct.
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
   * The number of the precinct.
   *
   * @param string $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }
  /**
   * @return string
   */
  public function getNumber()
  {
    return $this->number;
  }
  /**
   * Encouraged. The OCD ID of the precinct
   *
   * @param string[] $ocdId
   */
  public function setOcdId($ocdId)
  {
    $this->ocdId = $ocdId;
  }
  /**
   * @return string[]
   */
  public function getOcdId()
  {
    return $this->ocdId;
  }
  /**
   * ID(s) of the PollingLocation message(s) for this precinct.
   *
   * @param string[] $pollingLocationId
   */
  public function setPollingLocationId($pollingLocationId)
  {
    $this->pollingLocationId = $pollingLocationId;
  }
  /**
   * @return string[]
   */
  public function getPollingLocationId()
  {
    return $this->pollingLocationId;
  }
  /**
   * ID(s) of the SpatialBoundary message(s) for this precinct. Used to specify
   * a geometrical boundary of the precinct.
   *
   * @param string[] $spatialBoundaryId
   */
  public function setSpatialBoundaryId($spatialBoundaryId)
  {
    $this->spatialBoundaryId = $spatialBoundaryId;
  }
  /**
   * @return string[]
   */
  public function getSpatialBoundaryId()
  {
    return $this->spatialBoundaryId;
  }
  /**
   * If present, this proto corresponds to one portion of split precinct. Other
   * portions of this precinct are guaranteed to have the same `name`. If not
   * present, this proto represents a full precicnt.
   *
   * @param string $splitName
   */
  public function setSplitName($splitName)
  {
    $this->splitName = $splitName;
  }
  /**
   * @return string
   */
  public function getSplitName()
  {
    return $this->splitName;
  }
  /**
   * Specifies the ward the precinct is contained within.
   *
   * @param string $ward
   */
  public function setWard($ward)
  {
    $this->ward = $ward;
  }
  /**
   * @return string
   */
  public function getWard()
  {
    return $this->ward;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2Precinct::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2Precinct');
