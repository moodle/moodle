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

class VolumeVolumeInfo extends \Google\Collection
{
  protected $collection_key = 'industryIdentifiers';
  /**
   * Whether anonymous logging should be allowed.
   *
   * @var bool
   */
  public $allowAnonLogging;
  /**
   * The names of the authors and/or editors for this volume. (In LITE
   * projection)
   *
   * @var string[]
   */
  public $authors;
  /**
   * The mean review rating for this volume. (min = 1.0, max = 5.0)
   *
   * @var 
   */
  public $averageRating;
  /**
   * Canonical URL for a volume. (In LITE projection.)
   *
   * @var string
   */
  public $canonicalVolumeLink;
  /**
   * A list of subject categories, such as "Fiction", "Suspense", etc.
   *
   * @var string[]
   */
  public $categories;
  /**
   * Whether the volume has comics content.
   *
   * @var bool
   */
  public $comicsContent;
  /**
   * An identifier for the version of the volume content (text & images). (In
   * LITE projection)
   *
   * @var string
   */
  public $contentVersion;
  /**
   * A synopsis of the volume. The text of the description is formatted in HTML
   * and includes simple formatting elements, such as b, i, and br tags. (In
   * LITE projection.)
   *
   * @var string
   */
  public $description;
  protected $dimensionsType = VolumeVolumeInfoDimensions::class;
  protected $dimensionsDataType = '';
  protected $imageLinksType = VolumeVolumeInfoImageLinks::class;
  protected $imageLinksDataType = '';
  protected $industryIdentifiersType = VolumeVolumeInfoIndustryIdentifiers::class;
  protected $industryIdentifiersDataType = 'array';
  /**
   * URL to view information about this volume on the Google Books site. (In
   * LITE projection)
   *
   * @var string
   */
  public $infoLink;
  /**
   * Best language for this volume (based on content). It is the two-letter ISO
   * 639-1 code such as 'fr', 'en', etc.
   *
   * @var string
   */
  public $language;
  /**
   * The main category to which this volume belongs. It will be the category
   * from the categories list returned below that has the highest weight.
   *
   * @var string
   */
  public $mainCategory;
  /**
   * @var string
   */
  public $maturityRating;
  /**
   * Total number of pages as per publisher metadata.
   *
   * @var int
   */
  public $pageCount;
  protected $panelizationSummaryType = VolumeVolumeInfoPanelizationSummary::class;
  protected $panelizationSummaryDataType = '';
  /**
   * URL to preview this volume on the Google Books site.
   *
   * @var string
   */
  public $previewLink;
  /**
   * Type of publication of this volume. Possible values are BOOK or MAGAZINE.
   *
   * @var string
   */
  public $printType;
  /**
   * Total number of printed pages in generated pdf representation.
   *
   * @var int
   */
  public $printedPageCount;
  /**
   * Date of publication. (In LITE projection.)
   *
   * @var string
   */
  public $publishedDate;
  /**
   * Publisher of this volume. (In LITE projection.)
   *
   * @var string
   */
  public $publisher;
  /**
   * The number of review ratings for this volume.
   *
   * @var int
   */
  public $ratingsCount;
  protected $readingModesType = VolumeVolumeInfoReadingModes::class;
  protected $readingModesDataType = '';
  /**
   * Total number of sample pages as per publisher metadata.
   *
   * @var int
   */
  public $samplePageCount;
  protected $seriesInfoType = Volumeseriesinfo::class;
  protected $seriesInfoDataType = '';
  /**
   * Volume subtitle. (In LITE projection.)
   *
   * @var string
   */
  public $subtitle;
  /**
   * Volume title. (In LITE projection.)
   *
   * @var string
   */
  public $title;

  /**
   * Whether anonymous logging should be allowed.
   *
   * @param bool $allowAnonLogging
   */
  public function setAllowAnonLogging($allowAnonLogging)
  {
    $this->allowAnonLogging = $allowAnonLogging;
  }
  /**
   * @return bool
   */
  public function getAllowAnonLogging()
  {
    return $this->allowAnonLogging;
  }
  /**
   * The names of the authors and/or editors for this volume. (In LITE
   * projection)
   *
   * @param string[] $authors
   */
  public function setAuthors($authors)
  {
    $this->authors = $authors;
  }
  /**
   * @return string[]
   */
  public function getAuthors()
  {
    return $this->authors;
  }
  public function setAverageRating($averageRating)
  {
    $this->averageRating = $averageRating;
  }
  public function getAverageRating()
  {
    return $this->averageRating;
  }
  /**
   * Canonical URL for a volume. (In LITE projection.)
   *
   * @param string $canonicalVolumeLink
   */
  public function setCanonicalVolumeLink($canonicalVolumeLink)
  {
    $this->canonicalVolumeLink = $canonicalVolumeLink;
  }
  /**
   * @return string
   */
  public function getCanonicalVolumeLink()
  {
    return $this->canonicalVolumeLink;
  }
  /**
   * A list of subject categories, such as "Fiction", "Suspense", etc.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Whether the volume has comics content.
   *
   * @param bool $comicsContent
   */
  public function setComicsContent($comicsContent)
  {
    $this->comicsContent = $comicsContent;
  }
  /**
   * @return bool
   */
  public function getComicsContent()
  {
    return $this->comicsContent;
  }
  /**
   * An identifier for the version of the volume content (text & images). (In
   * LITE projection)
   *
   * @param string $contentVersion
   */
  public function setContentVersion($contentVersion)
  {
    $this->contentVersion = $contentVersion;
  }
  /**
   * @return string
   */
  public function getContentVersion()
  {
    return $this->contentVersion;
  }
  /**
   * A synopsis of the volume. The text of the description is formatted in HTML
   * and includes simple formatting elements, such as b, i, and br tags. (In
   * LITE projection.)
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Physical dimensions of this volume.
   *
   * @param VolumeVolumeInfoDimensions $dimensions
   */
  public function setDimensions(VolumeVolumeInfoDimensions $dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return VolumeVolumeInfoDimensions
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * A list of image links for all the sizes that are available. (In LITE
   * projection.)
   *
   * @param VolumeVolumeInfoImageLinks $imageLinks
   */
  public function setImageLinks(VolumeVolumeInfoImageLinks $imageLinks)
  {
    $this->imageLinks = $imageLinks;
  }
  /**
   * @return VolumeVolumeInfoImageLinks
   */
  public function getImageLinks()
  {
    return $this->imageLinks;
  }
  /**
   * Industry standard identifiers for this volume.
   *
   * @param VolumeVolumeInfoIndustryIdentifiers[] $industryIdentifiers
   */
  public function setIndustryIdentifiers($industryIdentifiers)
  {
    $this->industryIdentifiers = $industryIdentifiers;
  }
  /**
   * @return VolumeVolumeInfoIndustryIdentifiers[]
   */
  public function getIndustryIdentifiers()
  {
    return $this->industryIdentifiers;
  }
  /**
   * URL to view information about this volume on the Google Books site. (In
   * LITE projection)
   *
   * @param string $infoLink
   */
  public function setInfoLink($infoLink)
  {
    $this->infoLink = $infoLink;
  }
  /**
   * @return string
   */
  public function getInfoLink()
  {
    return $this->infoLink;
  }
  /**
   * Best language for this volume (based on content). It is the two-letter ISO
   * 639-1 code such as 'fr', 'en', etc.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The main category to which this volume belongs. It will be the category
   * from the categories list returned below that has the highest weight.
   *
   * @param string $mainCategory
   */
  public function setMainCategory($mainCategory)
  {
    $this->mainCategory = $mainCategory;
  }
  /**
   * @return string
   */
  public function getMainCategory()
  {
    return $this->mainCategory;
  }
  /**
   * @param string $maturityRating
   */
  public function setMaturityRating($maturityRating)
  {
    $this->maturityRating = $maturityRating;
  }
  /**
   * @return string
   */
  public function getMaturityRating()
  {
    return $this->maturityRating;
  }
  /**
   * Total number of pages as per publisher metadata.
   *
   * @param int $pageCount
   */
  public function setPageCount($pageCount)
  {
    $this->pageCount = $pageCount;
  }
  /**
   * @return int
   */
  public function getPageCount()
  {
    return $this->pageCount;
  }
  /**
   * A top-level summary of the panelization info in this volume.
   *
   * @param VolumeVolumeInfoPanelizationSummary $panelizationSummary
   */
  public function setPanelizationSummary(VolumeVolumeInfoPanelizationSummary $panelizationSummary)
  {
    $this->panelizationSummary = $panelizationSummary;
  }
  /**
   * @return VolumeVolumeInfoPanelizationSummary
   */
  public function getPanelizationSummary()
  {
    return $this->panelizationSummary;
  }
  /**
   * URL to preview this volume on the Google Books site.
   *
   * @param string $previewLink
   */
  public function setPreviewLink($previewLink)
  {
    $this->previewLink = $previewLink;
  }
  /**
   * @return string
   */
  public function getPreviewLink()
  {
    return $this->previewLink;
  }
  /**
   * Type of publication of this volume. Possible values are BOOK or MAGAZINE.
   *
   * @param string $printType
   */
  public function setPrintType($printType)
  {
    $this->printType = $printType;
  }
  /**
   * @return string
   */
  public function getPrintType()
  {
    return $this->printType;
  }
  /**
   * Total number of printed pages in generated pdf representation.
   *
   * @param int $printedPageCount
   */
  public function setPrintedPageCount($printedPageCount)
  {
    $this->printedPageCount = $printedPageCount;
  }
  /**
   * @return int
   */
  public function getPrintedPageCount()
  {
    return $this->printedPageCount;
  }
  /**
   * Date of publication. (In LITE projection.)
   *
   * @param string $publishedDate
   */
  public function setPublishedDate($publishedDate)
  {
    $this->publishedDate = $publishedDate;
  }
  /**
   * @return string
   */
  public function getPublishedDate()
  {
    return $this->publishedDate;
  }
  /**
   * Publisher of this volume. (In LITE projection.)
   *
   * @param string $publisher
   */
  public function setPublisher($publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return string
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
  /**
   * The number of review ratings for this volume.
   *
   * @param int $ratingsCount
   */
  public function setRatingsCount($ratingsCount)
  {
    $this->ratingsCount = $ratingsCount;
  }
  /**
   * @return int
   */
  public function getRatingsCount()
  {
    return $this->ratingsCount;
  }
  /**
   * The reading modes available for this volume.
   *
   * @param VolumeVolumeInfoReadingModes $readingModes
   */
  public function setReadingModes(VolumeVolumeInfoReadingModes $readingModes)
  {
    $this->readingModes = $readingModes;
  }
  /**
   * @return VolumeVolumeInfoReadingModes
   */
  public function getReadingModes()
  {
    return $this->readingModes;
  }
  /**
   * Total number of sample pages as per publisher metadata.
   *
   * @param int $samplePageCount
   */
  public function setSamplePageCount($samplePageCount)
  {
    $this->samplePageCount = $samplePageCount;
  }
  /**
   * @return int
   */
  public function getSamplePageCount()
  {
    return $this->samplePageCount;
  }
  /**
   * @param Volumeseriesinfo $seriesInfo
   */
  public function setSeriesInfo(Volumeseriesinfo $seriesInfo)
  {
    $this->seriesInfo = $seriesInfo;
  }
  /**
   * @return Volumeseriesinfo
   */
  public function getSeriesInfo()
  {
    return $this->seriesInfo;
  }
  /**
   * Volume subtitle. (In LITE projection.)
   *
   * @param string $subtitle
   */
  public function setSubtitle($subtitle)
  {
    $this->subtitle = $subtitle;
  }
  /**
   * @return string
   */
  public function getSubtitle()
  {
    return $this->subtitle;
  }
  /**
   * Volume title. (In LITE projection.)
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeVolumeInfo::class, 'Google_Service_Books_VolumeVolumeInfo');
