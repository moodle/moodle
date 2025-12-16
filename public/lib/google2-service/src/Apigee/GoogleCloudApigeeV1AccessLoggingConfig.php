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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AccessLoggingConfig extends \Google\Model
{
  /**
   * Optional. Boolean flag that specifies whether the customer access log
   * feature is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. Ship the access log entries that match the status_code defined in
   * the filter. The status_code is the only expected/supported filter field.
   * (Ex: status_code) The filter will parse it to the Common Expression
   * Language semantics for expression evaluation to build the filter condition.
   * (Ex: "filter": status_code >= 200 && status_code < 300 )
   *
   * @var string
   */
  public $filter;

  /**
   * Optional. Boolean flag that specifies whether the customer access log
   * feature is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. Ship the access log entries that match the status_code defined in
   * the filter. The status_code is the only expected/supported filter field.
   * (Ex: status_code) The filter will parse it to the Common Expression
   * Language semantics for expression evaluation to build the filter condition.
   * (Ex: "filter": status_code >= 200 && status_code < 300 )
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AccessLoggingConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AccessLoggingConfig');
