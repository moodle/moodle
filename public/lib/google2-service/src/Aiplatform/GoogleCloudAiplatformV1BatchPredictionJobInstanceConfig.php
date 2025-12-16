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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig extends \Google\Collection
{
  protected $collection_key = 'includedFields';
  /**
   * Fields that will be excluded in the prediction instance that is sent to the
   * Model. Excluded will be attached to the batch prediction output if
   * key_field is not specified. When excluded_fields is populated,
   * included_fields must be empty. The input must be JSONL with objects at each
   * line, BigQuery or TfRecord.
   *
   * @var string[]
   */
  public $excludedFields;
  /**
   * Fields that will be included in the prediction instance that is sent to the
   * Model. If instance_type is `array`, the order of field names in
   * included_fields also determines the order of the values in the array. When
   * included_fields is populated, excluded_fields must be empty. The input must
   * be JSONL with objects at each line, BigQuery or TfRecord.
   *
   * @var string[]
   */
  public $includedFields;
  /**
   * The format of the instance that the Model accepts. Vertex AI will convert
   * compatible batch prediction input instance formats to the specified format.
   * Supported values are: * `object`: Each input is converted to JSON object
   * format. * For `bigquery`, each row is converted to an object. * For
   * `jsonl`, each line of the JSONL input must be an object. * Does not apply
   * to `csv`, `file-list`, `tf-record`, or `tf-record-gzip`. * `array`: Each
   * input is converted to JSON array format. * For `bigquery`, each row is
   * converted to an array. The order of columns is determined by the BigQuery
   * column order, unless included_fields is populated. included_fields must be
   * populated for specifying field orders. * For `jsonl`, if each line of the
   * JSONL input is an object, included_fields must be populated for specifying
   * field orders. * Does not apply to `csv`, `file-list`, `tf-record`, or `tf-
   * record-gzip`. If not specified, Vertex AI converts the batch prediction
   * input as follows: * For `bigquery` and `csv`, the behavior is the same as
   * `array`. The order of columns is the same as defined in the file or table,
   * unless included_fields is populated. * For `jsonl`, the prediction instance
   * format is determined by each line of the input. * For `tf-record`/`tf-
   * record-gzip`, each record will be converted to an object in the format of
   * `{"b64": }`, where `` is the Base64-encoded string of the content of the
   * record. * For `file-list`, each file in the list will be converted to an
   * object in the format of `{"b64": }`, where `` is the Base64-encoded string
   * of the content of the file.
   *
   * @var string
   */
  public $instanceType;
  /**
   * The name of the field that is considered as a key. The values identified by
   * the key field is not included in the transformed instances that is sent to
   * the Model. This is similar to specifying this name of the field in
   * excluded_fields. In addition, the batch prediction output will not include
   * the instances. Instead the output will only include the value of the key
   * field, in a field named `key` in the output: * For `jsonl` output format,
   * the output will have a `key` field instead of the `instance` field. * For
   * `csv`/`bigquery` output format, the output will have have a `key` column
   * instead of the instance feature columns. The input must be JSONL with
   * objects at each line, CSV, BigQuery or TfRecord.
   *
   * @var string
   */
  public $keyField;

  /**
   * Fields that will be excluded in the prediction instance that is sent to the
   * Model. Excluded will be attached to the batch prediction output if
   * key_field is not specified. When excluded_fields is populated,
   * included_fields must be empty. The input must be JSONL with objects at each
   * line, BigQuery or TfRecord.
   *
   * @param string[] $excludedFields
   */
  public function setExcludedFields($excludedFields)
  {
    $this->excludedFields = $excludedFields;
  }
  /**
   * @return string[]
   */
  public function getExcludedFields()
  {
    return $this->excludedFields;
  }
  /**
   * Fields that will be included in the prediction instance that is sent to the
   * Model. If instance_type is `array`, the order of field names in
   * included_fields also determines the order of the values in the array. When
   * included_fields is populated, excluded_fields must be empty. The input must
   * be JSONL with objects at each line, BigQuery or TfRecord.
   *
   * @param string[] $includedFields
   */
  public function setIncludedFields($includedFields)
  {
    $this->includedFields = $includedFields;
  }
  /**
   * @return string[]
   */
  public function getIncludedFields()
  {
    return $this->includedFields;
  }
  /**
   * The format of the instance that the Model accepts. Vertex AI will convert
   * compatible batch prediction input instance formats to the specified format.
   * Supported values are: * `object`: Each input is converted to JSON object
   * format. * For `bigquery`, each row is converted to an object. * For
   * `jsonl`, each line of the JSONL input must be an object. * Does not apply
   * to `csv`, `file-list`, `tf-record`, or `tf-record-gzip`. * `array`: Each
   * input is converted to JSON array format. * For `bigquery`, each row is
   * converted to an array. The order of columns is determined by the BigQuery
   * column order, unless included_fields is populated. included_fields must be
   * populated for specifying field orders. * For `jsonl`, if each line of the
   * JSONL input is an object, included_fields must be populated for specifying
   * field orders. * Does not apply to `csv`, `file-list`, `tf-record`, or `tf-
   * record-gzip`. If not specified, Vertex AI converts the batch prediction
   * input as follows: * For `bigquery` and `csv`, the behavior is the same as
   * `array`. The order of columns is the same as defined in the file or table,
   * unless included_fields is populated. * For `jsonl`, the prediction instance
   * format is determined by each line of the input. * For `tf-record`/`tf-
   * record-gzip`, each record will be converted to an object in the format of
   * `{"b64": }`, where `` is the Base64-encoded string of the content of the
   * record. * For `file-list`, each file in the list will be converted to an
   * object in the format of `{"b64": }`, where `` is the Base64-encoded string
   * of the content of the file.
   *
   * @param string $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return string
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * The name of the field that is considered as a key. The values identified by
   * the key field is not included in the transformed instances that is sent to
   * the Model. This is similar to specifying this name of the field in
   * excluded_fields. In addition, the batch prediction output will not include
   * the instances. Instead the output will only include the value of the key
   * field, in a field named `key` in the output: * For `jsonl` output format,
   * the output will have a `key` field instead of the `instance` field. * For
   * `csv`/`bigquery` output format, the output will have have a `key` column
   * instead of the instance feature columns. The input must be JSONL with
   * objects at each line, CSV, BigQuery or TfRecord.
   *
   * @param string $keyField
   */
  public function setKeyField($keyField)
  {
    $this->keyField = $keyField;
  }
  /**
   * @return string
   */
  public function getKeyField()
  {
    return $this->keyField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig');
