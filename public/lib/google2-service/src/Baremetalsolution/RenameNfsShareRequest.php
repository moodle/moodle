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

namespace Google\Service\Baremetalsolution;

class RenameNfsShareRequest extends \Google\Model
{
  /**
   * Required. The new `id` of the nfsshare.
   *
   * @var string
   */
  public $newNfsshareId;

  /**
   * Required. The new `id` of the nfsshare.
   *
   * @param string $newNfsshareId
   */
  public function setNewNfsshareId($newNfsshareId)
  {
    $this->newNfsshareId = $newNfsshareId;
  }
  /**
   * @return string
   */
  public function getNewNfsshareId()
  {
    return $this->newNfsshareId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenameNfsShareRequest::class, 'Google_Service_Baremetalsolution_RenameNfsShareRequest');
