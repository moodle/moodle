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

class Volumeannotation extends \Google\Collection
{
  protected $collection_key = 'pageIds';
  /**
   * The annotation data id for this volume annotation.
   *
   * @var string
   */
  public $annotationDataId;
  /**
   * Link to get data for this annotation.
   *
   * @var string
   */
  public $annotationDataLink;
  /**
   * The type of annotation this is.
   *
   * @var string
   */
  public $annotationType;
  protected $contentRangesType = VolumeannotationContentRanges::class;
  protected $contentRangesDataType = '';
  /**
   * Data for this annotation.
   *
   * @var string
   */
  public $data;
  /**
   * Indicates that this annotation is deleted.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Unique id of this volume annotation.
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
   * The Layer this annotation is for.
   *
   * @var string
   */
  public $layerId;
  /**
   * Pages the annotation spans.
   *
   * @var string[]
   */
  public $pageIds;
  /**
   * Excerpt from the volume.
   *
   * @var string
   */
  public $selectedText;
  /**
   * URL to this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Timestamp for the last time this anntoation was updated. (RFC 3339 UTC
   * date-time format).
   *
   * @var string
   */
  public $updated;
  /**
   * The Volume this annotation is for.
   *
   * @var string
   */
  public $volumeId;

  /**
   * The annotation data id for this volume annotation.
   *
   * @param string $annotationDataId
   */
  public function setAnnotationDataId($annotationDataId)
  {
    $this->annotationDataId = $annotationDataId;
  }
  /**
   * @return string
   */
  public function getAnnotationDataId()
  {
    return $this->annotationDataId;
  }
  /**
   * Link to get data for this annotation.
   *
   * @param string $annotationDataLink
   */
  public function setAnnotationDataLink($annotationDataLink)
  {
    $this->annotationDataLink = $annotationDataLink;
  }
  /**
   * @return string
   */
  public function getAnnotationDataLink()
  {
    return $this->annotationDataLink;
  }
  /**
   * The type of annotation this is.
   *
   * @param string $annotationType
   */
  public function setAnnotationType($annotationType)
  {
    $this->annotationType = $annotationType;
  }
  /**
   * @return string
   */
  public function getAnnotationType()
  {
    return $this->annotationType;
  }
  /**
   * The content ranges to identify the selected text.
   *
   * @param VolumeannotationContentRanges $contentRanges
   */
  public function setContentRanges(VolumeannotationContentRanges $contentRanges)
  {
    $this->contentRanges = $contentRanges;
  }
  /**
   * @return VolumeannotationContentRanges
   */
  public function getContentRanges()
  {
    return $this->contentRanges;
  }
  /**
   * Data for this annotation.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Indicates that this annotation is deleted.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Unique id of this volume annotation.
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
   * The Layer this annotation is for.
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
   * Pages the annotation spans.
   *
   * @param string[] $pageIds
   */
  public function setPageIds($pageIds)
  {
    $this->pageIds = $pageIds;
  }
  /**
   * @return string[]
   */
  public function getPageIds()
  {
    return $this->pageIds;
  }
  /**
   * Excerpt from the volume.
   *
   * @param string $selectedText
   */
  public function setSelectedText($selectedText)
  {
    $this->selectedText = $selectedText;
  }
  /**
   * @return string
   */
  public function getSelectedText()
  {
    return $this->selectedText;
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
   * Timestamp for the last time this anntoation was updated. (RFC 3339 UTC
   * date-time format).
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
   * The Volume this annotation is for.
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
class_alias(Volumeannotation::class, 'Google_Service_Books_Volumeannotation');
