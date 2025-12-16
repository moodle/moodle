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

class GoogleCloudRetailV2CompletionConfig extends \Google\Model
{
  protected $allowlistInputConfigType = GoogleCloudRetailV2CompletionDataInputConfig::class;
  protected $allowlistInputConfigDataType = '';
  /**
   * If set to true, the auto learning function is enabled. Auto learning uses
   * user data to generate suggestions using ML techniques. Default value is
   * false. Only after enabling auto learning can users use `cloud-retail` data
   * in CompleteQueryRequest.
   *
   * @var bool
   */
  public $autoLearning;
  protected $denylistInputConfigType = GoogleCloudRetailV2CompletionDataInputConfig::class;
  protected $denylistInputConfigDataType = '';
  /**
   * Output only. Name of the LRO corresponding to the latest allowlist import.
   * Can use GetOperation API to retrieve the latest state of the Long Running
   * Operation.
   *
   * @var string
   */
  public $lastAllowlistImportOperation;
  /**
   * Output only. Name of the LRO corresponding to the latest denylist import.
   * Can use GetOperation API to retrieve the latest state of the Long Running
   * Operation.
   *
   * @var string
   */
  public $lastDenylistImportOperation;
  /**
   * Output only. Name of the LRO corresponding to the latest suggestion terms
   * list import. Can use GetOperation API method to retrieve the latest state
   * of the Long Running Operation.
   *
   * @var string
   */
  public $lastSuggestionsImportOperation;
  /**
   * Specifies the matching order for autocomplete suggestions, e.g., a query
   * consisting of 'sh' with 'out-of-order' specified would suggest "women's
   * shoes", whereas a query of 'red s' with 'exact-prefix' specified would
   * suggest "red shoes". Currently supported values: * 'out-of-order' * 'exact-
   * prefix' Default value: 'exact-prefix'.
   *
   * @var string
   */
  public $matchingOrder;
  /**
   * The maximum number of autocomplete suggestions returned per term. Default
   * value is 20. If left unset or set to 0, then will fallback to default
   * value. Value range is 1 to 20.
   *
   * @var int
   */
  public $maxSuggestions;
  /**
   * The minimum number of characters needed to be typed in order to get
   * suggestions. Default value is 2. If left unset or set to 0, then will
   * fallback to default value. Value range is 1 to 20.
   *
   * @var int
   */
  public $minPrefixLength;
  /**
   * Required. Immutable. Fully qualified name
   * `projects/locations/catalogs/completionConfig`
   *
   * @var string
   */
  public $name;
  protected $suggestionsInputConfigType = GoogleCloudRetailV2CompletionDataInputConfig::class;
  protected $suggestionsInputConfigDataType = '';

  /**
   * Output only. The source data for the latest import of the autocomplete
   * allowlist phrases.
   *
   * @param GoogleCloudRetailV2CompletionDataInputConfig $allowlistInputConfig
   */
  public function setAllowlistInputConfig(GoogleCloudRetailV2CompletionDataInputConfig $allowlistInputConfig)
  {
    $this->allowlistInputConfig = $allowlistInputConfig;
  }
  /**
   * @return GoogleCloudRetailV2CompletionDataInputConfig
   */
  public function getAllowlistInputConfig()
  {
    return $this->allowlistInputConfig;
  }
  /**
   * If set to true, the auto learning function is enabled. Auto learning uses
   * user data to generate suggestions using ML techniques. Default value is
   * false. Only after enabling auto learning can users use `cloud-retail` data
   * in CompleteQueryRequest.
   *
   * @param bool $autoLearning
   */
  public function setAutoLearning($autoLearning)
  {
    $this->autoLearning = $autoLearning;
  }
  /**
   * @return bool
   */
  public function getAutoLearning()
  {
    return $this->autoLearning;
  }
  /**
   * Output only. The source data for the latest import of the autocomplete
   * denylist phrases.
   *
   * @param GoogleCloudRetailV2CompletionDataInputConfig $denylistInputConfig
   */
  public function setDenylistInputConfig(GoogleCloudRetailV2CompletionDataInputConfig $denylistInputConfig)
  {
    $this->denylistInputConfig = $denylistInputConfig;
  }
  /**
   * @return GoogleCloudRetailV2CompletionDataInputConfig
   */
  public function getDenylistInputConfig()
  {
    return $this->denylistInputConfig;
  }
  /**
   * Output only. Name of the LRO corresponding to the latest allowlist import.
   * Can use GetOperation API to retrieve the latest state of the Long Running
   * Operation.
   *
   * @param string $lastAllowlistImportOperation
   */
  public function setLastAllowlistImportOperation($lastAllowlistImportOperation)
  {
    $this->lastAllowlistImportOperation = $lastAllowlistImportOperation;
  }
  /**
   * @return string
   */
  public function getLastAllowlistImportOperation()
  {
    return $this->lastAllowlistImportOperation;
  }
  /**
   * Output only. Name of the LRO corresponding to the latest denylist import.
   * Can use GetOperation API to retrieve the latest state of the Long Running
   * Operation.
   *
   * @param string $lastDenylistImportOperation
   */
  public function setLastDenylistImportOperation($lastDenylistImportOperation)
  {
    $this->lastDenylistImportOperation = $lastDenylistImportOperation;
  }
  /**
   * @return string
   */
  public function getLastDenylistImportOperation()
  {
    return $this->lastDenylistImportOperation;
  }
  /**
   * Output only. Name of the LRO corresponding to the latest suggestion terms
   * list import. Can use GetOperation API method to retrieve the latest state
   * of the Long Running Operation.
   *
   * @param string $lastSuggestionsImportOperation
   */
  public function setLastSuggestionsImportOperation($lastSuggestionsImportOperation)
  {
    $this->lastSuggestionsImportOperation = $lastSuggestionsImportOperation;
  }
  /**
   * @return string
   */
  public function getLastSuggestionsImportOperation()
  {
    return $this->lastSuggestionsImportOperation;
  }
  /**
   * Specifies the matching order for autocomplete suggestions, e.g., a query
   * consisting of 'sh' with 'out-of-order' specified would suggest "women's
   * shoes", whereas a query of 'red s' with 'exact-prefix' specified would
   * suggest "red shoes". Currently supported values: * 'out-of-order' * 'exact-
   * prefix' Default value: 'exact-prefix'.
   *
   * @param string $matchingOrder
   */
  public function setMatchingOrder($matchingOrder)
  {
    $this->matchingOrder = $matchingOrder;
  }
  /**
   * @return string
   */
  public function getMatchingOrder()
  {
    return $this->matchingOrder;
  }
  /**
   * The maximum number of autocomplete suggestions returned per term. Default
   * value is 20. If left unset or set to 0, then will fallback to default
   * value. Value range is 1 to 20.
   *
   * @param int $maxSuggestions
   */
  public function setMaxSuggestions($maxSuggestions)
  {
    $this->maxSuggestions = $maxSuggestions;
  }
  /**
   * @return int
   */
  public function getMaxSuggestions()
  {
    return $this->maxSuggestions;
  }
  /**
   * The minimum number of characters needed to be typed in order to get
   * suggestions. Default value is 2. If left unset or set to 0, then will
   * fallback to default value. Value range is 1 to 20.
   *
   * @param int $minPrefixLength
   */
  public function setMinPrefixLength($minPrefixLength)
  {
    $this->minPrefixLength = $minPrefixLength;
  }
  /**
   * @return int
   */
  public function getMinPrefixLength()
  {
    return $this->minPrefixLength;
  }
  /**
   * Required. Immutable. Fully qualified name
   * `projects/locations/catalogs/completionConfig`
   *
   * @param string $name
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
   * Output only. The source data for the latest import of the autocomplete
   * suggestion phrases.
   *
   * @param GoogleCloudRetailV2CompletionDataInputConfig $suggestionsInputConfig
   */
  public function setSuggestionsInputConfig(GoogleCloudRetailV2CompletionDataInputConfig $suggestionsInputConfig)
  {
    $this->suggestionsInputConfig = $suggestionsInputConfig;
  }
  /**
   * @return GoogleCloudRetailV2CompletionDataInputConfig
   */
  public function getSuggestionsInputConfig()
  {
    return $this->suggestionsInputConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CompletionConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CompletionConfig');
