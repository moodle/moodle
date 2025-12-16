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

namespace Google\Service\CloudNaturalLanguage;

class XPSConfusionMatrix extends \Google\Collection
{
  protected $collection_key = 'sentimentLabel';
  /**
   * For the following three repeated fields, only one is intended to be set.
   * annotation_spec_id_token is preferable to be set. ID tokens of the
   * annotation specs used in the confusion matrix.
   *
   * @var string[]
   */
  public $annotationSpecIdToken;
  /**
   * Category (mainly for segmentation). Set only for image segmentation models.
   * Note: uCAIP Image Segmentation should use annotation_spec_id_token.
   *
   * @var int[]
   */
  public $category;
  protected $rowType = XPSConfusionMatrixRow::class;
  protected $rowDataType = 'array';
  /**
   * Sentiment labels used in the confusion matrix. Set only for text sentiment
   * models. For AutoML Text Revamp, use `annotation_spec_id_token` instead and
   * leave this field empty.
   *
   * @var int[]
   */
  public $sentimentLabel;

  /**
   * For the following three repeated fields, only one is intended to be set.
   * annotation_spec_id_token is preferable to be set. ID tokens of the
   * annotation specs used in the confusion matrix.
   *
   * @param string[] $annotationSpecIdToken
   */
  public function setAnnotationSpecIdToken($annotationSpecIdToken)
  {
    $this->annotationSpecIdToken = $annotationSpecIdToken;
  }
  /**
   * @return string[]
   */
  public function getAnnotationSpecIdToken()
  {
    return $this->annotationSpecIdToken;
  }
  /**
   * Category (mainly for segmentation). Set only for image segmentation models.
   * Note: uCAIP Image Segmentation should use annotation_spec_id_token.
   *
   * @param int[] $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return int[]
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Rows in the confusion matrix. The number of rows is equal to the size of
   * `annotation_spec_id_token`. `row[i].value[j]` is the number of examples
   * that have ground truth of the `annotation_spec_id_token[i]` and are
   * predicted as `annotation_spec_id_token[j]` by the model being evaluated.
   *
   * @param XPSConfusionMatrixRow[] $row
   */
  public function setRow($row)
  {
    $this->row = $row;
  }
  /**
   * @return XPSConfusionMatrixRow[]
   */
  public function getRow()
  {
    return $this->row;
  }
  /**
   * Sentiment labels used in the confusion matrix. Set only for text sentiment
   * models. For AutoML Text Revamp, use `annotation_spec_id_token` instead and
   * leave this field empty.
   *
   * @param int[] $sentimentLabel
   */
  public function setSentimentLabel($sentimentLabel)
  {
    $this->sentimentLabel = $sentimentLabel;
  }
  /**
   * @return int[]
   */
  public function getSentimentLabel()
  {
    return $this->sentimentLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSConfusionMatrix::class, 'Google_Service_CloudNaturalLanguage_XPSConfusionMatrix');
