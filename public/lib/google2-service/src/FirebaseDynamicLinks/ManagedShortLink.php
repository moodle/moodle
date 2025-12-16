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

namespace Google\Service\FirebaseDynamicLinks;

class ManagedShortLink extends \Google\Collection
{
  /**
   * Visibility of the link is not specified.
   */
  public const VISIBILITY_UNSPECIFIED_VISIBILITY = 'UNSPECIFIED_VISIBILITY';
  /**
   * Link created in console and should be shown in console.
   */
  public const VISIBILITY_UNARCHIVED = 'UNARCHIVED';
  /**
   * Link created in console and should not be shown in console (but can be
   * shown in the console again if it is unarchived).
   */
  public const VISIBILITY_ARCHIVED = 'ARCHIVED';
  /**
   * Link created outside of console and should never be shown in console.
   */
  public const VISIBILITY_NEVER_SHOWN = 'NEVER_SHOWN';
  protected $collection_key = 'flaggedAttribute';
  /**
   * Creation timestamp of the short link.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Attributes that have been flagged about this short url.
   *
   * @var string[]
   */
  public $flaggedAttribute;
  protected $infoType = DynamicLinkInfo::class;
  protected $infoDataType = '';
  /**
   * Short durable link url, for example, "https://sample.app.goo.gl/xyz123".
   * Required.
   *
   * @var string
   */
  public $link;
  /**
   * Link name defined by the creator. Required.
   *
   * @var string
   */
  public $linkName;
  /**
   * Visibility status of link.
   *
   * @var string
   */
  public $visibility;

  /**
   * Creation timestamp of the short link.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Attributes that have been flagged about this short url.
   *
   * @param string[] $flaggedAttribute
   */
  public function setFlaggedAttribute($flaggedAttribute)
  {
    $this->flaggedAttribute = $flaggedAttribute;
  }
  /**
   * @return string[]
   */
  public function getFlaggedAttribute()
  {
    return $this->flaggedAttribute;
  }
  /**
   * Full Dyamic Link info
   *
   * @param DynamicLinkInfo $info
   */
  public function setInfo(DynamicLinkInfo $info)
  {
    $this->info = $info;
  }
  /**
   * @return DynamicLinkInfo
   */
  public function getInfo()
  {
    return $this->info;
  }
  /**
   * Short durable link url, for example, "https://sample.app.goo.gl/xyz123".
   * Required.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * Link name defined by the creator. Required.
   *
   * @param string $linkName
   */
  public function setLinkName($linkName)
  {
    $this->linkName = $linkName;
  }
  /**
   * @return string
   */
  public function getLinkName()
  {
    return $this->linkName;
  }
  /**
   * Visibility status of link.
   *
   * Accepted values: UNSPECIFIED_VISIBILITY, UNARCHIVED, ARCHIVED, NEVER_SHOWN
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedShortLink::class, 'Google_Service_FirebaseDynamicLinks_ManagedShortLink');
