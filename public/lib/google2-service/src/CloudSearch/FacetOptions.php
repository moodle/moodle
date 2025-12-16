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

namespace Google\Service\CloudSearch;

class FacetOptions extends \Google\Model
{
  protected $integerFacetingOptionsType = IntegerFacetingOptions::class;
  protected $integerFacetingOptionsDataType = '';
  /**
   * Maximum number of facet buckets that should be returned for this facet.
   * Defaults to 10. Maximum value is 100.
   *
   * @var int
   */
  public $numFacetBuckets;
  /**
   * If object_type is set, only those objects of that type will be used to
   * compute facets. If empty, then all objects will be used to compute facets.
   *
   * @var string
   */
  public $objectType;
  /**
   * The name of the operator chosen for faceting. @see
   * cloudsearch.SchemaPropertyOptions
   *
   * @var string
   */
  public $operatorName;
  /**
   * Source name to facet on. Format: datasources/{source_id} If empty, all data
   * sources will be used.
   *
   * @var string
   */
  public $sourceName;

  /**
   * If set, describes integer faceting options for the given integer property.
   * The corresponding integer property in the schema should be marked
   * isFacetable. The number of buckets returned would be minimum of this and
   * num_facet_buckets.
   *
   * @param IntegerFacetingOptions $integerFacetingOptions
   */
  public function setIntegerFacetingOptions(IntegerFacetingOptions $integerFacetingOptions)
  {
    $this->integerFacetingOptions = $integerFacetingOptions;
  }
  /**
   * @return IntegerFacetingOptions
   */
  public function getIntegerFacetingOptions()
  {
    return $this->integerFacetingOptions;
  }
  /**
   * Maximum number of facet buckets that should be returned for this facet.
   * Defaults to 10. Maximum value is 100.
   *
   * @param int $numFacetBuckets
   */
  public function setNumFacetBuckets($numFacetBuckets)
  {
    $this->numFacetBuckets = $numFacetBuckets;
  }
  /**
   * @return int
   */
  public function getNumFacetBuckets()
  {
    return $this->numFacetBuckets;
  }
  /**
   * If object_type is set, only those objects of that type will be used to
   * compute facets. If empty, then all objects will be used to compute facets.
   *
   * @param string $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return string
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * The name of the operator chosen for faceting. @see
   * cloudsearch.SchemaPropertyOptions
   *
   * @param string $operatorName
   */
  public function setOperatorName($operatorName)
  {
    $this->operatorName = $operatorName;
  }
  /**
   * @return string
   */
  public function getOperatorName()
  {
    return $this->operatorName;
  }
  /**
   * Source name to facet on. Format: datasources/{source_id} If empty, all data
   * sources will be used.
   *
   * @param string $sourceName
   */
  public function setSourceName($sourceName)
  {
    $this->sourceName = $sourceName;
  }
  /**
   * @return string
   */
  public function getSourceName()
  {
    return $this->sourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FacetOptions::class, 'Google_Service_CloudSearch_FacetOptions');
