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

namespace Google\Service\ShoppingContent;

class Css extends \Google\Collection
{
  protected $collection_key = 'labelIds';
  /**
   * Output only. Immutable. The CSS domain ID.
   *
   * @var string
   */
  public $cssDomainId;
  /**
   * Output only. Immutable. The ID of the CSS group this CSS domain is
   * affiliated with. Only populated for CSS group users.
   *
   * @var string
   */
  public $cssGroupId;
  /**
   * Output only. Immutable. The CSS domain's display name, used when space is
   * constrained.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Immutable. The CSS domain's full name.
   *
   * @var string
   */
  public $fullName;
  /**
   * Output only. Immutable. The CSS domain's homepage.
   *
   * @var string
   */
  public $homepageUri;
  /**
   * A list of label IDs that are assigned to this CSS domain by its CSS group.
   * Only populated for CSS group users.
   *
   * @var string[]
   */
  public $labelIds;

  /**
   * Output only. Immutable. The CSS domain ID.
   *
   * @param string $cssDomainId
   */
  public function setCssDomainId($cssDomainId)
  {
    $this->cssDomainId = $cssDomainId;
  }
  /**
   * @return string
   */
  public function getCssDomainId()
  {
    return $this->cssDomainId;
  }
  /**
   * Output only. Immutable. The ID of the CSS group this CSS domain is
   * affiliated with. Only populated for CSS group users.
   *
   * @param string $cssGroupId
   */
  public function setCssGroupId($cssGroupId)
  {
    $this->cssGroupId = $cssGroupId;
  }
  /**
   * @return string
   */
  public function getCssGroupId()
  {
    return $this->cssGroupId;
  }
  /**
   * Output only. Immutable. The CSS domain's display name, used when space is
   * constrained.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Immutable. The CSS domain's full name.
   *
   * @param string $fullName
   */
  public function setFullName($fullName)
  {
    $this->fullName = $fullName;
  }
  /**
   * @return string
   */
  public function getFullName()
  {
    return $this->fullName;
  }
  /**
   * Output only. Immutable. The CSS domain's homepage.
   *
   * @param string $homepageUri
   */
  public function setHomepageUri($homepageUri)
  {
    $this->homepageUri = $homepageUri;
  }
  /**
   * @return string
   */
  public function getHomepageUri()
  {
    return $this->homepageUri;
  }
  /**
   * A list of label IDs that are assigned to this CSS domain by its CSS group.
   * Only populated for CSS group users.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Css::class, 'Google_Service_ShoppingContent_Css');
