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

use Google\Service\Dataproc\AccessSessionSparkApplicationEnvironmentInfoResponse;
use Google\Service\Dataproc\AccessSessionSparkApplicationJobResponse;
use Google\Service\Dataproc\AccessSessionSparkApplicationResponse;
use Google\Service\Dataproc\AccessSessionSparkApplicationSqlQueryResponse;
use Google\Service\Dataproc\AccessSessionSparkApplicationSqlSparkPlanGraphResponse;
use Google\Service\Dataproc\AccessSessionSparkApplicationStageAttemptResponse;
use Google\Service\Dataproc\AccessSessionSparkApplicationStageRddOperationGraphResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationExecutorStageSummaryResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationExecutorsResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationJobsResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationSqlQueriesResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationStageAttemptTasksResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationStageAttemptsResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationStagesResponse;
use Google\Service\Dataproc\SearchSessionSparkApplicationsResponse;
use Google\Service\Dataproc\SummarizeSessionSparkApplicationExecutorsResponse;
use Google\Service\Dataproc\SummarizeSessionSparkApplicationJobsResponse;
use Google\Service\Dataproc\SummarizeSessionSparkApplicationStageAttemptTasksResponse;
use Google\Service\Dataproc\SummarizeSessionSparkApplicationStagesResponse;
use Google\Service\Dataproc\WriteSessionSparkApplicationContextRequest;
use Google\Service\Dataproc\WriteSessionSparkApplicationContextResponse;

/**
 * The "sparkApplications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataprocService = new Google\Service\Dataproc(...);
 *   $sparkApplications = $dataprocService->projects_locations_sessions_sparkApplications;
 *  </code>
 */
class ProjectsLocationsSessionsSparkApplications extends \Google\Service\Resource
{
  /**
   * Obtain high level information corresponding to a single Spark Application.
   * (sparkApplications.access)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return AccessSessionSparkApplicationResponse
   * @throws \Google\Service\Exception
   */
  public function access($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('access', [$params], AccessSessionSparkApplicationResponse::class);
  }
  /**
   * Obtain environment details for a Spark Application
   * (sparkApplications.accessEnvironmentInfo)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return AccessSessionSparkApplicationEnvironmentInfoResponse
   * @throws \Google\Service\Exception
   */
  public function accessEnvironmentInfo($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('accessEnvironmentInfo', [$params], AccessSessionSparkApplicationEnvironmentInfoResponse::class);
  }
  /**
   * Obtain data corresponding to a spark job for a Spark Application.
   * (sparkApplications.accessJob)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string jobId Required. Job ID to fetch data for.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return AccessSessionSparkApplicationJobResponse
   * @throws \Google\Service\Exception
   */
  public function accessJob($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('accessJob', [$params], AccessSessionSparkApplicationJobResponse::class);
  }
  /**
   * Obtain Spark Plan Graph for a Spark Application SQL execution. Limits the
   * number of clusters returned as part of the graph to 10000.
   * (sparkApplications.accessSqlPlan)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string executionId Required. Execution ID
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return AccessSessionSparkApplicationSqlSparkPlanGraphResponse
   * @throws \Google\Service\Exception
   */
  public function accessSqlPlan($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('accessSqlPlan', [$params], AccessSessionSparkApplicationSqlSparkPlanGraphResponse::class);
  }
  /**
   * Obtain data corresponding to a particular SQL Query for a Spark Application.
   * (sparkApplications.accessSqlQuery)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool details Optional. Lists/ hides details of Spark plan nodes.
   * True is set to list and false to hide.
   * @opt_param string executionId Required. Execution ID
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param bool planDescription Optional. Enables/ disables physical plan
   * description on demand
   * @return AccessSessionSparkApplicationSqlQueryResponse
   * @throws \Google\Service\Exception
   */
  public function accessSqlQuery($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('accessSqlQuery', [$params], AccessSessionSparkApplicationSqlQueryResponse::class);
  }
  /**
   * Obtain data corresponding to a spark stage attempt for a Spark Application.
   * (sparkApplications.accessStageAttempt)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param int stageAttemptId Required. Stage Attempt ID
   * @opt_param string stageId Required. Stage ID
   * @opt_param string summaryMetricsMask Optional. The list of summary metrics
   * fields to include. Empty list will default to skip all summary metrics
   * fields. Example, if the response should include TaskQuantileMetrics, the
   * request should have task_quantile_metrics in summary_metrics_mask field
   * @return AccessSessionSparkApplicationStageAttemptResponse
   * @throws \Google\Service\Exception
   */
  public function accessStageAttempt($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('accessStageAttempt', [$params], AccessSessionSparkApplicationStageAttemptResponse::class);
  }
  /**
   * Obtain RDD operation graph for a Spark Application Stage. Limits the number
   * of clusters returned as part of the graph to 10000.
   * (sparkApplications.accessStageRddGraph)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param string stageId Required. Stage ID
   * @return AccessSessionSparkApplicationStageRddOperationGraphResponse
   * @throws \Google\Service\Exception
   */
  public function accessStageRddGraph($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('accessStageRddGraph', [$params], AccessSessionSparkApplicationStageRddOperationGraphResponse::class);
  }
  /**
   * Obtain high level information and list of Spark Applications corresponding to
   * a batch (sparkApplications.search)
   *
   * @param string $parent Required. The fully qualified name of the session to
   * retrieve in the format
   * "projects/PROJECT_ID/locations/DATAPROC_REGION/sessions/SESSION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string applicationStatus Optional. Search only applications in the
   * chosen state.
   * @opt_param string maxEndTime Optional. Latest end timestamp to list.
   * @opt_param string maxTime Optional. Latest start timestamp to list.
   * @opt_param string minEndTime Optional. Earliest end timestamp to list.
   * @opt_param string minTime Optional. Earliest start timestamp to list.
   * @opt_param int pageSize Optional. Maximum number of applications to return in
   * each response. The service may return fewer than this. The default page size
   * is 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplications call. Provide this token to retrieve the
   * subsequent page.
   * @return SearchSessionSparkApplicationsResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], SearchSessionSparkApplicationsResponse::class);
  }
  /**
   * Obtain executor summary with respect to a spark stage attempt.
   * (sparkApplications.searchExecutorStageSummary)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of executors to return in
   * each response. The service may return fewer than this. The default page size
   * is 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationExecutorStageSummary call. Provide this token to
   * retrieve the subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param int stageAttemptId Required. Stage Attempt ID
   * @opt_param string stageId Required. Stage ID
   * @return SearchSessionSparkApplicationExecutorStageSummaryResponse
   * @throws \Google\Service\Exception
   */
  public function searchExecutorStageSummary($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchExecutorStageSummary', [$params], SearchSessionSparkApplicationExecutorStageSummaryResponse::class);
  }
  /**
   * Obtain data corresponding to executors for a Spark Application.
   * (sparkApplications.searchExecutors)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string executorStatus Optional. Filter to select whether active/
   * dead or all executors should be selected.
   * @opt_param int pageSize Optional. Maximum number of executors to return in
   * each response. The service may return fewer than this. The default page size
   * is 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationExecutors call. Provide this token to retrieve
   * the subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return SearchSessionSparkApplicationExecutorsResponse
   * @throws \Google\Service\Exception
   */
  public function searchExecutors($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchExecutors', [$params], SearchSessionSparkApplicationExecutorsResponse::class);
  }
  /**
   * Obtain list of spark jobs corresponding to a Spark Application.
   * (sparkApplications.searchJobs)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string jobIds Optional. List of Job IDs to filter by if provided.
   * @opt_param string jobStatus Optional. List only jobs in the specific state.
   * @opt_param int pageSize Optional. Maximum number of jobs to return in each
   * response. The service may return fewer than this. The default page size is
   * 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationJobs call. Provide this token to retrieve the
   * subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return SearchSessionSparkApplicationJobsResponse
   * @throws \Google\Service\Exception
   */
  public function searchJobs($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchJobs', [$params], SearchSessionSparkApplicationJobsResponse::class);
  }
  /**
   * Obtain data corresponding to SQL Queries for a Spark Application.
   * (sparkApplications.searchSqlQueries)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool details Optional. Lists/ hides details of Spark plan nodes.
   * True is set to list and false to hide.
   * @opt_param string operationIds Optional. List of Spark Connect operation IDs
   * to filter by if provided.
   * @opt_param int pageSize Optional. Maximum number of queries to return in each
   * response. The service may return fewer than this. The default page size is
   * 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationSqlQueries call. Provide this token to retrieve
   * the subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param bool planDescription Optional. Enables/ disables physical plan
   * description on demand
   * @return SearchSessionSparkApplicationSqlQueriesResponse
   * @throws \Google\Service\Exception
   */
  public function searchSqlQueries($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchSqlQueries', [$params], SearchSessionSparkApplicationSqlQueriesResponse::class);
  }
  /**
   * Obtain data corresponding to tasks for a spark stage attempt for a Spark
   * Application. (sparkApplications.searchStageAttemptTasks)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of tasks to return in each
   * response. The service may return fewer than this. The default page size is
   * 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationStageAttemptTasks call. Provide this token to
   * retrieve the subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param bool sortRuntime Optional. Sort the tasks by runtime.
   * @opt_param int stageAttemptId Optional. Stage Attempt ID
   * @opt_param string stageId Optional. Stage ID
   * @opt_param string taskStatus Optional. List only tasks in the state.
   * @return SearchSessionSparkApplicationStageAttemptTasksResponse
   * @throws \Google\Service\Exception
   */
  public function searchStageAttemptTasks($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchStageAttemptTasks', [$params], SearchSessionSparkApplicationStageAttemptTasksResponse::class);
  }
  /**
   * Obtain data corresponding to a spark stage attempts for a Spark Application.
   * (sparkApplications.searchStageAttempts)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of stage attempts (paging
   * based on stage_attempt_id) to return in each response. The service may return
   * fewer than this. The default page size is 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationStageAttempts call. Provide this token to
   * retrieve the subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param string stageId Required. Stage ID for which attempts are to be
   * fetched
   * @opt_param string summaryMetricsMask Optional. The list of summary metrics
   * fields to include. Empty list will default to skip all summary metrics
   * fields. Example, if the response should include TaskQuantileMetrics, the
   * request should have task_quantile_metrics in summary_metrics_mask field
   * @return SearchSessionSparkApplicationStageAttemptsResponse
   * @throws \Google\Service\Exception
   */
  public function searchStageAttempts($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchStageAttempts', [$params], SearchSessionSparkApplicationStageAttemptsResponse::class);
  }
  /**
   * Obtain data corresponding to stages for a Spark Application.
   * (sparkApplications.searchStages)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of stages (paging based on
   * stage_id) to return in each response. The service may return fewer than this.
   * The default page size is 10; the maximum page size is 100.
   * @opt_param string pageToken Optional. A page token received from a previous
   * SearchSessionSparkApplicationStages call. Provide this token to retrieve the
   * subsequent page.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param string stageIds Optional. List of Stage IDs to filter by if
   * provided.
   * @opt_param string stageStatus Optional. List only stages in the given state.
   * @opt_param string summaryMetricsMask Optional. The list of summary metrics
   * fields to include. Empty list will default to skip all summary metrics
   * fields. Example, if the response should include TaskQuantileMetrics, the
   * request should have task_quantile_metrics in summary_metrics_mask field
   * @return SearchSessionSparkApplicationStagesResponse
   * @throws \Google\Service\Exception
   */
  public function searchStages($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchStages', [$params], SearchSessionSparkApplicationStagesResponse::class);
  }
  /**
   * Obtain summary of Executor Summary for a Spark Application
   * (sparkApplications.summarizeExecutors)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return SummarizeSessionSparkApplicationExecutorsResponse
   * @throws \Google\Service\Exception
   */
  public function summarizeExecutors($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('summarizeExecutors', [$params], SummarizeSessionSparkApplicationExecutorsResponse::class);
  }
  /**
   * Obtain summary of Jobs for a Spark Application
   * (sparkApplications.summarizeJobs)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string jobIds Optional. List of Job IDs to filter by if provided.
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @return SummarizeSessionSparkApplicationJobsResponse
   * @throws \Google\Service\Exception
   */
  public function summarizeJobs($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('summarizeJobs', [$params], SummarizeSessionSparkApplicationJobsResponse::class);
  }
  /**
   * Obtain summary of Tasks for a Spark Application Stage Attempt
   * (sparkApplications.summarizeStageAttemptTasks)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param int stageAttemptId Required. Stage Attempt ID
   * @opt_param string stageId Required. Stage ID
   * @return SummarizeSessionSparkApplicationStageAttemptTasksResponse
   * @throws \Google\Service\Exception
   */
  public function summarizeStageAttemptTasks($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('summarizeStageAttemptTasks', [$params], SummarizeSessionSparkApplicationStageAttemptTasksResponse::class);
  }
  /**
   * Obtain summary of Stages for a Spark Application
   * (sparkApplications.summarizeStages)
   *
   * @param string $name Required. The fully qualified name of the session to
   * retrieve in the format "projects/PROJECT_ID/locations/DATAPROC_REGION/session
   * s/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. Parent (Session) resource reference.
   * @opt_param string stageIds Optional. List of Stage IDs to filter by if
   * provided.
   * @return SummarizeSessionSparkApplicationStagesResponse
   * @throws \Google\Service\Exception
   */
  public function summarizeStages($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('summarizeStages', [$params], SummarizeSessionSparkApplicationStagesResponse::class);
  }
  /**
   * Write wrapper objects from dataplane to spanner (sparkApplications.write)
   *
   * @param string $name Required. The fully qualified name of the spark
   * application to write data about in the format "projects/PROJECT_ID/locations/
   * DATAPROC_REGION/sessions/SESSION_ID/sparkApplications/APPLICATION_ID"
   * @param WriteSessionSparkApplicationContextRequest $postBody
   * @param array $optParams Optional parameters.
   * @return WriteSessionSparkApplicationContextResponse
   * @throws \Google\Service\Exception
   */
  public function write($name, WriteSessionSparkApplicationContextRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('write', [$params], WriteSessionSparkApplicationContextResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSessionsSparkApplications::class, 'Google_Service_Dataproc_Resource_ProjectsLocationsSessionsSparkApplications');
