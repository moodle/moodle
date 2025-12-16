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

namespace Google\Service\CloudObservability\Resource;

use Google\Service\CloudObservability\ListTraceScopesResponse;
use Google\Service\CloudObservability\ObservabilityEmpty;
use Google\Service\CloudObservability\TraceScope;

/**
 * The "traceScopes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $observabilityService = new Google\Service\CloudObservability(...);
 *   $traceScopes = $observabilityService->projects_locations_traceScopes;
 *  </code>
 */
class ProjectsLocationsTraceScopes extends \Google\Service\Resource
{
  /**
   * Create a new TraceScope. (traceScopes.create)
   *
   * @param string $parent Required. The full resource name of the location where
   * the trace scope should be created
   * projects/[PROJECT_ID]/locations/[LOCATION_ID] For example: projects/my-
   * project/locations/global
   * @param TraceScope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string traceScopeId Required. A client-assigned identifier for the
   * trace scope.
   * @return TraceScope
   * @throws \Google\Service\Exception
   */
  public function create($parent, TraceScope $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], TraceScope::class);
  }
  /**
   * Delete a TraceScope. (traceScopes.delete)
   *
   * @param string $name Required. The full resource name of the trace scope to
   * delete:
   * projects/[PROJECT_ID]/locations/[LOCATION_ID]/traceScopes/[TRACE_SCOPE_ID]
   * For example: projects/my-project/locations/global/traceScopes/my-trace-scope
   * @param array $optParams Optional parameters.
   * @return ObservabilityEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ObservabilityEmpty::class);
  }
  /**
   * Get TraceScope resource. (traceScopes.get)
   *
   * @param string $name Required. The resource name of the trace scope:
   * projects/[PROJECT_ID]/locations/[LOCATION_ID]/traceScopes/[TRACE_SCOPE_ID]
   * For example: projects/my-project/locations/global/traceScopes/my-trace-scope
   * @param array $optParams Optional parameters.
   * @return TraceScope
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TraceScope::class);
  }
  /**
   * List TraceScopes of a project in a particular location.
   * (traceScopes.listProjectsLocationsTraceScopes)
   *
   * @param string $parent Required. The full resource name of the location to
   * look for trace scopes: projects/[PROJECT_ID]/locations/[LOCATION_ID] For
   * example: projects/my-project/locations/global
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results to return
   * from this request. Non-positive values are ignored. The presence of
   * `next_page_token` in the response indicates that more results might be
   * available.
   * @opt_param string pageToken Optional. If present, then retrieve the next
   * batch of results from the preceding call to this method. `page_token` must be
   * the value of `next_page_token` from the previous response. The values of
   * other method parameters should be identical to those in the previous call.
   * @return ListTraceScopesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTraceScopes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTraceScopesResponse::class);
  }
  /**
   * Update a TraceScope. (traceScopes.patch)
   *
   * @param string $name Identifier. The resource name of the trace scope. For
   * example: projects/my-project/locations/global/traceScopes/my-trace-scope
   * @param TraceScope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return TraceScope
   * @throws \Google\Service\Exception
   */
  public function patch($name, TraceScope $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], TraceScope::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTraceScopes::class, 'Google_Service_CloudObservability_Resource_ProjectsLocationsTraceScopes');
