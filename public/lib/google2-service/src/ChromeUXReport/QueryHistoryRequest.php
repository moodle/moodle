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

namespace Google\Service\ChromeUXReport;

class QueryHistoryRequest extends \Google\Collection
{
  /**
   * The default value, representing all device classes.
   */
  public const FORM_FACTOR_ALL_FORM_FACTORS = 'ALL_FORM_FACTORS';
  /**
   * The device class representing a "mobile"/"phone" sized client.
   */
  public const FORM_FACTOR_PHONE = 'PHONE';
  /**
   * The device class representing a "desktop"/"laptop" type full size client.
   */
  public const FORM_FACTOR_DESKTOP = 'DESKTOP';
  /**
   * The device class representing a "tablet" type client.
   */
  public const FORM_FACTOR_TABLET = 'TABLET';
  protected $collection_key = 'metrics';
  /**
   * The number of collection periods to return. If not specified, the default
   * is 25. If present, must be in the range [1, 40].
   *
   * @var int
   */
  public $collectionPeriodCount;
  /**
   * The form factor is a query dimension that specifies the device class that
   * the record's data should belong to. Note: If no form factor is specified,
   * then a special record with aggregated data over all form factors will be
   * returned.
   *
   * @var string
   */
  public $formFactor;
  /**
   * The metrics that should be included in the response. If none are specified
   * then any metrics found will be returned. Allowed values:
   * ["first_contentful_paint", "first_input_delay", "largest_contentful_paint",
   * "cumulative_layout_shift", "experimental_time_to_first_byte",
   * "experimental_interaction_to_next_paint"]
   *
   * @var string[]
   */
  public $metrics;
  /**
   * The url pattern "origin" refers to a url pattern that is the origin of a
   * website. Examples: "https://example.com", "https://cloud.google.com"
   *
   * @var string
   */
  public $origin;
  /**
   * The url pattern "url" refers to a url pattern that is any arbitrary url.
   * Examples: "https://example.com/", "https://cloud.google.com/why-google-
   * cloud/"
   *
   * @var string
   */
  public $url;

  /**
   * The number of collection periods to return. If not specified, the default
   * is 25. If present, must be in the range [1, 40].
   *
   * @param int $collectionPeriodCount
   */
  public function setCollectionPeriodCount($collectionPeriodCount)
  {
    $this->collectionPeriodCount = $collectionPeriodCount;
  }
  /**
   * @return int
   */
  public function getCollectionPeriodCount()
  {
    return $this->collectionPeriodCount;
  }
  /**
   * The form factor is a query dimension that specifies the device class that
   * the record's data should belong to. Note: If no form factor is specified,
   * then a special record with aggregated data over all form factors will be
   * returned.
   *
   * Accepted values: ALL_FORM_FACTORS, PHONE, DESKTOP, TABLET
   *
   * @param self::FORM_FACTOR_* $formFactor
   */
  public function setFormFactor($formFactor)
  {
    $this->formFactor = $formFactor;
  }
  /**
   * @return self::FORM_FACTOR_*
   */
  public function getFormFactor()
  {
    return $this->formFactor;
  }
  /**
   * The metrics that should be included in the response. If none are specified
   * then any metrics found will be returned. Allowed values:
   * ["first_contentful_paint", "first_input_delay", "largest_contentful_paint",
   * "cumulative_layout_shift", "experimental_time_to_first_byte",
   * "experimental_interaction_to_next_paint"]
   *
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The url pattern "origin" refers to a url pattern that is the origin of a
   * website. Examples: "https://example.com", "https://cloud.google.com"
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * The url pattern "url" refers to a url pattern that is any arbitrary url.
   * Examples: "https://example.com/", "https://cloud.google.com/why-google-
   * cloud/"
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryHistoryRequest::class, 'Google_Service_ChromeUXReport_QueryHistoryRequest');
