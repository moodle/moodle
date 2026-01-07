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

namespace Google\Service\AppHub\Resource;

use Google\Service\AppHub\ListServiceProjectAttachmentsResponse;
use Google\Service\AppHub\Operation;
use Google\Service\AppHub\ServiceProjectAttachment;

/**
 * The "serviceProjectAttachments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apphubService = new Google\Service\AppHub(...);
 *   $serviceProjectAttachments = $apphubService->projects_locations_serviceProjectAttachments;
 *  </code>
 */
class ProjectsLocationsServiceProjectAttachments extends \Google\Service\Resource
{
  /**
   * Attaches a service project to the host project.
   * (serviceProjectAttachments.create)
   *
   * @param string $parent Required. Host project ID and location to which service
   * project is being attached. Only global location is supported. Expected
   * format: `projects/{project}/locations/{location}`.
   * @param ServiceProjectAttachment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string serviceProjectAttachmentId Required. The service project
   * attachment identifier must contain the project id of the service project
   * specified in the service_project_attachment.service_project field.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ServiceProjectAttachment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a service project attachment. (serviceProjectAttachments.delete)
   *
   * @param string $name Required. Fully qualified name of the service project
   * attachment to delete. Expected format: `projects/{project}/locations/{locatio
   * n}/serviceProjectAttachments/{serviceProjectAttachment}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Gets a service project attachment. (serviceProjectAttachments.get)
   *
   * @param string $name Required. Fully qualified name of the service project
   * attachment to retrieve. Expected format: `projects/{project}/locations/{locat
   * ion}/serviceProjectAttachments/{serviceProjectAttachment}`.
   * @param array $optParams Optional parameters.
   * @return ServiceProjectAttachment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ServiceProjectAttachment::class);
  }
  /**
   * Lists service projects attached to the host project.
   * (serviceProjectAttachments.listProjectsLocationsServiceProjectAttachments)
   *
   * @param string $parent Required. Host project ID and location to list service
   * project attachments. Only global location is supported. Expected format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListServiceProjectAttachmentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServiceProjectAttachments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListServiceProjectAttachmentsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServiceProjectAttachments::class, 'Google_Service_AppHub_Resource_ProjectsLocationsServiceProjectAttachments');
