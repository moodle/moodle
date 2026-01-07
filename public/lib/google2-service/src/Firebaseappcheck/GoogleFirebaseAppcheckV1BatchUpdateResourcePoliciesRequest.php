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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesRequest extends \Google\Collection
{
  protected $collection_key = 'requests';
  protected $requestsType = GoogleFirebaseAppcheckV1UpdateResourcePolicyRequest::class;
  protected $requestsDataType = 'array';
  /**
   * Optional. A comma-separated list of names of fields in the ResourcePolicy
   * objects to update. Example: `enforcement_mode`. If this field is present,
   * the `update_mask` field in the UpdateResourcePolicyRequest messages must
   * all match this field, or the entire batch fails and no updates will be
   * committed.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The request messages specifying the ResourcePolicy objects to
   * update. A maximum of 100 objects can be updated in a batch.
   *
   * @param GoogleFirebaseAppcheckV1UpdateResourcePolicyRequest[] $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return GoogleFirebaseAppcheckV1UpdateResourcePolicyRequest[]
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * Optional. A comma-separated list of names of fields in the ResourcePolicy
   * objects to update. Example: `enforcement_mode`. If this field is present,
   * the `update_mask` field in the UpdateResourcePolicyRequest messages must
   * all match this field, or the entire batch fails and no updates will be
   * committed.
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
class_alias(GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesRequest::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesRequest');
