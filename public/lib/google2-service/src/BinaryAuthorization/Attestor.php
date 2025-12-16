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

namespace Google\Service\BinaryAuthorization;

class Attestor extends \Google\Model
{
  /**
   * Optional. A descriptive comment. This field may be updated. The field may
   * be displayed in chooser dialogs.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. A checksum, returned by the server, that can be sent on update
   * requests to ensure the attestor has an up-to-date value before attempting
   * to update it. See https://google.aip.dev/154.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. The resource name, in the format: `projects/attestors`. This
   * field may not be updated.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time when the attestor was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $userOwnedGrafeasNoteType = UserOwnedGrafeasNote::class;
  protected $userOwnedGrafeasNoteDataType = '';

  /**
   * Optional. A descriptive comment. This field may be updated. The field may
   * be displayed in chooser dialogs.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. A checksum, returned by the server, that can be sent on update
   * requests to ensure the attestor has an up-to-date value before attempting
   * to update it. See https://google.aip.dev/154.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. The resource name, in the format: `projects/attestors`. This
   * field may not be updated.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Time when the attestor was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * This specifies how an attestation will be read, and how it will be used
   * during policy enforcement.
   *
   * @param UserOwnedGrafeasNote $userOwnedGrafeasNote
   */
  public function setUserOwnedGrafeasNote(UserOwnedGrafeasNote $userOwnedGrafeasNote)
  {
    $this->userOwnedGrafeasNote = $userOwnedGrafeasNote;
  }
  /**
   * @return UserOwnedGrafeasNote
   */
  public function getUserOwnedGrafeasNote()
  {
    return $this->userOwnedGrafeasNote;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attestor::class, 'Google_Service_BinaryAuthorization_Attestor');
