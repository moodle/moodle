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

namespace Google\Service\CloudControlsPartnerService;

class EkmMetadata extends \Google\Model
{
  /**
   * Unspecified EKM solution
   */
  public const EKM_SOLUTION_EKM_SOLUTION_UNSPECIFIED = 'EKM_SOLUTION_UNSPECIFIED';
  /**
   * EKM Partner Fortanix
   */
  public const EKM_SOLUTION_FORTANIX = 'FORTANIX';
  /**
   * EKM Partner FutureX
   */
  public const EKM_SOLUTION_FUTUREX = 'FUTUREX';
  /**
   * EKM Partner Thales
   */
  public const EKM_SOLUTION_THALES = 'THALES';
  /**
   * This enum value is never used.
   *
   * @deprecated
   */
  public const EKM_SOLUTION_VIRTRU = 'VIRTRU';
  /**
   * Endpoint for sending requests to the EKM for key provisioning during
   * Assured Workload creation.
   *
   * @var string
   */
  public $ekmEndpointUri;
  /**
   * The Cloud EKM partner.
   *
   * @var string
   */
  public $ekmSolution;

  /**
   * Endpoint for sending requests to the EKM for key provisioning during
   * Assured Workload creation.
   *
   * @param string $ekmEndpointUri
   */
  public function setEkmEndpointUri($ekmEndpointUri)
  {
    $this->ekmEndpointUri = $ekmEndpointUri;
  }
  /**
   * @return string
   */
  public function getEkmEndpointUri()
  {
    return $this->ekmEndpointUri;
  }
  /**
   * The Cloud EKM partner.
   *
   * Accepted values: EKM_SOLUTION_UNSPECIFIED, FORTANIX, FUTUREX, THALES,
   * VIRTRU
   *
   * @param self::EKM_SOLUTION_* $ekmSolution
   */
  public function setEkmSolution($ekmSolution)
  {
    $this->ekmSolution = $ekmSolution;
  }
  /**
   * @return self::EKM_SOLUTION_*
   */
  public function getEkmSolution()
  {
    return $this->ekmSolution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EkmMetadata::class, 'Google_Service_CloudControlsPartnerService_EkmMetadata');
