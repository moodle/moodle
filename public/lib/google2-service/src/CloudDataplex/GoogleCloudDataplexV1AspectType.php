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

class GoogleCloudDataplexV1AspectType extends \Google\Model
{
  /**
   * Denotes that the aspect contains only metadata.
   */
  public const DATA_CLASSIFICATION_DATA_CLASSIFICATION_UNSPECIFIED = 'DATA_CLASSIFICATION_UNSPECIFIED';
  /**
   * Metadata and data classification.
   */
  public const DATA_CLASSIFICATION_METADATA_AND_DATA = 'METADATA_AND_DATA';
  /**
   * The default value. It is set for resources that were not subject for
   * migration from Data Catalog service.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_UNSPECIFIED = 'TRANSFER_STATUS_UNSPECIFIED';
  /**
   * Indicates that a resource was migrated from Data Catalog service but it
   * hasn't been transferred yet. In particular the resource cannot be updated
   * from Dataplex API.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_MIGRATED = 'TRANSFER_STATUS_MIGRATED';
  /**
   * Indicates that a resource was transferred from Data Catalog service. The
   * resource can only be updated from Dataplex API.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_TRANSFERRED = 'TRANSFER_STATUS_TRANSFERRED';
  protected $authorizationType = GoogleCloudDataplexV1AspectTypeAuthorization::class;
  protected $authorizationDataType = '';
  /**
   * Output only. The time when the AspectType was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Immutable. Stores data classification of the aspect.
   *
   * @var string
   */
  public $dataClassification;
  /**
   * Optional. Description of the AspectType.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The service computes this checksum. The client may send it on update and
   * delete requests to ensure it has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the AspectType.
   *
   * @var string[]
   */
  public $labels;
  protected $metadataTemplateType = GoogleCloudDataplexV1AspectTypeMetadataTemplate::class;
  protected $metadataTemplateDataType = '';
  /**
   * Output only. The relative resource name of the AspectType, of the form: pro
   * jects/{project_number}/locations/{location_id}/aspectTypes/{aspect_type_id}
   * .
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Denotes the transfer status of the Aspect Type. It is
   * unspecified for Aspect Types created from Dataplex API.
   *
   * @var string
   */
  public $transferStatus;
  /**
   * Output only. System generated globally unique ID for the AspectType. If you
   * delete and recreate the AspectType with the same name, then this ID will be
   * different.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the AspectType was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Immutable. Defines the Authorization for this type.
   *
   * @param GoogleCloudDataplexV1AspectTypeAuthorization $authorization
   */
  public function setAuthorization(GoogleCloudDataplexV1AspectTypeAuthorization $authorization)
  {
    $this->authorization = $authorization;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeAuthorization
   */
  public function getAuthorization()
  {
    return $this->authorization;
  }
  /**
   * Output only. The time when the AspectType was created.
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
   * Optional. Immutable. Stores data classification of the aspect.
   *
   * Accepted values: DATA_CLASSIFICATION_UNSPECIFIED, METADATA_AND_DATA
   *
   * @param self::DATA_CLASSIFICATION_* $dataClassification
   */
  public function setDataClassification($dataClassification)
  {
    $this->dataClassification = $dataClassification;
  }
  /**
   * @return self::DATA_CLASSIFICATION_*
   */
  public function getDataClassification()
  {
    return $this->dataClassification;
  }
  /**
   * Optional. Description of the AspectType.
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
   * Optional. User friendly display name.
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
   * The service computes this checksum. The client may send it on update and
   * delete requests to ensure it has an up-to-date value before proceeding.
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
   * Optional. User-defined labels for the AspectType.
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
   * Required. MetadataTemplate of the aspect.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplate $metadataTemplate
   */
  public function setMetadataTemplate(GoogleCloudDataplexV1AspectTypeMetadataTemplate $metadataTemplate)
  {
    $this->metadataTemplate = $metadataTemplate;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplate
   */
  public function getMetadataTemplate()
  {
    return $this->metadataTemplate;
  }
  /**
   * Output only. The relative resource name of the AspectType, of the form: pro
   * jects/{project_number}/locations/{location_id}/aspectTypes/{aspect_type_id}
   * .
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
   * Output only. Denotes the transfer status of the Aspect Type. It is
   * unspecified for Aspect Types created from Dataplex API.
   *
   * Accepted values: TRANSFER_STATUS_UNSPECIFIED, TRANSFER_STATUS_MIGRATED,
   * TRANSFER_STATUS_TRANSFERRED
   *
   * @param self::TRANSFER_STATUS_* $transferStatus
   */
  public function setTransferStatus($transferStatus)
  {
    $this->transferStatus = $transferStatus;
  }
  /**
   * @return self::TRANSFER_STATUS_*
   */
  public function getTransferStatus()
  {
    return $this->transferStatus;
  }
  /**
   * Output only. System generated globally unique ID for the AspectType. If you
   * delete and recreate the AspectType with the same name, then this ID will be
   * different.
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
   * Output only. The time when the AspectType was last updated.
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
class_alias(GoogleCloudDataplexV1AspectType::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AspectType');
