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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesConversionTrackingSetting extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CONVERSION_TRACKING_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const CONVERSION_TRACKING_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Customer does not use any conversion tracking.
   */
  public const CONVERSION_TRACKING_STATUS_NOT_CONVERSION_TRACKED = 'NOT_CONVERSION_TRACKED';
  /**
   * The conversion actions are created and managed by this customer.
   */
  public const CONVERSION_TRACKING_STATUS_CONVERSION_TRACKING_MANAGED_BY_SELF = 'CONVERSION_TRACKING_MANAGED_BY_SELF';
  /**
   * The conversion actions are created and managed by the manager specified in
   * the request's `login-customer-id`.
   */
  public const CONVERSION_TRACKING_STATUS_CONVERSION_TRACKING_MANAGED_BY_THIS_MANAGER = 'CONVERSION_TRACKING_MANAGED_BY_THIS_MANAGER';
  /**
   * The conversion actions are created and managed by a manager different from
   * the customer or manager specified in the request's `login-customer-id`.
   */
  public const CONVERSION_TRACKING_STATUS_CONVERSION_TRACKING_MANAGED_BY_ANOTHER_MANAGER = 'CONVERSION_TRACKING_MANAGED_BY_ANOTHER_MANAGER';
  /**
   * Output only. Whether the customer has accepted customer data terms. If
   * using cross-account conversion tracking, this value is inherited from the
   * manager. This field is read-only. For more information, see
   * https://support.google.com/adspolicy/answer/7475709.
   *
   * @var bool
   */
  public $acceptedCustomerDataTerms;
  /**
   * Output only. The conversion tracking id used for this account. This id
   * doesn't indicate whether the customer uses conversion tracking
   * (conversion_tracking_status does). This field is read-only.
   *
   * @var string
   */
  public $conversionTrackingId;
  /**
   * Output only. Conversion tracking status. It indicates whether the customer
   * is using conversion tracking, and who is the conversion tracking owner of
   * this customer. If this customer is using cross-account conversion tracking,
   * the value returned will differ based on the `login-customer-id` of the
   * request.
   *
   * @var string
   */
  public $conversionTrackingStatus;
  /**
   * Output only. The conversion tracking id of the customer's manager. This is
   * set when the customer is opted into cross-account conversion tracking, and
   * it overrides conversion_tracking_id.
   *
   * @var string
   */
  public $crossAccountConversionTrackingId;
  /**
   * Output only. Whether the customer is opted-in for enhanced conversions for
   * leads. If using cross-account conversion tracking, this value is inherited
   * from the manager. This field is read-only.
   *
   * @var bool
   */
  public $enhancedConversionsForLeadsEnabled;
  /**
   * Output only. The resource name of the customer where conversions are
   * created and managed. This field is read-only.
   *
   * @var string
   */
  public $googleAdsConversionCustomer;
  /**
   * Output only. The conversion tracking id of the customer's manager. This is
   * set when the customer is opted into conversion tracking, and it overrides
   * conversion_tracking_id. This field can only be managed through the Google
   * Ads UI. This field is read-only.
   *
   * @var string
   */
  public $googleAdsCrossAccountConversionTrackingId;

  /**
   * Output only. Whether the customer has accepted customer data terms. If
   * using cross-account conversion tracking, this value is inherited from the
   * manager. This field is read-only. For more information, see
   * https://support.google.com/adspolicy/answer/7475709.
   *
   * @param bool $acceptedCustomerDataTerms
   */
  public function setAcceptedCustomerDataTerms($acceptedCustomerDataTerms)
  {
    $this->acceptedCustomerDataTerms = $acceptedCustomerDataTerms;
  }
  /**
   * @return bool
   */
  public function getAcceptedCustomerDataTerms()
  {
    return $this->acceptedCustomerDataTerms;
  }
  /**
   * Output only. The conversion tracking id used for this account. This id
   * doesn't indicate whether the customer uses conversion tracking
   * (conversion_tracking_status does). This field is read-only.
   *
   * @param string $conversionTrackingId
   */
  public function setConversionTrackingId($conversionTrackingId)
  {
    $this->conversionTrackingId = $conversionTrackingId;
  }
  /**
   * @return string
   */
  public function getConversionTrackingId()
  {
    return $this->conversionTrackingId;
  }
  /**
   * Output only. Conversion tracking status. It indicates whether the customer
   * is using conversion tracking, and who is the conversion tracking owner of
   * this customer. If this customer is using cross-account conversion tracking,
   * the value returned will differ based on the `login-customer-id` of the
   * request.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, NOT_CONVERSION_TRACKED,
   * CONVERSION_TRACKING_MANAGED_BY_SELF,
   * CONVERSION_TRACKING_MANAGED_BY_THIS_MANAGER,
   * CONVERSION_TRACKING_MANAGED_BY_ANOTHER_MANAGER
   *
   * @param self::CONVERSION_TRACKING_STATUS_* $conversionTrackingStatus
   */
  public function setConversionTrackingStatus($conversionTrackingStatus)
  {
    $this->conversionTrackingStatus = $conversionTrackingStatus;
  }
  /**
   * @return self::CONVERSION_TRACKING_STATUS_*
   */
  public function getConversionTrackingStatus()
  {
    return $this->conversionTrackingStatus;
  }
  /**
   * Output only. The conversion tracking id of the customer's manager. This is
   * set when the customer is opted into cross-account conversion tracking, and
   * it overrides conversion_tracking_id.
   *
   * @param string $crossAccountConversionTrackingId
   */
  public function setCrossAccountConversionTrackingId($crossAccountConversionTrackingId)
  {
    $this->crossAccountConversionTrackingId = $crossAccountConversionTrackingId;
  }
  /**
   * @return string
   */
  public function getCrossAccountConversionTrackingId()
  {
    return $this->crossAccountConversionTrackingId;
  }
  /**
   * Output only. Whether the customer is opted-in for enhanced conversions for
   * leads. If using cross-account conversion tracking, this value is inherited
   * from the manager. This field is read-only.
   *
   * @param bool $enhancedConversionsForLeadsEnabled
   */
  public function setEnhancedConversionsForLeadsEnabled($enhancedConversionsForLeadsEnabled)
  {
    $this->enhancedConversionsForLeadsEnabled = $enhancedConversionsForLeadsEnabled;
  }
  /**
   * @return bool
   */
  public function getEnhancedConversionsForLeadsEnabled()
  {
    return $this->enhancedConversionsForLeadsEnabled;
  }
  /**
   * Output only. The resource name of the customer where conversions are
   * created and managed. This field is read-only.
   *
   * @param string $googleAdsConversionCustomer
   */
  public function setGoogleAdsConversionCustomer($googleAdsConversionCustomer)
  {
    $this->googleAdsConversionCustomer = $googleAdsConversionCustomer;
  }
  /**
   * @return string
   */
  public function getGoogleAdsConversionCustomer()
  {
    return $this->googleAdsConversionCustomer;
  }
  /**
   * Output only. The conversion tracking id of the customer's manager. This is
   * set when the customer is opted into conversion tracking, and it overrides
   * conversion_tracking_id. This field can only be managed through the Google
   * Ads UI. This field is read-only.
   *
   * @param string $googleAdsCrossAccountConversionTrackingId
   */
  public function setGoogleAdsCrossAccountConversionTrackingId($googleAdsCrossAccountConversionTrackingId)
  {
    $this->googleAdsCrossAccountConversionTrackingId = $googleAdsCrossAccountConversionTrackingId;
  }
  /**
   * @return string
   */
  public function getGoogleAdsCrossAccountConversionTrackingId()
  {
    return $this->googleAdsCrossAccountConversionTrackingId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesConversionTrackingSetting::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesConversionTrackingSetting');
