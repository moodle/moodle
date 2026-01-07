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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource extends \Google\Model
{
  protected $accountType = GoogleAnalyticsAdminV1betaAccount::class;
  protected $accountDataType = '';
  protected $conversionEventType = GoogleAnalyticsAdminV1betaConversionEvent::class;
  protected $conversionEventDataType = '';
  protected $dataRetentionSettingsType = GoogleAnalyticsAdminV1betaDataRetentionSettings::class;
  protected $dataRetentionSettingsDataType = '';
  protected $dataStreamType = GoogleAnalyticsAdminV1betaDataStream::class;
  protected $dataStreamDataType = '';
  protected $firebaseLinkType = GoogleAnalyticsAdminV1betaFirebaseLink::class;
  protected $firebaseLinkDataType = '';
  protected $googleAdsLinkType = GoogleAnalyticsAdminV1betaGoogleAdsLink::class;
  protected $googleAdsLinkDataType = '';
  protected $measurementProtocolSecretType = GoogleAnalyticsAdminV1betaMeasurementProtocolSecret::class;
  protected $measurementProtocolSecretDataType = '';
  protected $propertyType = GoogleAnalyticsAdminV1betaProperty::class;
  protected $propertyDataType = '';

  /**
   * A snapshot of an Account resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaAccount $account
   */
  public function setAccount(GoogleAnalyticsAdminV1betaAccount $account)
  {
    $this->account = $account;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccount
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * A snapshot of a ConversionEvent resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaConversionEvent $conversionEvent
   */
  public function setConversionEvent(GoogleAnalyticsAdminV1betaConversionEvent $conversionEvent)
  {
    $this->conversionEvent = $conversionEvent;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaConversionEvent
   */
  public function getConversionEvent()
  {
    return $this->conversionEvent;
  }
  /**
   * A snapshot of a data retention settings resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaDataRetentionSettings $dataRetentionSettings
   */
  public function setDataRetentionSettings(GoogleAnalyticsAdminV1betaDataRetentionSettings $dataRetentionSettings)
  {
    $this->dataRetentionSettings = $dataRetentionSettings;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaDataRetentionSettings
   */
  public function getDataRetentionSettings()
  {
    return $this->dataRetentionSettings;
  }
  /**
   * A snapshot of a DataStream resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaDataStream $dataStream
   */
  public function setDataStream(GoogleAnalyticsAdminV1betaDataStream $dataStream)
  {
    $this->dataStream = $dataStream;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaDataStream
   */
  public function getDataStream()
  {
    return $this->dataStream;
  }
  /**
   * A snapshot of a FirebaseLink resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaFirebaseLink $firebaseLink
   */
  public function setFirebaseLink(GoogleAnalyticsAdminV1betaFirebaseLink $firebaseLink)
  {
    $this->firebaseLink = $firebaseLink;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaFirebaseLink
   */
  public function getFirebaseLink()
  {
    return $this->firebaseLink;
  }
  /**
   * A snapshot of a GoogleAdsLink resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaGoogleAdsLink $googleAdsLink
   */
  public function setGoogleAdsLink(GoogleAnalyticsAdminV1betaGoogleAdsLink $googleAdsLink)
  {
    $this->googleAdsLink = $googleAdsLink;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaGoogleAdsLink
   */
  public function getGoogleAdsLink()
  {
    return $this->googleAdsLink;
  }
  /**
   * A snapshot of a MeasurementProtocolSecret resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaMeasurementProtocolSecret $measurementProtocolSecret
   */
  public function setMeasurementProtocolSecret(GoogleAnalyticsAdminV1betaMeasurementProtocolSecret $measurementProtocolSecret)
  {
    $this->measurementProtocolSecret = $measurementProtocolSecret;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaMeasurementProtocolSecret
   */
  public function getMeasurementProtocolSecret()
  {
    return $this->measurementProtocolSecret;
  }
  /**
   * A snapshot of a Property resource in change history.
   *
   * @param GoogleAnalyticsAdminV1betaProperty $property
   */
  public function setProperty(GoogleAnalyticsAdminV1betaProperty $property)
  {
    $this->property = $property;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaProperty
   */
  public function getProperty()
  {
    return $this->property;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource');
