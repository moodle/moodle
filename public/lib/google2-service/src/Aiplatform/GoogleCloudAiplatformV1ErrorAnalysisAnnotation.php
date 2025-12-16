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

class GoogleCloudAiplatformV1ErrorAnalysisAnnotation extends \Google\Collection
{
  /**
   * Unspecified query type for model error analysis.
   */
  public const QUERY_TYPE_QUERY_TYPE_UNSPECIFIED = 'QUERY_TYPE_UNSPECIFIED';
  /**
   * Query similar samples across all classes in the dataset.
   */
  public const QUERY_TYPE_ALL_SIMILAR = 'ALL_SIMILAR';
  /**
   * Query similar samples from the same class of the input sample.
   */
  public const QUERY_TYPE_SAME_CLASS_SIMILAR = 'SAME_CLASS_SIMILAR';
  /**
   * Query dissimilar samples from the same class of the input sample.
   */
  public const QUERY_TYPE_SAME_CLASS_DISSIMILAR = 'SAME_CLASS_DISSIMILAR';
  protected $collection_key = 'attributedItems';
  protected $attributedItemsType = GoogleCloudAiplatformV1ErrorAnalysisAnnotationAttributedItem::class;
  protected $attributedItemsDataType = 'array';
  /**
   * The outlier score of this annotated item. Usually defined as the min of all
   * distances from attributed items.
   *
   * @var 
   */
  public $outlierScore;
  /**
   * The threshold used to determine if this annotation is an outlier or not.
   *
   * @var 
   */
  public $outlierThreshold;
  /**
   * The query type used for finding the attributed items.
   *
   * @var string
   */
  public $queryType;

  /**
   * Attributed items for a given annotation, typically representing neighbors
   * from the training sets constrained by the query type.
   *
   * @param GoogleCloudAiplatformV1ErrorAnalysisAnnotationAttributedItem[] $attributedItems
   */
  public function setAttributedItems($attributedItems)
  {
    $this->attributedItems = $attributedItems;
  }
  /**
   * @return GoogleCloudAiplatformV1ErrorAnalysisAnnotationAttributedItem[]
   */
  public function getAttributedItems()
  {
    return $this->attributedItems;
  }
  public function setOutlierScore($outlierScore)
  {
    $this->outlierScore = $outlierScore;
  }
  public function getOutlierScore()
  {
    return $this->outlierScore;
  }
  public function setOutlierThreshold($outlierThreshold)
  {
    $this->outlierThreshold = $outlierThreshold;
  }
  public function getOutlierThreshold()
  {
    return $this->outlierThreshold;
  }
  /**
   * The query type used for finding the attributed items.
   *
   * Accepted values: QUERY_TYPE_UNSPECIFIED, ALL_SIMILAR, SAME_CLASS_SIMILAR,
   * SAME_CLASS_DISSIMILAR
   *
   * @param self::QUERY_TYPE_* $queryType
   */
  public function setQueryType($queryType)
  {
    $this->queryType = $queryType;
  }
  /**
   * @return self::QUERY_TYPE_*
   */
  public function getQueryType()
  {
    return $this->queryType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ErrorAnalysisAnnotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ErrorAnalysisAnnotation');
