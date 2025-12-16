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

namespace Google\Service\PagespeedInsights;

class PagespeedApiLoadingExperienceV5 extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "initialUrl" => "initial_url",
        "originFallback" => "origin_fallback",
        "overallCategory" => "overall_category",
  ];
  /**
   * The url, pattern or origin which the metrics are on.
   *
   * @var string
   */
  public $id;
  /**
   * The requested URL, which may differ from the resolved "id".
   *
   * @var string
   */
  public $initialUrl;
  protected $metricsType = UserPageLoadMetricV5::class;
  protected $metricsDataType = 'map';
  /**
   * True if the result is an origin fallback from a page, false otherwise.
   *
   * @var bool
   */
  public $originFallback;
  /**
   * The human readable speed "category" of the id.
   *
   * @var string
   */
  public $overallCategory;

  /**
   * The url, pattern or origin which the metrics are on.
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
   * The requested URL, which may differ from the resolved "id".
   *
   * @param string $initialUrl
   */
  public function setInitialUrl($initialUrl)
  {
    $this->initialUrl = $initialUrl;
  }
  /**
   * @return string
   */
  public function getInitialUrl()
  {
    return $this->initialUrl;
  }
  /**
   * The map of .
   *
   * @param UserPageLoadMetricV5[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return UserPageLoadMetricV5[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * True if the result is an origin fallback from a page, false otherwise.
   *
   * @param bool $originFallback
   */
  public function setOriginFallback($originFallback)
  {
    $this->originFallback = $originFallback;
  }
  /**
   * @return bool
   */
  public function getOriginFallback()
  {
    return $this->originFallback;
  }
  /**
   * The human readable speed "category" of the id.
   *
   * @param string $overallCategory
   */
  public function setOverallCategory($overallCategory)
  {
    $this->overallCategory = $overallCategory;
  }
  /**
   * @return string
   */
  public function getOverallCategory()
  {
    return $this->overallCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PagespeedApiLoadingExperienceV5::class, 'Google_Service_PagespeedInsights_PagespeedApiLoadingExperienceV5');
