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

namespace Google\Service\CloudSearch;

class Item extends \Google\Model
{
  public const ITEM_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * An item that is indexed for the only purpose of serving information. These
   * items cannot be referred in containerName or inheritAclFrom fields.
   */
  public const ITEM_TYPE_CONTENT_ITEM = 'CONTENT_ITEM';
  /**
   * An item that gets indexed and whose purpose is to supply other items with
   * ACLs and/or contain other items.
   */
  public const ITEM_TYPE_CONTAINER_ITEM = 'CONTAINER_ITEM';
  /**
   * An item that does not get indexed, but otherwise has the same purpose as
   * CONTAINER_ITEM.
   */
  public const ITEM_TYPE_VIRTUAL_CONTAINER_ITEM = 'VIRTUAL_CONTAINER_ITEM';
  protected $aclType = ItemAcl::class;
  protected $aclDataType = '';
  protected $contentType = ItemContent::class;
  protected $contentDataType = '';
  /**
   * The type for this item.
   *
   * @var string
   */
  public $itemType;
  protected $metadataType = ItemMetadata::class;
  protected $metadataDataType = '';
  /**
   * The name of the Item. Format: datasources/{source_id}/items/{item_id} This
   * is a required field. The maximum length is 1536 characters.
   *
   * @var string
   */
  public $name;
  /**
   * Additional state connector can store for this item. The maximum length is
   * 10000 bytes.
   *
   * @var string
   */
  public $payload;
  /**
   * Queue this item belongs to. The maximum length is 100 characters.
   *
   * @var string
   */
  public $queue;
  protected $statusType = ItemStatus::class;
  protected $statusDataType = '';
  protected $structuredDataType = ItemStructuredData::class;
  protected $structuredDataDataType = '';
  /**
   * Required. The indexing system stores the version from the datasource as a
   * byte string and compares the Item version in the index to the version of
   * the queued Item using lexical ordering. Cloud Search Indexing won't index
   * or delete any queued item with a version value that is less than or equal
   * to the version of the currently indexed item. The maximum length for this
   * field is 1024 bytes. For information on how item version affects the
   * deletion process, refer to [Handle revisions after manual
   * deletes](https://developers.google.com/cloud-
   * search/docs/guides/operations).
   *
   * @var string
   */
  public $version;

  /**
   * Access control list for this item.
   *
   * @param ItemAcl $acl
   */
  public function setAcl(ItemAcl $acl)
  {
    $this->acl = $acl;
  }
  /**
   * @return ItemAcl
   */
  public function getAcl()
  {
    return $this->acl;
  }
  /**
   * Item content to be indexed and made text searchable.
   *
   * @param ItemContent $content
   */
  public function setContent(ItemContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return ItemContent
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The type for this item.
   *
   * Accepted values: UNSPECIFIED, CONTENT_ITEM, CONTAINER_ITEM,
   * VIRTUAL_CONTAINER_ITEM
   *
   * @param self::ITEM_TYPE_* $itemType
   */
  public function setItemType($itemType)
  {
    $this->itemType = $itemType;
  }
  /**
   * @return self::ITEM_TYPE_*
   */
  public function getItemType()
  {
    return $this->itemType;
  }
  /**
   * The metadata information.
   *
   * @param ItemMetadata $metadata
   */
  public function setMetadata(ItemMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return ItemMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The name of the Item. Format: datasources/{source_id}/items/{item_id} This
   * is a required field. The maximum length is 1536 characters.
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
   * Additional state connector can store for this item. The maximum length is
   * 10000 bytes.
   *
   * @param string $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return string
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Queue this item belongs to. The maximum length is 100 characters.
   *
   * @param string $queue
   */
  public function setQueue($queue)
  {
    $this->queue = $queue;
  }
  /**
   * @return string
   */
  public function getQueue()
  {
    return $this->queue;
  }
  /**
   * Status of the item. Output only field.
   *
   * @param ItemStatus $status
   */
  public function setStatus(ItemStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ItemStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The structured data for the item that should conform to a registered object
   * definition in the schema for the data source.
   *
   * @param ItemStructuredData $structuredData
   */
  public function setStructuredData(ItemStructuredData $structuredData)
  {
    $this->structuredData = $structuredData;
  }
  /**
   * @return ItemStructuredData
   */
  public function getStructuredData()
  {
    return $this->structuredData;
  }
  /**
   * Required. The indexing system stores the version from the datasource as a
   * byte string and compares the Item version in the index to the version of
   * the queued Item using lexical ordering. Cloud Search Indexing won't index
   * or delete any queued item with a version value that is less than or equal
   * to the version of the currently indexed item. The maximum length for this
   * field is 1024 bytes. For information on how item version affects the
   * deletion process, refer to [Handle revisions after manual
   * deletes](https://developers.google.com/cloud-
   * search/docs/guides/operations).
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Item::class, 'Google_Service_CloudSearch_Item');
