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

class GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataRecordError extends \Google\Model
{
  /**
   * Default, shall not be used.
   */
  public const ERROR_TYPE_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * The record is empty.
   */
  public const ERROR_TYPE_EMPTY_LINE = 'EMPTY_LINE';
  /**
   * Invalid json format.
   */
  public const ERROR_TYPE_INVALID_JSON_SYNTAX = 'INVALID_JSON_SYNTAX';
  /**
   * Invalid csv format.
   */
  public const ERROR_TYPE_INVALID_CSV_SYNTAX = 'INVALID_CSV_SYNTAX';
  /**
   * Invalid avro format.
   */
  public const ERROR_TYPE_INVALID_AVRO_SYNTAX = 'INVALID_AVRO_SYNTAX';
  /**
   * The embedding id is not valid.
   */
  public const ERROR_TYPE_INVALID_EMBEDDING_ID = 'INVALID_EMBEDDING_ID';
  /**
   * The size of the dense embedding vectors does not match with the specified
   * dimension.
   */
  public const ERROR_TYPE_EMBEDDING_SIZE_MISMATCH = 'EMBEDDING_SIZE_MISMATCH';
  /**
   * The `namespace` field is missing.
   */
  public const ERROR_TYPE_NAMESPACE_MISSING = 'NAMESPACE_MISSING';
  /**
   * Generic catch-all error. Only used for validation failure where the root
   * cause cannot be easily retrieved programmatically.
   */
  public const ERROR_TYPE_PARSING_ERROR = 'PARSING_ERROR';
  /**
   * There are multiple restricts with the same `namespace` value.
   */
  public const ERROR_TYPE_DUPLICATE_NAMESPACE = 'DUPLICATE_NAMESPACE';
  /**
   * Numeric restrict has operator specified in datapoint.
   */
  public const ERROR_TYPE_OP_IN_DATAPOINT = 'OP_IN_DATAPOINT';
  /**
   * Numeric restrict has multiple values specified.
   */
  public const ERROR_TYPE_MULTIPLE_VALUES = 'MULTIPLE_VALUES';
  /**
   * Numeric restrict has invalid numeric value specified.
   */
  public const ERROR_TYPE_INVALID_NUMERIC_VALUE = 'INVALID_NUMERIC_VALUE';
  /**
   * File is not in UTF_8 format.
   */
  public const ERROR_TYPE_INVALID_ENCODING = 'INVALID_ENCODING';
  /**
   * Error parsing sparse dimensions field.
   */
  public const ERROR_TYPE_INVALID_SPARSE_DIMENSIONS = 'INVALID_SPARSE_DIMENSIONS';
  /**
   * Token restrict value is invalid.
   */
  public const ERROR_TYPE_INVALID_TOKEN_VALUE = 'INVALID_TOKEN_VALUE';
  /**
   * Invalid sparse embedding.
   */
  public const ERROR_TYPE_INVALID_SPARSE_EMBEDDING = 'INVALID_SPARSE_EMBEDDING';
  /**
   * Invalid dense embedding.
   */
  public const ERROR_TYPE_INVALID_EMBEDDING = 'INVALID_EMBEDDING';
  /**
   * Invalid embedding metadata.
   */
  public const ERROR_TYPE_INVALID_EMBEDDING_METADATA = 'INVALID_EMBEDDING_METADATA';
  /**
   * Embedding metadata exceeds size limit.
   */
  public const ERROR_TYPE_EMBEDDING_METADATA_EXCEEDS_SIZE_LIMIT = 'EMBEDDING_METADATA_EXCEEDS_SIZE_LIMIT';
  /**
   * Empty if the embedding id is failed to parse.
   *
   * @var string
   */
  public $embeddingId;
  /**
   * A human-readable message that is shown to the user to help them fix the
   * error. Note that this message may change from time to time, your code
   * should check against error_type as the source of truth.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The error type of this record.
   *
   * @var string
   */
  public $errorType;
  /**
   * The original content of this record.
   *
   * @var string
   */
  public $rawRecord;
  /**
   * Cloud Storage URI pointing to the original file in user's bucket.
   *
   * @var string
   */
  public $sourceGcsUri;

  /**
   * Empty if the embedding id is failed to parse.
   *
   * @param string $embeddingId
   */
  public function setEmbeddingId($embeddingId)
  {
    $this->embeddingId = $embeddingId;
  }
  /**
   * @return string
   */
  public function getEmbeddingId()
  {
    return $this->embeddingId;
  }
  /**
   * A human-readable message that is shown to the user to help them fix the
   * error. Note that this message may change from time to time, your code
   * should check against error_type as the source of truth.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * The error type of this record.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, EMPTY_LINE, INVALID_JSON_SYNTAX,
   * INVALID_CSV_SYNTAX, INVALID_AVRO_SYNTAX, INVALID_EMBEDDING_ID,
   * EMBEDDING_SIZE_MISMATCH, NAMESPACE_MISSING, PARSING_ERROR,
   * DUPLICATE_NAMESPACE, OP_IN_DATAPOINT, MULTIPLE_VALUES,
   * INVALID_NUMERIC_VALUE, INVALID_ENCODING, INVALID_SPARSE_DIMENSIONS,
   * INVALID_TOKEN_VALUE, INVALID_SPARSE_EMBEDDING, INVALID_EMBEDDING,
   * INVALID_EMBEDDING_METADATA, EMBEDDING_METADATA_EXCEEDS_SIZE_LIMIT
   *
   * @param self::ERROR_TYPE_* $errorType
   */
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  /**
   * @return self::ERROR_TYPE_*
   */
  public function getErrorType()
  {
    return $this->errorType;
  }
  /**
   * The original content of this record.
   *
   * @param string $rawRecord
   */
  public function setRawRecord($rawRecord)
  {
    $this->rawRecord = $rawRecord;
  }
  /**
   * @return string
   */
  public function getRawRecord()
  {
    return $this->rawRecord;
  }
  /**
   * Cloud Storage URI pointing to the original file in user's bucket.
   *
   * @param string $sourceGcsUri
   */
  public function setSourceGcsUri($sourceGcsUri)
  {
    $this->sourceGcsUri = $sourceGcsUri;
  }
  /**
   * @return string
   */
  public function getSourceGcsUri()
  {
    return $this->sourceGcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataRecordError::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataRecordError');
