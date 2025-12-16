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

namespace Google\Service\ServiceUsage;

class MethodPolicy extends \Google\Collection
{
  protected $collection_key = 'requestPolicies';
  protected $requestPoliciesType = FieldPolicy::class;
  protected $requestPoliciesDataType = 'array';
  /**
   * Selects a method to which these policies should be enforced, for example,
   * "google.pubsub.v1.Subscriber.CreateSubscription". Refer to selector for
   * syntax details. NOTE: This field must not be set in the proto annotation.
   * It will be automatically filled by the service config compiler .
   *
   * @var string
   */
  public $selector;

  /**
   * Policies that are applicable to the request message.
   *
   * @param FieldPolicy[] $requestPolicies
   */
  public function setRequestPolicies($requestPolicies)
  {
    $this->requestPolicies = $requestPolicies;
  }
  /**
   * @return FieldPolicy[]
   */
  public function getRequestPolicies()
  {
    return $this->requestPolicies;
  }
  /**
   * Selects a method to which these policies should be enforced, for example,
   * "google.pubsub.v1.Subscriber.CreateSubscription". Refer to selector for
   * syntax details. NOTE: This field must not be set in the proto annotation.
   * It will be automatically filled by the service config compiler .
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MethodPolicy::class, 'Google_Service_ServiceUsage_MethodPolicy');
