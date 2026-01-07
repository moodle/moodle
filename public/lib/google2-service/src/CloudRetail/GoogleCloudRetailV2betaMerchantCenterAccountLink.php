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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2betaMerchantCenterAccountLink extends \Google\Collection
{
  protected $collection_key = 'feedFilters';
  /**
   * @var string
   */
  public $branchId;
  protected $feedFiltersType = GoogleCloudRetailV2betaMerchantCenterAccountLinkMerchantCenterFeedFilter::class;
  protected $feedFiltersDataType = 'array';
  /**
   * @var string
   */
  public $feedLabel;
  /**
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $languageCode;
  /**
   * @var string
   */
  public $merchantCenterAccountId;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $projectId;
  /**
   * @var string
   */
  public $source;
  /**
   * @var string
   */
  public $state;

  /**
   * @param string
   */
  public function setBranchId($branchId)
  {
    $this->branchId = $branchId;
  }
  /**
   * @return string
   */
  public function getBranchId()
  {
    return $this->branchId;
  }
  /**
   * @param GoogleCloudRetailV2betaMerchantCenterAccountLinkMerchantCenterFeedFilter[]
   */
  public function setFeedFilters($feedFilters)
  {
    $this->feedFilters = $feedFilters;
  }
  /**
   * @return GoogleCloudRetailV2betaMerchantCenterAccountLinkMerchantCenterFeedFilter[]
   */
  public function getFeedFilters()
  {
    return $this->feedFilters;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * @param string
   */
  public function setMerchantCenterAccountId($merchantCenterAccountId)
  {
    $this->merchantCenterAccountId = $merchantCenterAccountId;
  }
  /**
   * @return string
   */
  public function getMerchantCenterAccountId()
  {
    return $this->merchantCenterAccountId;
  }
  /**
   * @param string
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param string
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * @param string
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2betaMerchantCenterAccountLink::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2betaMerchantCenterAccountLink');
