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

namespace Google\Service\CloudAsset;

class WindowsUpdatePackage extends \Google\Collection
{
  protected $collection_key = 'moreInfoUrls';
  protected $categoriesType = WindowsUpdateCategory::class;
  protected $categoriesDataType = 'array';
  /**
   * The localized description of the update package.
   *
   * @var string
   */
  public $description;
  /**
   * A collection of Microsoft Knowledge Base article IDs that are associated
   * with the update package.
   *
   * @var string[]
   */
  public $kbArticleIds;
  /**
   * The last published date of the update, in (UTC) date and time.
   *
   * @var string
   */
  public $lastDeploymentChangeTime;
  /**
   * A collection of URLs that provide more information about the update
   * package.
   *
   * @var string[]
   */
  public $moreInfoUrls;
  /**
   * The revision number of this update package.
   *
   * @var int
   */
  public $revisionNumber;
  /**
   * A hyperlink to the language-specific support information for the update.
   *
   * @var string
   */
  public $supportUrl;
  /**
   * The localized title of the update package.
   *
   * @var string
   */
  public $title;
  /**
   * Gets the identifier of an update package. Stays the same across revisions.
   *
   * @var string
   */
  public $updateId;

  /**
   * The categories that are associated with this update package.
   *
   * @param WindowsUpdateCategory[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return WindowsUpdateCategory[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * The localized description of the update package.
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
   * A collection of Microsoft Knowledge Base article IDs that are associated
   * with the update package.
   *
   * @param string[] $kbArticleIds
   */
  public function setKbArticleIds($kbArticleIds)
  {
    $this->kbArticleIds = $kbArticleIds;
  }
  /**
   * @return string[]
   */
  public function getKbArticleIds()
  {
    return $this->kbArticleIds;
  }
  /**
   * The last published date of the update, in (UTC) date and time.
   *
   * @param string $lastDeploymentChangeTime
   */
  public function setLastDeploymentChangeTime($lastDeploymentChangeTime)
  {
    $this->lastDeploymentChangeTime = $lastDeploymentChangeTime;
  }
  /**
   * @return string
   */
  public function getLastDeploymentChangeTime()
  {
    return $this->lastDeploymentChangeTime;
  }
  /**
   * A collection of URLs that provide more information about the update
   * package.
   *
   * @param string[] $moreInfoUrls
   */
  public function setMoreInfoUrls($moreInfoUrls)
  {
    $this->moreInfoUrls = $moreInfoUrls;
  }
  /**
   * @return string[]
   */
  public function getMoreInfoUrls()
  {
    return $this->moreInfoUrls;
  }
  /**
   * The revision number of this update package.
   *
   * @param int $revisionNumber
   */
  public function setRevisionNumber($revisionNumber)
  {
    $this->revisionNumber = $revisionNumber;
  }
  /**
   * @return int
   */
  public function getRevisionNumber()
  {
    return $this->revisionNumber;
  }
  /**
   * A hyperlink to the language-specific support information for the update.
   *
   * @param string $supportUrl
   */
  public function setSupportUrl($supportUrl)
  {
    $this->supportUrl = $supportUrl;
  }
  /**
   * @return string
   */
  public function getSupportUrl()
  {
    return $this->supportUrl;
  }
  /**
   * The localized title of the update package.
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
  /**
   * Gets the identifier of an update package. Stays the same across revisions.
   *
   * @param string $updateId
   */
  public function setUpdateId($updateId)
  {
    $this->updateId = $updateId;
  }
  /**
   * @return string
   */
  public function getUpdateId()
  {
    return $this->updateId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WindowsUpdatePackage::class, 'Google_Service_CloudAsset_WindowsUpdatePackage');
