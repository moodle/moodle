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

use Google\Service\SecurityCommandCenter\EventThreatDetectionCustomModule;
use Google\Service\SecurityCommandCenter\ListDescendantEventThreatDetectionCustomModulesResponse;
use Google\Service\SecurityCommandCenter\ListEventThreatDetectionCustomModulesResponse;
use Google\Service\SecurityCommandCenter\SecuritycenterEmpty;

/**
 * The "customModules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $customModules = $securitycenterService->organizations_eventThreatDetectionSettings_customModules;
 *  </code>
 */
class OrganizationsEventThreatDetectionSettingsCustomModules extends \Google\Service\Resource
{
  /**
   * Creates a resident Event Threat Detection custom module at the scope of the
   * given Resource Manager parent, and also creates inherited custom modules for
   * all descendants of the given parent. These modules are enabled by default.
   * (customModules.create)
   *
   * @param string $parent Required. The new custom module's parent. Its format
   * is: * `organizations/{organization}/eventThreatDetectionSettings`. *
   * `folders/{folder}/eventThreatDetectionSettings`. *
   * `projects/{project}/eventThreatDetectionSettings`.
   * @param EventThreatDetectionCustomModule $postBody
   * @param array $optParams Optional parameters.
   * @return EventThreatDetectionCustomModule
   * @throws \Google\Service\Exception
   */
  public function create($parent, EventThreatDetectionCustomModule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], EventThreatDetectionCustomModule::class);
  }
  /**
   * Deletes the specified Event Threat Detection custom module and all of its
   * descendants in the Resource Manager hierarchy. This method is only supported
   * for resident custom modules. (customModules.delete)
   *
   * @param string $name Required. Name of the custom module to delete. Its format
   * is: * `organizations/{organization}/eventThreatDetectionSettings/customModule
   * s/{module}`. *
   * `folders/{folder}/eventThreatDetectionSettings/customModules/{module}`. *
   * `projects/{project}/eventThreatDetectionSettings/customModules/{module}`.
   * @param array $optParams Optional parameters.
   * @return SecuritycenterEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SecuritycenterEmpty::class);
  }
  /**
   * Gets an Event Threat Detection custom module. (customModules.get)
   *
   * @param string $name Required. Name of the custom module to get. Its format
   * is: * `organizations/{organization}/eventThreatDetectionSettings/customModule
   * s/{module}`. *
   * `folders/{folder}/eventThreatDetectionSettings/customModules/{module}`. *
   * `projects/{project}/eventThreatDetectionSettings/customModules/{module}`.
   * @param array $optParams Optional parameters.
   * @return EventThreatDetectionCustomModule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], EventThreatDetectionCustomModule::class);
  }
  /**
   * Lists all Event Threat Detection custom modules for the given Resource
   * Manager parent. This includes resident modules defined at the scope of the
   * parent along with modules inherited from ancestors.
   * (customModules.listOrganizationsEventThreatDetectionSettingsCustomModules)
   *
   * @param string $parent Required. Name of the parent to list custom modules
   * under. Its format is: *
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
   * `ListEventThreatDetectionCustomModules` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `ListEventThreatDetectionCustomModules` must match the call that provided the
   * page token.
   * @return ListEventThreatDetectionCustomModulesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsEventThreatDetectionSettingsCustomModules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEventThreatDetectionCustomModulesResponse::class);
  }
  /**
   * Lists all resident Event Threat Detection custom modules under the given
   * Resource Manager parent and its descendants. (customModules.listDescendant)
   *
   * @param string $parent Required. Name of the parent to list custom modules
   * under. Its format is: *
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
   * `ListDescendantEventThreatDetectionCustomModules` call. Provide this to
   * retrieve the subsequent page. When paginating, all other parameters provided
   * to `ListDescendantEventThreatDetectionCustomModules` must match the call that
   * provided the page token.
   * @return ListDescendantEventThreatDetectionCustomModulesResponse
   * @throws \Google\Service\Exception
   */
  public function listDescendant($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listDescendant', [$params], ListDescendantEventThreatDetectionCustomModulesResponse::class);
  }
  /**
   * Updates the Event Threat Detection custom module with the given name based on
   * the given update mask. Updating the enablement state is supported for both
   * resident and inherited modules (though resident modules cannot have an
   * enablement state of "inherited"). Updating the display name or configuration
   * of a module is supported for resident modules only. The type of a module
   * cannot be changed. (customModules.patch)
   *
   * @param string $name Immutable. The resource name of the Event Threat
   * Detection custom module. Its format is: * `organizations/{organization}/event
   * ThreatDetectionSettings/customModules/{module}`. *
   * `folders/{folder}/eventThreatDetectionSettings/customModules/{module}`. *
   * `projects/{project}/eventThreatDetectionSettings/customModules/{module}`.
   * @param EventThreatDetectionCustomModule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated. If empty all
   * mutable fields will be updated.
   * @return EventThreatDetectionCustomModule
   * @throws \Google\Service\Exception
   */
  public function patch($name, EventThreatDetectionCustomModule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], EventThreatDetectionCustomModule::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsEventThreatDetectionSettingsCustomModules::class, 'Google_Service_SecurityCommandCenter_Resource_OrganizationsEventThreatDetectionSettingsCustomModules');
