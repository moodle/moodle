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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1MutatePartnerPermissionsRequest extends \Google\Model
{
  /**
   * Optional. The etag of the workload. If this is provided, it must match the
   * server's etag.
   *
   * @var string
   */
  public $etag;
  protected $partnerPermissionsType = GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions::class;
  protected $partnerPermissionsDataType = '';
  /**
   * Required. The list of fields to be updated. E.g. update_mask { paths:
   * "partner_permissions.data_logs_viewer"}
   *
   * @var string
   */
  public $updateMask;

  /**
   * Optional. The etag of the workload. If this is provided, it must match the
   * server's etag.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. The partner permissions to be updated.
   *
   * @param GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions $partnerPermissions
   */
  public function setPartnerPermissions(GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions $partnerPermissions)
  {
    $this->partnerPermissions = $partnerPermissions;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions
   */
  public function getPartnerPermissions()
  {
    return $this->partnerPermissions;
  }
  /**
   * Required. The list of fields to be updated. E.g. update_mask { paths:
   * "partner_permissions.data_logs_viewer"}
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1MutatePartnerPermissionsRequest::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1MutatePartnerPermissionsRequest');
