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

namespace Google\Service\Pubsub;

class BigQueryConfig extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The subscription can actively send messages to BigQuery
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Cannot write to the BigQuery table because of permission denied errors.
   * This can happen if - Pub/Sub SA has not been granted the [appropriate
   * BigQuery IAM permissions](https://cloud.google.com/pubsub/docs/create-
   * subscription#assign_bigquery_service_account) - bigquery.googleapis.com API
   * is not enabled for the project
   * ([instructions](https://cloud.google.com/service-usage/docs/enable-
   * disable))
   */
  public const STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * Cannot write to the BigQuery table because it does not exist.
   */
  public const STATE_NOT_FOUND = 'NOT_FOUND';
  /**
   * Cannot write to the BigQuery table due to a schema mismatch.
   */
  public const STATE_SCHEMA_MISMATCH = 'SCHEMA_MISMATCH';
  /**
   * Cannot write to the destination because enforce_in_transit is set to true
   * and the destination locations are not in the allowed regions.
   */
  public const STATE_IN_TRANSIT_LOCATION_RESTRICTION = 'IN_TRANSIT_LOCATION_RESTRICTION';
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
   * Output only. An output-only field that indicates whether or not the
   * subscription can receive messages.
   *
   * @var string
   */
  public $state;
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
   * Output only. An output-only field that indicates whether or not the
   * subscription can receive messages.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, PERMISSION_DENIED, NOT_FOUND,
   * SCHEMA_MISMATCH, IN_TRANSIT_LOCATION_RESTRICTION
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
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
class_alias(BigQueryConfig::class, 'Google_Service_Pubsub_BigQueryConfig');
