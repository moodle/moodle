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

class GoogleChecksReportV1alphaDataTypeEvidence extends \Google\Collection
{
  protected $collection_key = 'privacyPolicyTexts';
  protected $endpointsType = GoogleChecksReportV1alphaDataTypeEndpointEvidence::class;
  protected $endpointsDataType = 'array';
  protected $permissionsType = GoogleChecksReportV1alphaDataTypePermissionEvidence::class;
  protected $permissionsDataType = 'array';
  protected $privacyPolicyTextsType = GoogleChecksReportV1alphaDataTypePrivacyPolicyTextEvidence::class;
  protected $privacyPolicyTextsDataType = 'array';

  /**
   * List of endpoints the data type was sent to.
   *
   * @param GoogleChecksReportV1alphaDataTypeEndpointEvidence[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return GoogleChecksReportV1alphaDataTypeEndpointEvidence[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * List of included permissions that imply collection of the data type.
   *
   * @param GoogleChecksReportV1alphaDataTypePermissionEvidence[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return GoogleChecksReportV1alphaDataTypePermissionEvidence[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * List of privacy policy texts that imply collection of the data type.
   *
   * @param GoogleChecksReportV1alphaDataTypePrivacyPolicyTextEvidence[] $privacyPolicyTexts
   */
  public function setPrivacyPolicyTexts($privacyPolicyTexts)
  {
    $this->privacyPolicyTexts = $privacyPolicyTexts;
  }
  /**
   * @return GoogleChecksReportV1alphaDataTypePrivacyPolicyTextEvidence[]
   */
  public function getPrivacyPolicyTexts()
  {
    return $this->privacyPolicyTexts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaDataTypeEvidence::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaDataTypeEvidence');
