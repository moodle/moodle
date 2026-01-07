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

namespace Google\Service\OSConfig;

class OSPolicyResourceFileResource extends \Google\Model
{
  /**
   * Unspecified is invalid.
   */
  public const STATE_DESIRED_STATE_UNSPECIFIED = 'DESIRED_STATE_UNSPECIFIED';
  /**
   * Ensure file at path is present.
   */
  public const STATE_PRESENT = 'PRESENT';
  /**
   * Ensure file at path is absent.
   */
  public const STATE_ABSENT = 'ABSENT';
  /**
   * Ensure the contents of the file at path matches. If the file does not exist
   * it will be created.
   */
  public const STATE_CONTENTS_MATCH = 'CONTENTS_MATCH';
  /**
   * A a file with this content. The size of the content is limited to 32KiB.
   *
   * @var string
   */
  public $content;
  protected $fileType = OSPolicyResourceFile::class;
  protected $fileDataType = '';
  /**
   * Required. The absolute path of the file within the VM.
   *
   * @var string
   */
  public $path;
  /**
   * Consists of three octal digits which represent, in order, the permissions
   * of the owner, group, and other users for the file (similarly to the numeric
   * mode used in the linux chmod utility). Each digit represents a three bit
   * number with the 4 bit corresponding to the read permissions, the 2 bit
   * corresponds to the write bit, and the one bit corresponds to the execute
   * permission. Default behavior is 755. Below are some examples of permissions
   * and their associated values: read, write, and execute: 7 read and execute:
   * 5 read and write: 6 read only: 4
   *
   * @var string
   */
  public $permissions;
  /**
   * Required. Desired state of the file.
   *
   * @var string
   */
  public $state;

  /**
   * A a file with this content. The size of the content is limited to 32KiB.
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
   * A remote or local source.
   *
   * @param OSPolicyResourceFile $file
   */
  public function setFile(OSPolicyResourceFile $file)
  {
    $this->file = $file;
  }
  /**
   * @return OSPolicyResourceFile
   */
  public function getFile()
  {
    return $this->file;
  }
  /**
   * Required. The absolute path of the file within the VM.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Consists of three octal digits which represent, in order, the permissions
   * of the owner, group, and other users for the file (similarly to the numeric
   * mode used in the linux chmod utility). Each digit represents a three bit
   * number with the 4 bit corresponding to the read permissions, the 2 bit
   * corresponds to the write bit, and the one bit corresponds to the execute
   * permission. Default behavior is 755. Below are some examples of permissions
   * and their associated values: read, write, and execute: 7 read and execute:
   * 5 read and write: 6 read only: 4
   *
   * @param string $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return string
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Required. Desired state of the file.
   *
   * Accepted values: DESIRED_STATE_UNSPECIFIED, PRESENT, ABSENT, CONTENTS_MATCH
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyResourceFileResource::class, 'Google_Service_OSConfig_OSPolicyResourceFileResource');
