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

namespace Google\Service\Connectors\Resource;

use Google\Service\Connectors\Operation;

/**
 * The "entitieswithacls" collection of methods.
 * Typical usage is:
 *  <code>
 *   $connectorsService = new Google\Service\Connectors(...);
 *   $entitieswithacls = $connectorsService->projects_locations_connections_entityTypes_entitieswithacls;
 *  </code>
 */
class ProjectsLocationsConnectionsEntityTypesEntitieswithacls extends \Google\Service\Resource
{
  /**
   * Lists entity rows with ACLs of a particular entity type contained in the
   * request. Note: 1. Currently, only max of one 'sort_by' column is supported.
   * 2. If no 'sort_by' column is provided, the primary key of the table is used.
   * If zero or more than one primary key is available, we default to the
   * unpaginated list entities logic which only returns the first page. 3. The
   * values of the 'sort_by' columns must uniquely identify an entity row,
   * otherwise undefined behaviors may be observed during pagination. 4. Since
   * transactions are not supported, any updates, inserts or deletes during
   * pagination can lead to stale data being returned or other unexpected
   * behaviors. (entitieswithacls.listProjectsLocationsConnectionsEntityTypesEntit
   * ieswithacls)
   *
   * @param string $parent Required. Resource name of the Entity Type. Format: pro
   * jects/{project}/locations/{location}/connections/{connection}/entityTypes/{ty
   * pe}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string conditions Conditions to be used when listing entities.
   * From a proto standpoint, There are no restrictions on what can be passed
   * using this field. The connector documentation should have information about
   * what format of filters/conditions are supported.
   * @opt_param string gsutilUri Format: gs://object_path
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectionsEntityTypesEntitieswithacls($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnectionsEntityTypesEntitieswithacls::class, 'Google_Service_Connectors_Resource_ProjectsLocationsConnectionsEntityTypesEntitieswithacls');
