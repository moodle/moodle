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

namespace Google\Service\Dfareporting;

class ContentSource extends \Google\Model
{
  /**
   * The resource type is unspecified.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * The resource type is google spreadsheet.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_GOOGLE_SPREADSHEET = 'RESOURCE_TYPE_GOOGLE_SPREADSHEET';
  /**
   * The resource type is remote file.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_REMOTE_FILE = 'RESOURCE_TYPE_REMOTE_FILE';
  /**
   * Optional. The name of the content source. It is defaulted to content source
   * file name if not provided.
   *
   * @var string
   */
  public $contentSourceName;
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  protected $metaDataType = ContentSourceMetaData::class;
  protected $metaDataDataType = '';
  /**
   * Required. The link to the file of the content source.
   *
   * @var string
   */
  public $resourceLink;
  /**
   * Required. The resource type of the content source.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Optional. The name of the content source. It is defaulted to content source
   * file name if not provided.
   *
   * @param string $contentSourceName
   */
  public function setContentSourceName($contentSourceName)
  {
    $this->contentSourceName = $contentSourceName;
  }
  /**
   * @return string
   */
  public function getContentSourceName()
  {
    return $this->contentSourceName;
  }
  /**
   * Output only. The creation timestamp of the content source. This is a read-
   * only field.
   *
   * @param LastModifiedInfo $createInfo
   */
  public function setCreateInfo(LastModifiedInfo $createInfo)
  {
    $this->createInfo = $createInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreateInfo()
  {
    return $this->createInfo;
  }
  /**
   * Output only. The last modified timestamp of the content source. This is a
   * read-only field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Output only. Metadata of the content source. It contains the number of rows
   * and the column names from resource link. This is a read-only field.
   *
   * @param ContentSourceMetaData $metaData
   */
  public function setMetaData(ContentSourceMetaData $metaData)
  {
    $this->metaData = $metaData;
  }
  /**
   * @return ContentSourceMetaData
   */
  public function getMetaData()
  {
    return $this->metaData;
  }
  /**
   * Required. The link to the file of the content source.
   *
   * @param string $resourceLink
   */
  public function setResourceLink($resourceLink)
  {
    $this->resourceLink = $resourceLink;
  }
  /**
   * @return string
   */
  public function getResourceLink()
  {
    return $this->resourceLink;
  }
  /**
   * Required. The resource type of the content source.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED,
   * RESOURCE_TYPE_GOOGLE_SPREADSHEET, RESOURCE_TYPE_REMOTE_FILE
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentSource::class, 'Google_Service_Dfareporting_ContentSource');
