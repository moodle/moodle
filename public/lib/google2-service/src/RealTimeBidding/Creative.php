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

namespace Google\Service\RealTimeBidding;

class Creative extends \Google\Collection
{
  /**
   * The format is unknown.
   */
  public const CREATIVE_FORMAT_CREATIVE_FORMAT_UNSPECIFIED = 'CREATIVE_FORMAT_UNSPECIFIED';
  /**
   * HTML creative.
   */
  public const CREATIVE_FORMAT_HTML = 'HTML';
  /**
   * Video creative.
   */
  public const CREATIVE_FORMAT_VIDEO = 'VIDEO';
  /**
   * Native creative.
   */
  public const CREATIVE_FORMAT_NATIVE = 'NATIVE';
  protected $collection_key = 'restrictedCategories';
  /**
   * Output only. ID of the buyer account that this creative is owned by. Can be
   * used to filter the response of the creatives.list method with equality and
   * inequality check.
   *
   * @var string
   */
  public $accountId;
  /**
   * The link to AdChoices destination page. This is only supported for native
   * ads.
   *
   * @var string
   */
  public $adChoicesDestinationUrl;
  /**
   * The name of the company being advertised in the creative. Can be used to
   * filter the response of the creatives.list method.
   *
   * @var string
   */
  public $advertiserName;
  /**
   * The agency ID for this creative.
   *
   * @var string
   */
  public $agencyId;
  /**
   * Output only. The last update timestamp of the creative through the API.
   *
   * @var string
   */
  public $apiUpdateTime;
  /**
   * Output only. The format of this creative. Can be used to filter the
   * response of the creatives.list method.
   *
   * @var string
   */
  public $creativeFormat;
  /**
   * Buyer-specific creative ID that references this creative in bid responses.
   * This field is Ignored in update operations. Can be used to filter the
   * response of the creatives.list method. The maximum length of the creative
   * ID is 128 bytes.
   *
   * @var string
   */
  public $creativeId;
  protected $creativeServingDecisionType = CreativeServingDecision::class;
  protected $creativeServingDecisionDataType = '';
  /**
   * Output only. IDs of all of the deals with which this creative has been used
   * in bidding. Can be used to filter the response of the creatives.list
   * method.
   *
   * @var string[]
   */
  public $dealIds;
  /**
   * All declared attributes for the ads that may be shown from this creative.
   * Can be used to filter the response of the creatives.list method. If the
   * `excluded_attribute` field of a [bid
   * request](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto") contains one of the
   * attributes that were declared or detected for a given creative, and a bid
   * is submitted with that creative, the bid will be filtered before the
   * auction.
   *
   * @var string[]
   */
  public $declaredAttributes;
  /**
   * The set of declared destination URLs for the creative. Can be used to
   * filter the response of the creatives.list method.
   *
   * @var string[]
   */
  public $declaredClickThroughUrls;
  /**
   * All declared restricted categories for the ads that may be shown from this
   * creative. Can be used to filter the response of the creatives.list method.
   *
   * @deprecated
   * @var string[]
   */
  public $declaredRestrictedCategories;
  /**
   * IDs for the declared ad technology vendors that may be used by this
   * creative. See https://storage.googleapis.com/adx-rtb-
   * dictionaries/vendors.txt for possible values. Can be used to filter the
   * response of the creatives.list method.
   *
   * @var int[]
   */
  public $declaredVendorIds;
  protected $htmlType = HtmlContent::class;
  protected $htmlDataType = '';
  /**
   * The set of URLs to be called to record an impression.
   *
   * @var string[]
   */
  public $impressionTrackingUrls;
  /**
   * Output only. Name of the creative. Follows the pattern
   * `buyers/{buyer}/creatives/{creative}`, where `{buyer}` represents the
   * account ID of the buyer who owns the creative, and `{creative}` is the
   * buyer-specific creative ID that references this creative in the bid
   * response.
   *
   * @var string
   */
  public $name;
  protected $nativeType = NativeContent::class;
  protected $nativeDataType = '';
  /**
   * Experimental field that can be used during the [FLEDGE Origin
   * Trial](/authorized-buyers/rtb/fledge-origin-trial). The URL to fetch an
   * interest group ad used in [TURTLEDOVE on-device
   * auction](https://github.com/WICG/turtledove/blob/main/FLEDGE.md#1-browsers-
   * record-interest-groups"). This should be unique among all creatives for a
   * given `accountId`. This URL should be the same as the URL returned by [gene
   * rateBid()](https://github.com/WICG/turtledove/blob/main/FLEDGE.md#32-on-
   * device-bidding).
   *
   * @var string
   */
  public $renderUrl;
  /**
   * All restricted categories for the ads that may be shown from this creative.
   *
   * @deprecated
   * @var string[]
   */
  public $restrictedCategories;
  /**
   * Output only. The version of the creative. Version for a new creative is 1
   * and it increments during subsequent creative updates.
   *
   * @deprecated
   * @var int
   */
  public $version;
  protected $videoType = VideoContent::class;
  protected $videoDataType = '';

  /**
   * Output only. ID of the buyer account that this creative is owned by. Can be
   * used to filter the response of the creatives.list method with equality and
   * inequality check.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The link to AdChoices destination page. This is only supported for native
   * ads.
   *
   * @param string $adChoicesDestinationUrl
   */
  public function setAdChoicesDestinationUrl($adChoicesDestinationUrl)
  {
    $this->adChoicesDestinationUrl = $adChoicesDestinationUrl;
  }
  /**
   * @return string
   */
  public function getAdChoicesDestinationUrl()
  {
    return $this->adChoicesDestinationUrl;
  }
  /**
   * The name of the company being advertised in the creative. Can be used to
   * filter the response of the creatives.list method.
   *
   * @param string $advertiserName
   */
  public function setAdvertiserName($advertiserName)
  {
    $this->advertiserName = $advertiserName;
  }
  /**
   * @return string
   */
  public function getAdvertiserName()
  {
    return $this->advertiserName;
  }
  /**
   * The agency ID for this creative.
   *
   * @param string $agencyId
   */
  public function setAgencyId($agencyId)
  {
    $this->agencyId = $agencyId;
  }
  /**
   * @return string
   */
  public function getAgencyId()
  {
    return $this->agencyId;
  }
  /**
   * Output only. The last update timestamp of the creative through the API.
   *
   * @param string $apiUpdateTime
   */
  public function setApiUpdateTime($apiUpdateTime)
  {
    $this->apiUpdateTime = $apiUpdateTime;
  }
  /**
   * @return string
   */
  public function getApiUpdateTime()
  {
    return $this->apiUpdateTime;
  }
  /**
   * Output only. The format of this creative. Can be used to filter the
   * response of the creatives.list method.
   *
   * Accepted values: CREATIVE_FORMAT_UNSPECIFIED, HTML, VIDEO, NATIVE
   *
   * @param self::CREATIVE_FORMAT_* $creativeFormat
   */
  public function setCreativeFormat($creativeFormat)
  {
    $this->creativeFormat = $creativeFormat;
  }
  /**
   * @return self::CREATIVE_FORMAT_*
   */
  public function getCreativeFormat()
  {
    return $this->creativeFormat;
  }
  /**
   * Buyer-specific creative ID that references this creative in bid responses.
   * This field is Ignored in update operations. Can be used to filter the
   * response of the creatives.list method. The maximum length of the creative
   * ID is 128 bytes.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
  }
  /**
   * Output only. Top level status and detected attributes of a creative (for
   * example domain, language, advertiser, product category, etc.) that affect
   * whether (status) and where (context) a creative will be allowed to serve.
   *
   * @param CreativeServingDecision $creativeServingDecision
   */
  public function setCreativeServingDecision(CreativeServingDecision $creativeServingDecision)
  {
    $this->creativeServingDecision = $creativeServingDecision;
  }
  /**
   * @return CreativeServingDecision
   */
  public function getCreativeServingDecision()
  {
    return $this->creativeServingDecision;
  }
  /**
   * Output only. IDs of all of the deals with which this creative has been used
   * in bidding. Can be used to filter the response of the creatives.list
   * method.
   *
   * @param string[] $dealIds
   */
  public function setDealIds($dealIds)
  {
    $this->dealIds = $dealIds;
  }
  /**
   * @return string[]
   */
  public function getDealIds()
  {
    return $this->dealIds;
  }
  /**
   * All declared attributes for the ads that may be shown from this creative.
   * Can be used to filter the response of the creatives.list method. If the
   * `excluded_attribute` field of a [bid
   * request](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto") contains one of the
   * attributes that were declared or detected for a given creative, and a bid
   * is submitted with that creative, the bid will be filtered before the
   * auction.
   *
   * @param string[] $declaredAttributes
   */
  public function setDeclaredAttributes($declaredAttributes)
  {
    $this->declaredAttributes = $declaredAttributes;
  }
  /**
   * @return string[]
   */
  public function getDeclaredAttributes()
  {
    return $this->declaredAttributes;
  }
  /**
   * The set of declared destination URLs for the creative. Can be used to
   * filter the response of the creatives.list method.
   *
   * @param string[] $declaredClickThroughUrls
   */
  public function setDeclaredClickThroughUrls($declaredClickThroughUrls)
  {
    $this->declaredClickThroughUrls = $declaredClickThroughUrls;
  }
  /**
   * @return string[]
   */
  public function getDeclaredClickThroughUrls()
  {
    return $this->declaredClickThroughUrls;
  }
  /**
   * All declared restricted categories for the ads that may be shown from this
   * creative. Can be used to filter the response of the creatives.list method.
   *
   * @deprecated
   * @param string[] $declaredRestrictedCategories
   */
  public function setDeclaredRestrictedCategories($declaredRestrictedCategories)
  {
    $this->declaredRestrictedCategories = $declaredRestrictedCategories;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getDeclaredRestrictedCategories()
  {
    return $this->declaredRestrictedCategories;
  }
  /**
   * IDs for the declared ad technology vendors that may be used by this
   * creative. See https://storage.googleapis.com/adx-rtb-
   * dictionaries/vendors.txt for possible values. Can be used to filter the
   * response of the creatives.list method.
   *
   * @param int[] $declaredVendorIds
   */
  public function setDeclaredVendorIds($declaredVendorIds)
  {
    $this->declaredVendorIds = $declaredVendorIds;
  }
  /**
   * @return int[]
   */
  public function getDeclaredVendorIds()
  {
    return $this->declaredVendorIds;
  }
  /**
   * An HTML creative.
   *
   * @param HtmlContent $html
   */
  public function setHtml(HtmlContent $html)
  {
    $this->html = $html;
  }
  /**
   * @return HtmlContent
   */
  public function getHtml()
  {
    return $this->html;
  }
  /**
   * The set of URLs to be called to record an impression.
   *
   * @param string[] $impressionTrackingUrls
   */
  public function setImpressionTrackingUrls($impressionTrackingUrls)
  {
    $this->impressionTrackingUrls = $impressionTrackingUrls;
  }
  /**
   * @return string[]
   */
  public function getImpressionTrackingUrls()
  {
    return $this->impressionTrackingUrls;
  }
  /**
   * Output only. Name of the creative. Follows the pattern
   * `buyers/{buyer}/creatives/{creative}`, where `{buyer}` represents the
   * account ID of the buyer who owns the creative, and `{creative}` is the
   * buyer-specific creative ID that references this creative in the bid
   * response.
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
   * A native creative.
   *
   * @param NativeContent $native
   */
  public function setNative(NativeContent $native)
  {
    $this->native = $native;
  }
  /**
   * @return NativeContent
   */
  public function getNative()
  {
    return $this->native;
  }
  /**
   * Experimental field that can be used during the [FLEDGE Origin
   * Trial](/authorized-buyers/rtb/fledge-origin-trial). The URL to fetch an
   * interest group ad used in [TURTLEDOVE on-device
   * auction](https://github.com/WICG/turtledove/blob/main/FLEDGE.md#1-browsers-
   * record-interest-groups"). This should be unique among all creatives for a
   * given `accountId`. This URL should be the same as the URL returned by [gene
   * rateBid()](https://github.com/WICG/turtledove/blob/main/FLEDGE.md#32-on-
   * device-bidding).
   *
   * @param string $renderUrl
   */
  public function setRenderUrl($renderUrl)
  {
    $this->renderUrl = $renderUrl;
  }
  /**
   * @return string
   */
  public function getRenderUrl()
  {
    return $this->renderUrl;
  }
  /**
   * All restricted categories for the ads that may be shown from this creative.
   *
   * @deprecated
   * @param string[] $restrictedCategories
   */
  public function setRestrictedCategories($restrictedCategories)
  {
    $this->restrictedCategories = $restrictedCategories;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getRestrictedCategories()
  {
    return $this->restrictedCategories;
  }
  /**
   * Output only. The version of the creative. Version for a new creative is 1
   * and it increments during subsequent creative updates.
   *
   * @deprecated
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * A video creative.
   *
   * @param VideoContent $video
   */
  public function setVideo(VideoContent $video)
  {
    $this->video = $video;
  }
  /**
   * @return VideoContent
   */
  public function getVideo()
  {
    return $this->video;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Creative::class, 'Google_Service_RealTimeBidding_Creative');
