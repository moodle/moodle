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

namespace Google\Service\Dfareporting;

class SiteSettings extends \Google\Model
{
  /**
   * DEFAULT means Google chooses which adapter, if any, to serve.
   */
  public const VPAID_ADAPTER_CHOICE_TEMPLATE_DEFAULT = 'DEFAULT';
  public const VPAID_ADAPTER_CHOICE_TEMPLATE_FLASH = 'FLASH';
  public const VPAID_ADAPTER_CHOICE_TEMPLATE_HTML5 = 'HTML5';
  public const VPAID_ADAPTER_CHOICE_TEMPLATE_BOTH = 'BOTH';
  /**
   * Whether active view creatives are disabled for this site.
   *
   * @var bool
   */
  public $activeViewOptOut;
  /**
   * Whether this site opts out of ad blocking. When true, ad blocking is
   * disabled for all placements under the site, regardless of the individual
   * placement settings. When false, the campaign and placement settings take
   * effect.
   *
   * @var bool
   */
  public $adBlockingOptOut;
  /**
   * Whether new cookies are disabled for this site.
   *
   * @var bool
   */
  public $disableNewCookie;
  protected $tagSettingType = TagSetting::class;
  protected $tagSettingDataType = '';
  /**
   * Whether Verification and ActiveView for in-stream video creatives are
   * disabled by default for new placements created under this site. This value
   * will be used to populate the placement.videoActiveViewOptOut field, when no
   * value is specified for the new placement.
   *
   * @var bool
   */
  public $videoActiveViewOptOutTemplate;
  /**
   * Default VPAID adapter setting for new placements created under this site.
   * This value will be used to populate the placements.vpaidAdapterChoice
   * field, when no value is specified for the new placement. Controls which
   * VPAID format the measurement adapter will use for in-stream video creatives
   * assigned to the placement. The publisher's specifications will typically
   * determine this setting. For VPAID creatives, the adapter format will match
   * the VPAID format (HTML5 VPAID creatives use the HTML5 adapter). *Note:*
   * Flash is no longer supported. This field now defaults to HTML5 when the
   * following values are provided: FLASH, BOTH.
   *
   * @var string
   */
  public $vpaidAdapterChoiceTemplate;

  /**
   * Whether active view creatives are disabled for this site.
   *
   * @param bool $activeViewOptOut
   */
  public function setActiveViewOptOut($activeViewOptOut)
  {
    $this->activeViewOptOut = $activeViewOptOut;
  }
  /**
   * @return bool
   */
  public function getActiveViewOptOut()
  {
    return $this->activeViewOptOut;
  }
  /**
   * Whether this site opts out of ad blocking. When true, ad blocking is
   * disabled for all placements under the site, regardless of the individual
   * placement settings. When false, the campaign and placement settings take
   * effect.
   *
   * @param bool $adBlockingOptOut
   */
  public function setAdBlockingOptOut($adBlockingOptOut)
  {
    $this->adBlockingOptOut = $adBlockingOptOut;
  }
  /**
   * @return bool
   */
  public function getAdBlockingOptOut()
  {
    return $this->adBlockingOptOut;
  }
  /**
   * Whether new cookies are disabled for this site.
   *
   * @param bool $disableNewCookie
   */
  public function setDisableNewCookie($disableNewCookie)
  {
    $this->disableNewCookie = $disableNewCookie;
  }
  /**
   * @return bool
   */
  public function getDisableNewCookie()
  {
    return $this->disableNewCookie;
  }
  /**
   * Configuration settings for dynamic and image floodlight tags.
   *
   * @param TagSetting $tagSetting
   */
  public function setTagSetting(TagSetting $tagSetting)
  {
    $this->tagSetting = $tagSetting;
  }
  /**
   * @return TagSetting
   */
  public function getTagSetting()
  {
    return $this->tagSetting;
  }
  /**
   * Whether Verification and ActiveView for in-stream video creatives are
   * disabled by default for new placements created under this site. This value
   * will be used to populate the placement.videoActiveViewOptOut field, when no
   * value is specified for the new placement.
   *
   * @param bool $videoActiveViewOptOutTemplate
   */
  public function setVideoActiveViewOptOutTemplate($videoActiveViewOptOutTemplate)
  {
    $this->videoActiveViewOptOutTemplate = $videoActiveViewOptOutTemplate;
  }
  /**
   * @return bool
   */
  public function getVideoActiveViewOptOutTemplate()
  {
    return $this->videoActiveViewOptOutTemplate;
  }
  /**
   * Default VPAID adapter setting for new placements created under this site.
   * This value will be used to populate the placements.vpaidAdapterChoice
   * field, when no value is specified for the new placement. Controls which
   * VPAID format the measurement adapter will use for in-stream video creatives
   * assigned to the placement. The publisher's specifications will typically
   * determine this setting. For VPAID creatives, the adapter format will match
   * the VPAID format (HTML5 VPAID creatives use the HTML5 adapter). *Note:*
   * Flash is no longer supported. This field now defaults to HTML5 when the
   * following values are provided: FLASH, BOTH.
   *
   * Accepted values: DEFAULT, FLASH, HTML5, BOTH
   *
   * @param self::VPAID_ADAPTER_CHOICE_TEMPLATE_* $vpaidAdapterChoiceTemplate
   */
  public function setVpaidAdapterChoiceTemplate($vpaidAdapterChoiceTemplate)
  {
    $this->vpaidAdapterChoiceTemplate = $vpaidAdapterChoiceTemplate;
  }
  /**
   * @return self::VPAID_ADAPTER_CHOICE_TEMPLATE_*
   */
  public function getVpaidAdapterChoiceTemplate()
  {
    return $this->vpaidAdapterChoiceTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SiteSettings::class, 'Google_Service_Dfareporting_SiteSettings');
