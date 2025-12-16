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

namespace Google\Service\Books;

class Layersummary extends \Google\Collection
{
  protected $collection_key = 'annotationTypes';
  /**
   * The number of annotations for this layer.
   *
   * @var int
   */
  public $annotationCount;
  /**
   * The list of annotation types contained for this layer.
   *
   * @var string[]
   */
  public $annotationTypes;
  /**
   * Link to get data for this annotation.
   *
   * @var string
   */
  public $annotationsDataLink;
  /**
   * The link to get the annotations for this layer.
   *
   * @var string
   */
  public $annotationsLink;
  /**
   * The content version this resource is for.
   *
   * @var string
   */
  public $contentVersion;
  /**
   * The number of data items for this layer.
   *
   * @var int
   */
  public $dataCount;
  /**
   * Unique id of this layer summary.
   *
   * @var string
   */
  public $id;
  /**
   * Resource Type
   *
   * @var string
   */
  public $kind;
  /**
   * The layer id for this summary.
   *
   * @var string
   */
  public $layerId;
  /**
   * URL to this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Timestamp for the last time an item in this layer was updated. (RFC 3339
   * UTC date-time format).
   *
   * @var string
   */
  public $updated;
  /**
   * The current version of this layer's volume annotations. Note that this
   * version applies only to the data in the books.layers.volumeAnnotations.*
   * responses. The actual annotation data is versioned separately.
   *
   * @var string
   */
  public $volumeAnnotationsVersion;
  /**
   * The volume id this resource is for.
   *
   * @var string
   */
  public $volumeId;

  /**
   * The number of annotations for this layer.
   *
   * @param int $annotationCount
   */
  public function setAnnotationCount($annotationCount)
  {
    $this->annotationCount = $annotationCount;
  }
  /**
   * @return int
   */
  public function getAnnotationCount()
  {
    return $this->annotationCount;
  }
  /**
   * The list of annotation types contained for this layer.
   *
   * @param string[] $annotationTypes
   */
  public function setAnnotationTypes($annotationTypes)
  {
    $this->annotationTypes = $annotationTypes;
  }
  /**
   * @return string[]
   */
  public function getAnnotationTypes()
  {
    return $this->annotationTypes;
  }
  /**
   * Link to get data for this annotation.
   *
   * @param string $annotationsDataLink
   */
  public function setAnnotationsDataLink($annotationsDataLink)
  {
    $this->annotationsDataLink = $annotationsDataLink;
  }
  /**
   * @return string
   */
  public function getAnnotationsDataLink()
  {
    return $this->annotationsDataLink;
  }
  /**
   * The link to get the annotations for this layer.
   *
   * @param string $annotationsLink
   */
  public function setAnnotationsLink($annotationsLink)
  {
    $this->annotationsLink = $annotationsLink;
  }
  /**
   * @return string
   */
  public function getAnnotationsLink()
  {
    return $this->annotationsLink;
  }
  /**
   * The content version this resource is for.
   *
   * @param string $contentVersion
   */
  public function setContentVersion($contentVersion)
  {
    $this->contentVersion = $contentVersion;
  }
  /**
   * @return string
   */
  public function getContentVersion()
  {
    return $this->contentVersion;
  }
  /**
   * The number of data items for this layer.
   *
   * @param int $dataCount
   */
  public function setDataCount($dataCount)
  {
    $this->dataCount = $dataCount;
  }
  /**
   * @return int
   */
  public function getDataCount()
  {
    return $this->dataCount;
  }
  /**
   * Unique id of this layer summary.
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
   * Resource Type
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
   * The layer id for this summary.
   *
   * @param string $layerId
   */
  public function setLayerId($layerId)
  {
    $this->layerId = $layerId;
  }
  /**
   * @return string
   */
  public function getLayerId()
  {
    return $this->layerId;
  }
  /**
   * URL to this resource.
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
   * Timestamp for the last time an item in this layer was updated. (RFC 3339
   * UTC date-time format).
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
   * The current version of this layer's volume annotations. Note that this
   * version applies only to the data in the books.layers.volumeAnnotations.*
   * responses. The actual annotation data is versioned separately.
   *
   * @param string $volumeAnnotationsVersion
   */
  public function setVolumeAnnotationsVersion($volumeAnnotationsVersion)
  {
    $this->volumeAnnotationsVersion = $volumeAnnotationsVersion;
  }
  /**
   * @return string
   */
  public function getVolumeAnnotationsVersion()
  {
    return $this->volumeAnnotationsVersion;
  }
  /**
   * The volume id this resource is for.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Layersummary::class, 'Google_Service_Books_Layersummary');
