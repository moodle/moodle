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

use Google\Service\SecurityCommandCenter\EffectiveEventThreatDetectionCustomModule;
use Google\Service\SecurityCommandCenter\ListEffectiveEventThreatDetectionCustomModulesResponse;

/**
 * The "effectiveCustomModules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $effectiveCustomModules = $securitycenterService->projects_eventThreatDetectionSettings_effectiveCustomModules;
 *  </code>
 */
class ProjectsEventThreatDetectionSettingsEffectiveCustomModules extends \Google\Service\Resource
{
  /**
   * Gets an effective Event Threat Detection custom module at the given level.
   * (effectiveCustomModules.get)
   *
   * @param string $name Required. The resource name of the effective Event Threat
   * Detection custom module. Its format is: * `organizations/{organization}/event
   * ThreatDetectionSettings/effectiveCustomModules/{module}`. * `folders/{folder}
   * /eventThreatDetectionSettings/effectiveCustomModules/{module}`. * `projects/{
   * project}/eventThreatDetectionSettings/effectiveCustomModules/{module}`.
   * @param array $optParams Optional parameters.
   * @return EffectiveEventThreatDetectionCustomModule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], EffectiveEventThreatDetectionCustomModule::class);
  }
  /**
   * Lists all effective Event Threat Detection custom modules for the given
   * parent. This includes resident modules defined at the scope of the parent
   * along with modules inherited from its ancestors. (effectiveCustomModules.list
   * ProjectsEventThreatDetectionSettingsEffectiveCustomModules)
   *
   * @param string $parent Required. Name of the parent to list custom modules
   * for. Its format is: *
   * `organizations/{organization}/eventThreatDetectionSettings`. *
   * `folders/{folder}/eventThreatDetectionSettings`. *
   * `projects/{project}/eventThreatDetectionSettings`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of modules to return. The service
   * may return fewer than this value. If unspecified, at most 10 configs will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListEffectiveEventThreatDetectionCustomModules` call. Provide this to
   * retrieve the subsequent page. When paginating, all other parameters provided
   * to `ListEffectiveEventThreatDetectionCustomModules` must match the call that
   * provided the page token.
   * @return ListEffectiveEventThreatDetectionCustomModulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsEventThreatDetectionSettingsEffectiveCustomModules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEffectiveEventThreatDetectionCustomModulesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsEventThreatDetectionSettingsEffectiveCustomModules::class, 'Google_Service_SecurityCommandCenter_Resource_ProjectsEventThreatDetectionSettingsEffectiveCustomModules');
