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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\ApihubEmpty;
use Google\Service\APIhub\GoogleCloudApihubV1ListRuntimeProjectAttachmentsResponse;
use Google\Service\APIhub\GoogleCloudApihubV1RuntimeProjectAttachment;

/**
 * The "runtimeProjectAttachments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $runtimeProjectAttachments = $apihubService->projects_locations_runtimeProjectAttachments;
 *  </code>
 */
class ProjectsLocationsRuntimeProjectAttachments extends \Google\Service\Resource
{
  /**
   * Attaches a runtime project to the host project.
   * (runtimeProjectAttachments.create)
   *
   * @param string $parent Required. The parent resource for the Runtime Project
   * Attachment. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1RuntimeProjectAttachment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string runtimeProjectAttachmentId Required. The ID to use for the
   * Runtime Project Attachment, which will become the final component of the
   * Runtime Project Attachment's name. The ID must be the same as the project ID
   * of the Google cloud project specified in the
   * runtime_project_attachment.runtime_project field.
   * @return GoogleCloudApihubV1RuntimeProjectAttachment
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1RuntimeProjectAttachment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1RuntimeProjectAttachment::class);
  }
  /**
   * Delete a runtime project attachment in the API Hub. This call will detach the
   * runtime project from the host project. (runtimeProjectAttachments.delete)
   *
   * @param string $name Required. The name of the Runtime Project Attachment to
   * delete. Format: `projects/{project}/locations/{location}/runtimeProjectAttach
   * ments/{runtime_project_attachment}`
   * @param array $optParams Optional parameters.
   * @return ApihubEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ApihubEmpty::class);
  }
  /**
   * Gets a runtime project attachment. (runtimeProjectAttachments.get)
   *
   * @param string $name Required. The name of the API resource to retrieve.
   * Format: `projects/{project}/locations/{location}/runtimeProjectAttachments/{r
   * untime_project_attachment}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1RuntimeProjectAttachment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1RuntimeProjectAttachment::class);
  }
  /**
   * List runtime projects attached to the host project.
   * (runtimeProjectAttachments.listProjectsLocationsRuntimeProjectAttachments)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * runtime project attachments. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * RuntimeProjectAttachments. A filter expression consists of a field name, a
   * comparison operator, and a value for filtering. The value must be a string.
   * All standard operators as documented at https://google.aip.dev/160 are
   * supported. The following fields in the `RuntimeProjectAttachment` are
   * eligible for filtering: * `name` - The name of the RuntimeProjectAttachment.
   * * `create_time` - The time at which the RuntimeProjectAttachment was created.
   * The value should be in the (RFC3339)[https://tools.ietf.org/html/rfc3339]
   * format. * `runtime_project` - The Google cloud project associated with the
   * RuntimeProjectAttachment.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. The maximum number of runtime project
   * attachments to return. The service may return fewer than this value. If
   * unspecified, at most 50 runtime project attachments will be returned. The
   * maximum value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListRuntimeProjectAttachments` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters (except page_size) provided to
   * `ListRuntimeProjectAttachments` must match the call that provided the page
   * token.
   * @return GoogleCloudApihubV1ListRuntimeProjectAttachmentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRuntimeProjectAttachments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListRuntimeProjectAttachmentsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRuntimeProjectAttachments::class, 'Google_Service_APIhub_Resource_ProjectsLocationsRuntimeProjectAttachments');
