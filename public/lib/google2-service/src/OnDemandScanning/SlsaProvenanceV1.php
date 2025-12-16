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

namespace Google\Service\OnDemandScanning;

class SlsaProvenanceV1 extends \Google\Model
{
  protected $buildDefinitionType = BuildDefinition::class;
  protected $buildDefinitionDataType = '';
  protected $runDetailsType = RunDetails::class;
  protected $runDetailsDataType = '';

  /**
   * @param BuildDefinition $buildDefinition
   */
  public function setBuildDefinition(BuildDefinition $buildDefinition)
  {
    $this->buildDefinition = $buildDefinition;
  }
  /**
   * @return BuildDefinition
   */
  public function getBuildDefinition()
  {
    return $this->buildDefinition;
  }
  /**
   * @param RunDetails $runDetails
   */
  public function setRunDetails(RunDetails $runDetails)
  {
    $this->runDetails = $runDetails;
  }
  /**
   * @return RunDetails
   */
  public function getRunDetails()
  {
    return $this->runDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlsaProvenanceV1::class, 'Google_Service_OnDemandScanning_SlsaProvenanceV1');
