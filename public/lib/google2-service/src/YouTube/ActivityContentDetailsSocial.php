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

namespace Google\Service\YouTube;

class ActivityContentDetailsSocial extends \Google\Model
{
  public const TYPE_unspecified = 'unspecified';
  public const TYPE_googlePlus = 'googlePlus';
  public const TYPE_facebook = 'facebook';
  public const TYPE_twitter = 'twitter';
  /**
   * The author of the social network post.
   *
   * @var string
   */
  public $author;
  /**
   * An image of the post's author.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * The URL of the social network post.
   *
   * @var string
   */
  public $referenceUrl;
  protected $resourceIdType = ResourceId::class;
  protected $resourceIdDataType = '';
  /**
   * The name of the social network.
   *
   * @var string
   */
  public $type;

  /**
   * The author of the social network post.
   *
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }
  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * An image of the post's author.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * The URL of the social network post.
   *
   * @param string $referenceUrl
   */
  public function setReferenceUrl($referenceUrl)
  {
    $this->referenceUrl = $referenceUrl;
  }
  /**
   * @return string
   */
  public function getReferenceUrl()
  {
    return $this->referenceUrl;
  }
  /**
   * The resourceId object encapsulates information that identifies the resource
   * associated with a social network post.
   *
   * @param ResourceId $resourceId
   */
  public function setResourceId(ResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return ResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * The name of the social network.
   *
   * Accepted values: unspecified, googlePlus, facebook, twitter
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
class_alias(ActivityContentDetailsSocial::class, 'Google_Service_YouTube_ActivityContentDetailsSocial');
