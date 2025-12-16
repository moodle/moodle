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

use Google\Service\Dataproc\ListSessionsResponse;
use Google\Service\Dataproc\Operation;
use Google\Service\Dataproc\Session;
use Google\Service\Dataproc\TerminateSessionRequest;

/**
 * The "sessions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataprocService = new Google\Service\Dataproc(...);
 *   $sessions = $dataprocService->projects_locations_sessions;
 *  </code>
 */
class ProjectsLocationsSessions extends \Google\Service\Resource
{
  /**
   * Create an interactive session asynchronously. (sessions.create)
   *
   * @param string $parent Required. The parent resource where this session will
   * be created.
   * @param Session $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the service receives two CreateSessionRequests (https://cloud.goo
   * gle.com/dataproc/docs/reference/rpc/google.cloud.dataproc.v1#google.cloud.dat
   * aproc.v1.CreateSessionRequest)s with the same ID, the second request is
   * ignored, and the first Session is created and stored in the
   * backend.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The value must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @opt_param string sessionId Required. The ID to use for the session, which
   * becomes the final component of the session's resource name.This value must be
   * 4-63 characters. Valid characters are /a-z-/.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Session $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the interactive session resource. If the session is not in terminal
   * state, it is terminated, and then deleted. (sessions.delete)
   *
   * @param string $name Required. The name of the session resource to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the service receives two DeleteSessionRequest (https://cloud.goog
   * le.com/dataproc/docs/reference/rpc/google.cloud.dataproc.v1#google.cloud.data
   * proc.v1.DeleteSessionRequest)s with the same ID, the second request is
   * ignored.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The value must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets the resource representation for an interactive session. (sessions.get)
   *
   * @param string $name Required. The name of the session to retrieve.
   * @param array $optParams Optional parameters.
   * @return Session
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Session::class);
  }
  /**
   * Lists interactive sessions. (sessions.listProjectsLocationsSessions)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * sessions.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter for the sessions to return in the
   * response.A filter is a logical expression constraining the values of various
   * fields in each session resource. Filters are case sensitive, and may contain
   * multiple clauses combined with logical operators (AND, OR). Supported fields
   * are session_id, session_uuid, state, create_time, and labels.Example: state =
   * ACTIVE and create_time < "2023-01-01T00:00:00Z" is a filter for sessions in
   * an ACTIVE state that were created before 2023-01-01. state = ACTIVE and
   * labels.environment=production is a filter for sessions in an ACTIVE state
   * that have a production environment label.See
   * https://google.aip.dev/assets/misc/ebnf-filtering.txt for a detailed
   * description of the filter syntax and a list of supported comparators.
   * @opt_param int pageSize Optional. The maximum number of sessions to return in
   * each response. The service may return fewer than this value.
   * @opt_param string pageToken Optional. A page token received from a previous
   * ListSessions call. Provide this token to retrieve the subsequent page.
   * @return ListSessionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSessions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSessionsResponse::class);
  }
  /**
   * Terminates the interactive session. (sessions.terminate)
   *
   * @param string $name Required. The name of the session resource to terminate.
   * @param TerminateSessionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function terminate($name, TerminateSessionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('terminate', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSessions::class, 'Google_Service_Dataproc_Resource_ProjectsLocationsSessions');
