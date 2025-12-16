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

class GoogleCloudAiplatformV1DataItemView extends \Google\Collection
{
  protected $collection_key = 'annotations';
  protected $annotationsType = GoogleCloudAiplatformV1Annotation::class;
  protected $annotationsDataType = 'array';
  protected $dataItemType = GoogleCloudAiplatformV1DataItem::class;
  protected $dataItemDataType = '';
  /**
   * True if and only if the Annotations field has been truncated. It happens if
   * more Annotations for this DataItem met the request's annotation_filter than
   * are allowed to be returned by annotations_limit. Note that if Annotations
   * field is not being returned due to field mask, then this field will not be
   * set to true no matter how many Annotations are there.
   *
   * @var bool
   */
  public $hasTruncatedAnnotations;

  /**
   * The Annotations on the DataItem. If too many Annotations should be returned
   * for the DataItem, this field will be truncated per annotations_limit in
   * request. If it was, then the has_truncated_annotations will be set to true.
   *
   * @param GoogleCloudAiplatformV1Annotation[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return GoogleCloudAiplatformV1Annotation[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * The DataItem.
   *
   * @param GoogleCloudAiplatformV1DataItem $dataItem
   */
  public function setDataItem(GoogleCloudAiplatformV1DataItem $dataItem)
  {
    $this->dataItem = $dataItem;
  }
  /**
   * @return GoogleCloudAiplatformV1DataItem
   */
  public function getDataItem()
  {
    return $this->dataItem;
  }
  /**
   * True if and only if the Annotations field has been truncated. It happens if
   * more Annotations for this DataItem met the request's annotation_filter than
   * are allowed to be returned by annotations_limit. Note that if Annotations
   * field is not being returned due to field mask, then this field will not be
   * set to true no matter how many Annotations are there.
   *
   * @param bool $hasTruncatedAnnotations
   */
  public function setHasTruncatedAnnotations($hasTruncatedAnnotations)
  {
    $this->hasTruncatedAnnotations = $hasTruncatedAnnotations;
  }
  /**
   * @return bool
   */
  public function getHasTruncatedAnnotations()
  {
    return $this->hasTruncatedAnnotations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DataItemView::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DataItemView');
