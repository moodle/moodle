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

namespace Google\Service\Bigquery\Resource;

use Google\Service\Bigquery\GetQueryResultsResponse;
use Google\Service\Bigquery\Job;
use Google\Service\Bigquery\JobCancelResponse;
use Google\Service\Bigquery\JobList;
use Google\Service\Bigquery\QueryRequest;
use Google\Service\Bigquery\QueryResponse;

/**
 * The "jobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryService = new Google\Service\Bigquery(...);
 *   $jobs = $bigqueryService->jobs;
 *  </code>
 */
class Jobs extends \Google\Service\Resource
{
  /**
   * Requests that a job be cancelled. This call will return immediately, and the
   * client will need to poll for the job status to see if the cancel completed
   * successfully. Cancelled jobs may still incur costs. (jobs.cancel)
   *
   * @param string $projectId Required. Project ID of the job to cancel
   * @param string $jobId Required. Job ID of the job to cancel
   * @param array $optParams Optional parameters.
   *
   * @opt_param string location The geographic location of the job. You must
   * [specify the
   * location](https://cloud.google.com/bigquery/docs/locations#specify_locations)
   * to run the job for the following scenarios: * If the location to run a job is
   * not in the `us` or the `eu` multi-regional location * If the job's location
   * is in a single region (for example, `us-central1`)
   * @return JobCancelResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($projectId, $jobId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'jobId' => $jobId];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], JobCancelResponse::class);
  }
  /**
   * Requests the deletion of the metadata of a job. This call returns when the
   * job's metadata is deleted. (jobs.delete)
   *
   * @param string $projectId Required. Project ID of the job for which metadata
   * is to be deleted.
   * @param string $jobId Required. Job ID of the job for which metadata is to be
   * deleted. If this is a parent job which has child jobs, the metadata from all
   * child jobs will be deleted as well. Direct deletion of the metadata of child
   * jobs is not allowed.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string location The geographic location of the job. Required. For
   * more information, see how to [specify locations](https://cloud.google.com/big
   * query/docs/locations#specify_locations).
   * @throws \Google\Service\Exception
   */
  public function delete($projectId, $jobId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'jobId' => $jobId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Returns information about a specific job. Job information is available for a
   * six month period after creation. Requires that you're the person who ran the
   * job, or have the Is Owner project role. (jobs.get)
   *
   * @param string $projectId Required. Project ID of the requested job.
   * @param string $jobId Required. Job ID of the requested job.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string location The geographic location of the job. You must
   * specify the location to run the job for the following scenarios: * If the
   * location to run a job is not in the `us` or the `eu` multi-regional location
   * * If the job's location is in a single region (for example, `us-central1`)
   * For more information, see how to [specify locations](https://cloud.google.com
   * /bigquery/docs/locations#specify_locations).
   * @return Job
   * @throws \Google\Service\Exception
   */
  public function get($projectId, $jobId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'jobId' => $jobId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Job::class);
  }
  /**
   * RPC to get the results of a query job. (jobs.getQueryResults)
   *
   * @param string $projectId Required. Project ID of the query job.
   * @param string $jobId Required. Job ID of the query job.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string formatOptions.timestampOutputFormat Optional. The API
   * output format for a timestamp. This offers more explicit control over the
   * timestamp output format as compared to the existing `use_int64_timestamp`
   * option.
   * @opt_param bool formatOptions.useInt64Timestamp Optional. Output timestamp as
   * usec int64. Default is false.
   * @opt_param string location The geographic location of the job. You must
   * specify the location to run the job for the following scenarios: * If the
   * location to run a job is not in the `us` or the `eu` multi-regional location
   * * If the job's location is in a single region (for example, `us-central1`)
   * For more information, see how to [specify locations](https://cloud.google.com
   * /bigquery/docs/locations#specify_locations).
   * @opt_param string maxResults Maximum number of results to read.
   * @opt_param string pageToken Page token, returned by a previous call, to
   * request the next page of results.
   * @opt_param string startIndex Zero-based index of the starting row.
   * @opt_param string timeoutMs Optional: Specifies the maximum amount of time,
   * in milliseconds, that the client is willing to wait for the query to
   * complete. By default, this limit is 10 seconds (10,000 milliseconds). If the
   * query is complete, the jobComplete field in the response is true. If the
   * query has not yet completed, jobComplete is false. You can request a longer
   * timeout period in the timeoutMs field. However, the call is not guaranteed to
   * wait for the specified timeout; it typically returns after around 200 seconds
   * (200,000 milliseconds), even if the query is not complete. If jobComplete is
   * false, you can continue to wait for the query to complete by calling the
   * getQueryResults method until the jobComplete field in the getQueryResults
   * response is true.
   * @return GetQueryResultsResponse
   * @throws \Google\Service\Exception
   */
  public function getQueryResults($projectId, $jobId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'jobId' => $jobId];
    $params = array_merge($params, $optParams);
    return $this->call('getQueryResults', [$params], GetQueryResultsResponse::class);
  }
  /**
   * Starts a new asynchronous job. This API has two different kinds of endpoint
   * URIs, as this method supports a variety of use cases. * The *Metadata* URI is
   * used for most interactions, as it accepts the job configuration directly. *
   * The *Upload* URI is ONLY for the case when you're sending both a load job
   * configuration and a data stream together. In this case, the Upload URI
   * accepts the job configuration and the data as two distinct multipart MIME
   * parts. (jobs.insert)
   *
   * @param string $projectId Project ID of project that will be billed for the
   * job.
   * @param Job $postBody
   * @param array $optParams Optional parameters.
   * @return Job
   * @throws \Google\Service\Exception
   */
  public function insert($projectId, Job $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Job::class);
  }
  /**
   * Lists all jobs that you started in the specified project. Job information is
   * available for a six month period after creation. The job list is sorted in
   * reverse chronological order, by job creation time. Requires the Can View
   * project role, or the Is Owner project role if you set the allUsers property.
   * (jobs.listJobs)
   *
   * @param string $projectId Project ID of the jobs to list.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allUsers Whether to display jobs owned by all users in the
   * project. Default False.
   * @opt_param string maxCreationTime Max value for job creation time, in
   * milliseconds since the POSIX epoch. If set, only jobs created before or at
   * this timestamp are returned.
   * @opt_param string maxResults The maximum number of results to return in a
   * single response page. Leverage the page tokens to iterate through the entire
   * collection.
   * @opt_param string minCreationTime Min value for job creation time, in
   * milliseconds since the POSIX epoch. If set, only jobs created after or at
   * this timestamp are returned.
   * @opt_param string pageToken Page token, returned by a previous call, to
   * request the next page of results.
   * @opt_param string parentJobId If set, show only child jobs of the specified
   * parent. Otherwise, show all top-level jobs.
   * @opt_param string projection Restrict information returned to a set of
   * selected fields
   * @opt_param string stateFilter Filter for job state
   * @return JobList
   * @throws \Google\Service\Exception
   */
  public function listJobs($projectId, $optParams = [])
  {
    $params = ['projectId' => $projectId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], JobList::class);
  }
  /**
   * Runs a BigQuery SQL query synchronously and returns query results if the
   * query completes within a specified timeout. (jobs.query)
   *
   * @param string $projectId Required. Project ID of the query request.
   * @param QueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return QueryResponse
   * @throws \Google\Service\Exception
   */
  public function query($projectId, QueryRequest $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('query', [$params], QueryResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Jobs::class, 'Google_Service_Bigquery_Resource_Jobs');
