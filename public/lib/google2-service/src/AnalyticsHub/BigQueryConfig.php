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

namespace Google\Service\AnalyticsHub;

class BigQueryConfig extends \Google\Model
{
  /**
   * Optional. When true and use_topic_schema is true, any fields that are a
   * part of the topic schema that are not part of the BigQuery table schema are
   * dropped when writing to BigQuery. Otherwise, the schemas must be kept in
   * sync and any messages with extra fields are not written and remain in the
   * subscription's backlog.
   *
   * @var bool
   */
  public $dropUnknownFields;
  /**
   * Optional. The service account to use to write to BigQuery. The subscription
   * creator or updater that specifies this field must have
   * `iam.serviceAccounts.actAs` permission on the service account. If not
   * specified, the Pub/Sub [service
   * agent](https://cloud.google.com/iam/docs/service-agents),
   * service-{project_number}@gcp-sa-pubsub.iam.gserviceaccount.com, is used.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Optional. The name of the table to which to write data, of the form
   * {projectId}.{datasetId}.{tableId}
   *
   * @var string
   */
  public $table;
  /**
   * Optional. When true, use the BigQuery table's schema as the columns to
   * write to in BigQuery. `use_table_schema` and `use_topic_schema` cannot be
   * enabled at the same time.
   *
   * @var bool
   */
  public $useTableSchema;
  /**
   * Optional. When true, use the topic's schema as the columns to write to in
   * BigQuery, if it exists. `use_topic_schema` and `use_table_schema` cannot be
   * enabled at the same time.
   *
   * @var bool
   */
  public $useTopicSchema;
  /**
   * Optional. When true, write the subscription name, message_id, publish_time,
   * attributes, and ordering_key to additional columns in the table. The
   * subscription name, message_id, and publish_time fields are put in their own
   * columns while all other message properties (other than data) are written to
   * a JSON object in the attributes column.
   *
   * @var bool
   */
  public $writeMetadata;

  /**
   * Optional. When true and use_topic_schema is true, any fields that are a
   * part of the topic schema that are not part of the BigQuery table schema are
   * dropped when writing to BigQuery. Otherwise, the schemas must be kept in
   * sync and any messages with extra fields are not written and remain in the
   * subscription's backlog.
   *
   * @param bool $dropUnknownFields
   */
  public function setDropUnknownFields($dropUnknownFields)
  {
    $this->dropUnknownFields = $dropUnknownFields;
  }
  /**
   * @return bool
   */
  public function getDropUnknownFields()
  {
    return $this->dropUnknownFields;
  }
  /**
   * Optional. The service account to use to write to BigQuery. The subscription
   * creator or updater that specifies this field must have
   * `iam.serviceAccounts.actAs` permission on the service account. If not
   * specified, the Pub/Sub [service
   * agent](https://cloud.google.com/iam/docs/service-agents),
   * service-{project_number}@gcp-sa-pubsub.iam.gserviceaccount.com, is used.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Optional. The name of the table to which to write data, of the form
   * {projectId}.{datasetId}.{tableId}
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * Optional. When true, use the BigQuery table's schema as the columns to
   * write to in BigQuery. `use_table_schema` and `use_topic_schema` cannot be
   * enabled at the same time.
   *
   * @param bool $useTableSchema
   */
  public function setUseTableSchema($useTableSchema)
  {
    $this->useTableSchema = $useTableSchema;
  }
  /**
   * @return bool
   */
  public function getUseTableSchema()
  {
    return $this->useTableSchema;
  }
  /**
   * Optional. When true, use the topic's schema as the columns to write to in
   * BigQuery, if it exists. `use_topic_schema` and `use_table_schema` cannot be
   * enabled at the same time.
   *
   * @param bool $useTopicSchema
   */
  public function setUseTopicSchema($useTopicSchema)
  {
    $this->useTopicSchema = $useTopicSchema;
  }
  /**
   * @return bool
   */
  public function getUseTopicSchema()
  {
    return $this->useTopicSchema;
  }
  /**
   * Optional. When true, write the subscription name, message_id, publish_time,
   * attributes, and ordering_key to additional columns in the table. The
   * subscription name, message_id, and publish_time fields are put in their own
   * columns while all other message properties (other than data) are written to
   * a JSON object in the attributes column.
   *
   * @param bool $writeMetadata
   */
  public function setWriteMetadata($writeMetadata)
  {
    $this->writeMetadata = $writeMetadata;
  }
  /**
   * @return bool
   */
  public function getWriteMetadata()
  {
    return $this->writeMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryConfig::class, 'Google_Service_AnalyticsHub_BigQueryConfig');
