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

class UsageRule extends \Google\Model
{
  /**
   * Use this rule to configure unregistered calls for the service. Unregistered
   * calls are calls that do not contain consumer project identity. (Example:
   * calls that do not contain an API key). WARNING: By default, API methods do
   * not allow unregistered calls, and each method call must be identified by a
   * consumer project identity.
   *
   * @var bool
   */
  public $allowUnregisteredCalls;
  /**
   * Selects the methods to which this rule applies. Use '*' to indicate all
   * methods in all APIs. Refer to selector for syntax details.
   *
   * @var string
   */
  public $selector;
  /**
   * If true, the selected method should skip service control and the control
   * plane features, such as quota and billing, will not be available. This flag
   * is used by Google Cloud Endpoints to bypass checks for internal methods,
   * such as service health check methods.
   *
   * @var bool
   */
  public $skipServiceControl;

  /**
   * Use this rule to configure unregistered calls for the service. Unregistered
   * calls are calls that do not contain consumer project identity. (Example:
   * calls that do not contain an API key). WARNING: By default, API methods do
   * not allow unregistered calls, and each method call must be identified by a
   * consumer project identity.
   *
   * @param bool $allowUnregisteredCalls
   */
  public function setAllowUnregisteredCalls($allowUnregisteredCalls)
  {
    $this->allowUnregisteredCalls = $allowUnregisteredCalls;
  }
  /**
   * @return bool
   */
  public function getAllowUnregisteredCalls()
  {
    return $this->allowUnregisteredCalls;
  }
  /**
   * Selects the methods to which this rule applies. Use '*' to indicate all
   * methods in all APIs. Refer to selector for syntax details.
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
  /**
   * If true, the selected method should skip service control and the control
   * plane features, such as quota and billing, will not be available. This flag
   * is used by Google Cloud Endpoints to bypass checks for internal methods,
   * such as service health check methods.
   *
   * @param bool $skipServiceControl
   */
  public function setSkipServiceControl($skipServiceControl)
  {
    $this->skipServiceControl = $skipServiceControl;
  }
  /**
   * @return bool
   */
  public function getSkipServiceControl()
  {
    return $this->skipServiceControl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageRule::class, 'Google_Service_ServiceUsage_UsageRule');
