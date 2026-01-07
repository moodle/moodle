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

namespace Google\Service\Analytics;

class FilterExpression extends \Google\Model
{
  /**
   * Determines if the filter is case sensitive.
   *
   * @var bool
   */
  public $caseSensitive;
  /**
   * Filter expression value
   *
   * @var string
   */
  public $expressionValue;
  /**
   * Field to filter. Possible values: - Content and Traffic   -
   * PAGE_REQUEST_URI,  - PAGE_HOSTNAME,  - PAGE_TITLE,  - REFERRAL,  -
   * COST_DATA_URI (Campaign target URL),  - HIT_TYPE,  - INTERNAL_SEARCH_TERM,
   * - INTERNAL_SEARCH_TYPE,  - SOURCE_PROPERTY_TRACKING_ID,    - Campaign or
   * AdGroup   - CAMPAIGN_SOURCE,  - CAMPAIGN_MEDIUM,  - CAMPAIGN_NAME,  -
   * CAMPAIGN_AD_GROUP,  - CAMPAIGN_TERM,  - CAMPAIGN_CONTENT,  - CAMPAIGN_CODE,
   * - CAMPAIGN_REFERRAL_PATH,    - E-Commerce   - TRANSACTION_COUNTRY,  -
   * TRANSACTION_REGION,  - TRANSACTION_CITY,  - TRANSACTION_AFFILIATION (Store
   * or order location),  - ITEM_NAME,  - ITEM_CODE,  - ITEM_VARIATION,  -
   * TRANSACTION_ID,  - TRANSACTION_CURRENCY_CODE,  - PRODUCT_ACTION_TYPE,    -
   * Audience/Users   - BROWSER,  - BROWSER_VERSION,  - BROWSER_SIZE,  -
   * PLATFORM,  - PLATFORM_VERSION,  - LANGUAGE,  - SCREEN_RESOLUTION,  -
   * SCREEN_COLORS,  - JAVA_ENABLED (Boolean Field),  - FLASH_VERSION,  -
   * GEO_SPEED (Connection speed),  - VISITOR_TYPE,  - GEO_ORGANIZATION (ISP
   * organization),  - GEO_DOMAIN,  - GEO_IP_ADDRESS,  - GEO_IP_VERSION,    -
   * Location   - GEO_COUNTRY,  - GEO_REGION,  - GEO_CITY,    - Event   -
   * EVENT_CATEGORY,  - EVENT_ACTION,  - EVENT_LABEL,    - Other   -
   * CUSTOM_FIELD_1,  - CUSTOM_FIELD_2,  - USER_DEFINED_VALUE,    - Application
   * - APP_ID,  - APP_INSTALLER_ID,  - APP_NAME,  - APP_VERSION,  - SCREEN,  -
   * IS_APP (Boolean Field),  - IS_FATAL_EXCEPTION (Boolean Field),  -
   * EXCEPTION_DESCRIPTION,    - Mobile device   - IS_MOBILE (Boolean Field,
   * Deprecated. Use DEVICE_CATEGORY=mobile),  - IS_TABLET (Boolean Field,
   * Deprecated. Use DEVICE_CATEGORY=tablet),  - DEVICE_CATEGORY,  -
   * MOBILE_HAS_QWERTY_KEYBOARD (Boolean Field),  - MOBILE_HAS_NFC_SUPPORT
   * (Boolean Field),  - MOBILE_HAS_CELLULAR_RADIO (Boolean Field),  -
   * MOBILE_HAS_WIFI_SUPPORT (Boolean Field),  - MOBILE_BRAND_NAME,  -
   * MOBILE_MODEL_NAME,  - MOBILE_MARKETING_NAME,  - MOBILE_POINTING_METHOD,
   * - Social   - SOCIAL_NETWORK,  - SOCIAL_ACTION,  - SOCIAL_ACTION_TARGET,
   * - Custom dimension   - CUSTOM_DIMENSION (See accompanying field index),
   *
   * @var string
   */
  public $field;
  /**
   * The Index of the custom dimension. Set only if the field is a is
   * CUSTOM_DIMENSION.
   *
   * @var int
   */
  public $fieldIndex;
  /**
   * Kind value for filter expression
   *
   * @var string
   */
  public $kind;
  /**
   * Match type for this filter. Possible values are BEGINS_WITH, EQUAL,
   * ENDS_WITH, CONTAINS, or MATCHES. GEO_DOMAIN, GEO_IP_ADDRESS,
   * PAGE_REQUEST_URI, or PAGE_HOSTNAME filters can use any match type; all
   * other filters must use MATCHES.
   *
   * @var string
   */
  public $matchType;

  /**
   * Determines if the filter is case sensitive.
   *
   * @param bool $caseSensitive
   */
  public function setCaseSensitive($caseSensitive)
  {
    $this->caseSensitive = $caseSensitive;
  }
  /**
   * @return bool
   */
  public function getCaseSensitive()
  {
    return $this->caseSensitive;
  }
  /**
   * Filter expression value
   *
   * @param string $expressionValue
   */
  public function setExpressionValue($expressionValue)
  {
    $this->expressionValue = $expressionValue;
  }
  /**
   * @return string
   */
  public function getExpressionValue()
  {
    return $this->expressionValue;
  }
  /**
   * Field to filter. Possible values: - Content and Traffic   -
   * PAGE_REQUEST_URI,  - PAGE_HOSTNAME,  - PAGE_TITLE,  - REFERRAL,  -
   * COST_DATA_URI (Campaign target URL),  - HIT_TYPE,  - INTERNAL_SEARCH_TERM,
   * - INTERNAL_SEARCH_TYPE,  - SOURCE_PROPERTY_TRACKING_ID,    - Campaign or
   * AdGroup   - CAMPAIGN_SOURCE,  - CAMPAIGN_MEDIUM,  - CAMPAIGN_NAME,  -
   * CAMPAIGN_AD_GROUP,  - CAMPAIGN_TERM,  - CAMPAIGN_CONTENT,  - CAMPAIGN_CODE,
   * - CAMPAIGN_REFERRAL_PATH,    - E-Commerce   - TRANSACTION_COUNTRY,  -
   * TRANSACTION_REGION,  - TRANSACTION_CITY,  - TRANSACTION_AFFILIATION (Store
   * or order location),  - ITEM_NAME,  - ITEM_CODE,  - ITEM_VARIATION,  -
   * TRANSACTION_ID,  - TRANSACTION_CURRENCY_CODE,  - PRODUCT_ACTION_TYPE,    -
   * Audience/Users   - BROWSER,  - BROWSER_VERSION,  - BROWSER_SIZE,  -
   * PLATFORM,  - PLATFORM_VERSION,  - LANGUAGE,  - SCREEN_RESOLUTION,  -
   * SCREEN_COLORS,  - JAVA_ENABLED (Boolean Field),  - FLASH_VERSION,  -
   * GEO_SPEED (Connection speed),  - VISITOR_TYPE,  - GEO_ORGANIZATION (ISP
   * organization),  - GEO_DOMAIN,  - GEO_IP_ADDRESS,  - GEO_IP_VERSION,    -
   * Location   - GEO_COUNTRY,  - GEO_REGION,  - GEO_CITY,    - Event   -
   * EVENT_CATEGORY,  - EVENT_ACTION,  - EVENT_LABEL,    - Other   -
   * CUSTOM_FIELD_1,  - CUSTOM_FIELD_2,  - USER_DEFINED_VALUE,    - Application
   * - APP_ID,  - APP_INSTALLER_ID,  - APP_NAME,  - APP_VERSION,  - SCREEN,  -
   * IS_APP (Boolean Field),  - IS_FATAL_EXCEPTION (Boolean Field),  -
   * EXCEPTION_DESCRIPTION,    - Mobile device   - IS_MOBILE (Boolean Field,
   * Deprecated. Use DEVICE_CATEGORY=mobile),  - IS_TABLET (Boolean Field,
   * Deprecated. Use DEVICE_CATEGORY=tablet),  - DEVICE_CATEGORY,  -
   * MOBILE_HAS_QWERTY_KEYBOARD (Boolean Field),  - MOBILE_HAS_NFC_SUPPORT
   * (Boolean Field),  - MOBILE_HAS_CELLULAR_RADIO (Boolean Field),  -
   * MOBILE_HAS_WIFI_SUPPORT (Boolean Field),  - MOBILE_BRAND_NAME,  -
   * MOBILE_MODEL_NAME,  - MOBILE_MARKETING_NAME,  - MOBILE_POINTING_METHOD,
   * - Social   - SOCIAL_NETWORK,  - SOCIAL_ACTION,  - SOCIAL_ACTION_TARGET,
   * - Custom dimension   - CUSTOM_DIMENSION (See accompanying field index),
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * The Index of the custom dimension. Set only if the field is a is
   * CUSTOM_DIMENSION.
   *
   * @param int $fieldIndex
   */
  public function setFieldIndex($fieldIndex)
  {
    $this->fieldIndex = $fieldIndex;
  }
  /**
   * @return int
   */
  public function getFieldIndex()
  {
    return $this->fieldIndex;
  }
  /**
   * Kind value for filter expression
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
   * Match type for this filter. Possible values are BEGINS_WITH, EQUAL,
   * ENDS_WITH, CONTAINS, or MATCHES. GEO_DOMAIN, GEO_IP_ADDRESS,
   * PAGE_REQUEST_URI, or PAGE_HOSTNAME filters can use any match type; all
   * other filters must use MATCHES.
   *
   * @param string $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return string
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterExpression::class, 'Google_Service_Analytics_FilterExpression');
