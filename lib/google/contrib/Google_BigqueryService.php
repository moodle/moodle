<?php
/*
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


  /**
   * The "tables" collection of methods.
   * Typical usage is:
   *  <code>
   *   $bigqueryService = new Google_BigqueryService(...);
   *   $tables = $bigqueryService->tables;
   *  </code>
   */
  class Google_TablesServiceResource extends Google_ServiceResource {


    /**
     * Creates a new, empty table in the dataset. (tables.insert)
     *
     * @param string $projectId Project ID of the new table
     * @param string $datasetId Dataset ID of the new table
     * @param Google_Table $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Table
     */
    public function insert($projectId, $datasetId, Google_Table $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets the specified table resource by table ID. This method does not return the data in the table,
     * it only returns the table resource, which describes the structure of this table. (tables.get)
     *
     * @param string $projectId Project ID of the requested table
     * @param string $datasetId Dataset ID of the requested table
     * @param string $tableId Table ID of the requested table
     * @param array $optParams Optional parameters.
     * @return Google_Table
     */
    public function get($projectId, $datasetId, $tableId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists all tables in the specified dataset. (tables.list)
     *
     * @param string $projectId Project ID of the tables to list
     * @param string $datasetId Dataset ID of the tables to list
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Page token, returned by a previous call, to request the next page of results
     * @opt_param string maxResults Maximum number of results to return
     * @return Google_TableList
     */
    public function listTables($projectId, $datasetId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TableList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates information in an existing table, specified by tableId. (tables.update)
     *
     * @param string $projectId Project ID of the table to update
     * @param string $datasetId Dataset ID of the table to update
     * @param string $tableId Table ID of the table to update
     * @param Google_Table $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Table
     */
    public function update($projectId, $datasetId, $tableId, Google_Table $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates information in an existing table, specified by tableId. This method supports patch
     * semantics. (tables.patch)
     *
     * @param string $projectId Project ID of the table to update
     * @param string $datasetId Dataset ID of the table to update
     * @param string $tableId Table ID of the table to update
     * @param Google_Table $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Table
     */
    public function patch($projectId, $datasetId, $tableId, Google_Table $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the table specified by tableId from the dataset. If the table contains data, all the data
     * will be deleted. (tables.delete)
     *
     * @param string $projectId Project ID of the table to delete
     * @param string $datasetId Dataset ID of the table to delete
     * @param string $tableId Table ID of the table to delete
     * @param array $optParams Optional parameters.
     */
    public function delete($projectId, $datasetId, $tableId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "datasets" collection of methods.
   * Typical usage is:
   *  <code>
   *   $bigqueryService = new Google_BigqueryService(...);
   *   $datasets = $bigqueryService->datasets;
   *  </code>
   */
  class Google_DatasetsServiceResource extends Google_ServiceResource {


    /**
     * Creates a new empty dataset. (datasets.insert)
     *
     * @param string $projectId Project ID of the new dataset
     * @param Google_Dataset $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Dataset
     */
    public function insert($projectId, Google_Dataset $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Dataset($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the dataset specified by datasetID. (datasets.get)
     *
     * @param string $projectId Project ID of the requested dataset
     * @param string $datasetId Dataset ID of the requested dataset
     * @param array $optParams Optional parameters.
     * @return Google_Dataset
     */
    public function get($projectId, $datasetId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Dataset($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists all the datasets in the specified project to which the caller has read access; however, a
     * project owner can list (but not necessarily get) all datasets in his project. (datasets.list)
     *
     * @param string $projectId Project ID of the datasets to be listed
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Page token, returned by a previous call, to request the next page of results
     * @opt_param string maxResults The maximum number of results to return
     * @return Google_DatasetList
     */
    public function listDatasets($projectId, $optParams = array()) {
      $params = array('projectId' => $projectId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_DatasetList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates information in an existing dataset, specified by datasetId. Properties not included in
     * the submitted resource will not be changed. If you include the access property without any values
     * assigned, the request will fail as you must specify at least one owner for a dataset.
     * (datasets.update)
     *
     * @param string $projectId Project ID of the dataset being updated
     * @param string $datasetId Dataset ID of the dataset being updated
     * @param Google_Dataset $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Dataset
     */
    public function update($projectId, $datasetId, Google_Dataset $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Dataset($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates information in an existing dataset, specified by datasetId. Properties not included in
     * the submitted resource will not be changed. If you include the access property without any values
     * assigned, the request will fail as you must specify at least one owner for a dataset. This method
     * supports patch semantics. (datasets.patch)
     *
     * @param string $projectId Project ID of the dataset being updated
     * @param string $datasetId Dataset ID of the dataset being updated
     * @param Google_Dataset $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Dataset
     */
    public function patch($projectId, $datasetId, Google_Dataset $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Dataset($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the dataset specified by datasetId value. Before you can delete a dataset, you must
     * delete all its tables, either manually or by specifying deleteContents. Immediately after
     * deletion, you can create another dataset with the same name. (datasets.delete)
     *
     * @param string $projectId Project ID of the dataset being deleted
     * @param string $datasetId Dataset ID of dataset being deleted
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool deleteContents If True, delete all the tables in the dataset. If False and the dataset contains tables, the request will fail. Default is False
     */
    public function delete($projectId, $datasetId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "jobs" collection of methods.
   * Typical usage is:
   *  <code>
   *   $bigqueryService = new Google_BigqueryService(...);
   *   $jobs = $bigqueryService->jobs;
   *  </code>
   */
  class Google_JobsServiceResource extends Google_ServiceResource {


    /**
     * Starts a new asynchronous job. (jobs.insert)
     *
     * @param string $projectId Project ID of the project that will be billed for the job
     * @param Google_Job $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Job
     */
    public function insert($projectId, Google_Job $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Job($data);
      } else {
        return $data;
      }
    }
    /**
     * Runs a BigQuery SQL query synchronously and returns query results if the query completes within a
     * specified timeout. (jobs.query)
     *
     * @param string $projectId Project ID of the project billed for the query
     * @param Google_QueryRequest $postBody
     * @param array $optParams Optional parameters.
     * @return Google_QueryResponse
     */
    public function query($projectId, Google_QueryRequest $postBody, $optParams = array()) {
      $params = array('projectId' => $projectId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('query', array($params));
      if ($this->useObjects()) {
        return new Google_QueryResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists all the Jobs in the specified project that were started by the user. (jobs.list)
     *
     * @param string $projectId Project ID of the jobs to list
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Restrict information returned to a set of selected fields
     * @opt_param string stateFilter Filter for job state
     * @opt_param bool allUsers Whether to display jobs owned by all users in the project. Default false
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string pageToken Page token, returned by a previous call, to request the next page of results
     * @return Google_JobList
     */
    public function listJobs($projectId, $optParams = array()) {
      $params = array('projectId' => $projectId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_JobList($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the results of a query job. (jobs.getQueryResults)
     *
     * @param string $projectId Project ID of the query job
     * @param string $jobId Job ID of the query job
     * @param array $optParams Optional parameters.
     *
     * @opt_param string timeoutMs How long to wait for the query to complete, in milliseconds, before returning. Default is to return immediately. If the timeout passes before the job completes, the request will fail with a TIMEOUT error
     * @opt_param string startIndex Zero-based index of the starting row
     * @opt_param string maxResults Maximum number of results to read
     * @return Google_GetQueryResultsResponse
     */
    public function getQueryResults($projectId, $jobId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'jobId' => $jobId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('getQueryResults', array($params));
      if ($this->useObjects()) {
        return new Google_GetQueryResultsResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the specified job by ID. (jobs.get)
     *
     * @param string $projectId Project ID of the requested job
     * @param string $jobId Job ID of the requested job
     * @param array $optParams Optional parameters.
     * @return Google_Job
     */
    public function get($projectId, $jobId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'jobId' => $jobId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Job($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "tabledata" collection of methods.
   * Typical usage is:
   *  <code>
   *   $bigqueryService = new Google_BigqueryService(...);
   *   $tabledata = $bigqueryService->tabledata;
   *  </code>
   */
  class Google_TabledataServiceResource extends Google_ServiceResource {


    /**
     * Retrieves table data from a specified set of rows. (tabledata.list)
     *
     * @param string $projectId Project ID of the table to read
     * @param string $datasetId Dataset ID of the table to read
     * @param string $tableId Table ID of the table to read
     * @param array $optParams Optional parameters.
     *
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string pageToken Page token, returned by a previous call, identifying the result set
     * @opt_param string startIndex Zero-based index of the starting row to read
     * @return Google_TableDataList
     */
    public function listTabledata($projectId, $datasetId, $tableId, $optParams = array()) {
      $params = array('projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TableDataList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "projects" collection of methods.
   * Typical usage is:
   *  <code>
   *   $bigqueryService = new Google_BigqueryService(...);
   *   $projects = $bigqueryService->projects;
   *  </code>
   */
  class Google_ProjectsServiceResource extends Google_ServiceResource {


    /**
     * Lists the projects to which you have at least read access. (projects.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Page token, returned by a previous call, to request the next page of results
     * @opt_param string maxResults Maximum number of results to return
     * @return Google_ProjectList
     */
    public function listProjects($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ProjectList($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Bigquery (v2).
 *
 * <p>
 * A data platform for customers to create, manage, share and query data.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://code.google.com/apis/bigquery/docs/v2/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_BigqueryService extends Google_Service {
  public $tables;
  public $datasets;
  public $jobs;
  public $tabledata;
  public $projects;
  /**
   * Constructs the internal representation of the Bigquery service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'bigquery/v2/';
    $this->version = 'v2';
    $this->serviceName = 'bigquery';

    $client->addService($this->serviceName, $this->version);
    $this->tables = new Google_TablesServiceResource($this, $this->serviceName, 'tables', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Table"}, "response": {"$ref": "Table"}, "httpMethod": "POST", "path": "projects/{projectId}/datasets/{datasetId}/tables", "id": "bigquery.tables.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "tableId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.tables.get", "httpMethod": "GET", "path": "projects/{projectId}/datasets/{datasetId}/tables/{tableId}", "response": {"$ref": "Table"}}, "list": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"type": "integer", "location": "query", "format": "uint32"}, "datasetId": {"required": true, "type": "string", "location": "path"}, "projectId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.tables.list", "httpMethod": "GET", "path": "projects/{projectId}/datasets/{datasetId}/tables", "response": {"$ref": "TableList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "tableId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Table"}, "response": {"$ref": "Table"}, "httpMethod": "PUT", "path": "projects/{projectId}/datasets/{datasetId}/tables/{tableId}", "id": "bigquery.tables.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "tableId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Table"}, "response": {"$ref": "Table"}, "httpMethod": "PATCH", "path": "projects/{projectId}/datasets/{datasetId}/tables/{tableId}", "id": "bigquery.tables.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "path": "projects/{projectId}/datasets/{datasetId}/tables/{tableId}", "id": "bigquery.tables.delete", "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "tableId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->datasets = new Google_DatasetsServiceResource($this, $this->serviceName, 'datasets', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Dataset"}, "response": {"$ref": "Dataset"}, "httpMethod": "POST", "path": "projects/{projectId}/datasets", "id": "bigquery.datasets.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.datasets.get", "httpMethod": "GET", "path": "projects/{projectId}/datasets/{datasetId}", "response": {"$ref": "Dataset"}}, "list": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"type": "integer", "location": "query", "format": "uint32"}, "projectId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.datasets.list", "httpMethod": "GET", "path": "projects/{projectId}/datasets", "response": {"$ref": "DatasetList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Dataset"}, "response": {"$ref": "Dataset"}, "httpMethod": "PUT", "path": "projects/{projectId}/datasets/{datasetId}", "id": "bigquery.datasets.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Dataset"}, "response": {"$ref": "Dataset"}, "httpMethod": "PATCH", "path": "projects/{projectId}/datasets/{datasetId}", "id": "bigquery.datasets.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "path": "projects/{projectId}/datasets/{datasetId}", "id": "bigquery.datasets.delete", "parameters": {"deleteContents": {"type": "boolean", "location": "query"}, "datasetId": {"required": true, "type": "string", "location": "path"}, "projectId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->jobs = new Google_JobsServiceResource($this, $this->serviceName, 'jobs', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}}, "supportsMediaUpload": true, "request": {"$ref": "Job"}, "mediaUpload": {"protocols": {"simple": {"path": "/upload/bigquery/v2/projects/{projectId}/jobs", "multipart": true}, "resumable": {"path": "/resumable/upload/bigquery/v2/projects/{projectId}/jobs", "multipart": true}}, "accept": ["application/octet-stream"]}, "response": {"$ref": "Job"}, "httpMethod": "POST", "path": "projects/{projectId}/jobs", "id": "bigquery.jobs.insert"}, "query": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "QueryRequest"}, "response": {"$ref": "QueryResponse"}, "httpMethod": "POST", "path": "projects/{projectId}/queries", "id": "bigquery.jobs.query"}, "list": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projection": {"enum": ["full", "minimal"], "type": "string", "location": "query"}, "stateFilter": {"repeated": true, "enum": ["done", "pending", "running"], "type": "string", "location": "query"}, "projectId": {"required": true, "type": "string", "location": "path"}, "allUsers": {"type": "boolean", "location": "query"}, "maxResults": {"type": "integer", "location": "query", "format": "uint32"}, "pageToken": {"type": "string", "location": "query"}}, "id": "bigquery.jobs.list", "httpMethod": "GET", "path": "projects/{projectId}/jobs", "response": {"$ref": "JobList"}}, "getQueryResults": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"timeoutMs": {"type": "integer", "location": "query", "format": "uint32"}, "projectId": {"required": true, "type": "string", "location": "path"}, "startIndex": {"type": "string", "location": "query", "format": "uint64"}, "maxResults": {"type": "integer", "location": "query", "format": "uint32"}, "jobId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.jobs.getQueryResults", "httpMethod": "GET", "path": "projects/{projectId}/queries/{jobId}", "response": {"$ref": "GetQueryResultsResponse"}}, "get": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "jobId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.jobs.get", "httpMethod": "GET", "path": "projects/{projectId}/jobs/{jobId}", "response": {"$ref": "Job"}}}}', true));
    $this->tabledata = new Google_TabledataServiceResource($this, $this->serviceName, 'tabledata', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"projectId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"type": "integer", "location": "query", "format": "uint32"}, "pageToken": {"type": "string", "location": "query"}, "startIndex": {"type": "string", "location": "query", "format": "uint64"}, "tableId": {"required": true, "type": "string", "location": "path"}, "datasetId": {"required": true, "type": "string", "location": "path"}}, "id": "bigquery.tabledata.list", "httpMethod": "GET", "path": "projects/{projectId}/datasets/{datasetId}/tables/{tableId}/data", "response": {"$ref": "TableDataList"}}}}', true));
    $this->projects = new Google_ProjectsServiceResource($this, $this->serviceName, 'projects', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/bigquery"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"type": "integer", "location": "query", "format": "uint32"}}, "response": {"$ref": "ProjectList"}, "httpMethod": "GET", "path": "projects", "id": "bigquery.projects.list"}}}', true));

  }
}

class Google_Dataset extends Google_Model {
  public $kind;
  public $description;
  protected $__datasetReferenceType = 'Google_DatasetReference';
  protected $__datasetReferenceDataType = '';
  public $datasetReference;
  public $creationTime;
  protected $__accessType = 'Google_DatasetAccess';
  protected $__accessDataType = 'array';
  public $access;
  public $etag;
  public $friendlyName;
  public $lastModifiedTime;
  public $id;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setDatasetReference(Google_DatasetReference $datasetReference) {
    $this->datasetReference = $datasetReference;
  }
  public function getDatasetReference() {
    return $this->datasetReference;
  }
  public function setCreationTime($creationTime) {
    $this->creationTime = $creationTime;
  }
  public function getCreationTime() {
    return $this->creationTime;
  }
  public function setAccess(/* array(Google_DatasetAccess) */ $access) {
    $this->assertIsArray($access, 'Google_DatasetAccess', __METHOD__);
    $this->access = $access;
  }
  public function getAccess() {
    return $this->access;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setFriendlyName($friendlyName) {
    $this->friendlyName = $friendlyName;
  }
  public function getFriendlyName() {
    return $this->friendlyName;
  }
  public function setLastModifiedTime($lastModifiedTime) {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  public function getLastModifiedTime() {
    return $this->lastModifiedTime;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_DatasetAccess extends Google_Model {
  public $specialGroup;
  public $domain;
  public $role;
  public $groupByEmail;
  public $userByEmail;
  public function setSpecialGroup($specialGroup) {
    $this->specialGroup = $specialGroup;
  }
  public function getSpecialGroup() {
    return $this->specialGroup;
  }
  public function setDomain($domain) {
    $this->domain = $domain;
  }
  public function getDomain() {
    return $this->domain;
  }
  public function setRole($role) {
    $this->role = $role;
  }
  public function getRole() {
    return $this->role;
  }
  public function setGroupByEmail($groupByEmail) {
    $this->groupByEmail = $groupByEmail;
  }
  public function getGroupByEmail() {
    return $this->groupByEmail;
  }
  public function setUserByEmail($userByEmail) {
    $this->userByEmail = $userByEmail;
  }
  public function getUserByEmail() {
    return $this->userByEmail;
  }
}

class Google_DatasetList extends Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__datasetsType = 'Google_DatasetListDatasets';
  protected $__datasetsDataType = 'array';
  public $datasets;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDatasets(/* array(Google_DatasetListDatasets) */ $datasets) {
    $this->assertIsArray($datasets, 'Google_DatasetListDatasets', __METHOD__);
    $this->datasets = $datasets;
  }
  public function getDatasets() {
    return $this->datasets;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_DatasetListDatasets extends Google_Model {
  public $friendlyName;
  public $kind;
  public $id;
  protected $__datasetReferenceType = 'Google_DatasetReference';
  protected $__datasetReferenceDataType = '';
  public $datasetReference;
  public function setFriendlyName($friendlyName) {
    $this->friendlyName = $friendlyName;
  }
  public function getFriendlyName() {
    return $this->friendlyName;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setDatasetReference(Google_DatasetReference $datasetReference) {
    $this->datasetReference = $datasetReference;
  }
  public function getDatasetReference() {
    return $this->datasetReference;
  }
}

class Google_DatasetReference extends Google_Model {
  public $projectId;
  public $datasetId;
  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }
  public function getProjectId() {
    return $this->projectId;
  }
  public function setDatasetId($datasetId) {
    $this->datasetId = $datasetId;
  }
  public function getDatasetId() {
    return $this->datasetId;
  }
}

class Google_ErrorProto extends Google_Model {
  public $debugInfo;
  public $message;
  public $reason;
  public $location;
  public function setDebugInfo($debugInfo) {
    $this->debugInfo = $debugInfo;
  }
  public function getDebugInfo() {
    return $this->debugInfo;
  }
  public function setMessage($message) {
    $this->message = $message;
  }
  public function getMessage() {
    return $this->message;
  }
  public function setReason($reason) {
    $this->reason = $reason;
  }
  public function getReason() {
    return $this->reason;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
}

class Google_GetQueryResultsResponse extends Google_Model {
  public $kind;
  protected $__rowsType = 'Google_TableRow';
  protected $__rowsDataType = 'array';
  public $rows;
  protected $__jobReferenceType = 'Google_JobReference';
  protected $__jobReferenceDataType = '';
  public $jobReference;
  public $jobComplete;
  public $totalRows;
  public $etag;
  protected $__schemaType = 'Google_TableSchema';
  protected $__schemaDataType = '';
  public $schema;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows(/* array(Google_TableRow) */ $rows) {
    $this->assertIsArray($rows, 'Google_TableRow', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setJobReference(Google_JobReference $jobReference) {
    $this->jobReference = $jobReference;
  }
  public function getJobReference() {
    return $this->jobReference;
  }
  public function setJobComplete($jobComplete) {
    $this->jobComplete = $jobComplete;
  }
  public function getJobComplete() {
    return $this->jobComplete;
  }
  public function setTotalRows($totalRows) {
    $this->totalRows = $totalRows;
  }
  public function getTotalRows() {
    return $this->totalRows;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSchema(Google_TableSchema $schema) {
    $this->schema = $schema;
  }
  public function getSchema() {
    return $this->schema;
  }
}

class Google_Job extends Google_Model {
  protected $__statusType = 'Google_JobStatus';
  protected $__statusDataType = '';
  public $status;
  public $kind;
  protected $__statisticsType = 'Google_JobStatistics';
  protected $__statisticsDataType = '';
  public $statistics;
  protected $__jobReferenceType = 'Google_JobReference';
  protected $__jobReferenceDataType = '';
  public $jobReference;
  public $etag;
  protected $__configurationType = 'Google_JobConfiguration';
  protected $__configurationDataType = '';
  public $configuration;
  public $id;
  public $selfLink;
  public function setStatus(Google_JobStatus $status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStatistics(Google_JobStatistics $statistics) {
    $this->statistics = $statistics;
  }
  public function getStatistics() {
    return $this->statistics;
  }
  public function setJobReference(Google_JobReference $jobReference) {
    $this->jobReference = $jobReference;
  }
  public function getJobReference() {
    return $this->jobReference;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setConfiguration(Google_JobConfiguration $configuration) {
    $this->configuration = $configuration;
  }
  public function getConfiguration() {
    return $this->configuration;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_JobConfiguration extends Google_Model {
  protected $__loadType = 'Google_JobConfigurationLoad';
  protected $__loadDataType = '';
  public $load;
  protected $__linkType = 'Google_JobConfigurationLink';
  protected $__linkDataType = '';
  public $link;
  protected $__queryType = 'Google_JobConfigurationQuery';
  protected $__queryDataType = '';
  public $query;
  protected $__copyType = 'Google_JobConfigurationTableCopy';
  protected $__copyDataType = '';
  public $copy;
  protected $__extractType = 'Google_JobConfigurationExtract';
  protected $__extractDataType = '';
  public $extract;
  public $properties;
  public function setLoad(Google_JobConfigurationLoad $load) {
    $this->load = $load;
  }
  public function getLoad() {
    return $this->load;
  }
  public function setLink(Google_JobConfigurationLink $link) {
    $this->link = $link;
  }
  public function getLink() {
    return $this->link;
  }
  public function setQuery(Google_JobConfigurationQuery $query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
  public function setCopy(Google_JobConfigurationTableCopy $copy) {
    $this->copy = $copy;
  }
  public function getCopy() {
    return $this->copy;
  }
  public function setExtract(Google_JobConfigurationExtract $extract) {
    $this->extract = $extract;
  }
  public function getExtract() {
    return $this->extract;
  }
  public function setProperties($properties) {
    $this->properties = $properties;
  }
  public function getProperties() {
    return $this->properties;
  }
}

class Google_JobConfigurationExtract extends Google_Model {
  public $destinationUri;
  public $fieldDelimiter;
  protected $__sourceTableType = 'Google_TableReference';
  protected $__sourceTableDataType = '';
  public $sourceTable;
  public $printHeader;
  public function setDestinationUri($destinationUri) {
    $this->destinationUri = $destinationUri;
  }
  public function getDestinationUri() {
    return $this->destinationUri;
  }
  public function setFieldDelimiter($fieldDelimiter) {
    $this->fieldDelimiter = $fieldDelimiter;
  }
  public function getFieldDelimiter() {
    return $this->fieldDelimiter;
  }
  public function setSourceTable(Google_TableReference $sourceTable) {
    $this->sourceTable = $sourceTable;
  }
  public function getSourceTable() {
    return $this->sourceTable;
  }
  public function setPrintHeader($printHeader) {
    $this->printHeader = $printHeader;
  }
  public function getPrintHeader() {
    return $this->printHeader;
  }
}

class Google_JobConfigurationLink extends Google_Model {
  public $createDisposition;
  public $writeDisposition;
  protected $__destinationTableType = 'Google_TableReference';
  protected $__destinationTableDataType = '';
  public $destinationTable;
  public $sourceUri;
  public function setCreateDisposition($createDisposition) {
    $this->createDisposition = $createDisposition;
  }
  public function getCreateDisposition() {
    return $this->createDisposition;
  }
  public function setWriteDisposition($writeDisposition) {
    $this->writeDisposition = $writeDisposition;
  }
  public function getWriteDisposition() {
    return $this->writeDisposition;
  }
  public function setDestinationTable(Google_TableReference $destinationTable) {
    $this->destinationTable = $destinationTable;
  }
  public function getDestinationTable() {
    return $this->destinationTable;
  }
  public function setSourceUri(/* array(Google_string) */ $sourceUri) {
    $this->assertIsArray($sourceUri, 'Google_string', __METHOD__);
    $this->sourceUri = $sourceUri;
  }
  public function getSourceUri() {
    return $this->sourceUri;
  }
}

class Google_JobConfigurationLoad extends Google_Model {
  public $encoding;
  public $fieldDelimiter;
  protected $__destinationTableType = 'Google_TableReference';
  protected $__destinationTableDataType = '';
  public $destinationTable;
  public $writeDisposition;
  public $maxBadRecords;
  public $skipLeadingRows;
  public $sourceUris;
  public $quote;
  public $createDisposition;
  public $schemaInlineFormat;
  public $schemaInline;
  protected $__schemaType = 'Google_TableSchema';
  protected $__schemaDataType = '';
  public $schema;
  public function setEncoding($encoding) {
    $this->encoding = $encoding;
  }
  public function getEncoding() {
    return $this->encoding;
  }
  public function setFieldDelimiter($fieldDelimiter) {
    $this->fieldDelimiter = $fieldDelimiter;
  }
  public function getFieldDelimiter() {
    return $this->fieldDelimiter;
  }
  public function setDestinationTable(Google_TableReference $destinationTable) {
    $this->destinationTable = $destinationTable;
  }
  public function getDestinationTable() {
    return $this->destinationTable;
  }
  public function setWriteDisposition($writeDisposition) {
    $this->writeDisposition = $writeDisposition;
  }
  public function getWriteDisposition() {
    return $this->writeDisposition;
  }
  public function setMaxBadRecords($maxBadRecords) {
    $this->maxBadRecords = $maxBadRecords;
  }
  public function getMaxBadRecords() {
    return $this->maxBadRecords;
  }
  public function setSkipLeadingRows($skipLeadingRows) {
    $this->skipLeadingRows = $skipLeadingRows;
  }
  public function getSkipLeadingRows() {
    return $this->skipLeadingRows;
  }
  public function setSourceUris(/* array(Google_string) */ $sourceUris) {
    $this->assertIsArray($sourceUris, 'Google_string', __METHOD__);
    $this->sourceUris = $sourceUris;
  }
  public function getSourceUris() {
    return $this->sourceUris;
  }
  public function setQuote($quote) {
    $this->quote = $quote;
  }
  public function getQuote() {
    return $this->quote;
  }
  public function setCreateDisposition($createDisposition) {
    $this->createDisposition = $createDisposition;
  }
  public function getCreateDisposition() {
    return $this->createDisposition;
  }
  public function setSchemaInlineFormat($schemaInlineFormat) {
    $this->schemaInlineFormat = $schemaInlineFormat;
  }
  public function getSchemaInlineFormat() {
    return $this->schemaInlineFormat;
  }
  public function setSchemaInline($schemaInline) {
    $this->schemaInline = $schemaInline;
  }
  public function getSchemaInline() {
    return $this->schemaInline;
  }
  public function setSchema(Google_TableSchema $schema) {
    $this->schema = $schema;
  }
  public function getSchema() {
    return $this->schema;
  }
}

class Google_JobConfigurationQuery extends Google_Model {
  protected $__defaultDatasetType = 'Google_DatasetReference';
  protected $__defaultDatasetDataType = '';
  public $defaultDataset;
  protected $__destinationTableType = 'Google_TableReference';
  protected $__destinationTableDataType = '';
  public $destinationTable;
  public $priority;
  public $writeDisposition;
  public $createDisposition;
  public $query;
  public function setDefaultDataset(Google_DatasetReference $defaultDataset) {
    $this->defaultDataset = $defaultDataset;
  }
  public function getDefaultDataset() {
    return $this->defaultDataset;
  }
  public function setDestinationTable(Google_TableReference $destinationTable) {
    $this->destinationTable = $destinationTable;
  }
  public function getDestinationTable() {
    return $this->destinationTable;
  }
  public function setPriority($priority) {
    $this->priority = $priority;
  }
  public function getPriority() {
    return $this->priority;
  }
  public function setWriteDisposition($writeDisposition) {
    $this->writeDisposition = $writeDisposition;
  }
  public function getWriteDisposition() {
    return $this->writeDisposition;
  }
  public function setCreateDisposition($createDisposition) {
    $this->createDisposition = $createDisposition;
  }
  public function getCreateDisposition() {
    return $this->createDisposition;
  }
  public function setQuery($query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
}

class Google_JobConfigurationTableCopy extends Google_Model {
  public $createDisposition;
  public $writeDisposition;
  protected $__destinationTableType = 'Google_TableReference';
  protected $__destinationTableDataType = '';
  public $destinationTable;
  protected $__sourceTableType = 'Google_TableReference';
  protected $__sourceTableDataType = '';
  public $sourceTable;
  public function setCreateDisposition($createDisposition) {
    $this->createDisposition = $createDisposition;
  }
  public function getCreateDisposition() {
    return $this->createDisposition;
  }
  public function setWriteDisposition($writeDisposition) {
    $this->writeDisposition = $writeDisposition;
  }
  public function getWriteDisposition() {
    return $this->writeDisposition;
  }
  public function setDestinationTable(Google_TableReference $destinationTable) {
    $this->destinationTable = $destinationTable;
  }
  public function getDestinationTable() {
    return $this->destinationTable;
  }
  public function setSourceTable(Google_TableReference $sourceTable) {
    $this->sourceTable = $sourceTable;
  }
  public function getSourceTable() {
    return $this->sourceTable;
  }
}

class Google_JobList extends Google_Model {
  public $nextPageToken;
  public $totalItems;
  public $kind;
  public $etag;
  protected $__jobsType = 'Google_JobListJobs';
  protected $__jobsDataType = 'array';
  public $jobs;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setJobs(/* array(Google_JobListJobs) */ $jobs) {
    $this->assertIsArray($jobs, 'Google_JobListJobs', __METHOD__);
    $this->jobs = $jobs;
  }
  public function getJobs() {
    return $this->jobs;
  }
}

class Google_JobListJobs extends Google_Model {
  protected $__statusType = 'Google_JobStatus';
  protected $__statusDataType = '';
  public $status;
  public $kind;
  protected $__statisticsType = 'Google_JobStatistics';
  protected $__statisticsDataType = '';
  public $statistics;
  protected $__jobReferenceType = 'Google_JobReference';
  protected $__jobReferenceDataType = '';
  public $jobReference;
  public $state;
  protected $__configurationType = 'Google_JobConfiguration';
  protected $__configurationDataType = '';
  public $configuration;
  public $id;
  protected $__errorResultType = 'Google_ErrorProto';
  protected $__errorResultDataType = '';
  public $errorResult;
  public function setStatus(Google_JobStatus $status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStatistics(Google_JobStatistics $statistics) {
    $this->statistics = $statistics;
  }
  public function getStatistics() {
    return $this->statistics;
  }
  public function setJobReference(Google_JobReference $jobReference) {
    $this->jobReference = $jobReference;
  }
  public function getJobReference() {
    return $this->jobReference;
  }
  public function setState($state) {
    $this->state = $state;
  }
  public function getState() {
    return $this->state;
  }
  public function setConfiguration(Google_JobConfiguration $configuration) {
    $this->configuration = $configuration;
  }
  public function getConfiguration() {
    return $this->configuration;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setErrorResult(Google_ErrorProto $errorResult) {
    $this->errorResult = $errorResult;
  }
  public function getErrorResult() {
    return $this->errorResult;
  }
}

class Google_JobReference extends Google_Model {
  public $projectId;
  public $jobId;
  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }
  public function getProjectId() {
    return $this->projectId;
  }
  public function setJobId($jobId) {
    $this->jobId = $jobId;
  }
  public function getJobId() {
    return $this->jobId;
  }
}

class Google_JobStatistics extends Google_Model {
  public $endTime;
  public $totalBytesProcessed;
  public $startTime;
  public function setEndTime($endTime) {
    $this->endTime = $endTime;
  }
  public function getEndTime() {
    return $this->endTime;
  }
  public function setTotalBytesProcessed($totalBytesProcessed) {
    $this->totalBytesProcessed = $totalBytesProcessed;
  }
  public function getTotalBytesProcessed() {
    return $this->totalBytesProcessed;
  }
  public function setStartTime($startTime) {
    $this->startTime = $startTime;
  }
  public function getStartTime() {
    return $this->startTime;
  }
}

class Google_JobStatus extends Google_Model {
  public $state;
  protected $__errorsType = 'Google_ErrorProto';
  protected $__errorsDataType = 'array';
  public $errors;
  protected $__errorResultType = 'Google_ErrorProto';
  protected $__errorResultDataType = '';
  public $errorResult;
  public function setState($state) {
    $this->state = $state;
  }
  public function getState() {
    return $this->state;
  }
  public function setErrors(/* array(Google_ErrorProto) */ $errors) {
    $this->assertIsArray($errors, 'Google_ErrorProto', __METHOD__);
    $this->errors = $errors;
  }
  public function getErrors() {
    return $this->errors;
  }
  public function setErrorResult(Google_ErrorProto $errorResult) {
    $this->errorResult = $errorResult;
  }
  public function getErrorResult() {
    return $this->errorResult;
  }
}

class Google_ProjectList extends Google_Model {
  public $nextPageToken;
  public $totalItems;
  public $kind;
  public $etag;
  protected $__projectsType = 'Google_ProjectListProjects';
  protected $__projectsDataType = 'array';
  public $projects;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setProjects(/* array(Google_ProjectListProjects) */ $projects) {
    $this->assertIsArray($projects, 'Google_ProjectListProjects', __METHOD__);
    $this->projects = $projects;
  }
  public function getProjects() {
    return $this->projects;
  }
}

class Google_ProjectListProjects extends Google_Model {
  public $friendlyName;
  public $kind;
  public $id;
  protected $__projectReferenceType = 'Google_ProjectReference';
  protected $__projectReferenceDataType = '';
  public $projectReference;
  public function setFriendlyName($friendlyName) {
    $this->friendlyName = $friendlyName;
  }
  public function getFriendlyName() {
    return $this->friendlyName;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setProjectReference(Google_ProjectReference $projectReference) {
    $this->projectReference = $projectReference;
  }
  public function getProjectReference() {
    return $this->projectReference;
  }
}

class Google_ProjectReference extends Google_Model {
  public $projectId;
  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }
  public function getProjectId() {
    return $this->projectId;
  }
}

class Google_QueryRequest extends Google_Model {
  public $timeoutMs;
  public $kind;
  public $dryRun;
  protected $__defaultDatasetType = 'Google_DatasetReference';
  protected $__defaultDatasetDataType = '';
  public $defaultDataset;
  public $maxResults;
  public $query;
  public function setTimeoutMs($timeoutMs) {
    $this->timeoutMs = $timeoutMs;
  }
  public function getTimeoutMs() {
    return $this->timeoutMs;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDryRun($dryRun) {
    $this->dryRun = $dryRun;
  }
  public function getDryRun() {
    return $this->dryRun;
  }
  public function setDefaultDataset(Google_DatasetReference $defaultDataset) {
    $this->defaultDataset = $defaultDataset;
  }
  public function getDefaultDataset() {
    return $this->defaultDataset;
  }
  public function setMaxResults($maxResults) {
    $this->maxResults = $maxResults;
  }
  public function getMaxResults() {
    return $this->maxResults;
  }
  public function setQuery($query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
}

class Google_QueryResponse extends Google_Model {
  public $kind;
  protected $__rowsType = 'Google_TableRow';
  protected $__rowsDataType = 'array';
  public $rows;
  protected $__jobReferenceType = 'Google_JobReference';
  protected $__jobReferenceDataType = '';
  public $jobReference;
  public $jobComplete;
  public $totalRows;
  protected $__schemaType = 'Google_TableSchema';
  protected $__schemaDataType = '';
  public $schema;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows(/* array(Google_TableRow) */ $rows) {
    $this->assertIsArray($rows, 'Google_TableRow', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setJobReference(Google_JobReference $jobReference) {
    $this->jobReference = $jobReference;
  }
  public function getJobReference() {
    return $this->jobReference;
  }
  public function setJobComplete($jobComplete) {
    $this->jobComplete = $jobComplete;
  }
  public function getJobComplete() {
    return $this->jobComplete;
  }
  public function setTotalRows($totalRows) {
    $this->totalRows = $totalRows;
  }
  public function getTotalRows() {
    return $this->totalRows;
  }
  public function setSchema(Google_TableSchema $schema) {
    $this->schema = $schema;
  }
  public function getSchema() {
    return $this->schema;
  }
}

class Google_Table extends Google_Model {
  public $kind;
  public $lastModifiedTime;
  public $description;
  public $creationTime;
  protected $__tableReferenceType = 'Google_TableReference';
  protected $__tableReferenceDataType = '';
  public $tableReference;
  public $numRows;
  public $numBytes;
  public $etag;
  public $friendlyName;
  public $expirationTime;
  public $id;
  public $selfLink;
  protected $__schemaType = 'Google_TableSchema';
  protected $__schemaDataType = '';
  public $schema;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLastModifiedTime($lastModifiedTime) {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  public function getLastModifiedTime() {
    return $this->lastModifiedTime;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setCreationTime($creationTime) {
    $this->creationTime = $creationTime;
  }
  public function getCreationTime() {
    return $this->creationTime;
  }
  public function setTableReference(Google_TableReference $tableReference) {
    $this->tableReference = $tableReference;
  }
  public function getTableReference() {
    return $this->tableReference;
  }
  public function setNumRows($numRows) {
    $this->numRows = $numRows;
  }
  public function getNumRows() {
    return $this->numRows;
  }
  public function setNumBytes($numBytes) {
    $this->numBytes = $numBytes;
  }
  public function getNumBytes() {
    return $this->numBytes;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setFriendlyName($friendlyName) {
    $this->friendlyName = $friendlyName;
  }
  public function getFriendlyName() {
    return $this->friendlyName;
  }
  public function setExpirationTime($expirationTime) {
    $this->expirationTime = $expirationTime;
  }
  public function getExpirationTime() {
    return $this->expirationTime;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setSchema(Google_TableSchema $schema) {
    $this->schema = $schema;
  }
  public function getSchema() {
    return $this->schema;
  }
}

class Google_TableDataList extends Google_Model {
  public $pageToken;
  public $kind;
  public $etag;
  protected $__rowsType = 'Google_TableRow';
  protected $__rowsDataType = 'array';
  public $rows;
  public $totalRows;
  public function setPageToken($pageToken) {
    $this->pageToken = $pageToken;
  }
  public function getPageToken() {
    return $this->pageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setRows(/* array(Google_TableRow) */ $rows) {
    $this->assertIsArray($rows, 'Google_TableRow', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setTotalRows($totalRows) {
    $this->totalRows = $totalRows;
  }
  public function getTotalRows() {
    return $this->totalRows;
  }
}

class Google_TableFieldSchema extends Google_Model {
  protected $__fieldsType = 'Google_TableFieldSchema';
  protected $__fieldsDataType = 'array';
  public $fields;
  public $type;
  public $mode;
  public $name;
  public function setFields(/* array(Google_TableFieldSchema) */ $fields) {
    $this->assertIsArray($fields, 'Google_TableFieldSchema', __METHOD__);
    $this->fields = $fields;
  }
  public function getFields() {
    return $this->fields;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setMode($mode) {
    $this->mode = $mode;
  }
  public function getMode() {
    return $this->mode;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_TableList extends Google_Model {
  public $nextPageToken;
  protected $__tablesType = 'Google_TableListTables';
  protected $__tablesDataType = 'array';
  public $tables;
  public $kind;
  public $etag;
  public $totalItems;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setTables(/* array(Google_TableListTables) */ $tables) {
    $this->assertIsArray($tables, 'Google_TableListTables', __METHOD__);
    $this->tables = $tables;
  }
  public function getTables() {
    return $this->tables;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
}

class Google_TableListTables extends Google_Model {
  public $friendlyName;
  public $kind;
  public $id;
  protected $__tableReferenceType = 'Google_TableReference';
  protected $__tableReferenceDataType = '';
  public $tableReference;
  public function setFriendlyName($friendlyName) {
    $this->friendlyName = $friendlyName;
  }
  public function getFriendlyName() {
    return $this->friendlyName;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setTableReference(Google_TableReference $tableReference) {
    $this->tableReference = $tableReference;
  }
  public function getTableReference() {
    return $this->tableReference;
  }
}

class Google_TableReference extends Google_Model {
  public $projectId;
  public $tableId;
  public $datasetId;
  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }
  public function getProjectId() {
    return $this->projectId;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
  public function setDatasetId($datasetId) {
    $this->datasetId = $datasetId;
  }
  public function getDatasetId() {
    return $this->datasetId;
  }
}

class Google_TableRow extends Google_Model {
  protected $__fType = 'Google_TableRowF';
  protected $__fDataType = 'array';
  public $f;
  public function setF(/* array(Google_TableRowF) */ $f) {
    $this->assertIsArray($f, 'Google_TableRowF', __METHOD__);
    $this->f = $f;
  }
  public function getF() {
    return $this->f;
  }
}

class Google_TableRowF extends Google_Model {
  public $v;
  public function setV($v) {
    $this->v = $v;
  }
  public function getV() {
    return $this->v;
  }
}

class Google_TableSchema extends Google_Model {
  protected $__fieldsType = 'Google_TableFieldSchema';
  protected $__fieldsDataType = 'array';
  public $fields;
  public function setFields(/* array(Google_TableFieldSchema) */ $fields) {
    $this->assertIsArray($fields, 'Google_TableFieldSchema', __METHOD__);
    $this->fields = $fields;
  }
  public function getFields() {
    return $this->fields;
  }
}
