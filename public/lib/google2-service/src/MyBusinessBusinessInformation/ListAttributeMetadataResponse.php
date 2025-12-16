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

namespace Google\Service\MyBusinessBusinessInformation;

class ListAttributeMetadataResponse extends \Google\Collection
{
  protected $collection_key = 'attributeMetadata';
  protected $attributeMetadataType = AttributeMetadata::class;
  protected $attributeMetadataDataType = 'array';
  /**
   * If the number of attributes exceeded the requested page size, this field
   * will be populated with a token to fetch the next page of attributes on a
   * subsequent call to `attributes.list`. If there are no more attributes, this
   * field will not be present in the response.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * A collection of attribute metadata for the available attributes.
   *
   * @param AttributeMetadata[] $attributeMetadata
   */
  public function setAttributeMetadata($attributeMetadata)
  {
    $this->attributeMetadata = $attributeMetadata;
  }
  /**
   * @return AttributeMetadata[]
   */
  public function getAttributeMetadata()
  {
    return $this->attributeMetadata;
  }
  /**
   * If the number of attributes exceeded the requested page size, this field
   * will be populated with a token to fetch the next page of attributes on a
   * subsequent call to `attributes.list`. If there are no more attributes, this
   * field will not be present in the response.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAttributeMetadataResponse::class, 'Google_Service_MyBusinessBusinessInformation_ListAttributeMetadataResponse');
