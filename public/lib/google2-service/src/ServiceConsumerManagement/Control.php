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

namespace Google\Service\ServiceConsumerManagement;

class Control extends \Google\Collection
{
  protected $collection_key = 'methodPolicies';
  /**
   * The service controller environment to use. If empty, no control plane
   * feature (like quota and billing) will be enabled. The recommended value for
   * most services is servicecontrol.googleapis.com
   *
   * @var string
   */
  public $environment;
  protected $methodPoliciesType = MethodPolicy::class;
  protected $methodPoliciesDataType = 'array';

  /**
   * The service controller environment to use. If empty, no control plane
   * feature (like quota and billing) will be enabled. The recommended value for
   * most services is servicecontrol.googleapis.com
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Defines policies applying to the API methods of the service.
   *
   * @param MethodPolicy[] $methodPolicies
   */
  public function setMethodPolicies($methodPolicies)
  {
    $this->methodPolicies = $methodPolicies;
  }
  /**
   * @return MethodPolicy[]
   */
  public function getMethodPolicies()
  {
    return $this->methodPolicies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Control::class, 'Google_Service_ServiceConsumerManagement_Control');
