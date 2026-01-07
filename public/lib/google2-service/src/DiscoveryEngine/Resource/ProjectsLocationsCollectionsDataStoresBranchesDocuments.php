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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Document;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ImportDocumentsRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListDocumentsResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1PurgeDocumentsRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;
use Google\Service\DiscoveryEngine\GoogleProtobufEmpty;

/**
 * The "documents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $documents = $discoveryengineService->projects_locations_collections_dataStores_branches_documents;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresBranchesDocuments extends \Google\Service\Resource
{
  /**
   * Creates a Document. (documents.create)
   *
   * @param string $parent Required. The parent resource name, such as `projects/{
   * project}/locations/{location}/collections/{collection}/dataStores/{data_store
   * }/branches/{branch}`.
   * @param GoogleCloudDiscoveryengineV1Document $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string documentId Required. The ID to use for the Document, which
   * becomes the final component of the Document.name. If the caller does not have
   * permission to create the Document, regardless of whether or not it exists, a
   * `PERMISSION_DENIED` error is returned. This field must be unique among all
   * Documents with the same parent. Otherwise, an `ALREADY_EXISTS` error is
   * returned. This field must conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length limit
   * of 128 characters. Otherwise, an `INVALID_ARGUMENT` error is returned.
   * @return GoogleCloudDiscoveryengineV1Document
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Document $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1Document::class);
  }
  /**
   * Deletes a Document. (documents.delete)
   *
   * @param string $name Required. Full resource name of Document, such as `projec
   * ts/{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore}/branches/{branch}/documents/{document}`. If the caller does not have
   * permission to delete the Document, regardless of whether or not it exists, a
   * `PERMISSION_DENIED` error is returned. If the Document to delete does not
   * exist, a `NOT_FOUND` error is returned.
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
   * Gets a Document. (documents.get)
   *
   * @param string $name Required. Full resource name of Document, such as `projec
   * ts/{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore}/branches/{branch}/documents/{document}`. If the caller does not have
   * permission to access the Document, regardless of whether or not it exists, a
   * `PERMISSION_DENIED` error is returned. If the requested Document does not
   * exist, a `NOT_FOUND` error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Document
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Document::class);
  }
  /**
   * Bulk import of multiple Documents. Request processing may be synchronous.
   * Non-existing items are created. Note: It is possible for a subset of the
   * Documents to be successfully updated. (documents.import)
   *
   * @param string $parent Required. The parent branch resource name, such as `pro
   * jects/{project}/locations/{location}/collections/{collection}/dataStores/{dat
   * a_store}/branches/{branch}`. Requires create/update permission.
   * @param GoogleCloudDiscoveryengineV1ImportDocumentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function import($parent, GoogleCloudDiscoveryengineV1ImportDocumentsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a list of Documents.
   * (documents.listProjectsLocationsCollectionsDataStoresBranchesDocuments)
   *
   * @param string $parent Required. The parent branch resource name, such as `pro
   * jects/{project}/locations/{location}/collections/{collection}/dataStores/{dat
   * a_store}/branches/{branch}`. Use `default_branch` as the branch ID, to list
   * documents under the default branch. If the caller does not have permission to
   * list Documents under this branch, regardless of whether or not this branch
   * exists, a `PERMISSION_DENIED` error is returned.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of Documents to return. If
   * unspecified, defaults to 100. The maximum allowed value is 1000. Values above
   * 1000 are set to 1000. If this field is negative, an `INVALID_ARGUMENT` error
   * is returned.
   * @opt_param string pageToken A page token
   * ListDocumentsResponse.next_page_token, received from a previous
   * DocumentService.ListDocuments call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * DocumentService.ListDocuments must match the call that provided the page
   * token. Otherwise, an `INVALID_ARGUMENT` error is returned.
   * @return GoogleCloudDiscoveryengineV1ListDocumentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsDataStoresBranchesDocuments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListDocumentsResponse::class);
  }
  /**
   * Updates a Document. (documents.patch)
   *
   * @param string $name Immutable. The full resource name of the document.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store}/branches/{branch}/documents/{document_id}`. This field
   * must be a UTF-8 encoded string with a length limit of 1024 characters.
   * @param GoogleCloudDiscoveryengineV1Document $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to `true` and the Document is not found,
   * a new Document is be created.
   * @opt_param string updateMask Indicates which fields in the provided imported
   * 'document' to update. If not set, by default updates all fields.
   * @return GoogleCloudDiscoveryengineV1Document
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1Document $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1Document::class);
  }
  /**
   * Permanently deletes all selected Documents in a branch. This process is
   * asynchronous. Depending on the number of Documents to be deleted, this
   * operation can take hours to complete. Before the delete operation completes,
   * some Documents might still be returned by DocumentService.GetDocument or
   * DocumentService.ListDocuments. To get a list of the Documents to be deleted,
   * set PurgeDocumentsRequest.force to false. (documents.purge)
   *
   * @param string $parent Required. The parent resource name, such as `projects/{
   * project}/locations/{location}/collections/{collection}/dataStores/{data_store
   * }/branches/{branch}`.
   * @param GoogleCloudDiscoveryengineV1PurgeDocumentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function purge($parent, GoogleCloudDiscoveryengineV1PurgeDocumentsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('purge', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresBranchesDocuments::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresBranchesDocuments');
