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

namespace Google\Service\Compute;

class ZoneSetNestedPolicyRequest extends \Google\Collection
{
  protected $collection_key = 'bindings';
  protected $bindingsType = Binding::class;
  protected $bindingsDataType = 'array';
  /**
   * Flatten Policy to create a backward compatible wire-format. Deprecated. Use
   * 'policy' to specify the etag.
   *
   * @var string
   */
  public $etag;
  protected $policyType = Policy::class;
  protected $policyDataType = '';

  /**
   * Flatten Policy to create a backwacd compatible wire-format. Deprecated. Use
   * 'policy' to specify bindings.
   *
   * @param Binding[] $bindings
   */
  public function setBindings($bindings)
  {
    $this->bindings = $bindings;
  }
  /**
   * @return Binding[]
   */
  public function getBindings()
  {
    return $this->bindings;
  }
  /**
   * Flatten Policy to create a backward compatible wire-format. Deprecated. Use
   * 'policy' to specify the etag.
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
   * REQUIRED: The complete policy to be applied to the 'resource'. The size of
   * the policy is limited to a few 10s of KB. An empty policy is in general a
   * valid policy but certain services (like Projects) might reject them.
   *
   * @param Policy $policy
   */
  public function setPolicy(Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ZoneSetNestedPolicyRequest::class, 'Google_Service_Compute_ZoneSetNestedPolicyRequest');
