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

namespace Google\Service\AndroidPublisher;

class ExternalOfferDetails extends \Google\Model
{
  /**
   * Unspecified, do not use.
   */
  public const INSTALLED_APP_CATEGORY_EXTERNAL_OFFER_APP_CATEGORY_UNSPECIFIED = 'EXTERNAL_OFFER_APP_CATEGORY_UNSPECIFIED';
  /**
   * The app is classified under the app category.
   */
  public const INSTALLED_APP_CATEGORY_APP = 'APP';
  /**
   * The app is classified under the game category.
   */
  public const INSTALLED_APP_CATEGORY_GAME = 'GAME';
  /**
   * Unspecified, do not use.
   */
  public const LINK_TYPE_EXTERNAL_OFFER_LINK_TYPE_UNSPECIFIED = 'EXTERNAL_OFFER_LINK_TYPE_UNSPECIFIED';
  /**
   * An offer to purchase digital content.
   */
  public const LINK_TYPE_LINK_TO_DIGITAL_CONTENT_OFFER = 'LINK_TO_DIGITAL_CONTENT_OFFER';
  /**
   * An app install.
   */
  public const LINK_TYPE_LINK_TO_APP_DOWNLOAD = 'LINK_TO_APP_DOWNLOAD';
  /**
   * Optional. The external transaction id associated with the app download
   * event through an external link. Required when reporting transactions made
   * in externally installed apps.
   *
   * @var string
   */
  public $appDownloadEventExternalTransactionId;
  /**
   * Optional. The category of the downloaded app though this transaction. This
   * must match the category provided in Play Console during the external app
   * verification process. Only required for app downloads.
   *
   * @var string
   */
  public $installedAppCategory;
  /**
   * Optional. The package name of the app downloaded through this transaction.
   * Required when link_type is LINK_TO_APP_DOWNLOAD.
   *
   * @var string
   */
  public $installedAppPackage;
  /**
   * Optional. The type of content being reported by this transaction. Required
   * when reporting app downloads or purchased digital content offers made in
   * app installed through Google Play.
   *
   * @var string
   */
  public $linkType;

  /**
   * Optional. The external transaction id associated with the app download
   * event through an external link. Required when reporting transactions made
   * in externally installed apps.
   *
   * @param string $appDownloadEventExternalTransactionId
   */
  public function setAppDownloadEventExternalTransactionId($appDownloadEventExternalTransactionId)
  {
    $this->appDownloadEventExternalTransactionId = $appDownloadEventExternalTransactionId;
  }
  /**
   * @return string
   */
  public function getAppDownloadEventExternalTransactionId()
  {
    return $this->appDownloadEventExternalTransactionId;
  }
  /**
   * Optional. The category of the downloaded app though this transaction. This
   * must match the category provided in Play Console during the external app
   * verification process. Only required for app downloads.
   *
   * Accepted values: EXTERNAL_OFFER_APP_CATEGORY_UNSPECIFIED, APP, GAME
   *
   * @param self::INSTALLED_APP_CATEGORY_* $installedAppCategory
   */
  public function setInstalledAppCategory($installedAppCategory)
  {
    $this->installedAppCategory = $installedAppCategory;
  }
  /**
   * @return self::INSTALLED_APP_CATEGORY_*
   */
  public function getInstalledAppCategory()
  {
    return $this->installedAppCategory;
  }
  /**
   * Optional. The package name of the app downloaded through this transaction.
   * Required when link_type is LINK_TO_APP_DOWNLOAD.
   *
   * @param string $installedAppPackage
   */
  public function setInstalledAppPackage($installedAppPackage)
  {
    $this->installedAppPackage = $installedAppPackage;
  }
  /**
   * @return string
   */
  public function getInstalledAppPackage()
  {
    return $this->installedAppPackage;
  }
  /**
   * Optional. The type of content being reported by this transaction. Required
   * when reporting app downloads or purchased digital content offers made in
   * app installed through Google Play.
   *
   * Accepted values: EXTERNAL_OFFER_LINK_TYPE_UNSPECIFIED,
   * LINK_TO_DIGITAL_CONTENT_OFFER, LINK_TO_APP_DOWNLOAD
   *
   * @param self::LINK_TYPE_* $linkType
   */
  public function setLinkType($linkType)
  {
    $this->linkType = $linkType;
  }
  /**
   * @return self::LINK_TYPE_*
   */
  public function getLinkType()
  {
    return $this->linkType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalOfferDetails::class, 'Google_Service_AndroidPublisher_ExternalOfferDetails');
