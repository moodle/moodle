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

namespace Google\Service\Monitoring;

class Criteria extends \Google\Collection
{
  protected $collection_key = 'policies';
  /**
   * Optional. When you define a snooze, you can also define a filter for that
   * snooze. The filter is a string containing one or more key-value pairs. The
   * string uses the standard https://google.aip.dev/160 filter syntax. If you
   * define a filter for a snooze, then the snooze can only apply to one alert
   * policy. When the snooze is active, incidents won't be created when the
   * incident would have key-value pairs (labels) that match those specified by
   * the filter in the snooze.Snooze filters support resource, metric, and
   * metadata labels. If multiple labels are used, then they must be connected
   * with an AND operator. For example, the following filter applies the snooze
   * to incidents that have a resource label with an instance ID of 1234567890,
   * a metric label with an instance name of test_group, a metadata user label
   * with a key of foo and a value of bar, and a metadata system label with a
   * key of region and a value of us-central1: "filter":
   * "resource.labels.instance_id=\"1234567890\" AND
   * metric.labels.instance_name=\"test_group\" AND
   * metadata.user_labels.foo=\"bar\" AND metadata.system_labels.region=\"us-
   * central1\""
   *
   * @var string
   */
  public $filter;
  /**
   * The specific AlertPolicy names for the alert that should be snoozed. The
   * format is: projects/[PROJECT_ID_OR_NUMBER]/alertPolicies/[POLICY_ID] There
   * is a limit of 16 policies per snooze. This limit is checked during snooze
   * creation. Exactly 1 alert policy is required if filter is specified at the
   * same time.
   *
   * @var string[]
   */
  public $policies;

  /**
   * Optional. When you define a snooze, you can also define a filter for that
   * snooze. The filter is a string containing one or more key-value pairs. The
   * string uses the standard https://google.aip.dev/160 filter syntax. If you
   * define a filter for a snooze, then the snooze can only apply to one alert
   * policy. When the snooze is active, incidents won't be created when the
   * incident would have key-value pairs (labels) that match those specified by
   * the filter in the snooze.Snooze filters support resource, metric, and
   * metadata labels. If multiple labels are used, then they must be connected
   * with an AND operator. For example, the following filter applies the snooze
   * to incidents that have a resource label with an instance ID of 1234567890,
   * a metric label with an instance name of test_group, a metadata user label
   * with a key of foo and a value of bar, and a metadata system label with a
   * key of region and a value of us-central1: "filter":
   * "resource.labels.instance_id=\"1234567890\" AND
   * metric.labels.instance_name=\"test_group\" AND
   * metadata.user_labels.foo=\"bar\" AND metadata.system_labels.region=\"us-
   * central1\""
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The specific AlertPolicy names for the alert that should be snoozed. The
   * format is: projects/[PROJECT_ID_OR_NUMBER]/alertPolicies/[POLICY_ID] There
   * is a limit of 16 policies per snooze. This limit is checked during snooze
   * creation. Exactly 1 alert policy is required if filter is specified at the
   * same time.
   *
   * @param string[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return string[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Criteria::class, 'Google_Service_Monitoring_Criteria');
