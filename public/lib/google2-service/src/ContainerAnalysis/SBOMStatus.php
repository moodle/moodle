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

namespace Google\Service\ContainerAnalysis;

class SBOMStatus extends \Google\Model
{
  /**
   * Default unknown state.
   */
  public const SBOM_STATE_SBOM_STATE_UNSPECIFIED = 'SBOM_STATE_UNSPECIFIED';
  /**
   * SBOM scanning is pending.
   */
  public const SBOM_STATE_PENDING = 'PENDING';
  /**
   * SBOM scanning has completed.
   */
  public const SBOM_STATE_COMPLETE = 'COMPLETE';
  /**
   * If there was an error generating an SBOM, this will indicate what that
   * error was.
   *
   * @var string
   */
  public $error;
  /**
   * The progress of the SBOM generation.
   *
   * @var string
   */
  public $sbomState;

  /**
   * If there was an error generating an SBOM, this will indicate what that
   * error was.
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The progress of the SBOM generation.
   *
   * Accepted values: SBOM_STATE_UNSPECIFIED, PENDING, COMPLETE
   *
   * @param self::SBOM_STATE_* $sbomState
   */
  public function setSbomState($sbomState)
  {
    $this->sbomState = $sbomState;
  }
  /**
   * @return self::SBOM_STATE_*
   */
  public function getSbomState()
  {
    return $this->sbomState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SBOMStatus::class, 'Google_Service_ContainerAnalysis_SBOMStatus');
