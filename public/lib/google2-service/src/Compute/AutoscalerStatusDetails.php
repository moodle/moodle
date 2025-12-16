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

class AutoscalerStatusDetails extends \Google\Model
{
  /**
   * All instances in the instance group are unhealthy (not in RUNNING state).
   */
  public const TYPE_ALL_INSTANCES_UNHEALTHY = 'ALL_INSTANCES_UNHEALTHY';
  /**
   * There is no backend service attached to the instance group.
   */
  public const TYPE_BACKEND_SERVICE_DOES_NOT_EXIST = 'BACKEND_SERVICE_DOES_NOT_EXIST';
  /**
   * Autoscaler recommends a size greater than maxNumReplicas.
   */
  public const TYPE_CAPPED_AT_MAX_NUM_REPLICAS = 'CAPPED_AT_MAX_NUM_REPLICAS';
  /**
   * The custom metric samples are not exported often enough to be a credible
   * base for autoscaling.
   */
  public const TYPE_CUSTOM_METRIC_DATA_POINTS_TOO_SPARSE = 'CUSTOM_METRIC_DATA_POINTS_TOO_SPARSE';
  /**
   * The custom metric that was specified does not exist or does not have the
   * necessary labels.
   */
  public const TYPE_CUSTOM_METRIC_INVALID = 'CUSTOM_METRIC_INVALID';
  /**
   * The minNumReplicas is equal to maxNumReplicas. This means the autoscaler
   * cannot add or remove instances from the instance group.
   */
  public const TYPE_MIN_EQUALS_MAX = 'MIN_EQUALS_MAX';
  /**
   * The autoscaler did not receive any data from the custom metric configured
   * for autoscaling.
   */
  public const TYPE_MISSING_CUSTOM_METRIC_DATA_POINTS = 'MISSING_CUSTOM_METRIC_DATA_POINTS';
  /**
   * The autoscaler is configured to scale based on a load balancing signal but
   * the instance group has not received any requests from the load balancer.
   */
  public const TYPE_MISSING_LOAD_BALANCING_DATA_POINTS = 'MISSING_LOAD_BALANCING_DATA_POINTS';
  /**
   * Autoscaling is turned off. The number of instances in the group won't
   * change automatically. The autoscaling configuration is preserved.
   */
  public const TYPE_MODE_OFF = 'MODE_OFF';
  /**
   * Autoscaling is in the "Autoscale only scale out" mode. Instances in the
   * group will be only added.
   */
  public const TYPE_MODE_ONLY_SCALE_OUT = 'MODE_ONLY_SCALE_OUT';
  /**
   * Autoscaling is in the "Autoscale only out" mode. Instances in the group
   * will be only added.
   */
  public const TYPE_MODE_ONLY_UP = 'MODE_ONLY_UP';
  /**
   * The instance group cannot be autoscaled because it has more than one
   * backend service attached to it.
   */
  public const TYPE_MORE_THAN_ONE_BACKEND_SERVICE = 'MORE_THAN_ONE_BACKEND_SERVICE';
  /**
   * There is insufficient quota for the necessary resources, such as CPU or
   * number of instances.
   */
  public const TYPE_NOT_ENOUGH_QUOTA_AVAILABLE = 'NOT_ENOUGH_QUOTA_AVAILABLE';
  /**
   * Showed only for regional autoscalers: there is a resource stockout in the
   * chosen region.
   */
  public const TYPE_REGION_RESOURCE_STOCKOUT = 'REGION_RESOURCE_STOCKOUT';
  /**
   * The target to be scaled does not exist.
   */
  public const TYPE_SCALING_TARGET_DOES_NOT_EXIST = 'SCALING_TARGET_DOES_NOT_EXIST';
  /**
   * For some scaling schedules minRequiredReplicas is greater than
   * maxNumReplicas. Autoscaler always recommends at most maxNumReplicas
   * instances.
   */
  public const TYPE_SCHEDULED_INSTANCES_GREATER_THAN_AUTOSCALER_MAX = 'SCHEDULED_INSTANCES_GREATER_THAN_AUTOSCALER_MAX';
  /**
   * For some scaling schedules minRequiredReplicas is less than minNumReplicas.
   * Autoscaler always recommends at least minNumReplicas instances.
   */
  public const TYPE_SCHEDULED_INSTANCES_LESS_THAN_AUTOSCALER_MIN = 'SCHEDULED_INSTANCES_LESS_THAN_AUTOSCALER_MIN';
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Autoscaling does not work with an HTTP/S load balancer that has been
   * configured for maxRate.
   */
  public const TYPE_UNSUPPORTED_MAX_RATE_LOAD_BALANCING_CONFIGURATION = 'UNSUPPORTED_MAX_RATE_LOAD_BALANCING_CONFIGURATION';
  /**
   * For zonal autoscalers: there is a resource stockout in the chosen zone. For
   * regional autoscalers: in at least one of the zones you're using there is a
   * resource stockout.
   */
  public const TYPE_ZONE_RESOURCE_STOCKOUT = 'ZONE_RESOURCE_STOCKOUT';
  /**
   * The status message.
   *
   * @var string
   */
  public $message;
  /**
   * The type of error, warning, or notice returned. Current set of possible
   * values:        - ALL_INSTANCES_UNHEALTHY (WARNING):      All instances in
   * the instance group are unhealthy (not in RUNNING      state).    -
   * BACKEND_SERVICE_DOES_NOT_EXIST (ERROR):      There is no backend service
   * attached to the instance group.    - CAPPED_AT_MAX_NUM_REPLICAS (WARNING):
   * Autoscaler recommends a size greater than maxNumReplicas.    -
   * CUSTOM_METRIC_DATA_POINTS_TOO_SPARSE (WARNING):      The custom metric
   * samples are not exported often enough to be      a credible base for
   * autoscaling.    - CUSTOM_METRIC_INVALID (ERROR):      The custom metric
   * that was specified does not exist or does not have      the necessary
   * labels.    - MIN_EQUALS_MAX (WARNING):      The minNumReplicas is equal to
   * maxNumReplicas. This means the      autoscaler cannot add or remove
   * instances from the instance group.    - MISSING_CUSTOM_METRIC_DATA_POINTS
   * (WARNING):      The autoscaler did not receive any data from the custom
   * metric      configured for autoscaling.    -
   * MISSING_LOAD_BALANCING_DATA_POINTS (WARNING):      The autoscaler is
   * configured to scale based on a load balancing signal      but the instance
   * group has not received any requests from the load      balancer.    -
   * MODE_OFF (WARNING):      Autoscaling is turned off. The number of instances
   * in the group won't      change automatically. The autoscaling configuration
   * is preserved.    - MODE_ONLY_UP (WARNING):      Autoscaling is in the
   * "Autoscale only out" mode. The autoscaler can add      instances but not
   * remove any.    - MORE_THAN_ONE_BACKEND_SERVICE (ERROR):      The instance
   * group cannot be autoscaled because it has more than one      backend
   * service attached to it.    - NOT_ENOUGH_QUOTA_AVAILABLE (ERROR):      There
   * is insufficient quota for the necessary resources, such as CPU or
   * number of instances.    - REGION_RESOURCE_STOCKOUT (ERROR):      Shown only
   * for regional autoscalers: there is a resource stockout in      the chosen
   * region.    - SCALING_TARGET_DOES_NOT_EXIST (ERROR):      The target to be
   * scaled does not exist.    -
   * UNSUPPORTED_MAX_RATE_LOAD_BALANCING_CONFIGURATION      (ERROR): Autoscaling
   * does not work with an HTTP/S load balancer that      has been configured
   * for maxRate.    - ZONE_RESOURCE_STOCKOUT (ERROR):      For zonal
   * autoscalers: there is a resource stockout in the chosen zone.      For
   * regional autoscalers: in at least one of the zones you're using      there
   * is a resource stockout.
   *
   * New values might be added in the future. Some of the values might not be
   * available in all API versions.
   *
   * @var string
   */
  public $type;

  /**
   * The status message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The type of error, warning, or notice returned. Current set of possible
   * values:        - ALL_INSTANCES_UNHEALTHY (WARNING):      All instances in
   * the instance group are unhealthy (not in RUNNING      state).    -
   * BACKEND_SERVICE_DOES_NOT_EXIST (ERROR):      There is no backend service
   * attached to the instance group.    - CAPPED_AT_MAX_NUM_REPLICAS (WARNING):
   * Autoscaler recommends a size greater than maxNumReplicas.    -
   * CUSTOM_METRIC_DATA_POINTS_TOO_SPARSE (WARNING):      The custom metric
   * samples are not exported often enough to be      a credible base for
   * autoscaling.    - CUSTOM_METRIC_INVALID (ERROR):      The custom metric
   * that was specified does not exist or does not have      the necessary
   * labels.    - MIN_EQUALS_MAX (WARNING):      The minNumReplicas is equal to
   * maxNumReplicas. This means the      autoscaler cannot add or remove
   * instances from the instance group.    - MISSING_CUSTOM_METRIC_DATA_POINTS
   * (WARNING):      The autoscaler did not receive any data from the custom
   * metric      configured for autoscaling.    -
   * MISSING_LOAD_BALANCING_DATA_POINTS (WARNING):      The autoscaler is
   * configured to scale based on a load balancing signal      but the instance
   * group has not received any requests from the load      balancer.    -
   * MODE_OFF (WARNING):      Autoscaling is turned off. The number of instances
   * in the group won't      change automatically. The autoscaling configuration
   * is preserved.    - MODE_ONLY_UP (WARNING):      Autoscaling is in the
   * "Autoscale only out" mode. The autoscaler can add      instances but not
   * remove any.    - MORE_THAN_ONE_BACKEND_SERVICE (ERROR):      The instance
   * group cannot be autoscaled because it has more than one      backend
   * service attached to it.    - NOT_ENOUGH_QUOTA_AVAILABLE (ERROR):      There
   * is insufficient quota for the necessary resources, such as CPU or
   * number of instances.    - REGION_RESOURCE_STOCKOUT (ERROR):      Shown only
   * for regional autoscalers: there is a resource stockout in      the chosen
   * region.    - SCALING_TARGET_DOES_NOT_EXIST (ERROR):      The target to be
   * scaled does not exist.    -
   * UNSUPPORTED_MAX_RATE_LOAD_BALANCING_CONFIGURATION      (ERROR): Autoscaling
   * does not work with an HTTP/S load balancer that      has been configured
   * for maxRate.    - ZONE_RESOURCE_STOCKOUT (ERROR):      For zonal
   * autoscalers: there is a resource stockout in the chosen zone.      For
   * regional autoscalers: in at least one of the zones you're using      there
   * is a resource stockout.
   *
   * New values might be added in the future. Some of the values might not be
   * available in all API versions.
   *
   * Accepted values: ALL_INSTANCES_UNHEALTHY, BACKEND_SERVICE_DOES_NOT_EXIST,
   * CAPPED_AT_MAX_NUM_REPLICAS, CUSTOM_METRIC_DATA_POINTS_TOO_SPARSE,
   * CUSTOM_METRIC_INVALID, MIN_EQUALS_MAX, MISSING_CUSTOM_METRIC_DATA_POINTS,
   * MISSING_LOAD_BALANCING_DATA_POINTS, MODE_OFF, MODE_ONLY_SCALE_OUT,
   * MODE_ONLY_UP, MORE_THAN_ONE_BACKEND_SERVICE, NOT_ENOUGH_QUOTA_AVAILABLE,
   * REGION_RESOURCE_STOCKOUT, SCALING_TARGET_DOES_NOT_EXIST,
   * SCHEDULED_INSTANCES_GREATER_THAN_AUTOSCALER_MAX,
   * SCHEDULED_INSTANCES_LESS_THAN_AUTOSCALER_MIN, UNKNOWN,
   * UNSUPPORTED_MAX_RATE_LOAD_BALANCING_CONFIGURATION, ZONE_RESOURCE_STOCKOUT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalerStatusDetails::class, 'Google_Service_Compute_AutoscalerStatusDetails');
