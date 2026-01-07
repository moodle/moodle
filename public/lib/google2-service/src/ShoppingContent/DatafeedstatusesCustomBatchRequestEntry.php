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

namespace Google\Service\ShoppingContent;

class DatafeedstatusesCustomBatchRequestEntry extends \Google\Model
{
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  /**
   * Deprecated. Use `feedLabel` instead. The country to get the datafeed status
   * for. If this parameter is provided, then `language` must also be provided.
   * Note that for multi-target datafeeds this parameter is required.
   *
   * @var string
   */
  public $country;
  /**
   * The ID of the data feed to get.
   *
   * @var string
   */
  public $datafeedId;
  /**
   * The feed label to get the datafeed status for. If this parameter is
   * provided, then `language` must also be provided. Note that for multi-target
   * datafeeds this parameter is required.
   *
   * @var string
   */
  public $feedLabel;
  /**
   * The language to get the datafeed status for. If this parameter is provided
   * then `country` must also be provided. Note that for multi-target datafeeds
   * this parameter is required.
   *
   * @var string
   */
  public $language;
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`get`"
   *
   * @var string
   */
  public $method;

  /**
   * An entry ID, unique within the batch request.
   *
   * @param string $batchId
   */
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  /**
   * @return string
   */
  public function getBatchId()
  {
    return $this->batchId;
  }
  /**
   * Deprecated. Use `feedLabel` instead. The country to get the datafeed status
   * for. If this parameter is provided, then `language` must also be provided.
   * Note that for multi-target datafeeds this parameter is required.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The ID of the data feed to get.
   *
   * @param string $datafeedId
   */
  public function setDatafeedId($datafeedId)
  {
    $this->datafeedId = $datafeedId;
  }
  /**
   * @return string
   */
  public function getDatafeedId()
  {
    return $this->datafeedId;
  }
  /**
   * The feed label to get the datafeed status for. If this parameter is
   * provided, then `language` must also be provided. Note that for multi-target
   * datafeeds this parameter is required.
   *
   * @param string $feedLabel
   */
  public function setFeedLabel($feedLabel)
  {
    $this->feedLabel = $feedLabel;
  }
  /**
   * @return string
   */
  public function getFeedLabel()
  {
    return $this->feedLabel;
  }
  /**
   * The language to get the datafeed status for. If this parameter is provided
   * then `country` must also be provided. Note that for multi-target datafeeds
   * this parameter is required.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The ID of the managing account.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * The method of the batch entry. Acceptable values are: - "`get`"
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatafeedstatusesCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_DatafeedstatusesCustomBatchRequestEntry');
