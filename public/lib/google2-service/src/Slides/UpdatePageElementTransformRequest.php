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

namespace Google\Service\Slides;

class UpdatePageElementTransformRequest extends \Google\Model
{
  /**
   * Unspecified mode.
   */
  public const APPLY_MODE_APPLY_MODE_UNSPECIFIED = 'APPLY_MODE_UNSPECIFIED';
  /**
   * Applies the new AffineTransform matrix to the existing one, and replaces
   * the existing one with the resulting concatenation.
   */
  public const APPLY_MODE_RELATIVE = 'RELATIVE';
  /**
   * Replaces the existing AffineTransform matrix with the new one.
   */
  public const APPLY_MODE_ABSOLUTE = 'ABSOLUTE';
  /**
   * The apply mode of the transform update.
   *
   * @var string
   */
  public $applyMode;
  /**
   * The object ID of the page element to update.
   *
   * @var string
   */
  public $objectId;
  protected $transformType = AffineTransform::class;
  protected $transformDataType = '';

  /**
   * The apply mode of the transform update.
   *
   * Accepted values: APPLY_MODE_UNSPECIFIED, RELATIVE, ABSOLUTE
   *
   * @param self::APPLY_MODE_* $applyMode
   */
  public function setApplyMode($applyMode)
  {
    $this->applyMode = $applyMode;
  }
  /**
   * @return self::APPLY_MODE_*
   */
  public function getApplyMode()
  {
    return $this->applyMode;
  }
  /**
   * The object ID of the page element to update.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The input transform matrix used to update the page element.
   *
   * @param AffineTransform $transform
   */
  public function setTransform(AffineTransform $transform)
  {
    $this->transform = $transform;
  }
  /**
   * @return AffineTransform
   */
  public function getTransform()
  {
    return $this->transform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdatePageElementTransformRequest::class, 'Google_Service_Slides_UpdatePageElementTransformRequest');
