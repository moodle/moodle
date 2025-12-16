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

namespace Google\Service\SecurityCommandCenter\Resource;

use Google\Service\SecurityCommandCenter\GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule;
use Google\Service\SecurityCommandCenter\ListEffectiveSecurityHealthAnalyticsCustomModulesResponse;

/**
 * The "effectiveCustomModules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $effectiveCustomModules = $securitycenterService->folders_securityHealthAnalyticsSettings_effectiveCustomModules;
 *  </code>
 */
class FoldersSecurityHealthAnalyticsSettingsEffectiveCustomModules extends \Google\Service\Resource
{
  /**
   * Retrieves an EffectiveSecurityHealthAnalyticsCustomModule.
   * (effectiveCustomModules.get)
   *
   * @param string $name Required. Name of the effective custom module to get. Its
   * format is `organizations/{organization}/securityHealthAnalyticsSettings/effec
   * tiveCustomModules/{customModule}`, `folders/{folder}/securityHealthAnalyticsS
   * ettings/effectiveCustomModules/{customModule}`, or `projects/{project}/securi
   * tyHealthAnalyticsSettings/effectiveCustomModules/{customModule}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule::class);
  }
  /**
   * Returns a list of all EffectiveSecurityHealthAnalyticsCustomModules for the
   * given parent. This includes resident modules defined at the scope of the
   * parent, and inherited modules, inherited from CRM ancestors. (effectiveCustom
   * Modules.listFoldersSecurityHealthAnalyticsSettingsEffectiveCustomModules)
   *
   * @param string $parent Required. Name of parent to list effective custom
   * modules. Its format is
   * `organizations/{organization}/securityHealthAnalyticsSettings`,
   * `folders/{folder}/securityHealthAnalyticsSettings`, or
   * `projects/{project}/securityHealthAnalyticsSettings`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of results to return in a single
   * response. Default is 10, minimum is 1, maximum is 1000.
   * @opt_param string pageToken The value returned by the last call indicating a
   * continuation
   * @return ListEffectiveSecurityHealthAnalyticsCustomModulesResponse
   * @throws \Google\Service\Exception
   */
  public function listFoldersSecurityHealthAnalyticsSettingsEffectiveCustomModules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEffectiveSecurityHealthAnalyticsCustomModulesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FoldersSecurityHealthAnalyticsSettingsEffectiveCustomModules::class, 'Google_Service_SecurityCommandCenter_Resource_FoldersSecurityHealthAnalyticsSettingsEffectiveCustomModules');
