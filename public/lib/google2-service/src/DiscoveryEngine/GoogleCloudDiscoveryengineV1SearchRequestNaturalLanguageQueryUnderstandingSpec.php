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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SearchRequestNaturalLanguageQueryUnderstandingSpec extends \Google\Collection
{
  /**
   * `EXTRACTED_FILTER_BEHAVIOR_UNSPECIFIED` will use the default behavior for
   * extracted filters. For single datastore search, the default is to apply as
   * hard filters. For multi-datastore search, the default is to apply as soft
   * boosts.
   */
  public const EXTRACTED_FILTER_BEHAVIOR_EXTRACTED_FILTER_BEHAVIOR_UNSPECIFIED = 'EXTRACTED_FILTER_BEHAVIOR_UNSPECIFIED';
  /**
   * Applies all extracted filters as hard filters on the results. Results that
   * do not pass the extracted filters will not be returned in the result set.
   */
  public const EXTRACTED_FILTER_BEHAVIOR_HARD_FILTER = 'HARD_FILTER';
  /**
   * Applies all extracted filters as soft boosts. Results that pass the filters
   * will be boosted up to higher ranks in the result set.
   */
  public const EXTRACTED_FILTER_BEHAVIOR_SOFT_BOOST = 'SOFT_BOOST';
  /**
   * Server behavior defaults to `DISABLED`.
   */
  public const FILTER_EXTRACTION_CONDITION_CONDITION_UNSPECIFIED = 'CONDITION_UNSPECIFIED';
  /**
   * Disables NL filter extraction.
   */
  public const FILTER_EXTRACTION_CONDITION_DISABLED = 'DISABLED';
  /**
   * Enables NL filter extraction.
   */
  public const FILTER_EXTRACTION_CONDITION_ENABLED = 'ENABLED';
  protected $collection_key = 'geoSearchQueryDetectionFieldNames';
  /**
   * Optional. Allowlist of fields that can be used for natural language filter
   * extraction. By default, if this is unspecified, all indexable fields are
   * eligible for natural language filter extraction (but are not guaranteed to
   * be used). If any fields are specified in allowed_field_names, only the
   * fields that are both marked as indexable in the schema and specified in the
   * allowlist will be eligible for natural language filter extraction. Note:
   * for multi-datastore search, this is not yet supported, and will be ignored.
   *
   * @var string[]
   */
  public $allowedFieldNames;
  /**
   * Optional. Controls behavior of how extracted filters are applied to the
   * search. The default behavior depends on the request. For single datastore
   * structured search, the default is `HARD_FILTER`. For multi-datastore
   * search, the default behavior is `SOFT_BOOST`. Location-based filters are
   * always applied as hard filters, and the `SOFT_BOOST` setting will not
   * affect them. This field is only used if SearchRequest.natural_language_quer
   * y_understanding_spec.filter_extraction_condition is set to
   * FilterExtractionCondition.ENABLED.
   *
   * @var string
   */
  public $extractedFilterBehavior;
  /**
   * The condition under which filter extraction should occur. Server behavior
   * defaults to `DISABLED`.
   *
   * @var string
   */
  public $filterExtractionCondition;
  /**
   * Field names used for location-based filtering, where geolocation filters
   * are detected in natural language search queries. Only valid when the
   * FilterExtractionCondition is set to `ENABLED`. If this field is set, it
   * overrides the field names set in
   * ServingConfig.geo_search_query_detection_field_names.
   *
   * @var string[]
   */
  public $geoSearchQueryDetectionFieldNames;

  /**
   * Optional. Allowlist of fields that can be used for natural language filter
   * extraction. By default, if this is unspecified, all indexable fields are
   * eligible for natural language filter extraction (but are not guaranteed to
   * be used). If any fields are specified in allowed_field_names, only the
   * fields that are both marked as indexable in the schema and specified in the
   * allowlist will be eligible for natural language filter extraction. Note:
   * for multi-datastore search, this is not yet supported, and will be ignored.
   *
   * @param string[] $allowedFieldNames
   */
  public function setAllowedFieldNames($allowedFieldNames)
  {
    $this->allowedFieldNames = $allowedFieldNames;
  }
  /**
   * @return string[]
   */
  public function getAllowedFieldNames()
  {
    return $this->allowedFieldNames;
  }
  /**
   * Optional. Controls behavior of how extracted filters are applied to the
   * search. The default behavior depends on the request. For single datastore
   * structured search, the default is `HARD_FILTER`. For multi-datastore
   * search, the default behavior is `SOFT_BOOST`. Location-based filters are
   * always applied as hard filters, and the `SOFT_BOOST` setting will not
   * affect them. This field is only used if SearchRequest.natural_language_quer
   * y_understanding_spec.filter_extraction_condition is set to
   * FilterExtractionCondition.ENABLED.
   *
   * Accepted values: EXTRACTED_FILTER_BEHAVIOR_UNSPECIFIED, HARD_FILTER,
   * SOFT_BOOST
   *
   * @param self::EXTRACTED_FILTER_BEHAVIOR_* $extractedFilterBehavior
   */
  public function setExtractedFilterBehavior($extractedFilterBehavior)
  {
    $this->extractedFilterBehavior = $extractedFilterBehavior;
  }
  /**
   * @return self::EXTRACTED_FILTER_BEHAVIOR_*
   */
  public function getExtractedFilterBehavior()
  {
    return $this->extractedFilterBehavior;
  }
  /**
   * The condition under which filter extraction should occur. Server behavior
   * defaults to `DISABLED`.
   *
   * Accepted values: CONDITION_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::FILTER_EXTRACTION_CONDITION_* $filterExtractionCondition
   */
  public function setFilterExtractionCondition($filterExtractionCondition)
  {
    $this->filterExtractionCondition = $filterExtractionCondition;
  }
  /**
   * @return self::FILTER_EXTRACTION_CONDITION_*
   */
  public function getFilterExtractionCondition()
  {
    return $this->filterExtractionCondition;
  }
  /**
   * Field names used for location-based filtering, where geolocation filters
   * are detected in natural language search queries. Only valid when the
   * FilterExtractionCondition is set to `ENABLED`. If this field is set, it
   * overrides the field names set in
   * ServingConfig.geo_search_query_detection_field_names.
   *
   * @param string[] $geoSearchQueryDetectionFieldNames
   */
  public function setGeoSearchQueryDetectionFieldNames($geoSearchQueryDetectionFieldNames)
  {
    $this->geoSearchQueryDetectionFieldNames = $geoSearchQueryDetectionFieldNames;
  }
  /**
   * @return string[]
   */
  public function getGeoSearchQueryDetectionFieldNames()
  {
    return $this->geoSearchQueryDetectionFieldNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestNaturalLanguageQueryUnderstandingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestNaturalLanguageQueryUnderstandingSpec');
