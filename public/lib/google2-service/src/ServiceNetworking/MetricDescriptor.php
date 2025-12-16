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

namespace Google\Service\ServiceNetworking;

class MetricDescriptor extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  /**
   * Do not use this default value.
   */
  public const METRIC_KIND_METRIC_KIND_UNSPECIFIED = 'METRIC_KIND_UNSPECIFIED';
  /**
   * An instantaneous measurement of a value.
   */
  public const METRIC_KIND_GAUGE = 'GAUGE';
  /**
   * The change in a value during a time interval.
   */
  public const METRIC_KIND_DELTA = 'DELTA';
  /**
   * A value accumulated over a time interval. Cumulative measurements in a time
   * series should have the same start time and increasing end times, until an
   * event resets the cumulative value to zero and sets a new start time for the
   * following points.
   */
  public const METRIC_KIND_CUMULATIVE = 'CUMULATIVE';
  /**
   * Do not use this default value.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * The value is a boolean. This value type can be used only if the metric kind
   * is `GAUGE`.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * The value is a signed 64-bit integer.
   */
  public const VALUE_TYPE_INT64 = 'INT64';
  /**
   * The value is a double precision floating point number.
   */
  public const VALUE_TYPE_DOUBLE = 'DOUBLE';
  /**
   * The value is a text string. This value type can be used only if the metric
   * kind is `GAUGE`.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * The value is a `Distribution`.
   */
  public const VALUE_TYPE_DISTRIBUTION = 'DISTRIBUTION';
  /**
   * The value is money.
   */
  public const VALUE_TYPE_MONEY = 'MONEY';
  protected $collection_key = 'monitoredResourceTypes';
  /**
   * A detailed description of the metric, which can be used in documentation.
   *
   * @var string
   */
  public $description;
  /**
   * A concise name for the metric, which can be displayed in user interfaces.
   * Use sentence case without an ending period, for example "Request count".
   * This field is optional but it is recommended to be set for any metrics
   * associated with user-visible concepts, such as Quota.
   *
   * @var string
   */
  public $displayName;
  protected $labelsType = LabelDescriptor::class;
  protected $labelsDataType = 'array';
  /**
   * Optional. The launch stage of the metric definition.
   *
   * @var string
   */
  public $launchStage;
  protected $metadataType = MetricDescriptorMetadata::class;
  protected $metadataDataType = '';
  /**
   * Whether the metric records instantaneous values, changes to a value, etc.
   * Some combinations of `metric_kind` and `value_type` might not be supported.
   *
   * @var string
   */
  public $metricKind;
  /**
   * Read-only. If present, then a time series, which is identified partially by
   * a metric type and a MonitoredResourceDescriptor, that is associated with
   * this metric type can only be associated with one of the monitored resource
   * types listed here.
   *
   * @var string[]
   */
  public $monitoredResourceTypes;
  /**
   * The resource name of the metric descriptor.
   *
   * @var string
   */
  public $name;
  /**
   * The metric type, including its DNS name prefix. The type is not URL-
   * encoded. All user-defined metric types have the DNS name
   * `custom.googleapis.com` or `external.googleapis.com`. Metric types should
   * use a natural hierarchical grouping. For example:
   * "custom.googleapis.com/invoice/paid/amount"
   * "external.googleapis.com/prometheus/up"
   * "appengine.googleapis.com/http/server/response_latencies"
   *
   * @var string
   */
  public $type;
  /**
   * The units in which the metric value is reported. It is only applicable if
   * the `value_type` is `INT64`, `DOUBLE`, or `DISTRIBUTION`. The `unit`
   * defines the representation of the stored metric values. Different systems
   * might scale the values to be more easily displayed (so a value of `0.02kBy`
   * _might_ be displayed as `20By`, and a value of `3523kBy` _might_ be
   * displayed as `3.5MBy`). However, if the `unit` is `kBy`, then the value of
   * the metric is always in thousands of bytes, no matter how it might be
   * displayed. If you want a custom metric to record the exact number of CPU-
   * seconds used by a job, you can create an `INT64 CUMULATIVE` metric whose
   * `unit` is `s{CPU}` (or equivalently `1s{CPU}` or just `s`). If the job uses
   * 12,005 CPU-seconds, then the value is written as `12005`. Alternatively, if
   * you want a custom metric to record data in a more granular way, you can
   * create a `DOUBLE CUMULATIVE` metric whose `unit` is `ks{CPU}`, and then
   * write the value `12.005` (which is `12005/1000`), or use `Kis{CPU}` and
   * write `11.723` (which is `12005/1024`). The supported units are a subset of
   * [The Unified Code for Units of
   * Measure](https://unitsofmeasure.org/ucum.html) standard: **Basic units
   * (UNIT)** * `bit` bit * `By` byte * `s` second * `min` minute * `h` hour *
   * `d` day * `1` dimensionless **Prefixes (PREFIX)** * `k` kilo (10^3) * `M`
   * mega (10^6) * `G` giga (10^9) * `T` tera (10^12) * `P` peta (10^15) * `E`
   * exa (10^18) * `Z` zetta (10^21) * `Y` yotta (10^24) * `m` milli (10^-3) *
   * `u` micro (10^-6) * `n` nano (10^-9) * `p` pico (10^-12) * `f` femto
   * (10^-15) * `a` atto (10^-18) * `z` zepto (10^-21) * `y` yocto (10^-24) *
   * `Ki` kibi (2^10) * `Mi` mebi (2^20) * `Gi` gibi (2^30) * `Ti` tebi (2^40) *
   * `Pi` pebi (2^50) **Grammar** The grammar also includes these connectors: *
   * `/` division or ratio (as an infix operator). For examples, `kBy/{email}`
   * or `MiBy/10ms` (although you should almost never have `/s` in a metric
   * `unit`; rates should always be computed at query time from the underlying
   * cumulative or delta value). * `.` multiplication or composition (as an
   * infix operator). For examples, `GBy.d` or `k{watt}.h`. The grammar for a
   * unit is as follows: Expression = Component { "." Component } { "/"
   * Component } ; Component = ( [ PREFIX ] UNIT | "%" ) [ Annotation ] |
   * Annotation | "1" ; Annotation = "{" NAME "}" ; Notes: * `Annotation` is
   * just a comment if it follows a `UNIT`. If the annotation is used alone,
   * then the unit is equivalent to `1`. For examples, `{request}/s == 1/s`,
   * `By{transmitted}/s == By/s`. * `NAME` is a sequence of non-blank printable
   * ASCII characters not containing `{` or `}`. * `1` represents a unitary
   * [dimensionless unit](https://en.wikipedia.org/wiki/Dimensionless_quantity)
   * of 1, such as in `1/s`. It is typically used when none of the basic units
   * are appropriate. For example, "new users per day" can be represented as
   * `1/d` or `{new-users}/d` (and a metric value `5` would mean "5 new users).
   * Alternatively, "thousands of page views per day" would be represented as
   * `1000/d` or `k1/d` or `k{page_views}/d` (and a metric value of `5.3` would
   * mean "5300 page views per day"). * `%` represents dimensionless value of
   * 1/100, and annotates values giving a percentage (so the metric values are
   * typically in the range of 0..100, and a metric value `3` means "3
   * percent"). * `10^2.%` indicates a metric contains a ratio, typically in the
   * range 0..1, that will be multiplied by 100 and displayed as a percentage
   * (so a metric value `0.03` means "3 percent").
   *
   * @var string
   */
  public $unit;
  /**
   * Whether the measurement is an integer, a floating-point number, etc. Some
   * combinations of `metric_kind` and `value_type` might not be supported.
   *
   * @var string
   */
  public $valueType;

  /**
   * A detailed description of the metric, which can be used in documentation.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A concise name for the metric, which can be displayed in user interfaces.
   * Use sentence case without an ending period, for example "Request count".
   * This field is optional but it is recommended to be set for any metrics
   * associated with user-visible concepts, such as Quota.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The set of labels that can be used to describe a specific instance of this
   * metric type. For example, the
   * `appengine.googleapis.com/http/server/response_latencies` metric type has a
   * label for the HTTP response code, `response_code`, so you can look at
   * latencies for successful responses or just for responses that failed.
   *
   * @param LabelDescriptor[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return LabelDescriptor[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The launch stage of the metric definition.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * Optional. Metadata which can be used to guide usage of the metric.
   *
   * @param MetricDescriptorMetadata $metadata
   */
  public function setMetadata(MetricDescriptorMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return MetricDescriptorMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Whether the metric records instantaneous values, changes to a value, etc.
   * Some combinations of `metric_kind` and `value_type` might not be supported.
   *
   * Accepted values: METRIC_KIND_UNSPECIFIED, GAUGE, DELTA, CUMULATIVE
   *
   * @param self::METRIC_KIND_* $metricKind
   */
  public function setMetricKind($metricKind)
  {
    $this->metricKind = $metricKind;
  }
  /**
   * @return self::METRIC_KIND_*
   */
  public function getMetricKind()
  {
    return $this->metricKind;
  }
  /**
   * Read-only. If present, then a time series, which is identified partially by
   * a metric type and a MonitoredResourceDescriptor, that is associated with
   * this metric type can only be associated with one of the monitored resource
   * types listed here.
   *
   * @param string[] $monitoredResourceTypes
   */
  public function setMonitoredResourceTypes($monitoredResourceTypes)
  {
    $this->monitoredResourceTypes = $monitoredResourceTypes;
  }
  /**
   * @return string[]
   */
  public function getMonitoredResourceTypes()
  {
    return $this->monitoredResourceTypes;
  }
  /**
   * The resource name of the metric descriptor.
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
  /**
   * The metric type, including its DNS name prefix. The type is not URL-
   * encoded. All user-defined metric types have the DNS name
   * `custom.googleapis.com` or `external.googleapis.com`. Metric types should
   * use a natural hierarchical grouping. For example:
   * "custom.googleapis.com/invoice/paid/amount"
   * "external.googleapis.com/prometheus/up"
   * "appengine.googleapis.com/http/server/response_latencies"
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The units in which the metric value is reported. It is only applicable if
   * the `value_type` is `INT64`, `DOUBLE`, or `DISTRIBUTION`. The `unit`
   * defines the representation of the stored metric values. Different systems
   * might scale the values to be more easily displayed (so a value of `0.02kBy`
   * _might_ be displayed as `20By`, and a value of `3523kBy` _might_ be
   * displayed as `3.5MBy`). However, if the `unit` is `kBy`, then the value of
   * the metric is always in thousands of bytes, no matter how it might be
   * displayed. If you want a custom metric to record the exact number of CPU-
   * seconds used by a job, you can create an `INT64 CUMULATIVE` metric whose
   * `unit` is `s{CPU}` (or equivalently `1s{CPU}` or just `s`). If the job uses
   * 12,005 CPU-seconds, then the value is written as `12005`. Alternatively, if
   * you want a custom metric to record data in a more granular way, you can
   * create a `DOUBLE CUMULATIVE` metric whose `unit` is `ks{CPU}`, and then
   * write the value `12.005` (which is `12005/1000`), or use `Kis{CPU}` and
   * write `11.723` (which is `12005/1024`). The supported units are a subset of
   * [The Unified Code for Units of
   * Measure](https://unitsofmeasure.org/ucum.html) standard: **Basic units
   * (UNIT)** * `bit` bit * `By` byte * `s` second * `min` minute * `h` hour *
   * `d` day * `1` dimensionless **Prefixes (PREFIX)** * `k` kilo (10^3) * `M`
   * mega (10^6) * `G` giga (10^9) * `T` tera (10^12) * `P` peta (10^15) * `E`
   * exa (10^18) * `Z` zetta (10^21) * `Y` yotta (10^24) * `m` milli (10^-3) *
   * `u` micro (10^-6) * `n` nano (10^-9) * `p` pico (10^-12) * `f` femto
   * (10^-15) * `a` atto (10^-18) * `z` zepto (10^-21) * `y` yocto (10^-24) *
   * `Ki` kibi (2^10) * `Mi` mebi (2^20) * `Gi` gibi (2^30) * `Ti` tebi (2^40) *
   * `Pi` pebi (2^50) **Grammar** The grammar also includes these connectors: *
   * `/` division or ratio (as an infix operator). For examples, `kBy/{email}`
   * or `MiBy/10ms` (although you should almost never have `/s` in a metric
   * `unit`; rates should always be computed at query time from the underlying
   * cumulative or delta value). * `.` multiplication or composition (as an
   * infix operator). For examples, `GBy.d` or `k{watt}.h`. The grammar for a
   * unit is as follows: Expression = Component { "." Component } { "/"
   * Component } ; Component = ( [ PREFIX ] UNIT | "%" ) [ Annotation ] |
   * Annotation | "1" ; Annotation = "{" NAME "}" ; Notes: * `Annotation` is
   * just a comment if it follows a `UNIT`. If the annotation is used alone,
   * then the unit is equivalent to `1`. For examples, `{request}/s == 1/s`,
   * `By{transmitted}/s == By/s`. * `NAME` is a sequence of non-blank printable
   * ASCII characters not containing `{` or `}`. * `1` represents a unitary
   * [dimensionless unit](https://en.wikipedia.org/wiki/Dimensionless_quantity)
   * of 1, such as in `1/s`. It is typically used when none of the basic units
   * are appropriate. For example, "new users per day" can be represented as
   * `1/d` or `{new-users}/d` (and a metric value `5` would mean "5 new users).
   * Alternatively, "thousands of page views per day" would be represented as
   * `1000/d` or `k1/d` or `k{page_views}/d` (and a metric value of `5.3` would
   * mean "5300 page views per day"). * `%` represents dimensionless value of
   * 1/100, and annotates values giving a percentage (so the metric values are
   * typically in the range of 0..100, and a metric value `3` means "3
   * percent"). * `10^2.%` indicates a metric contains a ratio, typically in the
   * range 0..1, that will be multiplied by 100 and displayed as a percentage
   * (so a metric value `0.03` means "3 percent").
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * Whether the measurement is an integer, a floating-point number, etc. Some
   * combinations of `metric_kind` and `value_type` might not be supported.
   *
   * Accepted values: VALUE_TYPE_UNSPECIFIED, BOOL, INT64, DOUBLE, STRING,
   * DISTRIBUTION, MONEY
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricDescriptor::class, 'Google_Service_ServiceNetworking_MetricDescriptor');
