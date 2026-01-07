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

class GoogleCloudDiscoveryengineV1Document extends \Google\Model
{
  protected $aclInfoType = GoogleCloudDiscoveryengineV1DocumentAclInfo::class;
  protected $aclInfoDataType = '';
  protected $contentType = GoogleCloudDiscoveryengineV1DocumentContent::class;
  protected $contentDataType = '';
  /**
   * Output only. This field is OUTPUT_ONLY. It contains derived data that are
   * not in the original input document.
   *
   * @var array[]
   */
  public $derivedStructData;
  /**
   * Immutable. The identifier of the document. Id should conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length
   * limit of 128 characters.
   *
   * @var string
   */
  public $id;
  protected $indexStatusType = GoogleCloudDiscoveryengineV1DocumentIndexStatus::class;
  protected $indexStatusDataType = '';
  /**
   * Output only. The last time the document was indexed. If this field is set,
   * the document could be returned in search results. This field is
   * OUTPUT_ONLY. If this field is not populated, it means the document has
   * never been indexed.
   *
   * @var string
   */
  public $indexTime;
  /**
   * The JSON string representation of the document. It should conform to the
   * registered Schema or an `INVALID_ARGUMENT` error is thrown.
   *
   * @var string
   */
  public $jsonData;
  /**
   * Immutable. The full resource name of the document. Format: `projects/{proje
   * ct}/locations/{location}/collections/{collection}/dataStores/{data_store}/b
   * ranches/{branch}/documents/{document_id}`. This field must be a UTF-8
   * encoded string with a length limit of 1024 characters.
   *
   * @var string
   */
  public $name;
  /**
   * The identifier of the parent document. Currently supports at most two level
   * document hierarchy. Id should conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length
   * limit of 63 characters.
   *
   * @var string
   */
  public $parentDocumentId;
  /**
   * The identifier of the schema located in the same data store.
   *
   * @var string
   */
  public $schemaId;
  /**
   * The structured JSON data for the document. It should conform to the
   * registered Schema or an `INVALID_ARGUMENT` error is thrown.
   *
   * @var array[]
   */
  public $structData;

  /**
   * Access control information for the document.
   *
   * @param GoogleCloudDiscoveryengineV1DocumentAclInfo $aclInfo
   */
  public function setAclInfo(GoogleCloudDiscoveryengineV1DocumentAclInfo $aclInfo)
  {
    $this->aclInfo = $aclInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DocumentAclInfo
   */
  public function getAclInfo()
  {
    return $this->aclInfo;
  }
  /**
   * The unstructured data linked to this document. Content can only be set and
   * must be set if this document is under a `CONTENT_REQUIRED` data store.
   *
   * @param GoogleCloudDiscoveryengineV1DocumentContent $content
   */
  public function setContent(GoogleCloudDiscoveryengineV1DocumentContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DocumentContent
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Output only. This field is OUTPUT_ONLY. It contains derived data that are
   * not in the original input document.
   *
   * @param array[] $derivedStructData
   */
  public function setDerivedStructData($derivedStructData)
  {
    $this->derivedStructData = $derivedStructData;
  }
  /**
   * @return array[]
   */
  public function getDerivedStructData()
  {
    return $this->derivedStructData;
  }
  /**
   * Immutable. The identifier of the document. Id should conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length
   * limit of 128 characters.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The index status of the document. * If document is indexed
   * successfully, the index_time field is populated. * Otherwise, if document
   * is not indexed due to errors, the error_samples field is populated. *
   * Otherwise, if document's index is in progress, the pending_message field is
   * populated.
   *
   * @param GoogleCloudDiscoveryengineV1DocumentIndexStatus $indexStatus
   */
  public function setIndexStatus(GoogleCloudDiscoveryengineV1DocumentIndexStatus $indexStatus)
  {
    $this->indexStatus = $indexStatus;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DocumentIndexStatus
   */
  public function getIndexStatus()
  {
    return $this->indexStatus;
  }
  /**
   * Output only. The last time the document was indexed. If this field is set,
   * the document could be returned in search results. This field is
   * OUTPUT_ONLY. If this field is not populated, it means the document has
   * never been indexed.
   *
   * @param string $indexTime
   */
  public function setIndexTime($indexTime)
  {
    $this->indexTime = $indexTime;
  }
  /**
   * @return string
   */
  public function getIndexTime()
  {
    return $this->indexTime;
  }
  /**
   * The JSON string representation of the document. It should conform to the
   * registered Schema or an `INVALID_ARGUMENT` error is thrown.
   *
   * @param string $jsonData
   */
  public function setJsonData($jsonData)
  {
    $this->jsonData = $jsonData;
  }
  /**
   * @return string
   */
  public function getJsonData()
  {
    return $this->jsonData;
  }
  /**
   * Immutable. The full resource name of the document. Format: `projects/{proje
   * ct}/locations/{location}/collections/{collection}/dataStores/{data_store}/b
   * ranches/{branch}/documents/{document_id}`. This field must be a UTF-8
   * encoded string with a length limit of 1024 characters.
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
   * The identifier of the parent document. Currently supports at most two level
   * document hierarchy. Id should conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length
   * limit of 63 characters.
   *
   * @param string $parentDocumentId
   */
  public function setParentDocumentId($parentDocumentId)
  {
    $this->parentDocumentId = $parentDocumentId;
  }
  /**
   * @return string
   */
  public function getParentDocumentId()
  {
    return $this->parentDocumentId;
  }
  /**
   * The identifier of the schema located in the same data store.
   *
   * @param string $schemaId
   */
  public function setSchemaId($schemaId)
  {
    $this->schemaId = $schemaId;
  }
  /**
   * @return string
   */
  public function getSchemaId()
  {
    return $this->schemaId;
  }
  /**
   * The structured JSON data for the document. It should conform to the
   * registered Schema or an `INVALID_ARGUMENT` error is thrown.
   *
   * @param array[] $structData
   */
  public function setStructData($structData)
  {
    $this->structData = $structData;
  }
  /**
   * @return array[]
   */
  public function getStructData()
  {
    return $this->structData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Document::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Document');
