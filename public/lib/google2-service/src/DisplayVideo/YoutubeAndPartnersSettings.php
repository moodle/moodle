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

class YoutubeAndPartnersSettings extends \Google\Collection
{
  /**
   * Content category is not specified or is unknown in this version.
   */
  public const CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_UNSPECIFIED = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_UNSPECIFIED';
  /**
   * A category consisting of a wide range of content appropriate for most
   * brands. The content is based off of YouTube's [advertiser-friendly content
   * guidelines](https://support.google.com/youtube/answer/6162278).
   */
  public const CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_STANDARD = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_STANDARD';
  /**
   * A category including all content across YouTube and video partners that
   * meets standards for monetization.
   */
  public const CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_EXPANDED = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_EXPANDED';
  /**
   * A category consisting of a reduced range of content that meets heightened
   * requirements, especially regarding inappropriate language and sexual
   * suggestiveness.
   */
  public const CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_LIMITED = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_LIMITED';
  /**
   * Content category is not specified or is unknown in this version.
   */
  public const EFFECTIVE_CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_UNSPECIFIED = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_UNSPECIFIED';
  /**
   * A category consisting of a wide range of content appropriate for most
   * brands. The content is based off of YouTube's [advertiser-friendly content
   * guidelines](https://support.google.com/youtube/answer/6162278).
   */
  public const EFFECTIVE_CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_STANDARD = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_STANDARD';
  /**
   * A category including all content across YouTube and video partners that
   * meets standards for monetization.
   */
  public const EFFECTIVE_CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_EXPANDED = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_EXPANDED';
  /**
   * A category consisting of a reduced range of content that meets heightened
   * requirements, especially regarding inappropriate language and sexual
   * suggestiveness.
   */
  public const EFFECTIVE_CONTENT_CATEGORY_YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_LIMITED = 'YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_LIMITED';
  protected $collection_key = 'relatedVideoIds';
  /**
   * Output only. The kind of content on which the YouTube and Partners ads will
   * be shown. *Warning*: This field will be removed in the near future. Use
   * effective_content_category instead.
   *
   * @var string
   */
  public $contentCategory;
  /**
   * Output only. The content category which takes effect when serving the line
   * item. When content category is set in both line item and advertiser, the
   * stricter one will take effect when serving the line item. New line items
   * will only inherit the advertiser level setting.
   *
   * @var string
   */
  public $effectiveContentCategory;
  protected $inventorySourceSettingsType = YoutubeAndPartnersInventorySourceConfig::class;
  protected $inventorySourceSettingsDataType = '';
  /**
   * Optional. The ID of the form to generate leads.
   *
   * @var string
   */
  public $leadFormId;
  /**
   * Optional. The ID of the merchant which is linked to the line item for
   * product feed.
   *
   * @var string
   */
  public $linkedMerchantId;
  /**
   * Optional. The IDs of the videos appear below the primary video ad when the
   * ad is playing in the YouTube app on mobile devices.
   *
   * @var string[]
   */
  public $relatedVideoIds;
  protected $targetFrequencyType = TargetFrequency::class;
  protected $targetFrequencyDataType = '';
  protected $thirdPartyMeasurementConfigsType = ThirdPartyMeasurementConfigs::class;
  protected $thirdPartyMeasurementConfigsDataType = '';
  protected $videoAdInventoryControlType = VideoAdInventoryControl::class;
  protected $videoAdInventoryControlDataType = '';
  protected $videoAdSequenceSettingsType = VideoAdSequenceSettings::class;
  protected $videoAdSequenceSettingsDataType = '';
  protected $viewFrequencyCapType = FrequencyCap::class;
  protected $viewFrequencyCapDataType = '';

  /**
   * Output only. The kind of content on which the YouTube and Partners ads will
   * be shown. *Warning*: This field will be removed in the near future. Use
   * effective_content_category instead.
   *
   * Accepted values: YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_UNSPECIFIED,
   * YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_STANDARD,
   * YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_EXPANDED,
   * YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_LIMITED
   *
   * @param self::CONTENT_CATEGORY_* $contentCategory
   */
  public function setContentCategory($contentCategory)
  {
    $this->contentCategory = $contentCategory;
  }
  /**
   * @return self::CONTENT_CATEGORY_*
   */
  public function getContentCategory()
  {
    return $this->contentCategory;
  }
  /**
   * Output only. The content category which takes effect when serving the line
   * item. When content category is set in both line item and advertiser, the
   * stricter one will take effect when serving the line item. New line items
   * will only inherit the advertiser level setting.
   *
   * Accepted values: YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_UNSPECIFIED,
   * YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_STANDARD,
   * YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_EXPANDED,
   * YOUTUBE_AND_PARTNERS_CONTENT_CATEGORY_LIMITED
   *
   * @param self::EFFECTIVE_CONTENT_CATEGORY_* $effectiveContentCategory
   */
  public function setEffectiveContentCategory($effectiveContentCategory)
  {
    $this->effectiveContentCategory = $effectiveContentCategory;
  }
  /**
   * @return self::EFFECTIVE_CONTENT_CATEGORY_*
   */
  public function getEffectiveContentCategory()
  {
    return $this->effectiveContentCategory;
  }
  /**
   * Settings that control what YouTube and Partners inventories the line item
   * will target.
   *
   * @param YoutubeAndPartnersInventorySourceConfig $inventorySourceSettings
   */
  public function setInventorySourceSettings(YoutubeAndPartnersInventorySourceConfig $inventorySourceSettings)
  {
    $this->inventorySourceSettings = $inventorySourceSettings;
  }
  /**
   * @return YoutubeAndPartnersInventorySourceConfig
   */
  public function getInventorySourceSettings()
  {
    return $this->inventorySourceSettings;
  }
  /**
   * Optional. The ID of the form to generate leads.
   *
   * @param string $leadFormId
   */
  public function setLeadFormId($leadFormId)
  {
    $this->leadFormId = $leadFormId;
  }
  /**
   * @return string
   */
  public function getLeadFormId()
  {
    return $this->leadFormId;
  }
  /**
   * Optional. The ID of the merchant which is linked to the line item for
   * product feed.
   *
   * @param string $linkedMerchantId
   */
  public function setLinkedMerchantId($linkedMerchantId)
  {
    $this->linkedMerchantId = $linkedMerchantId;
  }
  /**
   * @return string
   */
  public function getLinkedMerchantId()
  {
    return $this->linkedMerchantId;
  }
  /**
   * Optional. The IDs of the videos appear below the primary video ad when the
   * ad is playing in the YouTube app on mobile devices.
   *
   * @param string[] $relatedVideoIds
   */
  public function setRelatedVideoIds($relatedVideoIds)
  {
    $this->relatedVideoIds = $relatedVideoIds;
  }
  /**
   * @return string[]
   */
  public function getRelatedVideoIds()
  {
    return $this->relatedVideoIds;
  }
  /**
   * Optional. The average number of times you want ads from this line item to
   * show to the same person over a certain period of time.
   *
   * @param TargetFrequency $targetFrequency
   */
  public function setTargetFrequency(TargetFrequency $targetFrequency)
  {
    $this->targetFrequency = $targetFrequency;
  }
  /**
   * @return TargetFrequency
   */
  public function getTargetFrequency()
  {
    return $this->targetFrequency;
  }
  /**
   * Optional. The third-party measurement configs of the line item.
   *
   * @param ThirdPartyMeasurementConfigs $thirdPartyMeasurementConfigs
   */
  public function setThirdPartyMeasurementConfigs(ThirdPartyMeasurementConfigs $thirdPartyMeasurementConfigs)
  {
    $this->thirdPartyMeasurementConfigs = $thirdPartyMeasurementConfigs;
  }
  /**
   * @return ThirdPartyMeasurementConfigs
   */
  public function getThirdPartyMeasurementConfigs()
  {
    return $this->thirdPartyMeasurementConfigs;
  }
  /**
   * Optional. The settings to control which inventory is allowed for this line
   * item.
   *
   * @param VideoAdInventoryControl $videoAdInventoryControl
   */
  public function setVideoAdInventoryControl(VideoAdInventoryControl $videoAdInventoryControl)
  {
    $this->videoAdInventoryControl = $videoAdInventoryControl;
  }
  /**
   * @return VideoAdInventoryControl
   */
  public function getVideoAdInventoryControl()
  {
    return $this->videoAdInventoryControl;
  }
  /**
   * Optional. The settings related to VideoAdSequence.
   *
   * @param VideoAdSequenceSettings $videoAdSequenceSettings
   */
  public function setVideoAdSequenceSettings(VideoAdSequenceSettings $videoAdSequenceSettings)
  {
    $this->videoAdSequenceSettings = $videoAdSequenceSettings;
  }
  /**
   * @return VideoAdSequenceSettings
   */
  public function getVideoAdSequenceSettings()
  {
    return $this->videoAdSequenceSettings;
  }
  /**
   * The view frequency cap settings of the line item. The max_views field in
   * this settings object must be used if assigning a limited cap.
   *
   * @param FrequencyCap $viewFrequencyCap
   */
  public function setViewFrequencyCap(FrequencyCap $viewFrequencyCap)
  {
    $this->viewFrequencyCap = $viewFrequencyCap;
  }
  /**
   * @return FrequencyCap
   */
  public function getViewFrequencyCap()
  {
    return $this->viewFrequencyCap;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAndPartnersSettings::class, 'Google_Service_DisplayVideo_YoutubeAndPartnersSettings');
