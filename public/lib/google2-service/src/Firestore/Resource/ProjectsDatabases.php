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

namespace Google\Service\Firestore\Resource;

use Google\Service\Firestore\GoogleFirestoreAdminV1BulkDeleteDocumentsRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1CloneDatabaseRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1Database;
use Google\Service\Firestore\GoogleFirestoreAdminV1ExportDocumentsRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1ImportDocumentsRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1ListDatabasesResponse;
use Google\Service\Firestore\GoogleFirestoreAdminV1RestoreDatabaseRequest;
use Google\Service\Firestore\GoogleLongrunningOperation;

/**
 * The "databases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firestoreService = new Google\Service\Firestore(...);
 *   $databases = $firestoreService->projects_databases;
 *  </code>
 */
class ProjectsDatabases extends \Google\Service\Resource
{
  /**
   * Bulk deletes a subset of documents from Google Cloud Firestore. Documents
   * created or updated after the underlying system starts to process the request
   * will not be deleted. The bulk delete occurs in the background and its
   * progress can be monitored and managed via the Operation resource that is
   * created. For more details on bulk delete behavior, refer to:
   * https://cloud.google.com/firestore/docs/manage-data/bulk-delete
   * (databases.bulkDeleteDocuments)
   *
   * @param string $name Required. Database to operate. Should be of the form:
   * `projects/{project_id}/databases/{database_id}`.
   * @param GoogleFirestoreAdminV1BulkDeleteDocumentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function bulkDeleteDocuments($name, GoogleFirestoreAdminV1BulkDeleteDocumentsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkDeleteDocuments', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a new database by cloning an existing one. The new database must be
   * in the same cloud region or multi-region location as the existing database.
   * This behaves similar to FirestoreAdmin.CreateDatabase except instead of
   * creating a new empty database, a new database is created with the database
   * type, index configuration, and documents from an existing database. The long-
   * running operation can be used to track the progress of the clone, with the
   * Operation's metadata field type being the CloneDatabaseMetadata. The response
   * type is the Database if the clone was successful. The new database is not
   * readable or writeable until the LRO has completed.
   * (databases.cloneProjectsDatabases)
   *
   * @param string $parent Required. The project to clone the database in. Format
   * is `projects/{project_id}`.
   * @param GoogleFirestoreAdminV1CloneDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function cloneProjectsDatabases($parent, GoogleFirestoreAdminV1CloneDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('clone', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Create a database. (databases.create)
   *
   * @param string $parent Required. A parent name of the form
   * `projects/{project_id}`
   * @param GoogleFirestoreAdminV1Database $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string databaseId Required. The ID to use for the database, which
   * will become the final component of the database's resource name. This value
   * should be 4-63 characters. Valid characters are /a-z-/ with first character a
   * letter and the last a letter or a number. Must not be UUID-like
   * /[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}/. "(default)" database ID is also
   * valid if the database is Standard edition.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleFirestoreAdminV1Database $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a database. (databases.delete)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag The current etag of the Database. If an etag is
   * provided and does not match the current etag of the database, deletion will
   * be blocked and a FAILED_PRECONDITION error will be returned.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Exports a copy of all or a subset of documents from Google Cloud Firestore to
   * another storage system, such as Google Cloud Storage. Recent updates to
   * documents may not be reflected in the export. The export occurs in the
   * background and its progress can be monitored and managed via the Operation
   * resource that is created. The output of an export may only be used once the
   * associated operation is done. If an export operation is cancelled before
   * completion it may leave partial data behind in Google Cloud Storage. For more
   * details on export behavior and output format, refer to:
   * https://cloud.google.com/firestore/docs/manage-data/export-import
   * (databases.exportDocuments)
   *
   * @param string $name Required. Database to export. Should be of the form:
   * `projects/{project_id}/databases/{database_id}`.
   * @param GoogleFirestoreAdminV1ExportDocumentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function exportDocuments($name, GoogleFirestoreAdminV1ExportDocumentsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportDocuments', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets information about a database. (databases.get)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1Database
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirestoreAdminV1Database::class);
  }
  /**
   * Imports documents into Google Cloud Firestore. Existing documents with the
   * same name are overwritten. The import occurs in the background and its
   * progress can be monitored and managed via the Operation resource that is
   * created. If an ImportDocuments operation is cancelled, it is possible that a
   * subset of the data has already been imported to Cloud Firestore.
   * (databases.importDocuments)
   *
   * @param string $name Required. Database to import into. Should be of the form:
   * `projects/{project_id}/databases/{database_id}`.
   * @param GoogleFirestoreAdminV1ImportDocumentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function importDocuments($name, GoogleFirestoreAdminV1ImportDocumentsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importDocuments', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * List all the databases in the project. (databases.listProjectsDatabases)
   *
   * @param string $parent Required. A parent name of the form
   * `projects/{project_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool showDeleted If true, also returns deleted resources.
   * @return GoogleFirestoreAdminV1ListDatabasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsDatabases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirestoreAdminV1ListDatabasesResponse::class);
  }
  /**
   * Updates a database. (databases.patch)
   *
   * @param string $name The resource name of the Database. Format:
   * `projects/{project}/databases/{database}`
   * @param GoogleFirestoreAdminV1Database $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleFirestoreAdminV1Database $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a new database by restoring from an existing backup. The new database
   * must be in the same cloud region or multi-region location as the existing
   * backup. This behaves similar to FirestoreAdmin.CreateDatabase except instead
   * of creating a new empty database, a new database is created with the database
   * type, index configuration, and documents from an existing backup. The long-
   * running operation can be used to track the progress of the restore, with the
   * Operation's metadata field type being the RestoreDatabaseMetadata. The
   * response type is the Database if the restore was successful. The new database
   * is not readable or writeable until the LRO has completed. (databases.restore)
   *
   * @param string $parent Required. The project to restore the database in.
   * Format is `projects/{project_id}`.
   * @param GoogleFirestoreAdminV1RestoreDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function restore($parent, GoogleFirestoreAdminV1RestoreDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsDatabases::class, 'Google_Service_Firestore_Resource_ProjectsDatabases');
