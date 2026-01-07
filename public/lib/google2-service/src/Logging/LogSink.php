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

class LogSink extends \Google\Collection
{
  /**
   * An unspecified format version that will default to V2.
   */
  public const OUTPUT_VERSION_FORMAT_VERSION_FORMAT_UNSPECIFIED = 'VERSION_FORMAT_UNSPECIFIED';
  /**
   * LogEntry version 2 format.
   */
  public const OUTPUT_VERSION_FORMAT_V2 = 'V2';
  /**
   * LogEntry version 1 format.
   */
  public const OUTPUT_VERSION_FORMAT_V1 = 'V1';
  protected $collection_key = 'exclusions';
  protected $bigqueryOptionsType = BigQueryOptions::class;
  protected $bigqueryOptionsDataType = '';
  /**
   * Output only. The creation timestamp of the sink.This field may not be
   * present for older sinks.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of this sink.The maximum length of the description
   * is 8000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The export destination: "storage.googleapis.com/[GCS_BUCKET]"
   * "bigquery.googleapis.com/projects/[PROJECT_ID]/datasets/[DATASET]"
   * "pubsub.googleapis.com/projects/[PROJECT_ID]/topics/[TOPIC_ID]"
   * "logging.googleapis.com/projects/[PROJECT_ID]" "logging.googleapis.com/proj
   * ects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" The sink's
   * writer_identity, set when the sink is created, must have permission to
   * write to the destination or else the log entries are not exported. For more
   * information, see Exporting Logs with Sinks
   * (https://cloud.google.com/logging/docs/api/tasks/exporting-logs).
   *
   * @var string
   */
  public $destination;
  /**
   * Optional. If set to true, then this sink is disabled and it does not export
   * any log entries.
   *
   * @var bool
   */
  public $disabled;
  protected $exclusionsType = LogExclusion::class;
  protected $exclusionsDataType = 'array';
  /**
   * Optional. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced-queries). The only
   * exported log entries are those that are in the resource owning the sink and
   * that match the filter.For
   * example:logName="projects/[PROJECT_ID]/logs/[LOG_ID]" AND severity>=ERROR
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. This field applies only to sinks owned by organizations and
   * folders. If the field is false, the default, only the logs owned by the
   * sink's parent resource are available for export. If the field is true, then
   * log entries from all the projects, folders, and billing accounts contained
   * in the sink's parent resource are also available for export. Whether a
   * particular log entry from the children is exported depends on the sink's
   * filter expression.For example, if this field is true, then the filter
   * resource.type=gce_instance would export all Compute Engine VM instance log
   * entries from all projects in the sink's parent.To only export entries from
   * certain child projects, filter on the project part of the log
   * name:logName:("projects/test-project1/" OR "projects/test-project2/") AND
   * resource.type=gce_instance
   *
   * @var bool
   */
  public $includeChildren;
  /**
   * Optional. This field applies only to sinks owned by organizations and
   * folders.When the value of 'intercept_children' is true, the following
   * restrictions apply: The sink must have the include_children flag set to
   * true. The sink destination must be a Cloud project.Also, the following
   * behaviors apply: Any logs matched by the sink won't be included by non-
   * _Required sinks owned by child resources. The sink appears in the results
   * of a ListSinks call from a child resource if the value of the filter field
   * in its request is either 'in_scope("ALL")' or 'in_scope("ANCESTOR")'.
   *
   * @var bool
   */
  public $interceptChildren;
  /**
   * Optional. The client-assigned sink identifier, unique within the
   * project.For example: "my-syslog-errors-to-pubsub".Sink identifiers are
   * limited to 100 characters and can include only the following characters:
   * upper and lower-case alphanumeric characters, underscores, hyphens,
   * periods.First character has to be alphanumeric.
   *
   * @var string
   */
  public $name;
  /**
   * Deprecated. This field is unused.
   *
   * @deprecated
   * @var string
   */
  public $outputVersionFormat;
  /**
   * Output only. The resource name of the sink.
   * "projects/[PROJECT_ID]/sinks/[SINK_NAME]
   * "organizations/[ORGANIZATION_ID]/sinks/[SINK_NAME]
   * "billingAccounts/[BILLING_ACCOUNT_ID]/sinks/[SINK_NAME]
   * "folders/[FOLDER_ID]/sinks/[SINK_NAME] For example:
   * projects/my_project/sinks/SINK_NAME
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The last update timestamp of the sink.This field may not be
   * present for older sinks.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. An IAM identity—a service account or group—under which Cloud
   * Logging writes the exported log entries to the sink's destination. This
   * field is either set by specifying custom_writer_identity or set
   * automatically by sinks.create and sinks.update based on the value of
   * unique_writer_identity in those methods.Until you grant this identity
   * write-access to the destination, log entry exports from this sink will
   * fail. For more information, see Granting Access for a Resource
   * (https://cloud.google.com/iam/docs/granting-roles-to-service-
   * accounts#granting_access_to_a_service_account_for_a_resource). Consult the
   * destination service's documentation to determine the appropriate IAM roles
   * to assign to the identity.Sinks that have a destination that is a log
   * bucket in the same project as the sink cannot have a writer_identity and no
   * additional permissions are required.
   *
   * @var string
   */
  public $writerIdentity;

  /**
   * Optional. Options that affect sinks exporting data to BigQuery.
   *
   * @param BigQueryOptions $bigqueryOptions
   */
  public function setBigqueryOptions(BigQueryOptions $bigqueryOptions)
  {
    $this->bigqueryOptions = $bigqueryOptions;
  }
  /**
   * @return BigQueryOptions
   */
  public function getBigqueryOptions()
  {
    return $this->bigqueryOptions;
  }
  /**
   * Output only. The creation timestamp of the sink.This field may not be
   * present for older sinks.
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
   * Optional. A description of this sink.The maximum length of the description
   * is 8000 characters.
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
   * Required. The export destination: "storage.googleapis.com/[GCS_BUCKET]"
   * "bigquery.googleapis.com/projects/[PROJECT_ID]/datasets/[DATASET]"
   * "pubsub.googleapis.com/projects/[PROJECT_ID]/topics/[TOPIC_ID]"
   * "logging.googleapis.com/projects/[PROJECT_ID]" "logging.googleapis.com/proj
   * ects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" The sink's
   * writer_identity, set when the sink is created, must have permission to
   * write to the destination or else the log entries are not exported. For more
   * information, see Exporting Logs with Sinks
   * (https://cloud.google.com/logging/docs/api/tasks/exporting-logs).
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Optional. If set to true, then this sink is disabled and it does not export
   * any log entries.
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
   * Optional. Log entries that match any of these exclusion filters will not be
   * exported.If a log entry is matched by both filter and one of exclusions it
   * will not be exported.
   *
   * @param LogExclusion[] $exclusions
   */
  public function setExclusions($exclusions)
  {
    $this->exclusions = $exclusions;
  }
  /**
   * @return LogExclusion[]
   */
  public function getExclusions()
  {
    return $this->exclusions;
  }
  /**
   * Optional. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced-queries). The only
   * exported log entries are those that are in the resource owning the sink and
   * that match the filter.For
   * example:logName="projects/[PROJECT_ID]/logs/[LOG_ID]" AND severity>=ERROR
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
   * Optional. This field applies only to sinks owned by organizations and
   * folders. If the field is false, the default, only the logs owned by the
   * sink's parent resource are available for export. If the field is true, then
   * log entries from all the projects, folders, and billing accounts contained
   * in the sink's parent resource are also available for export. Whether a
   * particular log entry from the children is exported depends on the sink's
   * filter expression.For example, if this field is true, then the filter
   * resource.type=gce_instance would export all Compute Engine VM instance log
   * entries from all projects in the sink's parent.To only export entries from
   * certain child projects, filter on the project part of the log
   * name:logName:("projects/test-project1/" OR "projects/test-project2/") AND
   * resource.type=gce_instance
   *
   * @param bool $includeChildren
   */
  public function setIncludeChildren($includeChildren)
  {
    $this->includeChildren = $includeChildren;
  }
  /**
   * @return bool
   */
  public function getIncludeChildren()
  {
    return $this->includeChildren;
  }
  /**
   * Optional. This field applies only to sinks owned by organizations and
   * folders.When the value of 'intercept_children' is true, the following
   * restrictions apply: The sink must have the include_children flag set to
   * true. The sink destination must be a Cloud project.Also, the following
   * behaviors apply: Any logs matched by the sink won't be included by non-
   * _Required sinks owned by child resources. The sink appears in the results
   * of a ListSinks call from a child resource if the value of the filter field
   * in its request is either 'in_scope("ALL")' or 'in_scope("ANCESTOR")'.
   *
   * @param bool $interceptChildren
   */
  public function setInterceptChildren($interceptChildren)
  {
    $this->interceptChildren = $interceptChildren;
  }
  /**
   * @return bool
   */
  public function getInterceptChildren()
  {
    return $this->interceptChildren;
  }
  /**
   * Optional. The client-assigned sink identifier, unique within the
   * project.For example: "my-syslog-errors-to-pubsub".Sink identifiers are
   * limited to 100 characters and can include only the following characters:
   * upper and lower-case alphanumeric characters, underscores, hyphens,
   * periods.First character has to be alphanumeric.
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
   * Deprecated. This field is unused.
   *
   * Accepted values: VERSION_FORMAT_UNSPECIFIED, V2, V1
   *
   * @deprecated
   * @param self::OUTPUT_VERSION_FORMAT_* $outputVersionFormat
   */
  public function setOutputVersionFormat($outputVersionFormat)
  {
    $this->outputVersionFormat = $outputVersionFormat;
  }
  /**
   * @deprecated
   * @return self::OUTPUT_VERSION_FORMAT_*
   */
  public function getOutputVersionFormat()
  {
    return $this->outputVersionFormat;
  }
  /**
   * Output only. The resource name of the sink.
   * "projects/[PROJECT_ID]/sinks/[SINK_NAME]
   * "organizations/[ORGANIZATION_ID]/sinks/[SINK_NAME]
   * "billingAccounts/[BILLING_ACCOUNT_ID]/sinks/[SINK_NAME]
   * "folders/[FOLDER_ID]/sinks/[SINK_NAME] For example:
   * projects/my_project/sinks/SINK_NAME
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
   * Output only. The last update timestamp of the sink.This field may not be
   * present for older sinks.
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
   * Output only. An IAM identity—a service account or group—under which Cloud
   * Logging writes the exported log entries to the sink's destination. This
   * field is either set by specifying custom_writer_identity or set
   * automatically by sinks.create and sinks.update based on the value of
   * unique_writer_identity in those methods.Until you grant this identity
   * write-access to the destination, log entry exports from this sink will
   * fail. For more information, see Granting Access for a Resource
   * (https://cloud.google.com/iam/docs/granting-roles-to-service-
   * accounts#granting_access_to_a_service_account_for_a_resource). Consult the
   * destination service's documentation to determine the appropriate IAM roles
   * to assign to the identity.Sinks that have a destination that is a log
   * bucket in the same project as the sink cannot have a writer_identity and no
   * additional permissions are required.
   *
   * @param string $writerIdentity
   */
  public function setWriterIdentity($writerIdentity)
  {
    $this->writerIdentity = $writerIdentity;
  }
  /**
   * @return string
   */
  public function getWriterIdentity()
  {
    return $this->writerIdentity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogSink::class, 'Google_Service_Logging_LogSink');
