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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProduct extends \Google\Collection
{
  protected $collection_key = 'ownerEmails';
  protected $accessGroupsType = GoogleCloudDataplexV1DataProductAccessGroup::class;
  protected $accessGroupsDataType = 'map';
  /**
   * Output only. Number of Data Assets associated with this Data Product.
   *
   * @var int
   */
  public $assetCount;
  /**
   * Output only. The time at which the Data Product was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the Data Product.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User-friendly display name of the Data Product.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Base64 encoded image representing the Data Product. Max Size:
   * 3.0MiB Expected image dimensions are 512x512 pixels, however the API only
   * performs validation on size of the encoded data. Note: For byte fields, the
   * content of the fields are base64-encoded (which increases the size of the
   * data by 33-36%) when using JSON on the wire.
   *
   * @var string
   */
  public $icon;
  /**
   * Optional. User-defined labels for the Data Product.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Resource name of the Data Product. Format: projects/{project_id
   * _or_number}/locations/{location_id}/dataProducts/{data_product_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Emails of the Data Product owners.
   *
   * @var string[]
   */
  public $ownerEmails;
  /**
   * Output only. System generated unique ID for the Data Product. This ID will
   * be different if the Data Product is deleted and re-created with the same
   * name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which the Data Product was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Data Product access groups by access group id as key. If Data
   * Product is used only for packaging Data Assets, then access groups may be
   * empty. However, if a Data Product is used for sharing Data Assets, then at
   * least one access group must be specified.
   *
   * @param GoogleCloudDataplexV1DataProductAccessGroup[] $accessGroups
   */
  public function setAccessGroups($accessGroups)
  {
    $this->accessGroups = $accessGroups;
  }
  /**
   * @return GoogleCloudDataplexV1DataProductAccessGroup[]
   */
  public function getAccessGroups()
  {
    return $this->accessGroups;
  }
  /**
   * Output only. Number of Data Assets associated with this Data Product.
   *
   * @param int $assetCount
   */
  public function setAssetCount($assetCount)
  {
    $this->assetCount = $assetCount;
  }
  /**
   * @return int
   */
  public function getAssetCount()
  {
    return $this->assetCount;
  }
  /**
   * Output only. The time at which the Data Product was created.
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
   * Optional. Description of the Data Product.
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
   * Required. User-friendly display name of the Data Product.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Base64 encoded image representing the Data Product. Max Size:
   * 3.0MiB Expected image dimensions are 512x512 pixels, however the API only
   * performs validation on size of the encoded data. Note: For byte fields, the
   * content of the fields are base64-encoded (which increases the size of the
   * data by 33-36%) when using JSON on the wire.
   *
   * @param string $icon
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return string
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * Optional. User-defined labels for the Data Product.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Resource name of the Data Product. Format: projects/{project_id
   * _or_number}/locations/{location_id}/dataProducts/{data_product_id}.
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
   * Required. Emails of the Data Product owners.
   *
   * @param string[] $ownerEmails
   */
  public function setOwnerEmails($ownerEmails)
  {
    $this->ownerEmails = $ownerEmails;
  }
  /**
   * @return string[]
   */
  public function getOwnerEmails()
  {
    return $this->ownerEmails;
  }
  /**
   * Output only. System generated unique ID for the Data Product. This ID will
   * be different if the Data Product is deleted and re-created with the same
   * name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time at which the Data Product was last updated.
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
class_alias(GoogleCloudDataplexV1DataProduct::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProduct');
