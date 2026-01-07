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

class SubnetworkLogConfig extends \Google\Collection
{
  public const AGGREGATION_INTERVAL_INTERVAL_10_MIN = 'INTERVAL_10_MIN';
  public const AGGREGATION_INTERVAL_INTERVAL_15_MIN = 'INTERVAL_15_MIN';
  public const AGGREGATION_INTERVAL_INTERVAL_1_MIN = 'INTERVAL_1_MIN';
  public const AGGREGATION_INTERVAL_INTERVAL_30_SEC = 'INTERVAL_30_SEC';
  public const AGGREGATION_INTERVAL_INTERVAL_5_MIN = 'INTERVAL_5_MIN';
  public const AGGREGATION_INTERVAL_INTERVAL_5_SEC = 'INTERVAL_5_SEC';
  public const METADATA_CUSTOM_METADATA = 'CUSTOM_METADATA';
  public const METADATA_EXCLUDE_ALL_METADATA = 'EXCLUDE_ALL_METADATA';
  public const METADATA_INCLUDE_ALL_METADATA = 'INCLUDE_ALL_METADATA';
  protected $collection_key = 'metadataFields';
  /**
   * Can only be specified if VPC flow logging for this subnetwork is enabled.
   * Toggles the aggregation interval for collecting flow logs. Increasing the
   * interval time will reduce the amount of generated flow logs for long
   * lasting connections. Default is an interval of 5 seconds per connection.
   *
   * @var string
   */
  public $aggregationInterval;
  /**
   * Whether to enable flow logging for this subnetwork. If this field is not
   * explicitly set, it will not appear in get listings. If not set the default
   * behavior is determined by the org policy, if there is no org policy
   * specified, then it will default to disabled. Flow logging isn't supported
   * if the subnet purpose field is set to REGIONAL_MANAGED_PROXY.
   *
   * @var bool
   */
  public $enable;
  /**
   * Can only be specified if VPC flow logs for this subnetwork is enabled. The
   * filter expression is used to define which VPC flow logs should be exported
   * to Cloud Logging.
   *
   * @var string
   */
  public $filterExpr;
  /**
   * Can only be specified if VPC flow logging for this subnetwork is enabled.
   * The value of the field must be in [0, 1]. Set the sampling rate of VPC flow
   * logs within the subnetwork where 1.0 means all collected logs are reported
   * and 0.0 means no logs are reported. Default is 0.5 unless otherwise
   * specified by the org policy, which means half of all collected logs are
   * reported.
   *
   * @var float
   */
  public $flowSampling;
  /**
   * Can only be specified if VPC flow logs for this subnetwork is enabled.
   * Configures whether all, none or a subset of metadata fields should be added
   * to the reported VPC flow logs. Default isEXCLUDE_ALL_METADATA.
   *
   * @var string
   */
  public $metadata;
  /**
   * Can only be specified if VPC flow logs for this subnetwork is enabled and
   * "metadata" was set to CUSTOM_METADATA.
   *
   * @var string[]
   */
  public $metadataFields;

  /**
   * Can only be specified if VPC flow logging for this subnetwork is enabled.
   * Toggles the aggregation interval for collecting flow logs. Increasing the
   * interval time will reduce the amount of generated flow logs for long
   * lasting connections. Default is an interval of 5 seconds per connection.
   *
   * Accepted values: INTERVAL_10_MIN, INTERVAL_15_MIN, INTERVAL_1_MIN,
   * INTERVAL_30_SEC, INTERVAL_5_MIN, INTERVAL_5_SEC
   *
   * @param self::AGGREGATION_INTERVAL_* $aggregationInterval
   */
  public function setAggregationInterval($aggregationInterval)
  {
    $this->aggregationInterval = $aggregationInterval;
  }
  /**
   * @return self::AGGREGATION_INTERVAL_*
   */
  public function getAggregationInterval()
  {
    return $this->aggregationInterval;
  }
  /**
   * Whether to enable flow logging for this subnetwork. If this field is not
   * explicitly set, it will not appear in get listings. If not set the default
   * behavior is determined by the org policy, if there is no org policy
   * specified, then it will default to disabled. Flow logging isn't supported
   * if the subnet purpose field is set to REGIONAL_MANAGED_PROXY.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Can only be specified if VPC flow logs for this subnetwork is enabled. The
   * filter expression is used to define which VPC flow logs should be exported
   * to Cloud Logging.
   *
   * @param string $filterExpr
   */
  public function setFilterExpr($filterExpr)
  {
    $this->filterExpr = $filterExpr;
  }
  /**
   * @return string
   */
  public function getFilterExpr()
  {
    return $this->filterExpr;
  }
  /**
   * Can only be specified if VPC flow logging for this subnetwork is enabled.
   * The value of the field must be in [0, 1]. Set the sampling rate of VPC flow
   * logs within the subnetwork where 1.0 means all collected logs are reported
   * and 0.0 means no logs are reported. Default is 0.5 unless otherwise
   * specified by the org policy, which means half of all collected logs are
   * reported.
   *
   * @param float $flowSampling
   */
  public function setFlowSampling($flowSampling)
  {
    $this->flowSampling = $flowSampling;
  }
  /**
   * @return float
   */
  public function getFlowSampling()
  {
    return $this->flowSampling;
  }
  /**
   * Can only be specified if VPC flow logs for this subnetwork is enabled.
   * Configures whether all, none or a subset of metadata fields should be added
   * to the reported VPC flow logs. Default isEXCLUDE_ALL_METADATA.
   *
   * Accepted values: CUSTOM_METADATA, EXCLUDE_ALL_METADATA,
   * INCLUDE_ALL_METADATA
   *
   * @param self::METADATA_* $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return self::METADATA_*
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Can only be specified if VPC flow logs for this subnetwork is enabled and
   * "metadata" was set to CUSTOM_METADATA.
   *
   * @param string[] $metadataFields
   */
  public function setMetadataFields($metadataFields)
  {
    $this->metadataFields = $metadataFields;
  }
  /**
   * @return string[]
   */
  public function getMetadataFields()
  {
    return $this->metadataFields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworkLogConfig::class, 'Google_Service_Compute_SubnetworkLogConfig');
