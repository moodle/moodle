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

class BackendServiceLocalityLoadBalancingPolicyConfigCustomPolicy extends \Google\Model
{
  /**
   * An optional, arbitrary JSON object with configuration data, understood by a
   * locally installed custom policy implementation.
   *
   * @var string
   */
  public $data;
  /**
   * Identifies the custom policy.
   *
   * The value should match the name of a custom implementation registered on
   * the gRPC clients. It should follow protocol buffer message naming
   * conventions and include the full path (for example, myorg.CustomLbPolicy).
   * The maximum length is 256 characters.
   *
   * Do not specify the same custom policy more than once for a backend. If you
   * do, the configuration is rejected.
   *
   * For an example of how to use this field, seeUse a custom policy.
   *
   * @var string
   */
  public $name;

  /**
   * An optional, arbitrary JSON object with configuration data, understood by a
   * locally installed custom policy implementation.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Identifies the custom policy.
   *
   * The value should match the name of a custom implementation registered on
   * the gRPC clients. It should follow protocol buffer message naming
   * conventions and include the full path (for example, myorg.CustomLbPolicy).
   * The maximum length is 256 characters.
   *
   * Do not specify the same custom policy more than once for a backend. If you
   * do, the configuration is rejected.
   *
   * For an example of how to use this field, seeUse a custom policy.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceLocalityLoadBalancingPolicyConfigCustomPolicy::class, 'Google_Service_Compute_BackendServiceLocalityLoadBalancingPolicyConfigCustomPolicy');
