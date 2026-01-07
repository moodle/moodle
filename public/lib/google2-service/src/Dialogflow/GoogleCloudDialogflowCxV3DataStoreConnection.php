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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3DataStoreConnection extends \Google\Model
{
  /**
   * Not specified. This value indicates that the data store type is not
   * specified, so it will not be used during search.
   */
  public const DATA_STORE_TYPE_DATA_STORE_TYPE_UNSPECIFIED = 'DATA_STORE_TYPE_UNSPECIFIED';
  /**
   * A data store that contains public web content.
   */
  public const DATA_STORE_TYPE_PUBLIC_WEB = 'PUBLIC_WEB';
  /**
   * A data store that contains unstructured private data.
   */
  public const DATA_STORE_TYPE_UNSTRUCTURED = 'UNSTRUCTURED';
  /**
   * A data store that contains structured data (for example FAQ).
   */
  public const DATA_STORE_TYPE_STRUCTURED = 'STRUCTURED';
  /**
   * Not specified. This should be set for STRUCTURED type data stores. Due to
   * legacy reasons this is considered as DOCUMENTS for STRUCTURED and
   * PUBLIC_WEB data stores.
   */
  public const DOCUMENT_PROCESSING_MODE_DOCUMENT_PROCESSING_MODE_UNSPECIFIED = 'DOCUMENT_PROCESSING_MODE_UNSPECIFIED';
  /**
   * Documents are processed as documents.
   */
  public const DOCUMENT_PROCESSING_MODE_DOCUMENTS = 'DOCUMENTS';
  /**
   * Documents are converted to chunks.
   */
  public const DOCUMENT_PROCESSING_MODE_CHUNKS = 'CHUNKS';
  /**
   * The full name of the referenced data store. Formats: `projects/{project}/lo
   * cations/{location}/collections/{collection}/dataStores/{data_store}`
   * `projects/{project}/locations/{location}/dataStores/{data_store}`
   *
   * @var string
   */
  public $dataStore;
  /**
   * The type of the connected data store.
   *
   * @var string
   */
  public $dataStoreType;
  /**
   * The document processing mode for the data store connection. Should only be
   * set for PUBLIC_WEB and UNSTRUCTURED data stores. If not set it is
   * considered as DOCUMENTS, as this is the legacy mode.
   *
   * @var string
   */
  public $documentProcessingMode;

  /**
   * The full name of the referenced data store. Formats: `projects/{project}/lo
   * cations/{location}/collections/{collection}/dataStores/{data_store}`
   * `projects/{project}/locations/{location}/dataStores/{data_store}`
   *
   * @param string $dataStore
   */
  public function setDataStore($dataStore)
  {
    $this->dataStore = $dataStore;
  }
  /**
   * @return string
   */
  public function getDataStore()
  {
    return $this->dataStore;
  }
  /**
   * The type of the connected data store.
   *
   * Accepted values: DATA_STORE_TYPE_UNSPECIFIED, PUBLIC_WEB, UNSTRUCTURED,
   * STRUCTURED
   *
   * @param self::DATA_STORE_TYPE_* $dataStoreType
   */
  public function setDataStoreType($dataStoreType)
  {
    $this->dataStoreType = $dataStoreType;
  }
  /**
   * @return self::DATA_STORE_TYPE_*
   */
  public function getDataStoreType()
  {
    return $this->dataStoreType;
  }
  /**
   * The document processing mode for the data store connection. Should only be
   * set for PUBLIC_WEB and UNSTRUCTURED data stores. If not set it is
   * considered as DOCUMENTS, as this is the legacy mode.
   *
   * Accepted values: DOCUMENT_PROCESSING_MODE_UNSPECIFIED, DOCUMENTS, CHUNKS
   *
   * @param self::DOCUMENT_PROCESSING_MODE_* $documentProcessingMode
   */
  public function setDocumentProcessingMode($documentProcessingMode)
  {
    $this->documentProcessingMode = $documentProcessingMode;
  }
  /**
   * @return self::DOCUMENT_PROCESSING_MODE_*
   */
  public function getDocumentProcessingMode()
  {
    return $this->documentProcessingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DataStoreConnection::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DataStoreConnection');
