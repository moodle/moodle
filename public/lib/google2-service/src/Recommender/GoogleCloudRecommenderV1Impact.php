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

namespace Google\Service\Recommender;

class GoogleCloudRecommenderV1Impact extends \Google\Collection
{
  /**
   * Default unspecified category. Don't use directly.
   */
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  /**
   * Indicates a potential increase or decrease in cost.
   */
  public const CATEGORY_COST = 'COST';
  /**
   * Indicates a potential increase or decrease in security.
   */
  public const CATEGORY_SECURITY = 'SECURITY';
  /**
   * Indicates a potential increase or decrease in performance.
   */
  public const CATEGORY_PERFORMANCE = 'PERFORMANCE';
  /**
   * Indicates a potential increase or decrease in manageability.
   */
  public const CATEGORY_MANAGEABILITY = 'MANAGEABILITY';
  /**
   * Indicates a potential increase or decrease in sustainability.
   */
  public const CATEGORY_SUSTAINABILITY = 'SUSTAINABILITY';
  /**
   * Indicates a potential increase or decrease in reliability.
   */
  public const CATEGORY_RELIABILITY = 'RELIABILITY';
  protected $collection_key = 'impactComponents';
  /**
   * Category that is being targeted.
   *
   * @var string
   */
  public $category;
  protected $costProjectionType = GoogleCloudRecommenderV1CostProjection::class;
  protected $costProjectionDataType = '';
  protected $impactComponentsType = GoogleCloudRecommenderV1Impact::class;
  protected $impactComponentsDataType = 'array';
  protected $reliabilityProjectionType = GoogleCloudRecommenderV1ReliabilityProjection::class;
  protected $reliabilityProjectionDataType = '';
  protected $securityProjectionType = GoogleCloudRecommenderV1SecurityProjection::class;
  protected $securityProjectionDataType = '';
  /**
   * The service that this impact is associated with.
   *
   * @var string
   */
  public $service;
  protected $sustainabilityProjectionType = GoogleCloudRecommenderV1SustainabilityProjection::class;
  protected $sustainabilityProjectionDataType = '';

  /**
   * Category that is being targeted.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, COST, SECURITY, PERFORMANCE,
   * MANAGEABILITY, SUSTAINABILITY, RELIABILITY
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Use with CategoryType.COST
   *
   * @param GoogleCloudRecommenderV1CostProjection $costProjection
   */
  public function setCostProjection(GoogleCloudRecommenderV1CostProjection $costProjection)
  {
    $this->costProjection = $costProjection;
  }
  /**
   * @return GoogleCloudRecommenderV1CostProjection
   */
  public function getCostProjection()
  {
    return $this->costProjection;
  }
  /**
   * If populated, the impact contains multiple components. In this case, the
   * top-level impact contains aggregated values and each component contains
   * per-service details.
   *
   * @param GoogleCloudRecommenderV1Impact[] $impactComponents
   */
  public function setImpactComponents($impactComponents)
  {
    $this->impactComponents = $impactComponents;
  }
  /**
   * @return GoogleCloudRecommenderV1Impact[]
   */
  public function getImpactComponents()
  {
    return $this->impactComponents;
  }
  /**
   * Use with CategoryType.RELIABILITY
   *
   * @param GoogleCloudRecommenderV1ReliabilityProjection $reliabilityProjection
   */
  public function setReliabilityProjection(GoogleCloudRecommenderV1ReliabilityProjection $reliabilityProjection)
  {
    $this->reliabilityProjection = $reliabilityProjection;
  }
  /**
   * @return GoogleCloudRecommenderV1ReliabilityProjection
   */
  public function getReliabilityProjection()
  {
    return $this->reliabilityProjection;
  }
  /**
   * Use with CategoryType.SECURITY
   *
   * @param GoogleCloudRecommenderV1SecurityProjection $securityProjection
   */
  public function setSecurityProjection(GoogleCloudRecommenderV1SecurityProjection $securityProjection)
  {
    $this->securityProjection = $securityProjection;
  }
  /**
   * @return GoogleCloudRecommenderV1SecurityProjection
   */
  public function getSecurityProjection()
  {
    return $this->securityProjection;
  }
  /**
   * The service that this impact is associated with.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Use with CategoryType.SUSTAINABILITY
   *
   * @param GoogleCloudRecommenderV1SustainabilityProjection $sustainabilityProjection
   */
  public function setSustainabilityProjection(GoogleCloudRecommenderV1SustainabilityProjection $sustainabilityProjection)
  {
    $this->sustainabilityProjection = $sustainabilityProjection;
  }
  /**
   * @return GoogleCloudRecommenderV1SustainabilityProjection
   */
  public function getSustainabilityProjection()
  {
    return $this->sustainabilityProjection;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommenderV1Impact::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1Impact');
