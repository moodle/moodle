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

namespace Google\Service\Walletobjects;

class SecurityAnimation extends \Google\Model
{
  public const ANIMATION_TYPE_ANIMATION_UNSPECIFIED = 'ANIMATION_UNSPECIFIED';
  /**
   * Default Foil & Shimmer animation
   */
  public const ANIMATION_TYPE_FOIL_SHIMMER = 'FOIL_SHIMMER';
  /**
   * Legacy alias for `FOIL_SHIMMER`. Deprecated.
   *
   * @deprecated
   */
  public const ANIMATION_TYPE_foilShimmer = 'foilShimmer';
  /**
   * Type of animation.
   *
   * @var string
   */
  public $animationType;

  /**
   * Type of animation.
   *
   * Accepted values: ANIMATION_UNSPECIFIED, FOIL_SHIMMER, foilShimmer
   *
   * @param self::ANIMATION_TYPE_* $animationType
   */
  public function setAnimationType($animationType)
  {
    $this->animationType = $animationType;
  }
  /**
   * @return self::ANIMATION_TYPE_*
   */
  public function getAnimationType()
  {
    return $this->animationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityAnimation::class, 'Google_Service_Walletobjects_SecurityAnimation');
