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

namespace Google\Service\Compute;

class MetadataFilter extends \Google\Collection
{
  /**
   * Specifies that all filterLabels must match for themetadataFilter to be
   * considered a match.
   */
  public const FILTER_MATCH_CRITERIA_MATCH_ALL = 'MATCH_ALL';
  /**
   * Specifies that any filterLabel must match for themetadataFilter to be
   * considered a match.
   */
  public const FILTER_MATCH_CRITERIA_MATCH_ANY = 'MATCH_ANY';
  /**
   * Indicates that the match criteria was not set. AmetadataFilter must never
   * be created with this value.
   */
  public const FILTER_MATCH_CRITERIA_NOT_SET = 'NOT_SET';
  protected $collection_key = 'filterLabels';
  protected $filterLabelsType = MetadataFilterLabelMatch::class;
  protected $filterLabelsDataType = 'array';
  /**
   * Specifies how individual filter label matches within the list of
   * filterLabels and contributes toward the overall metadataFilter match.
   *
   *  Supported values are:        - MATCH_ANY: at least one of the filterLabels
   * must have a matching label in the provided metadata.    - MATCH_ALL: all
   * filterLabels must have    matching labels in the provided metadata.
   *
   * @var string
   */
  public $filterMatchCriteria;

  /**
   * The list of label value pairs that must match labels in the provided
   * metadata based on filterMatchCriteria
   *
   * This list must not be empty and can have at the most 64 entries.
   *
   * @param MetadataFilterLabelMatch[] $filterLabels
   */
  public function setFilterLabels($filterLabels)
  {
    $this->filterLabels = $filterLabels;
  }
  /**
   * @return MetadataFilterLabelMatch[]
   */
  public function getFilterLabels()
  {
    return $this->filterLabels;
  }
  /**
   * Specifies how individual filter label matches within the list of
   * filterLabels and contributes toward the overall metadataFilter match.
   *
   *  Supported values are:        - MATCH_ANY: at least one of the filterLabels
   * must have a matching label in the provided metadata.    - MATCH_ALL: all
   * filterLabels must have    matching labels in the provided metadata.
   *
   * Accepted values: MATCH_ALL, MATCH_ANY, NOT_SET
   *
   * @param self::FILTER_MATCH_CRITERIA_* $filterMatchCriteria
   */
  public function setFilterMatchCriteria($filterMatchCriteria)
  {
    $this->filterMatchCriteria = $filterMatchCriteria;
  }
  /**
   * @return self::FILTER_MATCH_CRITERIA_*
   */
  public function getFilterMatchCriteria()
  {
    return $this->filterMatchCriteria;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetadataFilter::class, 'Google_Service_Compute_MetadataFilter');
