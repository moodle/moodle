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

namespace Google\Service\DatabaseMigrationService\Resource;

use Google\Service\DatabaseMigrationService\DatamigrationEmpty;
use Google\Service\DatabaseMigrationService\ImportMappingRulesRequest;
use Google\Service\DatabaseMigrationService\ListMappingRulesResponse;
use Google\Service\DatabaseMigrationService\MappingRule;
use Google\Service\DatabaseMigrationService\Operation;

/**
 * The "mappingRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datamigrationService = new Google\Service\DatabaseMigrationService(...);
 *   $mappingRules = $datamigrationService->projects_locations_conversionWorkspaces_mappingRules;
 *  </code>
 */
class ProjectsLocationsConversionWorkspacesMappingRules extends \Google\Service\Resource
{
  /**
   * Creates a new mapping rule for a given conversion workspace.
   * (mappingRules.create)
   *
   * @param string $parent Required. The parent which owns this collection of
   * mapping rules.
   * @param MappingRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string mappingRuleId Required. The ID of the rule to create.
   * @opt_param string requestId A unique ID used to identify the request. If the
   * server receives two requests with the same ID, then the second request is
   * ignored. It is recommended to always set this value to a UUID. The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @return MappingRule
   * @throws \Google\Service\Exception
   */
  public function create($parent, MappingRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], MappingRule::class);
  }
  /**
   * Deletes a single mapping rule. (mappingRules.delete)
   *
   * @param string $name Required. Name of the mapping rule resource to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the server receives two requests with the same ID, then the
   * second request is ignored. It is recommended to always set this value to a
   * UUID. The ID must contain only letters (a-z, A-Z), numbers (0-9), underscores
   * (_), and hyphens (-). The maximum length is 40 characters.
   * @return DatamigrationEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DatamigrationEmpty::class);
  }
  /**
   * Gets the details of a mapping rule. (mappingRules.get)
   *
   * @param string $name Required. Name of the mapping rule resource to get.
   * Example: conversionWorkspaces/123/mappingRules/rule123 In order to retrieve a
   * previous revision of the mapping rule, also provide the revision ID. Example:
   * conversionWorkspace/123/mappingRules/rule123@c7cfa2a8c7cfa2a8c7cfa2a8c7cfa2a8
   * @param array $optParams Optional parameters.
   * @return MappingRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MappingRule::class);
  }
  /**
   * Imports the mapping rules for a given conversion workspace. Supports various
   * formats of external rules files. (mappingRules.import)
   *
   * @param string $parent Required. Name of the conversion workspace resource to
   * import the rules to in the form of: projects/{project}/locations/{location}/c
   * onversionWorkspaces/{conversion_workspace}.
   * @param ImportMappingRulesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($parent, ImportMappingRulesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Lists the mapping rules for a specific conversion workspace.
   * (mappingRules.listProjectsLocationsConversionWorkspacesMappingRules)
   *
   * @param string $parent Required. Name of the conversion workspace resource
   * whose mapping rules are listed in the form of: projects/{project}/locations/{
   * location}/conversionWorkspaces/{conversion_workspace}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of rules to return. The service
   * may return fewer than this value.
   * @opt_param string pageToken The nextPageToken value received in the previous
   * call to mappingRules.list, used in the subsequent request to retrieve the
   * next page of results. On first call this should be left blank. When
   * paginating, all other parameters provided to mappingRules.list must match the
   * call that provided the page token.
   * @return ListMappingRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConversionWorkspacesMappingRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMappingRulesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConversionWorkspacesMappingRules::class, 'Google_Service_DatabaseMigrationService_Resource_ProjectsLocationsConversionWorkspacesMappingRules');
