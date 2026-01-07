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

class EventTag extends \Google\Collection
{
  /**
   * The event tag should only ever fire on specified sites.
   */
  public const SITE_FILTER_TYPE_ALLOWLIST = 'ALLOWLIST';
  /**
   * The event tag should fire on all sites EXCEPT the specified sites.
   */
  public const SITE_FILTER_TYPE_BLOCKLIST = 'BLOCKLIST';
  public const STATUS_ENABLED = 'ENABLED';
  public const STATUS_DISABLED = 'DISABLED';
  /**
   * A third-party pixel for impression tracking.
   */
  public const TYPE_IMPRESSION_IMAGE_EVENT_TAG = 'IMPRESSION_IMAGE_EVENT_TAG';
  /**
   * A third-party JavaScript URL for impression tracking.
   */
  public const TYPE_IMPRESSION_JAVASCRIPT_EVENT_TAG = 'IMPRESSION_JAVASCRIPT_EVENT_TAG';
  /**
   * A third-party URL for click tracking that redirects to the landing page.
   */
  public const TYPE_CLICK_THROUGH_EVENT_TAG = 'CLICK_THROUGH_EVENT_TAG';
  protected $collection_key = 'siteIds';
  /**
   * Account ID of this event tag. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Advertiser ID of this event tag. This field or the campaignId field is
   * required on insertion.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Campaign ID of this event tag. This field or the advertiserId field is
   * required on insertion.
   *
   * @var string
   */
  public $campaignId;
  protected $campaignIdDimensionValueType = DimensionValue::class;
  protected $campaignIdDimensionValueDataType = '';
  /**
   * Whether this event tag should be automatically enabled for all of the
   * advertiser's campaigns and ads.
   *
   * @var bool
   */
  public $enabledByDefault;
  /**
   * Whether to remove this event tag from ads that are trafficked through
   * Display & Video 360 to Ad Exchange. This may be useful if the event tag
   * uses a pixel that is unapproved for Ad Exchange bids on one or more
   * networks, such as the Google Display Network.
   *
   * @var bool
   */
  public $excludeFromAdxRequests;
  /**
   * ID of this event tag. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#eventTag".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this event tag. This is a required field and must be less than 256
   * characters long.
   *
   * @var string
   */
  public $name;
  /**
   * Site filter type for this event tag. If no type is specified then the event
   * tag will be applied to all sites.
   *
   * @var string
   */
  public $siteFilterType;
  /**
   * Filter list of site IDs associated with this event tag. The siteFilterType
   * determines whether this is a allowlist or blocklist filter.
   *
   * @var string[]
   */
  public $siteIds;
  /**
   * Whether this tag is SSL-compliant or not. This is a read-only field.
   *
   * @var bool
   */
  public $sslCompliant;
  /**
   * Status of this event tag. Must be ENABLED for this event tag to fire. This
   * is a required field.
   *
   * @var string
   */
  public $status;
  /**
   * Subaccount ID of this event tag. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Event tag type. Can be used to specify whether to use a third-party pixel,
   * a third-party JavaScript URL, or a third-party click-through URL for either
   * impression or click tracking. This is a required field.
   *
   * @var string
   */
  public $type;
  /**
   * Payload URL for this event tag. The URL on a click-through event tag should
   * have a landing page URL appended to the end of it. This field is required
   * on insertion.
   *
   * @var string
   */
  public $url;
  /**
   * Number of times the landing page URL should be URL-escaped before being
   * appended to the click-through event tag URL. Only applies to click-through
   * event tags as specified by the event tag type.
   *
   * @var int
   */
  public $urlEscapeLevels;

  /**
   * Account ID of this event tag. This is a read-only field that can be left
   * blank.
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
   * Advertiser ID of this event tag. This field or the campaignId field is
   * required on insertion.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Dimension value for the ID of the advertiser. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $advertiserIdDimensionValue
   */
  public function setAdvertiserIdDimensionValue(DimensionValue $advertiserIdDimensionValue)
  {
    $this->advertiserIdDimensionValue = $advertiserIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getAdvertiserIdDimensionValue()
  {
    return $this->advertiserIdDimensionValue;
  }
  /**
   * Campaign ID of this event tag. This field or the advertiserId field is
   * required on insertion.
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * Dimension value for the ID of the campaign. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $campaignIdDimensionValue
   */
  public function setCampaignIdDimensionValue(DimensionValue $campaignIdDimensionValue)
  {
    $this->campaignIdDimensionValue = $campaignIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getCampaignIdDimensionValue()
  {
    return $this->campaignIdDimensionValue;
  }
  /**
   * Whether this event tag should be automatically enabled for all of the
   * advertiser's campaigns and ads.
   *
   * @param bool $enabledByDefault
   */
  public function setEnabledByDefault($enabledByDefault)
  {
    $this->enabledByDefault = $enabledByDefault;
  }
  /**
   * @return bool
   */
  public function getEnabledByDefault()
  {
    return $this->enabledByDefault;
  }
  /**
   * Whether to remove this event tag from ads that are trafficked through
   * Display & Video 360 to Ad Exchange. This may be useful if the event tag
   * uses a pixel that is unapproved for Ad Exchange bids on one or more
   * networks, such as the Google Display Network.
   *
   * @param bool $excludeFromAdxRequests
   */
  public function setExcludeFromAdxRequests($excludeFromAdxRequests)
  {
    $this->excludeFromAdxRequests = $excludeFromAdxRequests;
  }
  /**
   * @return bool
   */
  public function getExcludeFromAdxRequests()
  {
    return $this->excludeFromAdxRequests;
  }
  /**
   * ID of this event tag. This is a read-only, auto-generated field.
   *
   * @param string $id
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#eventTag".
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
   * Name of this event tag. This is a required field and must be less than 256
   * characters long.
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
   * Site filter type for this event tag. If no type is specified then the event
   * tag will be applied to all sites.
   *
   * Accepted values: ALLOWLIST, BLOCKLIST
   *
   * @param self::SITE_FILTER_TYPE_* $siteFilterType
   */
  public function setSiteFilterType($siteFilterType)
  {
    $this->siteFilterType = $siteFilterType;
  }
  /**
   * @return self::SITE_FILTER_TYPE_*
   */
  public function getSiteFilterType()
  {
    return $this->siteFilterType;
  }
  /**
   * Filter list of site IDs associated with this event tag. The siteFilterType
   * determines whether this is a allowlist or blocklist filter.
   *
   * @param string[] $siteIds
   */
  public function setSiteIds($siteIds)
  {
    $this->siteIds = $siteIds;
  }
  /**
   * @return string[]
   */
  public function getSiteIds()
  {
    return $this->siteIds;
  }
  /**
   * Whether this tag is SSL-compliant or not. This is a read-only field.
   *
   * @param bool $sslCompliant
   */
  public function setSslCompliant($sslCompliant)
  {
    $this->sslCompliant = $sslCompliant;
  }
  /**
   * @return bool
   */
  public function getSslCompliant()
  {
    return $this->sslCompliant;
  }
  /**
   * Status of this event tag. Must be ENABLED for this event tag to fire. This
   * is a required field.
   *
   * Accepted values: ENABLED, DISABLED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Subaccount ID of this event tag. This is a read-only field that can be left
   * blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Event tag type. Can be used to specify whether to use a third-party pixel,
   * a third-party JavaScript URL, or a third-party click-through URL for either
   * impression or click tracking. This is a required field.
   *
   * Accepted values: IMPRESSION_IMAGE_EVENT_TAG,
   * IMPRESSION_JAVASCRIPT_EVENT_TAG, CLICK_THROUGH_EVENT_TAG
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Payload URL for this event tag. The URL on a click-through event tag should
   * have a landing page URL appended to the end of it. This field is required
   * on insertion.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * Number of times the landing page URL should be URL-escaped before being
   * appended to the click-through event tag URL. Only applies to click-through
   * event tags as specified by the event tag type.
   *
   * @param int $urlEscapeLevels
   */
  public function setUrlEscapeLevels($urlEscapeLevels)
  {
    $this->urlEscapeLevels = $urlEscapeLevels;
  }
  /**
   * @return int
   */
  public function getUrlEscapeLevels()
  {
    return $this->urlEscapeLevels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventTag::class, 'Google_Service_Dfareporting_EventTag');
