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

namespace Google\Service\Compute;

class AutoscalingPolicyCustomMetricUtilization extends \Google\Model
{
  /**
   * Sets the utilization target value for a cumulative or delta metric,
   * expressed as the rate of growth per minute.
   */
  public const UTILIZATION_TARGET_TYPE_DELTA_PER_MINUTE = 'DELTA_PER_MINUTE';
  /**
   * Sets the utilization target value for a cumulative or delta metric,
   * expressed as the rate of growth per second.
   */
  public const UTILIZATION_TARGET_TYPE_DELTA_PER_SECOND = 'DELTA_PER_SECOND';
  /**
   * Sets the utilization target value for a gauge metric. The autoscaler will
   * collect the average utilization of the virtual machines from the last
   * couple of minutes, and compare the value to the utilization target value to
   * perform autoscaling.
   */
  public const UTILIZATION_TARGET_TYPE_GAUGE = 'GAUGE';
  /**
   * A filter string, compatible with a Stackdriver Monitoringfilter string
   * forTimeSeries.list API call. This filter is used to select a specific
   * TimeSeries for the purpose of autoscaling and to determine whether the
   * metric is exporting per-instance or per-group data.
   *
   * For the filter to be valid for autoscaling purposes, the following rules
   * apply:             - You can only use the AND operator for joining
   * selectors.     - You can only use direct equality comparison operator
   * (=) without any functions for each selector.     - You can specify the
   * metric in both the filter string and in the        metric field. However,
   * if specified in both places, the metric must        be identical.     - The
   * monitored resource type        determines what kind of values are expected
   * for the metric. If it is        a gce_instance, the autoscaler expects the
   * metric to        include a separate TimeSeries for each instance in a
   * group. In such a        case, you cannot filter on resource labels.
   * If the resource type is any other value, the autoscaler expects        this
   * metric to contain values that apply to the entire autoscaled
   * instance group and resource label filtering can be performed to
   * point autoscaler at the correct TimeSeries to scale upon. This is
   * called a *per-group metric* for the purpose of autoscaling.            If
   * not specified, the type defaults to        gce_instance.
   *
   * Try to provide a filter that is selective enough to pick just one
   * TimeSeries for the autoscaled group or for each of the instances (if you
   * are using gce_instance resource type). If multiple TimeSeries are returned
   * upon the query execution, the autoscaler will sum their respective values
   * to obtain its scaling value.
   *
   * @var string
   */
  public $filter;
  /**
   * The identifier (type) of the Stackdriver Monitoring metric. The metric
   * cannot have negative values.
   *
   * The metric must have a value type of INT64 orDOUBLE.
   *
   * @var string
   */
  public $metric;
  /**
   * If scaling is based on a per-group metric value that represents the total
   * amount of work to be done or resource usage, set this value to an amount
   * assigned for a single instance of the scaled group. Autoscaler keeps the
   * number of instances proportional to the value of this metric. The metric
   * itself does not change value due to group resizing.
   *
   * A good metric to use with the target is for
   * examplepubsub.googleapis.com/subscription/num_undelivered_messages or a
   * custom metric exporting the total number of requests coming to your
   * instances.
   *
   * A bad example would be a metric exporting an average or median latency,
   * since this value can't include a chunk assignable to a single instance, it
   * could be better used with utilization_target instead.
   *
   * @var 
   */
  public $singleInstanceAssignment;
  /**
   * The target value of the metric that autoscaler maintains. This must be a
   * positive value. A utilization metric scales number of virtual machines
   * handling requests to increase or decrease proportionally to the metric.
   *
   * For example, a good metric to use as a utilization_target ishttps://www.goo
   * gleapis.com/compute/v1/instance/network/received_bytes_count. The
   * autoscaler works to keep this value constant for each of the instances.
   *
   * @var 
   */
  public $utilizationTarget;
  /**
   * Defines how target utilization value is expressed for a Stackdriver
   * Monitoring metric. Either GAUGE,DELTA_PER_SECOND, or DELTA_PER_MINUTE.
   *
   * @var string
   */
  public $utilizationTargetType;

  /**
   * A filter string, compatible with a Stackdriver Monitoringfilter string
   * forTimeSeries.list API call. This filter is used to select a specific
   * TimeSeries for the purpose of autoscaling and to determine whether the
   * metric is exporting per-instance or per-group data.
   *
   * For the filter to be valid for autoscaling purposes, the following rules
   * apply:             - You can only use the AND operator for joining
   * selectors.     - You can only use direct equality comparison operator
   * (=) without any functions for each selector.     - You can specify the
   * metric in both the filter string and in the        metric field. However,
   * if specified in both places, the metric must        be identical.     - The
   * monitored resource type        determines what kind of values are expected
   * for the metric. If it is        a gce_instance, the autoscaler expects the
   * metric to        include a separate TimeSeries for each instance in a
   * group. In such a        case, you cannot filter on resource labels.
   * If the resource type is any other value, the autoscaler expects        this
   * metric to contain values that apply to the entire autoscaled
   * instance group and resource label filtering can be performed to
   * point autoscaler at the correct TimeSeries to scale upon. This is
   * called a *per-group metric* for the purpose of autoscaling.            If
   * not specified, the type defaults to        gce_instance.
   *
   * Try to provide a filter that is selective enough to pick just one
   * TimeSeries for the autoscaled group or for each of the instances (if you
   * are using gce_instance resource type). If multiple TimeSeries are returned
   * upon the query execution, the autoscaler will sum their respective values
   * to obtain its scaling value.
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
   * The identifier (type) of the Stackdriver Monitoring metric. The metric
   * cannot have negative values.
   *
   * The metric must have a value type of INT64 orDOUBLE.
   *
   * @param string $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return string
   */
  public function getMetric()
  {
    return $this->metric;
  }
  public function setSingleInstanceAssignment($singleInstanceAssignment)
  {
    $this->singleInstanceAssignment = $singleInstanceAssignment;
  }
  public function getSingleInstanceAssignment()
  {
    return $this->singleInstanceAssignment;
  }
  public function setUtilizationTarget($utilizationTarget)
  {
    $this->utilizationTarget = $utilizationTarget;
  }
  public function getUtilizationTarget()
  {
    return $this->utilizationTarget;
  }
  /**
   * Defines how target utilization value is expressed for a Stackdriver
   * Monitoring metric. Either GAUGE,DELTA_PER_SECOND, or DELTA_PER_MINUTE.
   *
   * Accepted values: DELTA_PER_MINUTE, DELTA_PER_SECOND, GAUGE
   *
   * @param self::UTILIZATION_TARGET_TYPE_* $utilizationTargetType
   */
  public function setUtilizationTargetType($utilizationTargetType)
  {
    $this->utilizationTargetType = $utilizationTargetType;
  }
  /**
   * @return self::UTILIZATION_TARGET_TYPE_*
   */
  public function getUtilizationTargetType()
  {
    return $this->utilizationTargetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingPolicyCustomMetricUtilization::class, 'Google_Service_Compute_AutoscalingPolicyCustomMetricUtilization');
