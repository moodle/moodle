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

class ItemMetadata extends \Google\Collection
{
  protected $collection_key = 'keywords';
  /**
   * The name of the container for this item. Deletion of the container item
   * leads to automatic deletion of this item. Note: ACLs are not inherited from
   * a container item. To provide ACL inheritance for an item, use the
   * inheritAclFrom field. The maximum length is 1536 characters.
   *
   * @var string
   */
  public $containerName;
  /**
   * The BCP-47 language code for the item, such as "en-US" or "sr-Latn". For
   * more information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier. The maximum
   * length is 32 characters.
   *
   * @var string
   */
  public $contentLanguage;
  protected $contextAttributesType = ContextAttribute::class;
  protected $contextAttributesDataType = 'array';
  /**
   * The time when the item was created in the source repository.
   *
   * @var string
   */
  public $createTime;
  /**
   * Hashing value provided by the API caller. This can be used with the
   * items.push method to calculate modified state. The maximum length is 2048
   * characters.
   *
   * @var string
   */
  public $hash;
  protected $interactionsType = Interaction::class;
  protected $interactionsDataType = 'array';
  /**
   * Additional keywords or phrases that should match the item. Used internally
   * for user generated content. The maximum number of elements is 100. The
   * maximum length is 8192 characters.
   *
   * @var string[]
   */
  public $keywords;
  /**
   * The original mime-type of ItemContent.content in the source repository. The
   * maximum length is 256 characters.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The type of the item. This should correspond to the name of an object
   * definition in the schema registered for the data source. For example, if
   * the schema for the data source contains an object definition with name
   * 'document', then item indexing requests for objects of that type should set
   * objectType to 'document'. The maximum length is 256 characters.
   *
   * @var string
   */
  public $objectType;
  protected $searchQualityMetadataType = SearchQualityMetadata::class;
  protected $searchQualityMetadataDataType = '';
  /**
   * Link to the source repository serving the data. Seach results apply this
   * link to the title. Whitespace or special characters may cause Cloud Seach
   * result links to trigger a redirect notice; to avoid this, encode the URL.
   * The maximum length is 2048 characters.
   *
   * @var string
   */
  public $sourceRepositoryUrl;
  /**
   * The title of the item. If given, this will be the displayed title of the
   * Search result. The maximum length is 2048 characters.
   *
   * @var string
   */
  public $title;
  /**
   * The time when the item was last modified in the source repository.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The name of the container for this item. Deletion of the container item
   * leads to automatic deletion of this item. Note: ACLs are not inherited from
   * a container item. To provide ACL inheritance for an item, use the
   * inheritAclFrom field. The maximum length is 1536 characters.
   *
   * @param string $containerName
   */
  public function setContainerName($containerName)
  {
    $this->containerName = $containerName;
  }
  /**
   * @return string
   */
  public function getContainerName()
  {
    return $this->containerName;
  }
  /**
   * The BCP-47 language code for the item, such as "en-US" or "sr-Latn". For
   * more information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier. The maximum
   * length is 32 characters.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * A set of named attributes associated with the item. This can be used for
   * influencing the ranking of the item based on the context in the request.
   * The maximum number of elements is 10.
   *
   * @param ContextAttribute[] $contextAttributes
   */
  public function setContextAttributes($contextAttributes)
  {
    $this->contextAttributes = $contextAttributes;
  }
  /**
   * @return ContextAttribute[]
   */
  public function getContextAttributes()
  {
    return $this->contextAttributes;
  }
  /**
   * The time when the item was created in the source repository.
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
   * Hashing value provided by the API caller. This can be used with the
   * items.push method to calculate modified state. The maximum length is 2048
   * characters.
   *
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * A list of interactions for the item. Interactions are used to improve
   * Search quality, but are not exposed to end users. The maximum number of
   * elements is 1000.
   *
   * @param Interaction[] $interactions
   */
  public function setInteractions($interactions)
  {
    $this->interactions = $interactions;
  }
  /**
   * @return Interaction[]
   */
  public function getInteractions()
  {
    return $this->interactions;
  }
  /**
   * Additional keywords or phrases that should match the item. Used internally
   * for user generated content. The maximum number of elements is 100. The
   * maximum length is 8192 characters.
   *
   * @param string[] $keywords
   */
  public function setKeywords($keywords)
  {
    $this->keywords = $keywords;
  }
  /**
   * @return string[]
   */
  public function getKeywords()
  {
    return $this->keywords;
  }
  /**
   * The original mime-type of ItemContent.content in the source repository. The
   * maximum length is 256 characters.
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
   * The type of the item. This should correspond to the name of an object
   * definition in the schema registered for the data source. For example, if
   * the schema for the data source contains an object definition with name
   * 'document', then item indexing requests for objects of that type should set
   * objectType to 'document'. The maximum length is 256 characters.
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
   * Additional search quality metadata of the item
   *
   * @param SearchQualityMetadata $searchQualityMetadata
   */
  public function setSearchQualityMetadata(SearchQualityMetadata $searchQualityMetadata)
  {
    $this->searchQualityMetadata = $searchQualityMetadata;
  }
  /**
   * @return SearchQualityMetadata
   */
  public function getSearchQualityMetadata()
  {
    return $this->searchQualityMetadata;
  }
  /**
   * Link to the source repository serving the data. Seach results apply this
   * link to the title. Whitespace or special characters may cause Cloud Seach
   * result links to trigger a redirect notice; to avoid this, encode the URL.
   * The maximum length is 2048 characters.
   *
   * @param string $sourceRepositoryUrl
   */
  public function setSourceRepositoryUrl($sourceRepositoryUrl)
  {
    $this->sourceRepositoryUrl = $sourceRepositoryUrl;
  }
  /**
   * @return string
   */
  public function getSourceRepositoryUrl()
  {
    return $this->sourceRepositoryUrl;
  }
  /**
   * The title of the item. If given, this will be the displayed title of the
   * Search result. The maximum length is 2048 characters.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The time when the item was last modified in the source repository.
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
class_alias(ItemMetadata::class, 'Google_Service_CloudSearch_ItemMetadata');
