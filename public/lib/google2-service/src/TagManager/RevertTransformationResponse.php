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

namespace Google\Service\TagManager;

class RevertTransformationResponse extends \Google\Model
{
  protected $transformationType = Transformation::class;
  protected $transformationDataType = '';

  /**
   * Transformation as it appears in the latest container version since the last
   * workspace synchronization operation. If no transformation is present, that
   * means the transformation was deleted in the latest container version.
   *
   * @param Transformation $transformation
   */
  public function setTransformation(Transformation $transformation)
  {
    $this->transformation = $transformation;
  }
  /**
   * @return Transformation
   */
  public function getTransformation()
  {
    return $this->transformation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevertTransformationResponse::class, 'Google_Service_TagManager_RevertTransformationResponse');
