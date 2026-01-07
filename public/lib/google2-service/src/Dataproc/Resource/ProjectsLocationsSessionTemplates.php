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

namespace Google\Service\Dataproc\Resource;

use Google\Service\Dataproc\DataprocEmpty;
use Google\Service\Dataproc\ListSessionTemplatesResponse;
use Google\Service\Dataproc\SessionTemplate;

/**
 * The "sessionTemplates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataprocService = new Google\Service\Dataproc(...);
 *   $sessionTemplates = $dataprocService->projects_locations_sessionTemplates;
 *  </code>
 */
class ProjectsLocationsSessionTemplates extends \Google\Service\Resource
{
  /**
   * Create a session template synchronously. (sessionTemplates.create)
   *
   * @param string $parent Required. The parent resource where this session
   * template will be created.
   * @param SessionTemplate $postBody
   * @param array $optParams Optional parameters.
   * @return SessionTemplate
   * @throws \Google\Service\Exception
   */
  public function create($parent, SessionTemplate $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], SessionTemplate::class);
  }
  /**
   * Deletes a session template. (sessionTemplates.delete)
   *
   * @param string $name Required. The name of the session template resource to
   * delete.
   * @param array $optParams Optional parameters.
   * @return DataprocEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DataprocEmpty::class);
  }
  /**
   * Gets the resource representation for a session template.
   * (sessionTemplates.get)
   *
   * @param string $name Required. The name of the session template to retrieve.
   * @param array $optParams Optional parameters.
   * @return SessionTemplate
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SessionTemplate::class);
  }
  /**
   * Lists session templates.
   * (sessionTemplates.listProjectsLocationsSessionTemplates)
   *
   * @param string $parent Required. The parent that owns this collection of
   * session templates.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter for the session templates to
   * return in the response. Filters are case sensitive and have the following
   * syntax:field = value AND field = value ...
   * @opt_param int pageSize Optional. The maximum number of sessions to return in
   * each response. The service may return fewer than this value.
   * @opt_param string pageToken Optional. A page token received from a previous
   * ListSessions call. Provide this token to retrieve the subsequent page.
   * @return ListSessionTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSessionTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSessionTemplatesResponse::class);
  }
  /**
   * Updates the session template synchronously. (sessionTemplates.patch)
   *
   * @param string $name Required. Identifier. The resource name of the session
   * template.
   * @param SessionTemplate $postBody
   * @param array $optParams Optional parameters.
   * @return SessionTemplate
   * @throws \Google\Service\Exception
   */
  public function patch($name, SessionTemplate $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], SessionTemplate::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSessionTemplates::class, 'Google_Service_Dataproc_Resource_ProjectsLocationsSessionTemplates');
