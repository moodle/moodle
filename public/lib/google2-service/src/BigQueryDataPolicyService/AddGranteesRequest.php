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

namespace Google\Service\BigQueryDataPolicyService;

class AddGranteesRequest extends \Google\Collection
{
  protected $collection_key = 'grantees';
  /**
   * Required. IAM principal that should be granted Fine Grained Access to the
   * underlying data goverened by the data policy. The target data policy is
   * determined by the `data_policy` field. Uses the [IAM V2 principal
   * syntax](https://cloud.google.com/iam/docs/principal-identifiers#v2).
   * Supported principal types: * User * Group * Service account
   *
   * @var string[]
   */
  public $grantees;

  /**
   * Required. IAM principal that should be granted Fine Grained Access to the
   * underlying data goverened by the data policy. The target data policy is
   * determined by the `data_policy` field. Uses the [IAM V2 principal
   * syntax](https://cloud.google.com/iam/docs/principal-identifiers#v2).
   * Supported principal types: * User * Group * Service account
   *
   * @param string[] $grantees
   */
  public function setGrantees($grantees)
  {
    $this->grantees = $grantees;
  }
  /**
   * @return string[]
   */
  public function getGrantees()
  {
    return $this->grantees;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddGranteesRequest::class, 'Google_Service_BigQueryDataPolicyService_AddGranteesRequest');
