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

namespace Google\Service\AuthorizedBuyersMarketplace;

class CreativeRequirements extends \Google\Model
{
  /**
   * A placeholder for an unspecified creative format.
   */
  public const CREATIVE_FORMAT_CREATIVE_FORMAT_UNSPECIFIED = 'CREATIVE_FORMAT_UNSPECIFIED';
  /**
   * Banner creatives such as image or HTML5 assets.
   */
  public const CREATIVE_FORMAT_DISPLAY = 'DISPLAY';
  /**
   * Video creatives that can be played in a video player.
   */
  public const CREATIVE_FORMAT_VIDEO = 'VIDEO';
  /**
   * Audio creatives that can play during audio content or point to a third
   * party ad server.
   */
  public const CREATIVE_FORMAT_AUDIO = 'AUDIO';
  /**
   * A placeholder for an undefined creative pre-approval policy.
   */
  public const CREATIVE_PRE_APPROVAL_POLICY_CREATIVE_PRE_APPROVAL_POLICY_UNSPECIFIED = 'CREATIVE_PRE_APPROVAL_POLICY_UNSPECIFIED';
  /**
   * The seller needs to approve each creative before it can serve.
   */
  public const CREATIVE_PRE_APPROVAL_POLICY_SELLER_PRE_APPROVAL_REQUIRED = 'SELLER_PRE_APPROVAL_REQUIRED';
  /**
   * The seller does not need to approve each creative before it can serve.
   */
  public const CREATIVE_PRE_APPROVAL_POLICY_SELLER_PRE_APPROVAL_NOT_REQUIRED = 'SELLER_PRE_APPROVAL_NOT_REQUIRED';
  /**
   * A placeholder for an undefined creative safe-frame compatibility.
   */
  public const CREATIVE_SAFE_FRAME_COMPATIBILITY_CREATIVE_SAFE_FRAME_COMPATIBILITY_UNSPECIFIED = 'CREATIVE_SAFE_FRAME_COMPATIBILITY_UNSPECIFIED';
  /**
   * The creatives need to be compatible with the safe frame option.
   */
  public const CREATIVE_SAFE_FRAME_COMPATIBILITY_COMPATIBLE = 'COMPATIBLE';
  /**
   * The creatives can be incompatible with the safe frame option.
   */
  public const CREATIVE_SAFE_FRAME_COMPATIBILITY_INCOMPATIBLE = 'INCOMPATIBLE';
  /**
   * A placeholder for an undefined programmatic creative source.
   */
  public const PROGRAMMATIC_CREATIVE_SOURCE_PROGRAMMATIC_CREATIVE_SOURCE_UNSPECIFIED = 'PROGRAMMATIC_CREATIVE_SOURCE_UNSPECIFIED';
  /**
   * The advertiser provides the creatives.
   */
  public const PROGRAMMATIC_CREATIVE_SOURCE_ADVERTISER = 'ADVERTISER';
  /**
   * The publisher provides the creatives to be served.
   */
  public const PROGRAMMATIC_CREATIVE_SOURCE_PUBLISHER = 'PUBLISHER';
  /**
   * A placeholder for an unspecified skippable ad type.
   */
  public const SKIPPABLE_AD_TYPE_SKIPPABLE_AD_TYPE_UNSPECIFIED = 'SKIPPABLE_AD_TYPE_UNSPECIFIED';
  /**
   * Video ad that can be skipped after 5 seconds. This value will appear in RTB
   * bid requests as SkippableBidRequestType::REQUIRE_SKIPPABLE.
   */
  public const SKIPPABLE_AD_TYPE_SKIPPABLE = 'SKIPPABLE';
  /**
   * Video ad that can be skipped after 5 seconds, and is counted as engaged
   * view after 30 seconds. The creative is hosted on YouTube only, and
   * viewcount of the YouTube video increments after the engaged view. This
   * value will appear in RTB bid requests as
   * SkippableBidRequestType::REQUIRE_SKIPPABLE.
   */
  public const SKIPPABLE_AD_TYPE_INSTREAM_SELECT = 'INSTREAM_SELECT';
  /**
   * This video ad is not skippable. This value will appear in RTB bid requests
   * as SkippableBidRequestType::BLOCK_SKIPPABLE.
   */
  public const SKIPPABLE_AD_TYPE_NOT_SKIPPABLE = 'NOT_SKIPPABLE';
  /**
   * This video ad can be skipped after 5 seconds or not-skippable. This value
   * will appear in RTB bid requests as
   * SkippableBidRequestType::ALLOW_SKIPPABLE.
   */
  public const SKIPPABLE_AD_TYPE_ANY = 'ANY';
  /**
   * Output only. The format of the creative, only applicable for programmatic
   * guaranteed and preferred deals.
   *
   * @var string
   */
  public $creativeFormat;
  /**
   * Output only. Specifies the creative pre-approval policy.
   *
   * @var string
   */
  public $creativePreApprovalPolicy;
  /**
   * Output only. Specifies whether the creative is safeFrame compatible.
   *
   * @var string
   */
  public $creativeSafeFrameCompatibility;
  /**
   * Output only. The max duration of the video creative in milliseconds. only
   * applicable for deals with video creatives.
   *
   * @var string
   */
  public $maxAdDurationMs;
  /**
   * Output only. Specifies the creative source for programmatic deals.
   * PUBLISHER means creative is provided by seller and ADVERTISER means
   * creative is provided by the buyer.
   *
   * @var string
   */
  public $programmaticCreativeSource;
  /**
   * Output only. Skippable video ads allow viewers to skip ads after 5 seconds.
   * Only applicable for deals with video creatives.
   *
   * @var string
   */
  public $skippableAdType;

  /**
   * Output only. The format of the creative, only applicable for programmatic
   * guaranteed and preferred deals.
   *
   * Accepted values: CREATIVE_FORMAT_UNSPECIFIED, DISPLAY, VIDEO, AUDIO
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
   * Output only. Specifies the creative pre-approval policy.
   *
   * Accepted values: CREATIVE_PRE_APPROVAL_POLICY_UNSPECIFIED,
   * SELLER_PRE_APPROVAL_REQUIRED, SELLER_PRE_APPROVAL_NOT_REQUIRED
   *
   * @param self::CREATIVE_PRE_APPROVAL_POLICY_* $creativePreApprovalPolicy
   */
  public function setCreativePreApprovalPolicy($creativePreApprovalPolicy)
  {
    $this->creativePreApprovalPolicy = $creativePreApprovalPolicy;
  }
  /**
   * @return self::CREATIVE_PRE_APPROVAL_POLICY_*
   */
  public function getCreativePreApprovalPolicy()
  {
    return $this->creativePreApprovalPolicy;
  }
  /**
   * Output only. Specifies whether the creative is safeFrame compatible.
   *
   * Accepted values: CREATIVE_SAFE_FRAME_COMPATIBILITY_UNSPECIFIED, COMPATIBLE,
   * INCOMPATIBLE
   *
   * @param self::CREATIVE_SAFE_FRAME_COMPATIBILITY_* $creativeSafeFrameCompatibility
   */
  public function setCreativeSafeFrameCompatibility($creativeSafeFrameCompatibility)
  {
    $this->creativeSafeFrameCompatibility = $creativeSafeFrameCompatibility;
  }
  /**
   * @return self::CREATIVE_SAFE_FRAME_COMPATIBILITY_*
   */
  public function getCreativeSafeFrameCompatibility()
  {
    return $this->creativeSafeFrameCompatibility;
  }
  /**
   * Output only. The max duration of the video creative in milliseconds. only
   * applicable for deals with video creatives.
   *
   * @param string $maxAdDurationMs
   */
  public function setMaxAdDurationMs($maxAdDurationMs)
  {
    $this->maxAdDurationMs = $maxAdDurationMs;
  }
  /**
   * @return string
   */
  public function getMaxAdDurationMs()
  {
    return $this->maxAdDurationMs;
  }
  /**
   * Output only. Specifies the creative source for programmatic deals.
   * PUBLISHER means creative is provided by seller and ADVERTISER means
   * creative is provided by the buyer.
   *
   * Accepted values: PROGRAMMATIC_CREATIVE_SOURCE_UNSPECIFIED, ADVERTISER,
   * PUBLISHER
   *
   * @param self::PROGRAMMATIC_CREATIVE_SOURCE_* $programmaticCreativeSource
   */
  public function setProgrammaticCreativeSource($programmaticCreativeSource)
  {
    $this->programmaticCreativeSource = $programmaticCreativeSource;
  }
  /**
   * @return self::PROGRAMMATIC_CREATIVE_SOURCE_*
   */
  public function getProgrammaticCreativeSource()
  {
    return $this->programmaticCreativeSource;
  }
  /**
   * Output only. Skippable video ads allow viewers to skip ads after 5 seconds.
   * Only applicable for deals with video creatives.
   *
   * Accepted values: SKIPPABLE_AD_TYPE_UNSPECIFIED, SKIPPABLE, INSTREAM_SELECT,
   * NOT_SKIPPABLE, ANY
   *
   * @param self::SKIPPABLE_AD_TYPE_* $skippableAdType
   */
  public function setSkippableAdType($skippableAdType)
  {
    $this->skippableAdType = $skippableAdType;
  }
  /**
   * @return self::SKIPPABLE_AD_TYPE_*
   */
  public function getSkippableAdType()
  {
    return $this->skippableAdType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeRequirements::class, 'Google_Service_AuthorizedBuyersMarketplace_CreativeRequirements');
