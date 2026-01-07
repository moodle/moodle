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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ImportDocumentsRequest extends \Google\Model
{
  /**
   * Defaults to `INCREMENTAL`.
   */
  public const RECONCILIATION_MODE_RECONCILIATION_MODE_UNSPECIFIED = 'RECONCILIATION_MODE_UNSPECIFIED';
  /**
   * Inserts new documents or updates existing documents.
   */
  public const RECONCILIATION_MODE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Calculates diff and replaces the entire document dataset. Existing
   * documents may be deleted if they are not present in the source location.
   * When using this mode, there won't be any downtime on the dataset targeted.
   * Any document that should remain unchanged or that should be updated will
   * continue serving while the operation is running.
   */
  public const RECONCILIATION_MODE_FULL = 'FULL';
  protected $alloyDbSourceType = GoogleCloudDiscoveryengineV1AlloyDbSource::class;
  protected $alloyDbSourceDataType = '';
  /**
   * Whether to automatically generate IDs for the documents if absent. If set
   * to `true`, Document.ids are automatically generated based on the hash of
   * the payload, where IDs may not be consistent during multiple imports. In
   * which case ReconciliationMode.FULL is highly recommended to avoid duplicate
   * contents. If unset or set to `false`, Document.ids have to be specified
   * using id_field, otherwise, documents without IDs fail to be imported.
   * Supported data sources: * GcsSource. GcsSource.data_schema must be `custom`
   * or `csv`. Otherwise, an INVALID_ARGUMENT error is thrown. * BigQuerySource.
   * BigQuerySource.data_schema must be `custom` or `csv`. Otherwise, an
   * INVALID_ARGUMENT error is thrown. * SpannerSource. * CloudSqlSource. *
   * FirestoreSource. * BigtableSource.
   *
   * @var bool
   */
  public $autoGenerateIds;
  protected $bigquerySourceType = GoogleCloudDiscoveryengineV1BigQuerySource::class;
  protected $bigquerySourceDataType = '';
  protected $bigtableSourceType = GoogleCloudDiscoveryengineV1BigtableSource::class;
  protected $bigtableSourceDataType = '';
  protected $cloudSqlSourceType = GoogleCloudDiscoveryengineV1CloudSqlSource::class;
  protected $cloudSqlSourceDataType = '';
  protected $errorConfigType = GoogleCloudDiscoveryengineV1ImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $fhirStoreSourceType = GoogleCloudDiscoveryengineV1FhirStoreSource::class;
  protected $fhirStoreSourceDataType = '';
  protected $firestoreSourceType = GoogleCloudDiscoveryengineV1FirestoreSource::class;
  protected $firestoreSourceDataType = '';
  /**
   * Optional. Whether to force refresh the unstructured content of the
   * documents. If set to `true`, the content part of the documents will be
   * refreshed regardless of the update status of the referencing content.
   *
   * @var bool
   */
  public $forceRefreshContent;
  protected $gcsSourceType = GoogleCloudDiscoveryengineV1GcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * The field indicates the ID field or column to be used as unique IDs of the
   * documents. For GcsSource it is the key of the JSON field. For instance,
   * `my_id` for JSON `{"my_id": "some_uuid"}`. For others, it may be the column
   * name of the table where the unique ids are stored. The values of the JSON
   * field or the table column are used as the Document.ids. The JSON field or
   * the table column must be of string type, and the values must be set as
   * valid strings conform to [RFC-1034](https://tools.ietf.org/html/rfc1034)
   * with 1-63 characters. Otherwise, documents without valid IDs fail to be
   * imported. Only set this field when auto_generate_ids is unset or set as
   * `false`. Otherwise, an INVALID_ARGUMENT error is thrown. If it is unset, a
   * default value `_id` is used when importing from the allowed data sources.
   * Supported data sources: * GcsSource. GcsSource.data_schema must be `custom`
   * or `csv`. Otherwise, an INVALID_ARGUMENT error is thrown. * BigQuerySource.
   * BigQuerySource.data_schema must be `custom` or `csv`. Otherwise, an
   * INVALID_ARGUMENT error is thrown. * SpannerSource. * CloudSqlSource. *
   * BigtableSource.
   *
   * @var string
   */
  public $idField;
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1ImportDocumentsRequestInlineSource::class;
  protected $inlineSourceDataType = '';
  /**
   * The mode of reconciliation between existing documents and the documents to
   * be imported. Defaults to ReconciliationMode.INCREMENTAL.
   *
   * @var string
   */
  public $reconciliationMode;
  protected $spannerSourceType = GoogleCloudDiscoveryengineV1SpannerSource::class;
  protected $spannerSourceDataType = '';
  /**
   * Indicates which fields in the provided imported documents to update. If not
   * set, the default is to update all fields.
   *
   * @var string
   */
  public $updateMask;

  /**
   * AlloyDB input source.
   *
   * @param GoogleCloudDiscoveryengineV1AlloyDbSource $alloyDbSource
   */
  public function setAlloyDbSource(GoogleCloudDiscoveryengineV1AlloyDbSource $alloyDbSource)
  {
    $this->alloyDbSource = $alloyDbSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AlloyDbSource
   */
  public function getAlloyDbSource()
  {
    return $this->alloyDbSource;
  }
  /**
   * Whether to automatically generate IDs for the documents if absent. If set
   * to `true`, Document.ids are automatically generated based on the hash of
   * the payload, where IDs may not be consistent during multiple imports. In
   * which case ReconciliationMode.FULL is highly recommended to avoid duplicate
   * contents. If unset or set to `false`, Document.ids have to be specified
   * using id_field, otherwise, documents without IDs fail to be imported.
   * Supported data sources: * GcsSource. GcsSource.data_schema must be `custom`
   * or `csv`. Otherwise, an INVALID_ARGUMENT error is thrown. * BigQuerySource.
   * BigQuerySource.data_schema must be `custom` or `csv`. Otherwise, an
   * INVALID_ARGUMENT error is thrown. * SpannerSource. * CloudSqlSource. *
   * FirestoreSource. * BigtableSource.
   *
   * @param bool $autoGenerateIds
   */
  public function setAutoGenerateIds($autoGenerateIds)
  {
    $this->autoGenerateIds = $autoGenerateIds;
  }
  /**
   * @return bool
   */
  public function getAutoGenerateIds()
  {
    return $this->autoGenerateIds;
  }
  /**
   * BigQuery input source.
   *
   * @param GoogleCloudDiscoveryengineV1BigQuerySource $bigquerySource
   */
  public function setBigquerySource(GoogleCloudDiscoveryengineV1BigQuerySource $bigquerySource)
  {
    $this->bigquerySource = $bigquerySource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1BigQuerySource
   */
  public function getBigquerySource()
  {
    return $this->bigquerySource;
  }
  /**
   * Cloud Bigtable input source.
   *
   * @param GoogleCloudDiscoveryengineV1BigtableSource $bigtableSource
   */
  public function setBigtableSource(GoogleCloudDiscoveryengineV1BigtableSource $bigtableSource)
  {
    $this->bigtableSource = $bigtableSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1BigtableSource
   */
  public function getBigtableSource()
  {
    return $this->bigtableSource;
  }
  /**
   * Cloud SQL input source.
   *
   * @param GoogleCloudDiscoveryengineV1CloudSqlSource $cloudSqlSource
   */
  public function setCloudSqlSource(GoogleCloudDiscoveryengineV1CloudSqlSource $cloudSqlSource)
  {
    $this->cloudSqlSource = $cloudSqlSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CloudSqlSource
   */
  public function getCloudSqlSource()
  {
    return $this->cloudSqlSource;
  }
  /**
   * The desired location of errors incurred during the Import.
   *
   * @param GoogleCloudDiscoveryengineV1ImportErrorConfig $errorConfig
   */
  public function setErrorConfig(GoogleCloudDiscoveryengineV1ImportErrorConfig $errorConfig)
  {
    $this->errorConfig = $errorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ImportErrorConfig
   */
  public function getErrorConfig()
  {
    return $this->errorConfig;
  }
  /**
   * FhirStore input source.
   *
   * @param GoogleCloudDiscoveryengineV1FhirStoreSource $fhirStoreSource
   */
  public function setFhirStoreSource(GoogleCloudDiscoveryengineV1FhirStoreSource $fhirStoreSource)
  {
    $this->fhirStoreSource = $fhirStoreSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1FhirStoreSource
   */
  public function getFhirStoreSource()
  {
    return $this->fhirStoreSource;
  }
  /**
   * Firestore input source.
   *
   * @param GoogleCloudDiscoveryengineV1FirestoreSource $firestoreSource
   */
  public function setFirestoreSource(GoogleCloudDiscoveryengineV1FirestoreSource $firestoreSource)
  {
    $this->firestoreSource = $firestoreSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1FirestoreSource
   */
  public function getFirestoreSource()
  {
    return $this->firestoreSource;
  }
  /**
   * Optional. Whether to force refresh the unstructured content of the
   * documents. If set to `true`, the content part of the documents will be
   * refreshed regardless of the update status of the referencing content.
   *
   * @param bool $forceRefreshContent
   */
  public function setForceRefreshContent($forceRefreshContent)
  {
    $this->forceRefreshContent = $forceRefreshContent;
  }
  /**
   * @return bool
   */
  public function getForceRefreshContent()
  {
    return $this->forceRefreshContent;
  }
  /**
   * Cloud Storage location for the input content.
   *
   * @param GoogleCloudDiscoveryengineV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudDiscoveryengineV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * The field indicates the ID field or column to be used as unique IDs of the
   * documents. For GcsSource it is the key of the JSON field. For instance,
   * `my_id` for JSON `{"my_id": "some_uuid"}`. For others, it may be the column
   * name of the table where the unique ids are stored. The values of the JSON
   * field or the table column are used as the Document.ids. The JSON field or
   * the table column must be of string type, and the values must be set as
   * valid strings conform to [RFC-1034](https://tools.ietf.org/html/rfc1034)
   * with 1-63 characters. Otherwise, documents without valid IDs fail to be
   * imported. Only set this field when auto_generate_ids is unset or set as
   * `false`. Otherwise, an INVALID_ARGUMENT error is thrown. If it is unset, a
   * default value `_id` is used when importing from the allowed data sources.
   * Supported data sources: * GcsSource. GcsSource.data_schema must be `custom`
   * or `csv`. Otherwise, an INVALID_ARGUMENT error is thrown. * BigQuerySource.
   * BigQuerySource.data_schema must be `custom` or `csv`. Otherwise, an
   * INVALID_ARGUMENT error is thrown. * SpannerSource. * CloudSqlSource. *
   * BigtableSource.
   *
   * @param string $idField
   */
  public function setIdField($idField)
  {
    $this->idField = $idField;
  }
  /**
   * @return string
   */
  public function getIdField()
  {
    return $this->idField;
  }
  /**
   * The Inline source for the input content for documents.
   *
   * @param GoogleCloudDiscoveryengineV1ImportDocumentsRequestInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1ImportDocumentsRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ImportDocumentsRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
  /**
   * The mode of reconciliation between existing documents and the documents to
   * be imported. Defaults to ReconciliationMode.INCREMENTAL.
   *
   * Accepted values: RECONCILIATION_MODE_UNSPECIFIED, INCREMENTAL, FULL
   *
   * @param self::RECONCILIATION_MODE_* $reconciliationMode
   */
  public function setReconciliationMode($reconciliationMode)
  {
    $this->reconciliationMode = $reconciliationMode;
  }
  /**
   * @return self::RECONCILIATION_MODE_*
   */
  public function getReconciliationMode()
  {
    return $this->reconciliationMode;
  }
  /**
   * Spanner input source.
   *
   * @param GoogleCloudDiscoveryengineV1SpannerSource $spannerSource
   */
  public function setSpannerSource(GoogleCloudDiscoveryengineV1SpannerSource $spannerSource)
  {
    $this->spannerSource = $spannerSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SpannerSource
   */
  public function getSpannerSource()
  {
    return $this->spannerSource;
  }
  /**
   * Indicates which fields in the provided imported documents to update. If not
   * set, the default is to update all fields.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ImportDocumentsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ImportDocumentsRequest');
