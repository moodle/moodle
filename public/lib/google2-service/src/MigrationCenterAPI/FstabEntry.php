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

namespace Google\Service\MigrationCenterAPI;

class FstabEntry extends \Google\Model
{
  /**
   * The mount point for the filesystem.
   *
   * @var string
   */
  public $file;
  /**
   * Used by dump to determine which filesystems need to be dumped.
   *
   * @var int
   */
  public $freq;
  /**
   * Mount options associated with the filesystem.
   *
   * @var string
   */
  public $mntops;
  /**
   * Used by the fsck(8) program to determine the order in which filesystem
   * checks are done at reboot time.
   *
   * @var int
   */
  public $passno;
  /**
   * The block special device or remote filesystem to be mounted.
   *
   * @var string
   */
  public $spec;
  /**
   * The type of the filesystem.
   *
   * @var string
   */
  public $vfstype;

  /**
   * The mount point for the filesystem.
   *
   * @param string $file
   */
  public function setFile($file)
  {
    $this->file = $file;
  }
  /**
   * @return string
   */
  public function getFile()
  {
    return $this->file;
  }
  /**
   * Used by dump to determine which filesystems need to be dumped.
   *
   * @param int $freq
   */
  public function setFreq($freq)
  {
    $this->freq = $freq;
  }
  /**
   * @return int
   */
  public function getFreq()
  {
    return $this->freq;
  }
  /**
   * Mount options associated with the filesystem.
   *
   * @param string $mntops
   */
  public function setMntops($mntops)
  {
    $this->mntops = $mntops;
  }
  /**
   * @return string
   */
  public function getMntops()
  {
    return $this->mntops;
  }
  /**
   * Used by the fsck(8) program to determine the order in which filesystem
   * checks are done at reboot time.
   *
   * @param int $passno
   */
  public function setPassno($passno)
  {
    $this->passno = $passno;
  }
  /**
   * @return int
   */
  public function getPassno()
  {
    return $this->passno;
  }
  /**
   * The block special device or remote filesystem to be mounted.
   *
   * @param string $spec
   */
  public function setSpec($spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return string
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * The type of the filesystem.
   *
   * @param string $vfstype
   */
  public function setVfstype($vfstype)
  {
    $this->vfstype = $vfstype;
  }
  /**
   * @return string
   */
  public function getVfstype()
  {
    return $this->vfstype;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FstabEntry::class, 'Google_Service_MigrationCenterAPI_FstabEntry');
