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

namespace Google\Service\Contactcenterinsights\Resource;

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListNotesResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1Note;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "notes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $notes = $contactcenterinsightsService->projects_locations_conversations_assessments_notes;
 *  </code>
 */
class ProjectsLocationsConversationsAssessmentsNotes extends \Google\Service\Resource
{
  /**
   * Create Note. (notes.create)
   *
   * @param string $parent Required. The parent resource of the note.
   * @param GoogleCloudContactcenterinsightsV1Note $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Note
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1Note $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1Note::class);
  }
  /**
   * Deletes a Note. (notes.delete)
   *
   * @param string $name Required. The name of the note to delete.
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * List Notes. (notes.listProjectsLocationsConversationsAssessmentsNotes)
   *
   * @param string $parent Required. The parent resource of the notes.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of notes to return in
   * the response. If zero the service will select a default size. A call might
   * return fewer objects than requested. A non-empty `next_page_token` in the
   * response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListNotesResponse`. This value indicates that this is a continuation of a
   * prior `ListNotes` call and that the system should return the next page of
   * data.
   * @return GoogleCloudContactcenterinsightsV1ListNotesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConversationsAssessmentsNotes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListNotesResponse::class);
  }
  /**
   * Update Note. (notes.patch)
   *
   * @param string $name Identifier. The resource name of the note. Format: projec
   * ts/{project}/locations/{location}/conversations/{conversation}/assessments/{a
   * ssessment}/notes/{note}
   * @param GoogleCloudContactcenterinsightsV1Note $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to be updated. If
   * the update_mask is empty, all updateable fields will be updated. Acceptable
   * fields include: * `content`
   * @return GoogleCloudContactcenterinsightsV1Note
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1Note $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1Note::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConversationsAssessmentsNotes::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsConversationsAssessmentsNotes');
