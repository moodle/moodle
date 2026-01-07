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

class ParentEntityFilter extends \Google\Collection
{
  /**
   * Default value when type is unspecified or is unknown in this version.
   */
  public const FILTER_TYPE_FILTER_TYPE_UNSPECIFIED = 'FILTER_TYPE_UNSPECIFIED';
  /**
   * If selected, no filter will be applied to the download. Can only be used if
   * an Advertiser is specified in CreateSdfDownloadTaskRequest.
   */
  public const FILTER_TYPE_FILTER_TYPE_NONE = 'FILTER_TYPE_NONE';
  /**
   * Advertiser ID. If selected, all filter IDs must be Advertiser IDs that
   * belong to the Partner specified in CreateSdfDownloadTaskRequest.
   */
  public const FILTER_TYPE_FILTER_TYPE_ADVERTISER_ID = 'FILTER_TYPE_ADVERTISER_ID';
  /**
   * Campaign ID. If selected, all filter IDs must be Campaign IDs that belong
   * to the Advertiser or Partner specified in CreateSdfDownloadTaskRequest.
   */
  public const FILTER_TYPE_FILTER_TYPE_CAMPAIGN_ID = 'FILTER_TYPE_CAMPAIGN_ID';
  /**
   * Media Product ID. If selected, all filter IDs must be Media Product IDs
   * that belong to the Advertiser or Partner specified in
   * CreateSdfDownloadTaskRequest. Can only be used for downloading
   * `FILE_TYPE_MEDIA_PRODUCT`.
   */
  public const FILTER_TYPE_FILTER_TYPE_MEDIA_PRODUCT_ID = 'FILTER_TYPE_MEDIA_PRODUCT_ID';
  /**
   * Insertion Order ID. If selected, all filter IDs must be Insertion Order IDs
   * that belong to the Advertiser or Partner specified in
   * CreateSdfDownloadTaskRequest. Can only be used for downloading
   * `FILE_TYPE_INSERTION_ORDER`, `FILE_TYPE_LINE_ITEM`,
   * `FILE_TYPE_LINE_ITEM_QA`, `FILE_TYPE_AD_GROUP`, `FILE_TYPE_AD_GROUP_QA`,
   * and `FILE_TYPE_AD`.
   */
  public const FILTER_TYPE_FILTER_TYPE_INSERTION_ORDER_ID = 'FILTER_TYPE_INSERTION_ORDER_ID';
  /**
   * Line Item ID. If selected, all filter IDs must be Line Item IDs that belong
   * to the Advertiser or Partner specified in CreateSdfDownloadTaskRequest. Can
   * only be used for downloading `FILE_TYPE_LINE_ITEM`,
   * `FILE_TYPE_LINE_ITEM_QA`,`FILE_TYPE_AD_GROUP`, `FILE_TYPE_AD_GROUP_QA`, and
   * `FILE_TYPE_AD`.
   */
  public const FILTER_TYPE_FILTER_TYPE_LINE_ITEM_ID = 'FILTER_TYPE_LINE_ITEM_ID';
  protected $collection_key = 'filterIds';
  /**
   * Required. File types that will be returned.
   *
   * @var string[]
   */
  public $fileType;
  /**
   * The IDs of the specified filter type. This is used to filter entities to
   * fetch. If filter type is not `FILTER_TYPE_NONE`, at least one ID must be
   * specified.
   *
   * @var string[]
   */
  public $filterIds;
  /**
   * Required. Filter type used to filter fetched entities.
   *
   * @var string
   */
  public $filterType;

  /**
   * Required. File types that will be returned.
   *
   * @param string[] $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return string[]
   */
  public function getFileType()
  {
    return $this->fileType;
  }
  /**
   * The IDs of the specified filter type. This is used to filter entities to
   * fetch. If filter type is not `FILTER_TYPE_NONE`, at least one ID must be
   * specified.
   *
   * @param string[] $filterIds
   */
  public function setFilterIds($filterIds)
  {
    $this->filterIds = $filterIds;
  }
  /**
   * @return string[]
   */
  public function getFilterIds()
  {
    return $this->filterIds;
  }
  /**
   * Required. Filter type used to filter fetched entities.
   *
   * Accepted values: FILTER_TYPE_UNSPECIFIED, FILTER_TYPE_NONE,
   * FILTER_TYPE_ADVERTISER_ID, FILTER_TYPE_CAMPAIGN_ID,
   * FILTER_TYPE_MEDIA_PRODUCT_ID, FILTER_TYPE_INSERTION_ORDER_ID,
   * FILTER_TYPE_LINE_ITEM_ID
   *
   * @param self::FILTER_TYPE_* $filterType
   */
  public function setFilterType($filterType)
  {
    $this->filterType = $filterType;
  }
  /**
   * @return self::FILTER_TYPE_*
   */
  public function getFilterType()
  {
    return $this->filterType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParentEntityFilter::class, 'Google_Service_DisplayVideo_ParentEntityFilter');
