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

namespace Google\Service\Sasportal;

class SasPortalDeviceMetadata extends \Google\Model
{
  /**
   * If populated, the Antenna Model Pattern to use. Format is:
   * `RecordCreatorId:PatternId`
   *
   * @var string
   */
  public $antennaModel;
  /**
   * Common Channel Group (CCG). A group of CBSDs in the same ICG requesting a
   * common primary channel assignment. For more details, see [CBRSA-TS-2001
   * V3.0.0](https://ongoalliance.org/wp-content/uploads/2020/02/CBRSA-
   * TS-2001-V3.0.0_Approved-for-publication.pdf).
   *
   * @var string
   */
  public $commonChannelGroup;
  /**
   * Interference Coordination Group (ICG). A group of CBSDs that manage their
   * own interference with the group. For more details, see [CBRSA-TS-2001
   * V3.0.0](https://ongoalliance.org/wp-content/uploads/2020/02/CBRSA-
   * TS-2001-V3.0.0_Approved-for-publication.pdf).
   *
   * @var string
   */
  public $interferenceCoordinationGroup;
  /**
   * Output only. Set to `true` if a CPI has validated that they have
   * coordinated with the National Quiet Zone office.
   *
   * @deprecated
   * @var bool
   */
  public $nrqzValidated;
  protected $nrqzValidationType = SasPortalNrqzValidation::class;
  protected $nrqzValidationDataType = '';

  /**
   * If populated, the Antenna Model Pattern to use. Format is:
   * `RecordCreatorId:PatternId`
   *
   * @param string $antennaModel
   */
  public function setAntennaModel($antennaModel)
  {
    $this->antennaModel = $antennaModel;
  }
  /**
   * @return string
   */
  public function getAntennaModel()
  {
    return $this->antennaModel;
  }
  /**
   * Common Channel Group (CCG). A group of CBSDs in the same ICG requesting a
   * common primary channel assignment. For more details, see [CBRSA-TS-2001
   * V3.0.0](https://ongoalliance.org/wp-content/uploads/2020/02/CBRSA-
   * TS-2001-V3.0.0_Approved-for-publication.pdf).
   *
   * @param string $commonChannelGroup
   */
  public function setCommonChannelGroup($commonChannelGroup)
  {
    $this->commonChannelGroup = $commonChannelGroup;
  }
  /**
   * @return string
   */
  public function getCommonChannelGroup()
  {
    return $this->commonChannelGroup;
  }
  /**
   * Interference Coordination Group (ICG). A group of CBSDs that manage their
   * own interference with the group. For more details, see [CBRSA-TS-2001
   * V3.0.0](https://ongoalliance.org/wp-content/uploads/2020/02/CBRSA-
   * TS-2001-V3.0.0_Approved-for-publication.pdf).
   *
   * @param string $interferenceCoordinationGroup
   */
  public function setInterferenceCoordinationGroup($interferenceCoordinationGroup)
  {
    $this->interferenceCoordinationGroup = $interferenceCoordinationGroup;
  }
  /**
   * @return string
   */
  public function getInterferenceCoordinationGroup()
  {
    return $this->interferenceCoordinationGroup;
  }
  /**
   * Output only. Set to `true` if a CPI has validated that they have
   * coordinated with the National Quiet Zone office.
   *
   * @deprecated
   * @param bool $nrqzValidated
   */
  public function setNrqzValidated($nrqzValidated)
  {
    $this->nrqzValidated = $nrqzValidated;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getNrqzValidated()
  {
    return $this->nrqzValidated;
  }
  /**
   * Output only. National Radio Quiet Zone validation info.
   *
   * @param SasPortalNrqzValidation $nrqzValidation
   */
  public function setNrqzValidation(SasPortalNrqzValidation $nrqzValidation)
  {
    $this->nrqzValidation = $nrqzValidation;
  }
  /**
   * @return SasPortalNrqzValidation
   */
  public function getNrqzValidation()
  {
    return $this->nrqzValidation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalDeviceMetadata::class, 'Google_Service_Sasportal_SasPortalDeviceMetadata');
