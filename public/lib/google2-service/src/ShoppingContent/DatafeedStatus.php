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

class DatafeedStatus extends \Google\Collection
{
  protected $collection_key = 'warnings';
  /**
   * The country for which the status is reported, represented as a CLDR
   * territory code.
   *
   * @var string
   */
  public $country;
  /**
   * The ID of the feed for which the status is reported.
   *
   * @var string
   */
  public $datafeedId;
  protected $errorsType = DatafeedStatusError::class;
  protected $errorsDataType = 'array';
  /**
   * The feed label status is reported for.
   *
   * @var string
   */
  public $feedLabel;
  /**
   * The number of items in the feed that were processed.
   *
   * @var string
   */
  public $itemsTotal;
  /**
   * The number of items in the feed that were valid.
   *
   * @var string
   */
  public $itemsValid;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#datafeedStatus`"
   *
   * @var string
   */
  public $kind;
  /**
   * The two-letter ISO 639-1 language for which the status is reported.
   *
   * @var string
   */
  public $language;
  /**
   * The last date at which the feed was uploaded.
   *
   * @var string
   */
  public $lastUploadDate;
  /**
   * The processing status of the feed. Acceptable values are: - "`"`failure`":
   * The feed could not be processed or all items had errors.`" - "`in
   * progress`": The feed is being processed. - "`none`": The feed has not yet
   * been processed. For example, a feed that has never been uploaded will have
   * this processing status. - "`success`": The feed was processed successfully,
   * though some items might have had errors.
   *
   * @var string
   */
  public $processingStatus;
  protected $warningsType = DatafeedStatusError::class;
  protected $warningsDataType = 'array';

  /**
   * The country for which the status is reported, represented as a CLDR
   * territory code.
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
   * The ID of the feed for which the status is reported.
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
   * The list of errors occurring in the feed.
   *
   * @param DatafeedStatusError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return DatafeedStatusError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The feed label status is reported for.
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
   * The number of items in the feed that were processed.
   *
   * @param string $itemsTotal
   */
  public function setItemsTotal($itemsTotal)
  {
    $this->itemsTotal = $itemsTotal;
  }
  /**
   * @return string
   */
  public function getItemsTotal()
  {
    return $this->itemsTotal;
  }
  /**
   * The number of items in the feed that were valid.
   *
   * @param string $itemsValid
   */
  public function setItemsValid($itemsValid)
  {
    $this->itemsValid = $itemsValid;
  }
  /**
   * @return string
   */
  public function getItemsValid()
  {
    return $this->itemsValid;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#datafeedStatus`"
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The two-letter ISO 639-1 language for which the status is reported.
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
   * The last date at which the feed was uploaded.
   *
   * @param string $lastUploadDate
   */
  public function setLastUploadDate($lastUploadDate)
  {
    $this->lastUploadDate = $lastUploadDate;
  }
  /**
   * @return string
   */
  public function getLastUploadDate()
  {
    return $this->lastUploadDate;
  }
  /**
   * The processing status of the feed. Acceptable values are: - "`"`failure`":
   * The feed could not be processed or all items had errors.`" - "`in
   * progress`": The feed is being processed. - "`none`": The feed has not yet
   * been processed. For example, a feed that has never been uploaded will have
   * this processing status. - "`success`": The feed was processed successfully,
   * though some items might have had errors.
   *
   * @param string $processingStatus
   */
  public function setProcessingStatus($processingStatus)
  {
    $this->processingStatus = $processingStatus;
  }
  /**
   * @return string
   */
  public function getProcessingStatus()
  {
    return $this->processingStatus;
  }
  /**
   * The list of errors occurring in the feed.
   *
   * @param DatafeedStatusError[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return DatafeedStatusError[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatafeedStatus::class, 'Google_Service_ShoppingContent_DatafeedStatus');
