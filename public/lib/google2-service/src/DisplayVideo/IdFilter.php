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

namespace Google\Service\DisplayVideo;

class IdFilter extends \Google\Collection
{
  protected $collection_key = 'mediaProductIds';
  /**
   * YouTube Ads to download by ID. All IDs must belong to the same Advertiser
   * or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $adGroupAdIds;
  /**
   * YouTube Ad Groups to download by ID. All IDs must belong to the same
   * Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $adGroupIds;
  /**
   * Optional. YouTube Ad Groups, by ID, to download in QA format. All IDs must
   * belong to the same Advertiser or Partner specified in
   * CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $adGroupQaIds;
  /**
   * Campaigns to download by ID. All IDs must belong to the same Advertiser or
   * Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $campaignIds;
  /**
   * Insertion Orders to download by ID. All IDs must belong to the same
   * Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $insertionOrderIds;
  /**
   * Line Items to download by ID. All IDs must belong to the same Advertiser or
   * Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $lineItemIds;
  /**
   * Optional. Line Items, by ID, to download in QA format. All IDs must belong
   * to the same Advertiser or Partner specified in
   * CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $lineItemQaIds;
  /**
   * Media Products to download by ID. All IDs must belong to the same
   * Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @var string[]
   */
  public $mediaProductIds;

  /**
   * YouTube Ads to download by ID. All IDs must belong to the same Advertiser
   * or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @param string[] $adGroupAdIds
   */
  public function setAdGroupAdIds($adGroupAdIds)
  {
    $this->adGroupAdIds = $adGroupAdIds;
  }
  /**
   * @return string[]
   */
  public function getAdGroupAdIds()
  {
    return $this->adGroupAdIds;
  }
  /**
   * YouTube Ad Groups to download by ID. All IDs must belong to the same
   * Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @param string[] $adGroupIds
   */
  public function setAdGroupIds($adGroupIds)
  {
    $this->adGroupIds = $adGroupIds;
  }
  /**
   * @return string[]
   */
  public function getAdGroupIds()
  {
    return $this->adGroupIds;
  }
  /**
   * Optional. YouTube Ad Groups, by ID, to download in QA format. All IDs must
   * belong to the same Advertiser or Partner specified in
   * CreateSdfDownloadTaskRequest.
   *
   * @param string[] $adGroupQaIds
   */
  public function setAdGroupQaIds($adGroupQaIds)
  {
    $this->adGroupQaIds = $adGroupQaIds;
  }
  /**
   * @return string[]
   */
  public function getAdGroupQaIds()
  {
    return $this->adGroupQaIds;
  }
  /**
   * Campaigns to download by ID. All IDs must belong to the same Advertiser or
   * Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @param string[] $campaignIds
   */
  public function setCampaignIds($campaignIds)
  {
    $this->campaignIds = $campaignIds;
  }
  /**
   * @return string[]
   */
  public function getCampaignIds()
  {
    return $this->campaignIds;
  }
  /**
   * Insertion Orders to download by ID. All IDs must belong to the same
   * Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @param string[] $insertionOrderIds
   */
  public function setInsertionOrderIds($insertionOrderIds)
  {
    $this->insertionOrderIds = $insertionOrderIds;
  }
  /**
   * @return string[]
   */
  public function getInsertionOrderIds()
  {
    return $this->insertionOrderIds;
  }
  /**
   * Line Items to download by ID. All IDs must belong to the same Advertiser or
   * Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @param string[] $lineItemIds
   */
  public function setLineItemIds($lineItemIds)
  {
    $this->lineItemIds = $lineItemIds;
  }
  /**
   * @return string[]
   */
  public function getLineItemIds()
  {
    return $this->lineItemIds;
  }
  /**
   * Optional. Line Items, by ID, to download in QA format. All IDs must belong
   * to the same Advertiser or Partner specified in
   * CreateSdfDownloadTaskRequest.
   *
   * @param string[] $lineItemQaIds
   */
  public function setLineItemQaIds($lineItemQaIds)
  {
    $this->lineItemQaIds = $lineItemQaIds;
  }
  /**
   * @return string[]
   */
  public function getLineItemQaIds()
  {
    return $this->lineItemQaIds;
  }
  /**
   * Media Products to download by ID. All IDs must belong to the same
   * Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   *
   * @param string[] $mediaProductIds
   */
  public function setMediaProductIds($mediaProductIds)
  {
    $this->mediaProductIds = $mediaProductIds;
  }
  /**
   * @return string[]
   */
  public function getMediaProductIds()
  {
    return $this->mediaProductIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdFilter::class, 'Google_Service_DisplayVideo_IdFilter');
