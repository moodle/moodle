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

namespace Google\Service\CloudDataplex;

class CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation extends \Google\Collection
{
  protected $collection_key = 'policyId';
  /**
   * @var string[]
   */
  public $policyId;

  /**
   * @param string[]
   */
  public function setPolicyId($policyId)
  {
    $this->policyId = $policyId;
  }
  /**
   * @return string[]
   */
  public function getPolicyId()
  {
    return $this->policyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation::class, 'Google_Service_CloudDataplex_CloudReliabilityZicyWs3DataplaneProtosBlobstoreLocation');
