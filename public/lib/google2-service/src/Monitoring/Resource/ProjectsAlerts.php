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

namespace Google\Service\Monitoring\Resource;

use Google\Service\Monitoring\Alert;
use Google\Service\Monitoring\ListAlertsResponse;

/**
 * The "alerts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $monitoringService = new Google\Service\Monitoring(...);
 *   $alerts = $monitoringService->projects_alerts;
 *  </code>
 */
class ProjectsAlerts extends \Google\Service\Resource
{
  /**
   * Gets a single alert. (alerts.get)
   *
   * @param string $name Required. The name of the alert.The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/alerts/[ALERT_ID] The [ALERT_ID] is a system-
   * assigned unique identifier for the alert.
   * @param array $optParams Optional parameters.
   * @return Alert
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Alert::class);
  }
  /**
   * Lists the existing alerts for the metrics scope of the project.
   * (alerts.listProjectsAlerts)
   *
   * @param string $parent Required. The name of the project to list alerts for.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An alert is returned if there is a match
   * on any fields belonging to the alert or its subfields.
   * @opt_param string orderBy Optional. A comma-separated list of fields in Alert
   * to use for sorting. The default sort direction is ascending. To specify
   * descending order for a field, add a desc modifier. The following fields are
   * supported: open_time close_timeFor example, close_time desc, open_time will
   * return the alerts closed most recently, with ties broken in the order of
   * older alerts listed first.If the field is not set, the results are sorted by
   * open_time desc.
   * @opt_param int pageSize Optional. The maximum number of results to return in
   * a single response. If not set to a positive number, at most 50 alerts will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken Optional. If non-empty, page_token must contain a
   * value returned as the next_page_token in a previous response to request the
   * next set of results.
   * @return ListAlertsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsAlerts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAlertsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsAlerts::class, 'Google_Service_Monitoring_Resource_ProjectsAlerts');
