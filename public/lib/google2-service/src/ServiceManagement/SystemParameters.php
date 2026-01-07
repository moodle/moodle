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

namespace Google\Service\ServiceManagement;

class SystemParameters extends \Google\Collection
{
  protected $collection_key = 'rules';
  protected $rulesType = SystemParameterRule::class;
  protected $rulesDataType = 'array';

  /**
   * Define system parameters. The parameters defined here will override the
   * default parameters implemented by the system. If this field is missing from
   * the service config, default system parameters will be used. Default system
   * parameters and names is implementation-dependent. Example: define api key
   * for all methods system_parameters rules: - selector: "*" parameters: -
   * name: api_key url_query_parameter: api_key Example: define 2 api key names
   * for a specific method. system_parameters rules: - selector: "/ListShelves"
   * parameters: - name: api_key http_header: Api-Key1 - name: api_key
   * http_header: Api-Key2 **NOTE:** All service configuration rules follow
   * "last one wins" order.
   *
   * @param SystemParameterRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return SystemParameterRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SystemParameters::class, 'Google_Service_ServiceManagement_SystemParameters');
