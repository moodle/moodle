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

class Volume extends \Google\Model
{
  protected $accessInfoType = VolumeAccessInfo::class;
  protected $accessInfoDataType = '';
  /**
   * Opaque identifier for a specific version of a volume resource. (In LITE
   * projection)
   *
   * @var string
   */
  public $etag;
  /**
   * Unique identifier for a volume. (In LITE projection.)
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for a volume. (In LITE projection.)
   *
   * @var string
   */
  public $kind;
  protected $layerInfoType = VolumeLayerInfo::class;
  protected $layerInfoDataType = '';
  protected $recommendedInfoType = VolumeRecommendedInfo::class;
  protected $recommendedInfoDataType = '';
  protected $saleInfoType = VolumeSaleInfo::class;
  protected $saleInfoDataType = '';
  protected $searchInfoType = VolumeSearchInfo::class;
  protected $searchInfoDataType = '';
  /**
   * URL to this resource. (In LITE projection.)
   *
   * @var string
   */
  public $selfLink;
  protected $userInfoType = VolumeUserInfo::class;
  protected $userInfoDataType = '';
  protected $volumeInfoType = VolumeVolumeInfo::class;
  protected $volumeInfoDataType = '';

  /**
   * Any information about a volume related to reading or obtaining that volume
   * text. This information can depend on country (books may be public domain in
   * one country but not in another, e.g.).
   *
   * @param VolumeAccessInfo $accessInfo
   */
  public function setAccessInfo(VolumeAccessInfo $accessInfo)
  {
    $this->accessInfo = $accessInfo;
  }
  /**
   * @return VolumeAccessInfo
   */
  public function getAccessInfo()
  {
    return $this->accessInfo;
  }
  /**
   * Opaque identifier for a specific version of a volume resource. (In LITE
   * projection)
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Unique identifier for a volume. (In LITE projection.)
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
   * Resource type for a volume. (In LITE projection.)
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
   * What layers exist in this volume and high level information about them.
   *
   * @param VolumeLayerInfo $layerInfo
   */
  public function setLayerInfo(VolumeLayerInfo $layerInfo)
  {
    $this->layerInfo = $layerInfo;
  }
  /**
   * @return VolumeLayerInfo
   */
  public function getLayerInfo()
  {
    return $this->layerInfo;
  }
  /**
   * Recommendation related information for this volume.
   *
   * @param VolumeRecommendedInfo $recommendedInfo
   */
  public function setRecommendedInfo(VolumeRecommendedInfo $recommendedInfo)
  {
    $this->recommendedInfo = $recommendedInfo;
  }
  /**
   * @return VolumeRecommendedInfo
   */
  public function getRecommendedInfo()
  {
    return $this->recommendedInfo;
  }
  /**
   * Any information about a volume related to the eBookstore and/or
   * purchaseability. This information can depend on the country where the
   * request originates from (i.e. books may not be for sale in certain
   * countries).
   *
   * @param VolumeSaleInfo $saleInfo
   */
  public function setSaleInfo(VolumeSaleInfo $saleInfo)
  {
    $this->saleInfo = $saleInfo;
  }
  /**
   * @return VolumeSaleInfo
   */
  public function getSaleInfo()
  {
    return $this->saleInfo;
  }
  /**
   * Search result information related to this volume.
   *
   * @param VolumeSearchInfo $searchInfo
   */
  public function setSearchInfo(VolumeSearchInfo $searchInfo)
  {
    $this->searchInfo = $searchInfo;
  }
  /**
   * @return VolumeSearchInfo
   */
  public function getSearchInfo()
  {
    return $this->searchInfo;
  }
  /**
   * URL to this resource. (In LITE projection.)
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * User specific information related to this volume. (e.g. page this user last
   * read or whether they purchased this book)
   *
   * @param VolumeUserInfo $userInfo
   */
  public function setUserInfo(VolumeUserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return VolumeUserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
  /**
   * General volume information.
   *
   * @param VolumeVolumeInfo $volumeInfo
   */
  public function setVolumeInfo(VolumeVolumeInfo $volumeInfo)
  {
    $this->volumeInfo = $volumeInfo;
  }
  /**
   * @return VolumeVolumeInfo
   */
  public function getVolumeInfo()
  {
    return $this->volumeInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Volume::class, 'Google_Service_Books_Volume');
