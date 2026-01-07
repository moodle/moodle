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

class GoogleChecksReportV1alphaCheckPermissionRestrictionViolationEvidence extends \Google\Collection
{
  protected $collection_key = 'permissionDetails';
  protected $permissionDetailsType = GoogleChecksReportV1alphaCheckPermissionRestrictionViolationEvidencePermissionDetails::class;
  protected $permissionDetailsDataType = 'array';

  /**
   * Permissions in violation.
   *
   * @param GoogleChecksReportV1alphaCheckPermissionRestrictionViolationEvidencePermissionDetails[] $permissionDetails
   */
  public function setPermissionDetails($permissionDetails)
  {
    $this->permissionDetails = $permissionDetails;
  }
  /**
   * @return GoogleChecksReportV1alphaCheckPermissionRestrictionViolationEvidencePermissionDetails[]
   */
  public function getPermissionDetails()
  {
    return $this->permissionDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaCheckPermissionRestrictionViolationEvidence::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaCheckPermissionRestrictionViolationEvidence');
