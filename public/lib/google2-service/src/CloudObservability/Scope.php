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

namespace Google\Service\CloudObservability;

class Scope extends \Google\Model
{
  /**
   * Required. The full resource name of the `LogScope`. For example:
   * //logging.googleapis.com/projects/myproject/locations/global/logScopes/my-
   * log-scope
   *
   * @var string
   */
  public $logScope;
  /**
   * Identifier. Name of the resource. The format is:
   * projects/{project}/locations/{location}/scopes/{scope} The `{location}`
   * field must be set to `global`. The `{scope}` field must be set to
   * `_Default`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The resource name of the `TraceScope`. For example:
   * projects/myproject/locations/global/traceScopes/my-trace-scope
   *
   * @var string
   */
  public $traceScope;
  /**
   * Output only. Update timestamp. Note: The Update timestamp for the default
   * scope is initially unset.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The full resource name of the `LogScope`. For example:
   * //logging.googleapis.com/projects/myproject/locations/global/logScopes/my-
   * log-scope
   *
   * @param string $logScope
   */
  public function setLogScope($logScope)
  {
    $this->logScope = $logScope;
  }
  /**
   * @return string
   */
  public function getLogScope()
  {
    return $this->logScope;
  }
  /**
   * Identifier. Name of the resource. The format is:
   * projects/{project}/locations/{location}/scopes/{scope} The `{location}`
   * field must be set to `global`. The `{scope}` field must be set to
   * `_Default`.
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
  /**
   * Required. The resource name of the `TraceScope`. For example:
   * projects/myproject/locations/global/traceScopes/my-trace-scope
   *
   * @param string $traceScope
   */
  public function setTraceScope($traceScope)
  {
    $this->traceScope = $traceScope;
  }
  /**
   * @return string
   */
  public function getTraceScope()
  {
    return $this->traceScope;
  }
  /**
   * Output only. Update timestamp. Note: The Update timestamp for the default
   * scope is initially unset.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scope::class, 'Google_Service_CloudObservability_Scope');
