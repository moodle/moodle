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

class Annotation extends \Google\Collection
{
  protected $collection_key = 'pageIds';
  /**
   * Anchor text after excerpt. For requests, if the user bookmarked a screen
   * that has no flowing text on it, then this field should be empty.
   *
   * @var string
   */
  public $afterSelectedText;
  /**
   * Anchor text before excerpt. For requests, if the user bookmarked a screen
   * that has no flowing text on it, then this field should be empty.
   *
   * @var string
   */
  public $beforeSelectedText;
  protected $clientVersionRangesType = AnnotationClientVersionRanges::class;
  protected $clientVersionRangesDataType = '';
  /**
   * Timestamp for the created time of this annotation.
   *
   * @var string
   */
  public $created;
  protected $currentVersionRangesType = AnnotationCurrentVersionRanges::class;
  protected $currentVersionRangesDataType = '';
  /**
   * User-created data for this annotation.
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
   * The highlight style for this annotation.
   *
   * @var string
   */
  public $highlightStyle;
  /**
   * Id of this annotation, in the form of a GUID.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * The layer this annotation is for.
   *
   * @var string
   */
  public $layerId;
  protected $layerSummaryType = AnnotationLayerSummary::class;
  protected $layerSummaryDataType = '';
  /**
   * Pages that this annotation spans.
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
   * Timestamp for the last time this annotation was modified.
   *
   * @var string
   */
  public $updated;
  /**
   * The volume that this annotation belongs to.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Anchor text after excerpt. For requests, if the user bookmarked a screen
   * that has no flowing text on it, then this field should be empty.
   *
   * @param string $afterSelectedText
   */
  public function setAfterSelectedText($afterSelectedText)
  {
    $this->afterSelectedText = $afterSelectedText;
  }
  /**
   * @return string
   */
  public function getAfterSelectedText()
  {
    return $this->afterSelectedText;
  }
  /**
   * Anchor text before excerpt. For requests, if the user bookmarked a screen
   * that has no flowing text on it, then this field should be empty.
   *
   * @param string $beforeSelectedText
   */
  public function setBeforeSelectedText($beforeSelectedText)
  {
    $this->beforeSelectedText = $beforeSelectedText;
  }
  /**
   * @return string
   */
  public function getBeforeSelectedText()
  {
    return $this->beforeSelectedText;
  }
  /**
   * Selection ranges sent from the client.
   *
   * @param AnnotationClientVersionRanges $clientVersionRanges
   */
  public function setClientVersionRanges(AnnotationClientVersionRanges $clientVersionRanges)
  {
    $this->clientVersionRanges = $clientVersionRanges;
  }
  /**
   * @return AnnotationClientVersionRanges
   */
  public function getClientVersionRanges()
  {
    return $this->clientVersionRanges;
  }
  /**
   * Timestamp for the created time of this annotation.
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
   * Selection ranges for the most recent content version.
   *
   * @param AnnotationCurrentVersionRanges $currentVersionRanges
   */
  public function setCurrentVersionRanges(AnnotationCurrentVersionRanges $currentVersionRanges)
  {
    $this->currentVersionRanges = $currentVersionRanges;
  }
  /**
   * @return AnnotationCurrentVersionRanges
   */
  public function getCurrentVersionRanges()
  {
    return $this->currentVersionRanges;
  }
  /**
   * User-created data for this annotation.
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
   * The highlight style for this annotation.
   *
   * @param string $highlightStyle
   */
  public function setHighlightStyle($highlightStyle)
  {
    $this->highlightStyle = $highlightStyle;
  }
  /**
   * @return string
   */
  public function getHighlightStyle()
  {
    return $this->highlightStyle;
  }
  /**
   * Id of this annotation, in the form of a GUID.
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
   * Resource type.
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
   * The layer this annotation is for.
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
   * @param AnnotationLayerSummary $layerSummary
   */
  public function setLayerSummary(AnnotationLayerSummary $layerSummary)
  {
    $this->layerSummary = $layerSummary;
  }
  /**
   * @return AnnotationLayerSummary
   */
  public function getLayerSummary()
  {
    return $this->layerSummary;
  }
  /**
   * Pages that this annotation spans.
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
   * Timestamp for the last time this annotation was modified.
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
   * The volume that this annotation belongs to.
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
class_alias(Annotation::class, 'Google_Service_Books_Annotation');
