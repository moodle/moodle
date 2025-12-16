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

namespace Google\Service\CloudDataplex\Resource;

use Google\Service\CloudDataplex\GoogleCloudDataplexV1EntryLink;

/**
 * The "entryLinks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $entryLinks = $dataplexService->projects_locations_entryGroups_entryLinks;
 *  </code>
 */
class ProjectsLocationsEntryGroupsEntryLinks extends \Google\Service\Resource
{
  /**
   * Creates an Entry Link. (entryLinks.create)
   *
   * @param string $parent Required. The resource name of the parent Entry Group:
   * projects/{project_id_or_number}/locations/{location_id}/entryGroups/{entry_gr
   * oup_id}.
   * @param GoogleCloudDataplexV1EntryLink $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string entryLinkId Required. Entry Link identifier * Must contain
   * only lowercase letters, numbers and hyphens. * Must start with a letter. *
   * Must be between 1-63 characters. * Must end with a number or a letter. * Must
   * be unique within the EntryGroup.
   * @return GoogleCloudDataplexV1EntryLink
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1EntryLink $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDataplexV1EntryLink::class);
  }
  /**
   * Deletes an Entry Link. (entryLinks.delete)
   *
   * @param string $name Required. The resource name of the Entry Link: projects/{
   * project_id_or_number}/locations/{location_id}/entryGroups/{entry_group_id}/en
   * tryLinks/{entry_link_id}.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1EntryLink
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudDataplexV1EntryLink::class);
  }
  /**
   * Gets an Entry Link. (entryLinks.get)
   *
   * @param string $name Required. The resource name of the Entry Link: projects/{
   * project_id_or_number}/locations/{location_id}/entryGroups/{entry_group_id}/en
   * tryLinks/{entry_link_id}.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1EntryLink
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1EntryLink::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEntryGroupsEntryLinks::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsEntryGroupsEntryLinks');
