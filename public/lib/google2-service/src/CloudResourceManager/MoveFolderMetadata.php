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

namespace Google\Service\CloudResourceManager;

class MoveFolderMetadata extends \Google\Model
{
  /**
   * The resource name of the folder or organization to move the folder to.
   *
   * @var string
   */
  public $destinationParent;
  /**
   * The display name of the folder.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource name of the folder's parent.
   *
   * @var string
   */
  public $sourceParent;

  /**
   * The resource name of the folder or organization to move the folder to.
   *
   * @param string $destinationParent
   */
  public function setDestinationParent($destinationParent)
  {
    $this->destinationParent = $destinationParent;
  }
  /**
   * @return string
   */
  public function getDestinationParent()
  {
    return $this->destinationParent;
  }
  /**
   * The display name of the folder.
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
   * The resource name of the folder's parent.
   *
   * @param string $sourceParent
   */
  public function setSourceParent($sourceParent)
  {
    $this->sourceParent = $sourceParent;
  }
  /**
   * @return string
   */
  public function getSourceParent()
  {
    return $this->sourceParent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoveFolderMetadata::class, 'Google_Service_CloudResourceManager_MoveFolderMetadata');
