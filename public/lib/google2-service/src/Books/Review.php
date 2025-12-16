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

class Review extends \Google\Model
{
  protected $authorType = ReviewAuthor::class;
  protected $authorDataType = '';
  /**
   * Review text.
   *
   * @var string
   */
  public $content;
  /**
   * Date of this review.
   *
   * @var string
   */
  public $date;
  /**
   * URL for the full review text, for reviews gathered from the web.
   *
   * @var string
   */
  public $fullTextUrl;
  /**
   * Resource type for a review.
   *
   * @var string
   */
  public $kind;
  /**
   * Star rating for this review. Possible values are ONE, TWO, THREE, FOUR,
   * FIVE or NOT_RATED.
   *
   * @var string
   */
  public $rating;
  protected $sourceType = ReviewSource::class;
  protected $sourceDataType = '';
  /**
   * Title for this review.
   *
   * @var string
   */
  public $title;
  /**
   * Source type for this review. Possible values are EDITORIAL, WEB_USER or
   * GOOGLE_USER.
   *
   * @var string
   */
  public $type;
  /**
   * Volume that this review is for.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Author of this review.
   *
   * @param ReviewAuthor $author
   */
  public function setAuthor(ReviewAuthor $author)
  {
    $this->author = $author;
  }
  /**
   * @return ReviewAuthor
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Review text.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Date of this review.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * URL for the full review text, for reviews gathered from the web.
   *
   * @param string $fullTextUrl
   */
  public function setFullTextUrl($fullTextUrl)
  {
    $this->fullTextUrl = $fullTextUrl;
  }
  /**
   * @return string
   */
  public function getFullTextUrl()
  {
    return $this->fullTextUrl;
  }
  /**
   * Resource type for a review.
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
   * Star rating for this review. Possible values are ONE, TWO, THREE, FOUR,
   * FIVE or NOT_RATED.
   *
   * @param string $rating
   */
  public function setRating($rating)
  {
    $this->rating = $rating;
  }
  /**
   * @return string
   */
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * Information regarding the source of this review, when the review is not
   * from a Google Books user.
   *
   * @param ReviewSource $source
   */
  public function setSource(ReviewSource $source)
  {
    $this->source = $source;
  }
  /**
   * @return ReviewSource
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Title for this review.
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
   * Source type for this review. Possible values are EDITORIAL, WEB_USER or
   * GOOGLE_USER.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Volume that this review is for.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Review::class, 'Google_Service_Books_Review');
