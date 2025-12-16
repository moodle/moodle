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

namespace Google\Service\Bigquery;

class BatchDeleteRowAccessPoliciesRequest extends \Google\Collection
{
  protected $collection_key = 'policyIds';
  /**
   * If set to true, it deletes the row access policy even if it's the last row
   * access policy on the table and the deletion will widen the access rather
   * narrowing it.
   *
   * @var bool
   */
  public $force;
  /**
   * Required. Policy IDs of the row access policies.
   *
   * @var string[]
   */
  public $policyIds;

  /**
   * If set to true, it deletes the row access policy even if it's the last row
   * access policy on the table and the deletion will widen the access rather
   * narrowing it.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Required. Policy IDs of the row access policies.
   *
   * @param string[] $policyIds
   */
  public function setPolicyIds($policyIds)
  {
    $this->policyIds = $policyIds;
  }
  /**
   * @return string[]
   */
  public function getPolicyIds()
  {
    return $this->policyIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchDeleteRowAccessPoliciesRequest::class, 'Google_Service_Bigquery_BatchDeleteRowAccessPoliciesRequest');
