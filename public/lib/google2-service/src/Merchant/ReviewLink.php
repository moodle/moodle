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

namespace Google\Service\Merchant;

class ReviewLink extends \Google\Model
{
  /**
   * Type unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The review page contains only this single review.
   */
  public const TYPE_SINGLETON = 'SINGLETON';
  /**
   * The review page contains a group of reviews including this review.
   */
  public const TYPE_GROUP = 'GROUP';
  /**
   * Optional. The URI of the review landing page. For example:
   * `http://www.example.com/review_5.html`.
   *
   * @var string
   */
  public $link;
  /**
   * Optional. Type of the review URI.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The URI of the review landing page. For example:
   * `http://www.example.com/review_5.html`.
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
   * Optional. Type of the review URI.
   *
   * Accepted values: TYPE_UNSPECIFIED, SINGLETON, GROUP
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReviewLink::class, 'Google_Service_Merchant_ReviewLink');
