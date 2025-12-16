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

class Metadata extends \Google\Collection
{
  protected $collection_key = 'fields';
  /**
   * The creation time for this document or object in the search result.
   *
   * @var string
   */
  public $createTime;
  protected $displayOptionsType = ResultDisplayMetadata::class;
  protected $displayOptionsDataType = '';
  protected $fieldsType = NamedProperty::class;
  protected $fieldsDataType = 'array';
  /**
   * Mime type of the search result.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Object type of the search result.
   *
   * @var string
   */
  public $objectType;
  protected $ownerType = Person::class;
  protected $ownerDataType = '';
  protected $sourceType = Source::class;
  protected $sourceDataType = '';
  /**
   * The thumbnail URL of the result.
   *
   * @var string
   */
  public $thumbnailUrl;
  /**
   * The last modified date for the object in the search result. If not set in
   * the item, the value returned here is empty. When `updateTime` is used for
   * calculating freshness and is not set, this value defaults to 2 years from
   * the current time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The creation time for this document or object in the search result.
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
   * Options that specify how to display a structured data search result.
   *
   * @param ResultDisplayMetadata $displayOptions
   */
  public function setDisplayOptions(ResultDisplayMetadata $displayOptions)
  {
    $this->displayOptions = $displayOptions;
  }
  /**
   * @return ResultDisplayMetadata
   */
  public function getDisplayOptions()
  {
    return $this->displayOptions;
  }
  /**
   * Indexed fields in structured data, returned as a generic named property.
   *
   * @param NamedProperty[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return NamedProperty[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Mime type of the search result.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Object type of the search result.
   *
   * @param string $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return string
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * Owner (usually creator) of the document or object of the search result.
   *
   * @param Person $owner
   */
  public function setOwner(Person $owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return Person
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * The named source for the result, such as Gmail.
   *
   * @param Source $source
   */
  public function setSource(Source $source)
  {
    $this->source = $source;
  }
  /**
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The thumbnail URL of the result.
   *
   * @param string $thumbnailUrl
   */
  public function setThumbnailUrl($thumbnailUrl)
  {
    $this->thumbnailUrl = $thumbnailUrl;
  }
  /**
   * @return string
   */
  public function getThumbnailUrl()
  {
    return $this->thumbnailUrl;
  }
  /**
   * The last modified date for the object in the search result. If not set in
   * the item, the value returned here is empty. When `updateTime` is used for
   * calculating freshness and is not set, this value defaults to 2 years from
   * the current time.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metadata::class, 'Google_Service_CloudSearch_Metadata');
