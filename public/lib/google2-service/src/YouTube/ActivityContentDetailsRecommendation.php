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

class ActivityContentDetailsRecommendation extends \Google\Model
{
  public const REASON_reasonUnspecified = 'reasonUnspecified';
  public const REASON_videoFavorited = 'videoFavorited';
  public const REASON_videoLiked = 'videoLiked';
  public const REASON_videoWatched = 'videoWatched';
  /**
   * The reason that the resource is recommended to the user.
   *
   * @var string
   */
  public $reason;
  protected $resourceIdType = ResourceId::class;
  protected $resourceIdDataType = '';
  protected $seedResourceIdType = ResourceId::class;
  protected $seedResourceIdDataType = '';

  /**
   * The reason that the resource is recommended to the user.
   *
   * Accepted values: reasonUnspecified, videoFavorited, videoLiked,
   * videoWatched
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * The resourceId object contains information that identifies the recommended
   * resource.
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
   * The seedResourceId object contains information about the resource that
   * caused the recommendation.
   *
   * @param ResourceId $seedResourceId
   */
  public function setSeedResourceId(ResourceId $seedResourceId)
  {
    $this->seedResourceId = $seedResourceId;
  }
  /**
   * @return ResourceId
   */
  public function getSeedResourceId()
  {
    return $this->seedResourceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityContentDetailsRecommendation::class, 'Google_Service_YouTube_ActivityContentDetailsRecommendation');
