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

namespace Google\Service\Cloudchannel\Resource;

use Google\Service\Cloudchannel\GoogleCloudChannelV1ListReportsResponse;
use Google\Service\Cloudchannel\GoogleCloudChannelV1RunReportJobRequest;
use Google\Service\Cloudchannel\GoogleLongrunningOperation;

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudchannelService = new Google\Service\Cloudchannel(...);
 *   $reports = $cloudchannelService->accounts_reports;
 *  </code>
 */
class AccountsReports extends \Google\Service\Resource
{
  /**
   * Lists the reports that RunReportJob can run. These reports include an ID, a
   * description, and the list of columns that will be in the result. Deprecated:
   * Please use [Export Channel Services data to
   * BigQuery](https://cloud.google.com/channel/docs/rebilling/export-data-to-
   * bigquery) instead. (reports.listAccountsReports)
   *
   * @param string $parent Required. The resource name of the partner account to
   * list available reports for. Parent uses the format: accounts/{account_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode Optional. The BCP-47 language code, such as
   * "en-US". If specified, the response is localized to the corresponding
   * language code if the original data sources support it. Default is "en-US".
   * @opt_param int pageSize Optional. Requested page size of the report. The
   * server might return fewer results than requested. If unspecified, returns 20
   * reports. The maximum value is 100.
   * @opt_param string pageToken Optional. A token that specifies a page of
   * results beyond the first page. Obtained through
   * ListReportsResponse.next_page_token of the previous
   * CloudChannelReportsService.ListReports call.
   * @return GoogleCloudChannelV1ListReportsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsReports($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudChannelV1ListReportsResponse::class);
  }
  /**
   * Begins generation of data for a given report. The report identifier is a UID
   * (for example, `613bf59q`). Possible error codes: * PERMISSION_DENIED: The
   * user doesn't have access to this report. * INVALID_ARGUMENT: Required request
   * parameters are missing or invalid. * NOT_FOUND: The report identifier was not
   * found. * INTERNAL: Any non-user error related to a technical issue in the
   * backend. Contact Cloud Channel support. * UNKNOWN: Any non-user error related
   * to a technical issue in the backend. Contact Cloud Channel support. Return
   * value: The ID of a long-running operation. To get the results of the
   * operation, call the GetOperation method of CloudChannelOperationsService. The
   * Operation metadata contains an instance of OperationMetadata. To get the
   * results of report generation, call
   * CloudChannelReportsService.FetchReportResults with the
   * RunReportJobResponse.report_job. Deprecated: Please use [Export Channel
   * Services data to
   * BigQuery](https://cloud.google.com/channel/docs/rebilling/export-data-to-
   * bigquery) instead. (reports.run)
   *
   * @param string $name Required. The report's resource name. Specifies the
   * account and report used to generate report data. The report_id identifier is
   * a UID (for example, `613bf59q`). Name uses the format:
   * accounts/{account_id}/reports/{report_id}
   * @param GoogleCloudChannelV1RunReportJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function run($name, GoogleCloudChannelV1RunReportJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('run', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsReports::class, 'Google_Service_Cloudchannel_Resource_AccountsReports');
