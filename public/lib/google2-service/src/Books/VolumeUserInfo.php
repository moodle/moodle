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

class VolumeUserInfo extends \Google\Model
{
  /**
   * Timestamp when this volume was acquired by the user. (RFC 3339 UTC date-
   * time format) Acquiring includes purchase, user upload, receiving family
   * sharing, etc.
   *
   * @var string
   */
  public $acquiredTime;
  /**
   * How this volume was acquired.
   *
   * @var int
   */
  public $acquisitionType;
  protected $copyType = VolumeUserInfoCopy::class;
  protected $copyDataType = '';
  /**
   * Whether this volume is purchased, sample, pd download etc.
   *
   * @var int
   */
  public $entitlementType;
  protected $familySharingType = VolumeUserInfoFamilySharing::class;
  protected $familySharingDataType = '';
  /**
   * Whether or not the user shared this volume with the family.
   *
   * @var bool
   */
  public $isFamilySharedFromUser;
  /**
   * Whether or not the user received this volume through family sharing.
   *
   * @var bool
   */
  public $isFamilySharedToUser;
  /**
   * Deprecated: Replaced by familySharing.
   *
   * @var bool
   */
  public $isFamilySharingAllowed;
  /**
   * Deprecated: Replaced by familySharing.
   *
   * @var bool
   */
  public $isFamilySharingDisabledByFop;
  /**
   * Whether or not this volume is currently in "my books."
   *
   * @var bool
   */
  public $isInMyBooks;
  /**
   * Whether or not this volume was pre-ordered by the authenticated user making
   * the request. (In LITE projection.)
   *
   * @var bool
   */
  public $isPreordered;
  /**
   * Whether or not this volume was purchased by the authenticated user making
   * the request. (In LITE projection.)
   *
   * @var bool
   */
  public $isPurchased;
  /**
   * Whether or not this volume was user uploaded.
   *
   * @var bool
   */
  public $isUploaded;
  protected $readingPositionType = ReadingPosition::class;
  protected $readingPositionDataType = '';
  protected $rentalPeriodType = VolumeUserInfoRentalPeriod::class;
  protected $rentalPeriodDataType = '';
  /**
   * Whether this book is an active or an expired rental.
   *
   * @var string
   */
  public $rentalState;
  protected $reviewType = Review::class;
  protected $reviewDataType = '';
  /**
   * Timestamp when this volume was last modified by a user action, such as a
   * reading position update, volume purchase or writing a review. (RFC 3339 UTC
   * date-time format).
   *
   * @var string
   */
  public $updated;
  protected $userUploadedVolumeInfoType = VolumeUserInfoUserUploadedVolumeInfo::class;
  protected $userUploadedVolumeInfoDataType = '';

  /**
   * Timestamp when this volume was acquired by the user. (RFC 3339 UTC date-
   * time format) Acquiring includes purchase, user upload, receiving family
   * sharing, etc.
   *
   * @param string $acquiredTime
   */
  public function setAcquiredTime($acquiredTime)
  {
    $this->acquiredTime = $acquiredTime;
  }
  /**
   * @return string
   */
  public function getAcquiredTime()
  {
    return $this->acquiredTime;
  }
  /**
   * How this volume was acquired.
   *
   * @param int $acquisitionType
   */
  public function setAcquisitionType($acquisitionType)
  {
    $this->acquisitionType = $acquisitionType;
  }
  /**
   * @return int
   */
  public function getAcquisitionType()
  {
    return $this->acquisitionType;
  }
  /**
   * Copy/Paste accounting information.
   *
   * @param VolumeUserInfoCopy $copy
   */
  public function setCopy(VolumeUserInfoCopy $copy)
  {
    $this->copy = $copy;
  }
  /**
   * @return VolumeUserInfoCopy
   */
  public function getCopy()
  {
    return $this->copy;
  }
  /**
   * Whether this volume is purchased, sample, pd download etc.
   *
   * @param int $entitlementType
   */
  public function setEntitlementType($entitlementType)
  {
    $this->entitlementType = $entitlementType;
  }
  /**
   * @return int
   */
  public function getEntitlementType()
  {
    return $this->entitlementType;
  }
  /**
   * Information on the ability to share with the family.
   *
   * @param VolumeUserInfoFamilySharing $familySharing
   */
  public function setFamilySharing(VolumeUserInfoFamilySharing $familySharing)
  {
    $this->familySharing = $familySharing;
  }
  /**
   * @return VolumeUserInfoFamilySharing
   */
  public function getFamilySharing()
  {
    return $this->familySharing;
  }
  /**
   * Whether or not the user shared this volume with the family.
   *
   * @param bool $isFamilySharedFromUser
   */
  public function setIsFamilySharedFromUser($isFamilySharedFromUser)
  {
    $this->isFamilySharedFromUser = $isFamilySharedFromUser;
  }
  /**
   * @return bool
   */
  public function getIsFamilySharedFromUser()
  {
    return $this->isFamilySharedFromUser;
  }
  /**
   * Whether or not the user received this volume through family sharing.
   *
   * @param bool $isFamilySharedToUser
   */
  public function setIsFamilySharedToUser($isFamilySharedToUser)
  {
    $this->isFamilySharedToUser = $isFamilySharedToUser;
  }
  /**
   * @return bool
   */
  public function getIsFamilySharedToUser()
  {
    return $this->isFamilySharedToUser;
  }
  /**
   * Deprecated: Replaced by familySharing.
   *
   * @param bool $isFamilySharingAllowed
   */
  public function setIsFamilySharingAllowed($isFamilySharingAllowed)
  {
    $this->isFamilySharingAllowed = $isFamilySharingAllowed;
  }
  /**
   * @return bool
   */
  public function getIsFamilySharingAllowed()
  {
    return $this->isFamilySharingAllowed;
  }
  /**
   * Deprecated: Replaced by familySharing.
   *
   * @param bool $isFamilySharingDisabledByFop
   */
  public function setIsFamilySharingDisabledByFop($isFamilySharingDisabledByFop)
  {
    $this->isFamilySharingDisabledByFop = $isFamilySharingDisabledByFop;
  }
  /**
   * @return bool
   */
  public function getIsFamilySharingDisabledByFop()
  {
    return $this->isFamilySharingDisabledByFop;
  }
  /**
   * Whether or not this volume is currently in "my books."
   *
   * @param bool $isInMyBooks
   */
  public function setIsInMyBooks($isInMyBooks)
  {
    $this->isInMyBooks = $isInMyBooks;
  }
  /**
   * @return bool
   */
  public function getIsInMyBooks()
  {
    return $this->isInMyBooks;
  }
  /**
   * Whether or not this volume was pre-ordered by the authenticated user making
   * the request. (In LITE projection.)
   *
   * @param bool $isPreordered
   */
  public function setIsPreordered($isPreordered)
  {
    $this->isPreordered = $isPreordered;
  }
  /**
   * @return bool
   */
  public function getIsPreordered()
  {
    return $this->isPreordered;
  }
  /**
   * Whether or not this volume was purchased by the authenticated user making
   * the request. (In LITE projection.)
   *
   * @param bool $isPurchased
   */
  public function setIsPurchased($isPurchased)
  {
    $this->isPurchased = $isPurchased;
  }
  /**
   * @return bool
   */
  public function getIsPurchased()
  {
    return $this->isPurchased;
  }
  /**
   * Whether or not this volume was user uploaded.
   *
   * @param bool $isUploaded
   */
  public function setIsUploaded($isUploaded)
  {
    $this->isUploaded = $isUploaded;
  }
  /**
   * @return bool
   */
  public function getIsUploaded()
  {
    return $this->isUploaded;
  }
  /**
   * The user's current reading position in the volume, if one is available. (In
   * LITE projection.)
   *
   * @param ReadingPosition $readingPosition
   */
  public function setReadingPosition(ReadingPosition $readingPosition)
  {
    $this->readingPosition = $readingPosition;
  }
  /**
   * @return ReadingPosition
   */
  public function getReadingPosition()
  {
    return $this->readingPosition;
  }
  /**
   * Period during this book is/was a valid rental.
   *
   * @param VolumeUserInfoRentalPeriod $rentalPeriod
   */
  public function setRentalPeriod(VolumeUserInfoRentalPeriod $rentalPeriod)
  {
    $this->rentalPeriod = $rentalPeriod;
  }
  /**
   * @return VolumeUserInfoRentalPeriod
   */
  public function getRentalPeriod()
  {
    return $this->rentalPeriod;
  }
  /**
   * Whether this book is an active or an expired rental.
   *
   * @param string $rentalState
   */
  public function setRentalState($rentalState)
  {
    $this->rentalState = $rentalState;
  }
  /**
   * @return string
   */
  public function getRentalState()
  {
    return $this->rentalState;
  }
  /**
   * This user's review of this volume, if one exists.
   *
   * @param Review $review
   */
  public function setReview(Review $review)
  {
    $this->review = $review;
  }
  /**
   * @return Review
   */
  public function getReview()
  {
    return $this->review;
  }
  /**
   * Timestamp when this volume was last modified by a user action, such as a
   * reading position update, volume purchase or writing a review. (RFC 3339 UTC
   * date-time format).
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * @param VolumeUserInfoUserUploadedVolumeInfo $userUploadedVolumeInfo
   */
  public function setUserUploadedVolumeInfo(VolumeUserInfoUserUploadedVolumeInfo $userUploadedVolumeInfo)
  {
    $this->userUploadedVolumeInfo = $userUploadedVolumeInfo;
  }
  /**
   * @return VolumeUserInfoUserUploadedVolumeInfo
   */
  public function getUserUploadedVolumeInfo()
  {
    return $this->userUploadedVolumeInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeUserInfo::class, 'Google_Service_Books_VolumeUserInfo');
