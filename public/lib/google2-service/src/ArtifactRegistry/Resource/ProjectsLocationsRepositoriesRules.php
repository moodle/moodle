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

namespace Google\Service\ArtifactRegistry\Resource;

use Google\Service\ArtifactRegistry\ArtifactregistryEmpty;
use Google\Service\ArtifactRegistry\GoogleDevtoolsArtifactregistryV1Rule;
use Google\Service\ArtifactRegistry\ListRulesResponse;

/**
 * The "rules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $rules = $artifactregistryService->projects_locations_repositories_rules;
 *  </code>
 */
class ProjectsLocationsRepositoriesRules extends \Google\Service\Resource
{
  /**
   * Creates a rule. (rules.create)
   *
   * @param string $parent Required. The name of the parent resource where the
   * rule will be created.
   * @param GoogleDevtoolsArtifactregistryV1Rule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string ruleId The rule id to use for this repository.
   * @return GoogleDevtoolsArtifactregistryV1Rule
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleDevtoolsArtifactregistryV1Rule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleDevtoolsArtifactregistryV1Rule::class);
  }
  /**
   * Deletes a rule. (rules.delete)
   *
   * @param string $name Required. The name of the rule to delete.
   * @param array $optParams Optional parameters.
   * @return ArtifactregistryEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ArtifactregistryEmpty::class);
  }
  /**
   * Gets a rule. (rules.get)
   *
   * @param string $name Required. The name of the rule to retrieve.
   * @param array $optParams Optional parameters.
   * @return GoogleDevtoolsArtifactregistryV1Rule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleDevtoolsArtifactregistryV1Rule::class);
  }
  /**
   * Lists rules. (rules.listProjectsLocationsRepositoriesRules)
   *
   * @param string $parent Required. The name of the parent repository whose rules
   * will be listed. For example: `projects/p1/locations/us-
   * central1/repositories/repo1`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of rules to return. Maximum page
   * size is 1,000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request, if any.
   * @return ListRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRulesResponse::class);
  }
  /**
   * Updates a rule. (rules.patch)
   *
   * @param string $name The name of the rule, for example:
   * `projects/p1/locations/us-central1/repositories/repo1/rules/rule1`.
   * @param GoogleDevtoolsArtifactregistryV1Rule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask applies to the resource. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return GoogleDevtoolsArtifactregistryV1Rule
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleDevtoolsArtifactregistryV1Rule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleDevtoolsArtifactregistryV1Rule::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesRules::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesRules');
