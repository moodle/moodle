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

namespace Google\Service\Dataproc;

class Metric extends \Google\Collection
{
  /**
   * Required unspecified metric source.
   */
  public const METRIC_SOURCE_METRIC_SOURCE_UNSPECIFIED = 'METRIC_SOURCE_UNSPECIFIED';
  /**
   * Monitoring agent metrics. If this source is enabled, Dataproc enables the
   * monitoring agent in Compute Engine, and collects monitoring agent metrics,
   * which are published with an agent.googleapis.com prefix.
   */
  public const METRIC_SOURCE_MONITORING_AGENT_DEFAULTS = 'MONITORING_AGENT_DEFAULTS';
  /**
   * HDFS metric source.
   */
  public const METRIC_SOURCE_HDFS = 'HDFS';
  /**
   * Spark metric source.
   */
  public const METRIC_SOURCE_SPARK = 'SPARK';
  /**
   * YARN metric source.
   */
  public const METRIC_SOURCE_YARN = 'YARN';
  /**
   * Spark History Server metric source.
   */
  public const METRIC_SOURCE_SPARK_HISTORY_SERVER = 'SPARK_HISTORY_SERVER';
  /**
   * Hiveserver2 metric source.
   */
  public const METRIC_SOURCE_HIVESERVER2 = 'HIVESERVER2';
  /**
   * hivemetastore metric source
   */
  public const METRIC_SOURCE_HIVEMETASTORE = 'HIVEMETASTORE';
  /**
   * flink metric source
   */
  public const METRIC_SOURCE_FLINK = 'FLINK';
  protected $collection_key = 'metricOverrides';
  /**
   * Optional. Specify one or more Custom metrics
   * (https://cloud.google.com/dataproc/docs/guides/dataproc-
   * metrics#custom_metrics) to collect for the metric course (for the SPARK
   * metric source (any Spark metric
   * (https://spark.apache.org/docs/latest/monitoring.html#metrics) can be
   * specified).Provide metrics in the following format: METRIC_SOURCE:
   * INSTANCE:GROUP:METRIC Use camelcase as appropriate.Examples:
   * yarn:ResourceManager:QueueMetrics:AppsCompleted
   * spark:driver:DAGScheduler:job.allJobs
   * sparkHistoryServer:JVM:Memory:NonHeapMemoryUsage.committed
   * hiveserver2:JVM:Memory:NonHeapMemoryUsage.used Notes: Only the specified
   * overridden metrics are collected for the metric source. For example, if one
   * or more spark:executive metrics are listed as metric overrides, other SPARK
   * metrics are not collected. The collection of the metrics for other enabled
   * custom metric sources is unaffected. For example, if both SPARK and YARN
   * metric sources are enabled, and overrides are provided for Spark metrics
   * only, all YARN metrics are collected.
   *
   * @var string[]
   */
  public $metricOverrides;
  /**
   * Required. A standard set of metrics is collected unless metricOverrides are
   * specified for the metric source (see Custom metrics
   * (https://cloud.google.com/dataproc/docs/guides/dataproc-
   * metrics#custom_metrics) for more information).
   *
   * @var string
   */
  public $metricSource;

  /**
   * Optional. Specify one or more Custom metrics
   * (https://cloud.google.com/dataproc/docs/guides/dataproc-
   * metrics#custom_metrics) to collect for the metric course (for the SPARK
   * metric source (any Spark metric
   * (https://spark.apache.org/docs/latest/monitoring.html#metrics) can be
   * specified).Provide metrics in the following format: METRIC_SOURCE:
   * INSTANCE:GROUP:METRIC Use camelcase as appropriate.Examples:
   * yarn:ResourceManager:QueueMetrics:AppsCompleted
   * spark:driver:DAGScheduler:job.allJobs
   * sparkHistoryServer:JVM:Memory:NonHeapMemoryUsage.committed
   * hiveserver2:JVM:Memory:NonHeapMemoryUsage.used Notes: Only the specified
   * overridden metrics are collected for the metric source. For example, if one
   * or more spark:executive metrics are listed as metric overrides, other SPARK
   * metrics are not collected. The collection of the metrics for other enabled
   * custom metric sources is unaffected. For example, if both SPARK and YARN
   * metric sources are enabled, and overrides are provided for Spark metrics
   * only, all YARN metrics are collected.
   *
   * @param string[] $metricOverrides
   */
  public function setMetricOverrides($metricOverrides)
  {
    $this->metricOverrides = $metricOverrides;
  }
  /**
   * @return string[]
   */
  public function getMetricOverrides()
  {
    return $this->metricOverrides;
  }
  /**
   * Required. A standard set of metrics is collected unless metricOverrides are
   * specified for the metric source (see Custom metrics
   * (https://cloud.google.com/dataproc/docs/guides/dataproc-
   * metrics#custom_metrics) for more information).
   *
   * Accepted values: METRIC_SOURCE_UNSPECIFIED, MONITORING_AGENT_DEFAULTS,
   * HDFS, SPARK, YARN, SPARK_HISTORY_SERVER, HIVESERVER2, HIVEMETASTORE, FLINK
   *
   * @param self::METRIC_SOURCE_* $metricSource
   */
  public function setMetricSource($metricSource)
  {
    $this->metricSource = $metricSource;
  }
  /**
   * @return self::METRIC_SOURCE_*
   */
  public function getMetricSource()
  {
    return $this->metricSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metric::class, 'Google_Service_Dataproc_Metric');
