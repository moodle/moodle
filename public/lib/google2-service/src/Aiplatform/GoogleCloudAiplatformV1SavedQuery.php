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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SavedQuery extends \Google\Model
{
  /**
   * Output only. Filters on the Annotations in the dataset.
   *
   * @var string
   */
  public $annotationFilter;
  /**
   * Output only. Number of AnnotationSpecs in the context of the SavedQuery.
   *
   * @var int
   */
  public $annotationSpecCount;
  /**
   * Output only. Timestamp when this SavedQuery was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The user-defined name of the SavedQuery. The name can be up to
   * 128 characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Used to perform a consistent read-modify-write update. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Some additional information about the SavedQuery.
   *
   * @var array
   */
  public $metadata;
  /**
   * Output only. Resource name of the SavedQuery.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Problem type of the SavedQuery. Allowed values: *
   * IMAGE_CLASSIFICATION_SINGLE_LABEL * IMAGE_CLASSIFICATION_MULTI_LABEL *
   * IMAGE_BOUNDING_POLY * IMAGE_BOUNDING_BOX * TEXT_CLASSIFICATION_SINGLE_LABEL
   * * TEXT_CLASSIFICATION_MULTI_LABEL * TEXT_EXTRACTION * TEXT_SENTIMENT *
   * VIDEO_CLASSIFICATION * VIDEO_OBJECT_TRACKING
   *
   * @var string
   */
  public $problemType;
  /**
   * Output only. If the Annotations belonging to the SavedQuery can be used for
   * AutoML training.
   *
   * @var bool
   */
  public $supportAutomlTraining;
  /**
   * Output only. Timestamp when SavedQuery was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Filters on the Annotations in the dataset.
   *
   * @param string $annotationFilter
   */
  public function setAnnotationFilter($annotationFilter)
  {
    $this->annotationFilter = $annotationFilter;
  }
  /**
   * @return string
   */
  public function getAnnotationFilter()
  {
    return $this->annotationFilter;
  }
  /**
   * Output only. Number of AnnotationSpecs in the context of the SavedQuery.
   *
   * @param int $annotationSpecCount
   */
  public function setAnnotationSpecCount($annotationSpecCount)
  {
    $this->annotationSpecCount = $annotationSpecCount;
  }
  /**
   * @return int
   */
  public function getAnnotationSpecCount()
  {
    return $this->annotationSpecCount;
  }
  /**
   * Output only. Timestamp when this SavedQuery was created.
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
   * Required. The user-defined name of the SavedQuery. The name can be up to
   * 128 characters long and can consist of any UTF-8 characters.
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
   * Used to perform a consistent read-modify-write update. If not set, a blind
   * "overwrite" update happens.
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
   * Some additional information about the SavedQuery.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. Resource name of the SavedQuery.
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
   * Required. Problem type of the SavedQuery. Allowed values: *
   * IMAGE_CLASSIFICATION_SINGLE_LABEL * IMAGE_CLASSIFICATION_MULTI_LABEL *
   * IMAGE_BOUNDING_POLY * IMAGE_BOUNDING_BOX * TEXT_CLASSIFICATION_SINGLE_LABEL
   * * TEXT_CLASSIFICATION_MULTI_LABEL * TEXT_EXTRACTION * TEXT_SENTIMENT *
   * VIDEO_CLASSIFICATION * VIDEO_OBJECT_TRACKING
   *
   * @param string $problemType
   */
  public function setProblemType($problemType)
  {
    $this->problemType = $problemType;
  }
  /**
   * @return string
   */
  public function getProblemType()
  {
    return $this->problemType;
  }
  /**
   * Output only. If the Annotations belonging to the SavedQuery can be used for
   * AutoML training.
   *
   * @param bool $supportAutomlTraining
   */
  public function setSupportAutomlTraining($supportAutomlTraining)
  {
    $this->supportAutomlTraining = $supportAutomlTraining;
  }
  /**
   * @return bool
   */
  public function getSupportAutomlTraining()
  {
    return $this->supportAutomlTraining;
  }
  /**
   * Output only. Timestamp when SavedQuery was last updated.
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
class_alias(GoogleCloudAiplatformV1SavedQuery::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SavedQuery');
