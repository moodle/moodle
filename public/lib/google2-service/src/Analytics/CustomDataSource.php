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

namespace Google\Service\Analytics;

class CustomDataSource extends \Google\Collection
{
  protected $collection_key = 'schema';
  /**
   * Account ID to which this custom data source belongs.
   *
   * @var string
   */
  public $accountId;
  protected $childLinkType = CustomDataSourceChildLink::class;
  protected $childLinkDataType = '';
  /**
   * Time this custom data source was created.
   *
   * @var string
   */
  public $created;
  /**
   * Description of custom data source.
   *
   * @var string
   */
  public $description;
  /**
   * Custom data source ID.
   *
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $importBehavior;
  /**
   * Resource type for Analytics custom data source.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this custom data source.
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = CustomDataSourceParentLink::class;
  protected $parentLinkDataType = '';
  /**
   * IDs of views (profiles) linked to the custom data source.
   *
   * @var string[]
   */
  public $profilesLinked;
  /**
   * Collection of schema headers of the custom data source.
   *
   * @var string[]
   */
  public $schema;
  /**
   * Link for this Analytics custom data source.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Type of the custom data source.
   *
   * @var string
   */
  public $type;
  /**
   * Time this custom data source was last modified.
   *
   * @var string
   */
  public $updated;
  /**
   * Upload type of the custom data source.
   *
   * @var string
   */
  public $uploadType;
  /**
   * Web property ID of the form UA-XXXXX-YY to which this custom data source
   * belongs.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this custom data source belongs.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * @param CustomDataSourceChildLink $childLink
   */
  public function setChildLink(CustomDataSourceChildLink $childLink)
  {
    $this->childLink = $childLink;
  }
  /**
   * @return CustomDataSourceChildLink
   */
  public function getChildLink()
  {
    return $this->childLink;
  }
  /**
   * Time this custom data source was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Description of custom data source.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Custom data source ID.
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
   * @param string $importBehavior
   */
  public function setImportBehavior($importBehavior)
  {
    $this->importBehavior = $importBehavior;
  }
  /**
   * @return string
   */
  public function getImportBehavior()
  {
    return $this->importBehavior;
  }
  /**
   * Resource type for Analytics custom data source.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this custom data source.
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
   * Parent link for this custom data source. Points to the web property to
   * which this custom data source belongs.
   *
   * @param CustomDataSourceParentLink $parentLink
   */
  public function setParentLink(CustomDataSourceParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return CustomDataSourceParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * IDs of views (profiles) linked to the custom data source.
   *
   * @param string[] $profilesLinked
   */
  public function setProfilesLinked($profilesLinked)
  {
    $this->profilesLinked = $profilesLinked;
  }
  /**
   * @return string[]
   */
  public function getProfilesLinked()
  {
    return $this->profilesLinked;
  }
  /**
   * Collection of schema headers of the custom data source.
   *
   * @param string[] $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return string[]
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Link for this Analytics custom data source.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Type of the custom data source.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Time this custom data source was last modified.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Upload type of the custom data source.
   *
   * @param string $uploadType
   */
  public function setUploadType($uploadType)
  {
    $this->uploadType = $uploadType;
  }
  /**
   * @return string
   */
  public function getUploadType()
  {
    return $this->uploadType;
  }
  /**
   * Web property ID of the form UA-XXXXX-YY to which this custom data source
   * belongs.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomDataSource::class, 'Google_Service_Analytics_CustomDataSource');
