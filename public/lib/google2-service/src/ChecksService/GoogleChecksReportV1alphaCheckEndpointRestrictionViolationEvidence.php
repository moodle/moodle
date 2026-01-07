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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaCheckEndpointRestrictionViolationEvidence extends \Google\Collection
{
  protected $collection_key = 'endpointDetails';
  protected $endpointDetailsType = GoogleChecksReportV1alphaCheckEndpointRestrictionViolationEvidenceEndpointDetails::class;
  protected $endpointDetailsDataType = 'array';

  /**
   * Endpoints in violation.
   *
   * @param GoogleChecksReportV1alphaCheckEndpointRestrictionViolationEvidenceEndpointDetails[] $endpointDetails
   */
  public function setEndpointDetails($endpointDetails)
  {
    $this->endpointDetails = $endpointDetails;
  }
  /**
   * @return GoogleChecksReportV1alphaCheckEndpointRestrictionViolationEvidenceEndpointDetails[]
   */
  public function getEndpointDetails()
  {
    return $this->endpointDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaCheckEndpointRestrictionViolationEvidence::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaCheckEndpointRestrictionViolationEvidence');
