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

class XPSVisionErrorAnalysisConfig extends \Google\Model
{
  /**
   * Unspecified query type for model error analysis.
   */
  public const QUERY_TYPE_QUERY_TYPE_UNSPECIFIED = 'QUERY_TYPE_UNSPECIFIED';
  /**
   * Query similar samples across all classes in the dataset.
   */
  public const QUERY_TYPE_QUERY_TYPE_ALL_SIMILAR = 'QUERY_TYPE_ALL_SIMILAR';
  /**
   * Query similar samples from the same class of the input sample.
   */
  public const QUERY_TYPE_QUERY_TYPE_SAME_CLASS_SIMILAR = 'QUERY_TYPE_SAME_CLASS_SIMILAR';
  /**
   * Query dissimilar samples from the same class of the input sample.
   */
  public const QUERY_TYPE_QUERY_TYPE_SAME_CLASS_DISSIMILAR = 'QUERY_TYPE_SAME_CLASS_DISSIMILAR';
  /**
   * The number of query examples in error analysis.
   *
   * @var int
   */
  public $exampleCount;
  /**
   * The query type used in retrieval. The enum values are frozen in the
   * foreseeable future.
   *
   * @var string
   */
  public $queryType;

  /**
   * The number of query examples in error analysis.
   *
   * @param int $exampleCount
   */
  public function setExampleCount($exampleCount)
  {
    $this->exampleCount = $exampleCount;
  }
  /**
   * @return int
   */
  public function getExampleCount()
  {
    return $this->exampleCount;
  }
  /**
   * The query type used in retrieval. The enum values are frozen in the
   * foreseeable future.
   *
   * Accepted values: QUERY_TYPE_UNSPECIFIED, QUERY_TYPE_ALL_SIMILAR,
   * QUERY_TYPE_SAME_CLASS_SIMILAR, QUERY_TYPE_SAME_CLASS_DISSIMILAR
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
class_alias(XPSVisionErrorAnalysisConfig::class, 'Google_Service_CloudNaturalLanguage_XPSVisionErrorAnalysisConfig');
