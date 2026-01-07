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

namespace Google\Service\Logging;

class LogMetric extends \Google\Model
{
  /**
   * Logging API v2.
   */
  public const VERSION_V2 = 'V2';
  /**
   * Logging API v1.
   */
  public const VERSION_V1 = 'V1';
  /**
   * Optional. The resource name of the Log Bucket that owns the Log Metric.
   * Only Log Buckets in projects are supported. The bucket has to be in the
   * same project as the metric.For example:projects/my-
   * project/locations/global/buckets/my-bucketIf empty, then the Log Metric is
   * considered a non-Bucket Log Metric.
   *
   * @var string
   */
  public $bucketName;
  protected $bucketOptionsType = BucketOptions::class;
  protected $bucketOptionsDataType = '';
  /**
   * Output only. The creation timestamp of the metric.This field may not be
   * present for older metrics.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of this metric, which is used in documentation. The
   * maximum length of the description is 8000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. If set to True, then this metric is disabled and it does not
   * generate any points.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Required. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced_filters) which is used
   * to match log entries. Example: "resource.type=gae_app AND severity>=ERROR"
   * The maximum length of the filter is 20000 characters.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. A map from a label key string to an extractor expression which is
   * used to extract data from a log entry field and assign as the label value.
   * Each label key specified in the LabelDescriptor must have an associated
   * extractor expression in this map. The syntax of the extractor expression is
   * the same as for the value_extractor field.The extracted value is converted
   * to the type defined in the label descriptor. If either the extraction or
   * the type conversion fails, the label will have a default value. The default
   * value for a string label is an empty string, for an integer label its 0,
   * and for a boolean label its false.Note that there are upper bounds on the
   * maximum number of labels and the number of active time series that are
   * allowed in a project.
   *
   * @var string[]
   */
  public $labelExtractors;
  protected $metricDescriptorType = MetricDescriptor::class;
  protected $metricDescriptorDataType = '';
  /**
   * Required. The client-assigned metric identifier. Examples: "error_count",
   * "nginx/requests".Metric identifiers are limited to 100 characters and can
   * include only the following characters: A-Z, a-z, 0-9, and the special
   * characters _-.,+!*',()%/. The forward-slash character (/) denotes a
   * hierarchy of name pieces, and it cannot be the first character of the
   * name.This field is the [METRIC_ID] part of a metric resource name in the
   * format "projects/PROJECT_ID/metrics/METRIC_ID". Example: If the resource
   * name of a metric is "projects/my-project/metrics/nginx%2Frequests", this
   * field's value is "nginx/requests".
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the metric:
   * "projects/[PROJECT_ID]/metrics/[METRIC_ID]"
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The last update timestamp of the metric.This field may not be
   * present for older metrics.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. A value_extractor is required when using a distribution logs-
   * based metric to extract the values to record from a log entry. Two
   * functions are supported for value extraction: EXTRACT(field) or
   * REGEXP_EXTRACT(field, regex). The arguments are: field: The name of the log
   * entry field from which the value is to be extracted. regex: A regular
   * expression using the Google RE2 syntax
   * (https://github.com/google/re2/wiki/Syntax) with a single capture group to
   * extract data from the specified log entry field. The value of the field is
   * converted to a string before applying the regex. It is an error to specify
   * a regex that does not include exactly one capture group.The result of the
   * extraction must be convertible to a double type, as the distribution always
   * records double values. If either the extraction or the conversion to double
   * fails, then those values are not recorded in the distribution.Example:
   * REGEXP_EXTRACT(jsonPayload.request, ".*quantity=(\d+).*")
   *
   * @var string
   */
  public $valueExtractor;
  /**
   * Deprecated. The API version that created or updated this metric. The v2
   * format is used by default and cannot be changed.
   *
   * @deprecated
   * @var string
   */
  public $version;

  /**
   * Optional. The resource name of the Log Bucket that owns the Log Metric.
   * Only Log Buckets in projects are supported. The bucket has to be in the
   * same project as the metric.For example:projects/my-
   * project/locations/global/buckets/my-bucketIf empty, then the Log Metric is
   * considered a non-Bucket Log Metric.
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * Optional. The bucket_options are required when the logs-based metric is
   * using a DISTRIBUTION value type and it describes the bucket boundaries used
   * to create a histogram of the extracted values.
   *
   * @param BucketOptions $bucketOptions
   */
  public function setBucketOptions(BucketOptions $bucketOptions)
  {
    $this->bucketOptions = $bucketOptions;
  }
  /**
   * @return BucketOptions
   */
  public function getBucketOptions()
  {
    return $this->bucketOptions;
  }
  /**
   * Output only. The creation timestamp of the metric.This field may not be
   * present for older metrics.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A description of this metric, which is used in documentation. The
   * maximum length of the description is 8000 characters.
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
   * Optional. If set to True, then this metric is disabled and it does not
   * generate any points.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced_filters) which is used
   * to match log entries. Example: "resource.type=gae_app AND severity>=ERROR"
   * The maximum length of the filter is 20000 characters.
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
   * Optional. A map from a label key string to an extractor expression which is
   * used to extract data from a log entry field and assign as the label value.
   * Each label key specified in the LabelDescriptor must have an associated
   * extractor expression in this map. The syntax of the extractor expression is
   * the same as for the value_extractor field.The extracted value is converted
   * to the type defined in the label descriptor. If either the extraction or
   * the type conversion fails, the label will have a default value. The default
   * value for a string label is an empty string, for an integer label its 0,
   * and for a boolean label its false.Note that there are upper bounds on the
   * maximum number of labels and the number of active time series that are
   * allowed in a project.
   *
   * @param string[] $labelExtractors
   */
  public function setLabelExtractors($labelExtractors)
  {
    $this->labelExtractors = $labelExtractors;
  }
  /**
   * @return string[]
   */
  public function getLabelExtractors()
  {
    return $this->labelExtractors;
  }
  /**
   * Optional. The metric descriptor associated with the logs-based metric. If
   * unspecified, it uses a default metric descriptor with a DELTA metric kind,
   * INT64 value type, with no labels and a unit of "1". Such a metric counts
   * the number of log entries matching the filter expression.The name, type,
   * and description fields in the metric_descriptor are output only, and is
   * constructed using the name and description field in the LogMetric.To create
   * a logs-based metric that records a distribution of log values, a DELTA
   * metric kind with a DISTRIBUTION value type must be used along with a
   * value_extractor expression in the LogMetric.Each label in the metric
   * descriptor must have a matching label name as the key and an extractor
   * expression as the value in the label_extractors map.The metric_kind and
   * value_type fields in the metric_descriptor cannot be updated once initially
   * configured. New labels can be added in the metric_descriptor, but existing
   * labels cannot be modified except for their description.
   *
   * @param MetricDescriptor $metricDescriptor
   */
  public function setMetricDescriptor(MetricDescriptor $metricDescriptor)
  {
    $this->metricDescriptor = $metricDescriptor;
  }
  /**
   * @return MetricDescriptor
   */
  public function getMetricDescriptor()
  {
    return $this->metricDescriptor;
  }
  /**
   * Required. The client-assigned metric identifier. Examples: "error_count",
   * "nginx/requests".Metric identifiers are limited to 100 characters and can
   * include only the following characters: A-Z, a-z, 0-9, and the special
   * characters _-.,+!*',()%/. The forward-slash character (/) denotes a
   * hierarchy of name pieces, and it cannot be the first character of the
   * name.This field is the [METRIC_ID] part of a metric resource name in the
   * format "projects/PROJECT_ID/metrics/METRIC_ID". Example: If the resource
   * name of a metric is "projects/my-project/metrics/nginx%2Frequests", this
   * field's value is "nginx/requests".
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
   * Output only. The resource name of the metric:
   * "projects/[PROJECT_ID]/metrics/[METRIC_ID]"
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. The last update timestamp of the metric.This field may not be
   * present for older metrics.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. A value_extractor is required when using a distribution logs-
   * based metric to extract the values to record from a log entry. Two
   * functions are supported for value extraction: EXTRACT(field) or
   * REGEXP_EXTRACT(field, regex). The arguments are: field: The name of the log
   * entry field from which the value is to be extracted. regex: A regular
   * expression using the Google RE2 syntax
   * (https://github.com/google/re2/wiki/Syntax) with a single capture group to
   * extract data from the specified log entry field. The value of the field is
   * converted to a string before applying the regex. It is an error to specify
   * a regex that does not include exactly one capture group.The result of the
   * extraction must be convertible to a double type, as the distribution always
   * records double values. If either the extraction or the conversion to double
   * fails, then those values are not recorded in the distribution.Example:
   * REGEXP_EXTRACT(jsonPayload.request, ".*quantity=(\d+).*")
   *
   * @param string $valueExtractor
   */
  public function setValueExtractor($valueExtractor)
  {
    $this->valueExtractor = $valueExtractor;
  }
  /**
   * @return string
   */
  public function getValueExtractor()
  {
    return $this->valueExtractor;
  }
  /**
   * Deprecated. The API version that created or updated this metric. The v2
   * format is used by default and cannot be changed.
   *
   * Accepted values: V2, V1
   *
   * @deprecated
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @deprecated
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogMetric::class, 'Google_Service_Logging_LogMetric');
