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

class CreateDataPolicyRequest extends \Google\Model
{
  protected $dataPolicyType = DataPolicy::class;
  protected $dataPolicyDataType = '';
  /**
   * Required. User-assigned (human readable) ID of the data policy that needs
   * to be unique within a project. Used as {data_policy_id} in part of the
   * resource name.
   *
   * @var string
   */
  public $dataPolicyId;

  /**
   * Required. The data policy to create. The `name` field does not need to be
   * provided for the data policy creation.
   *
   * @param DataPolicy $dataPolicy
   */
  public function setDataPolicy(DataPolicy $dataPolicy)
  {
    $this->dataPolicy = $dataPolicy;
  }
  /**
   * @return DataPolicy
   */
  public function getDataPolicy()
  {
    return $this->dataPolicy;
  }
  /**
   * Required. User-assigned (human readable) ID of the data policy that needs
   * to be unique within a project. Used as {data_policy_id} in part of the
   * resource name.
   *
   * @param string $dataPolicyId
   */
  public function setDataPolicyId($dataPolicyId)
  {
    $this->dataPolicyId = $dataPolicyId;
  }
  /**
   * @return string
   */
  public function getDataPolicyId()
  {
    return $this->dataPolicyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateDataPolicyRequest::class, 'Google_Service_BigQueryDataPolicyService_CreateDataPolicyRequest');
