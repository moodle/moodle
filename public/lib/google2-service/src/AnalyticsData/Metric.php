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

namespace Google\Service\AnalyticsData;

class Metric extends \Google\Model
{
  /**
   * A mathematical expression for derived metrics. For example, the metric
   * Event count per user is `eventCount/totalUsers`.
   *
   * @var string
   */
  public $expression;
  /**
   * Indicates if a metric is invisible in the report response. If a metric is
   * invisible, the metric will not produce a column in the response, but can be
   * used in `metricFilter`, `orderBys`, or a metric `expression`.
   *
   * @var bool
   */
  public $invisible;
  /**
   * The name of the metric. See the [API Metrics](https://developers.google.com
   * /analytics/devguides/reporting/data/v1/api-schema#metrics) for the list of
   * metric names supported by core reporting methods such as `runReport` and
   * `batchRunReports`. See [Realtime Metrics](https://developers.google.com/ana
   * lytics/devguides/reporting/data/v1/realtime-api-schema#metrics) for the
   * list of metric names supported by the `runRealtimeReport` method. See
   * [Funnel Metrics](https://developers.google.com/analytics/devguides/reportin
   * g/data/v1/exploration-api-schema#metrics) for the list of metric names
   * supported by the `runFunnelReport` method. If `expression` is specified,
   * `name` can be any string that you would like within the allowed character
   * set. For example if `expression` is `screenPageViews/sessions`, you could
   * call that metric's name = `viewsPerSession`. Metric names that you choose
   * must match the regular expression `^[a-zA-Z0-9_]$`. Metrics are referenced
   * by `name` in `metricFilter`, `orderBys`, and metric `expression`.
   *
   * @var string
   */
  public $name;

  /**
   * A mathematical expression for derived metrics. For example, the metric
   * Event count per user is `eventCount/totalUsers`.
   *
   * @param string $expression
   */
  public function setExpression($expression)
  {
    $this->expression = $expression;
  }
  /**
   * @return string
   */
  public function getExpression()
  {
    return $this->expression;
  }
  /**
   * Indicates if a metric is invisible in the report response. If a metric is
   * invisible, the metric will not produce a column in the response, but can be
   * used in `metricFilter`, `orderBys`, or a metric `expression`.
   *
   * @param bool $invisible
   */
  public function setInvisible($invisible)
  {
    $this->invisible = $invisible;
  }
  /**
   * @return bool
   */
  public function getInvisible()
  {
    return $this->invisible;
  }
  /**
   * The name of the metric. See the [API Metrics](https://developers.google.com
   * /analytics/devguides/reporting/data/v1/api-schema#metrics) for the list of
   * metric names supported by core reporting methods such as `runReport` and
   * `batchRunReports`. See [Realtime Metrics](https://developers.google.com/ana
   * lytics/devguides/reporting/data/v1/realtime-api-schema#metrics) for the
   * list of metric names supported by the `runRealtimeReport` method. See
   * [Funnel Metrics](https://developers.google.com/analytics/devguides/reportin
   * g/data/v1/exploration-api-schema#metrics) for the list of metric names
   * supported by the `runFunnelReport` method. If `expression` is specified,
   * `name` can be any string that you would like within the allowed character
   * set. For example if `expression` is `screenPageViews/sessions`, you could
   * call that metric's name = `viewsPerSession`. Metric names that you choose
   * must match the regular expression `^[a-zA-Z0-9_]$`. Metrics are referenced
   * by `name` in `metricFilter`, `orderBys`, and metric `expression`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metric::class, 'Google_Service_AnalyticsData_Metric');
