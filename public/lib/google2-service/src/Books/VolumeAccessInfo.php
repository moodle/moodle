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

namespace Google\Service\Books;

class VolumeAccessInfo extends \Google\Model
{
  /**
   * Combines the access and viewability of this volume into a single status
   * field for this user. Values can be FULL_PURCHASED, FULL_PUBLIC_DOMAIN,
   * SAMPLE or NONE. (In LITE projection.)
   *
   * @var string
   */
  public $accessViewStatus;
  /**
   * The two-letter ISO_3166-1 country code for which this access information is
   * valid. (In LITE projection.)
   *
   * @var string
   */
  public $country;
  protected $downloadAccessType = DownloadAccessRestriction::class;
  protected $downloadAccessDataType = '';
  /**
   * URL to the Google Drive viewer if this volume is uploaded by the user by
   * selecting the file from Google Drive.
   *
   * @var string
   */
  public $driveImportedContentLink;
  /**
   * Whether this volume can be embedded in a viewport using the Embedded Viewer
   * API.
   *
   * @var bool
   */
  public $embeddable;
  protected $epubType = VolumeAccessInfoEpub::class;
  protected $epubDataType = '';
  /**
   * Whether this volume requires that the client explicitly request offline
   * download license rather than have it done automatically when loading the
   * content, if the client supports it.
   *
   * @var bool
   */
  public $explicitOfflineLicenseManagement;
  protected $pdfType = VolumeAccessInfoPdf::class;
  protected $pdfDataType = '';
  /**
   * Whether or not this book is public domain in the country listed above.
   *
   * @var bool
   */
  public $publicDomain;
  /**
   * Whether quote sharing is allowed for this volume.
   *
   * @var bool
   */
  public $quoteSharingAllowed;
  /**
   * Whether text-to-speech is permitted for this volume. Values can be ALLOWED,
   * ALLOWED_FOR_ACCESSIBILITY, or NOT_ALLOWED.
   *
   * @var string
   */
  public $textToSpeechPermission;
  /**
   * For ordered but not yet processed orders, we give a URL that can be used to
   * go to the appropriate Google Wallet page.
   *
   * @var string
   */
  public $viewOrderUrl;
  /**
   * The read access of a volume. Possible values are PARTIAL, ALL_PAGES,
   * NO_PAGES or UNKNOWN. This value depends on the country listed above. A
   * value of PARTIAL means that the publisher has allowed some portion of the
   * volume to be viewed publicly, without purchase. This can apply to eBooks as
   * well as non-eBooks. Public domain books will always have a value of
   * ALL_PAGES.
   *
   * @var string
   */
  public $viewability;
  /**
   * URL to read this volume on the Google Books site. Link will not allow users
   * to read non-viewable volumes.
   *
   * @var string
   */
  public $webReaderLink;

  /**
   * Combines the access and viewability of this volume into a single status
   * field for this user. Values can be FULL_PURCHASED, FULL_PUBLIC_DOMAIN,
   * SAMPLE or NONE. (In LITE projection.)
   *
   * @param string $accessViewStatus
   */
  public function setAccessViewStatus($accessViewStatus)
  {
    $this->accessViewStatus = $accessViewStatus;
  }
  /**
   * @return string
   */
  public function getAccessViewStatus()
  {
    return $this->accessViewStatus;
  }
  /**
   * The two-letter ISO_3166-1 country code for which this access information is
   * valid. (In LITE projection.)
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * Information about a volume's download license access restrictions.
   *
   * @param DownloadAccessRestriction $downloadAccess
   */
  public function setDownloadAccess(DownloadAccessRestriction $downloadAccess)
  {
    $this->downloadAccess = $downloadAccess;
  }
  /**
   * @return DownloadAccessRestriction
   */
  public function getDownloadAccess()
  {
    return $this->downloadAccess;
  }
  /**
   * URL to the Google Drive viewer if this volume is uploaded by the user by
   * selecting the file from Google Drive.
   *
   * @param string $driveImportedContentLink
   */
  public function setDriveImportedContentLink($driveImportedContentLink)
  {
    $this->driveImportedContentLink = $driveImportedContentLink;
  }
  /**
   * @return string
   */
  public function getDriveImportedContentLink()
  {
    return $this->driveImportedContentLink;
  }
  /**
   * Whether this volume can be embedded in a viewport using the Embedded Viewer
   * API.
   *
   * @param bool $embeddable
   */
  public function setEmbeddable($embeddable)
  {
    $this->embeddable = $embeddable;
  }
  /**
   * @return bool
   */
  public function getEmbeddable()
  {
    return $this->embeddable;
  }
  /**
   * Information about epub content. (In LITE projection.)
   *
   * @param VolumeAccessInfoEpub $epub
   */
  public function setEpub(VolumeAccessInfoEpub $epub)
  {
    $this->epub = $epub;
  }
  /**
   * @return VolumeAccessInfoEpub
   */
  public function getEpub()
  {
    return $this->epub;
  }
  /**
   * Whether this volume requires that the client explicitly request offline
   * download license rather than have it done automatically when loading the
   * content, if the client supports it.
   *
   * @param bool $explicitOfflineLicenseManagement
   */
  public function setExplicitOfflineLicenseManagement($explicitOfflineLicenseManagement)
  {
    $this->explicitOfflineLicenseManagement = $explicitOfflineLicenseManagement;
  }
  /**
   * @return bool
   */
  public function getExplicitOfflineLicenseManagement()
  {
    return $this->explicitOfflineLicenseManagement;
  }
  /**
   * Information about pdf content. (In LITE projection.)
   *
   * @param VolumeAccessInfoPdf $pdf
   */
  public function setPdf(VolumeAccessInfoPdf $pdf)
  {
    $this->pdf = $pdf;
  }
  /**
   * @return VolumeAccessInfoPdf
   */
  public function getPdf()
  {
    return $this->pdf;
  }
  /**
   * Whether or not this book is public domain in the country listed above.
   *
   * @param bool $publicDomain
   */
  public function setPublicDomain($publicDomain)
  {
    $this->publicDomain = $publicDomain;
  }
  /**
   * @return bool
   */
  public function getPublicDomain()
  {
    return $this->publicDomain;
  }
  /**
   * Whether quote sharing is allowed for this volume.
   *
   * @param bool $quoteSharingAllowed
   */
  public function setQuoteSharingAllowed($quoteSharingAllowed)
  {
    $this->quoteSharingAllowed = $quoteSharingAllowed;
  }
  /**
   * @return bool
   */
  public function getQuoteSharingAllowed()
  {
    return $this->quoteSharingAllowed;
  }
  /**
   * Whether text-to-speech is permitted for this volume. Values can be ALLOWED,
   * ALLOWED_FOR_ACCESSIBILITY, or NOT_ALLOWED.
   *
   * @param string $textToSpeechPermission
   */
  public function setTextToSpeechPermission($textToSpeechPermission)
  {
    $this->textToSpeechPermission = $textToSpeechPermission;
  }
  /**
   * @return string
   */
  public function getTextToSpeechPermission()
  {
    return $this->textToSpeechPermission;
  }
  /**
   * For ordered but not yet processed orders, we give a URL that can be used to
   * go to the appropriate Google Wallet page.
   *
   * @param string $viewOrderUrl
   */
  public function setViewOrderUrl($viewOrderUrl)
  {
    $this->viewOrderUrl = $viewOrderUrl;
  }
  /**
   * @return string
   */
  public function getViewOrderUrl()
  {
    return $this->viewOrderUrl;
  }
  /**
   * The read access of a volume. Possible values are PARTIAL, ALL_PAGES,
   * NO_PAGES or UNKNOWN. This value depends on the country listed above. A
   * value of PARTIAL means that the publisher has allowed some portion of the
   * volume to be viewed publicly, without purchase. This can apply to eBooks as
   * well as non-eBooks. Public domain books will always have a value of
   * ALL_PAGES.
   *
   * @param string $viewability
   */
  public function setViewability($viewability)
  {
    $this->viewability = $viewability;
  }
  /**
   * @return string
   */
  public function getViewability()
  {
    return $this->viewability;
  }
  /**
   * URL to read this volume on the Google Books site. Link will not allow users
   * to read non-viewable volumes.
   *
   * @param string $webReaderLink
   */
  public function setWebReaderLink($webReaderLink)
  {
    $this->webReaderLink = $webReaderLink;
  }
  /**
   * @return string
   */
  public function getWebReaderLink()
  {
    return $this->webReaderLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeAccessInfo::class, 'Google_Service_Books_VolumeAccessInfo');
