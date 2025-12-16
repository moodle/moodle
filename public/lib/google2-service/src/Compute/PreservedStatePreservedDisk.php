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

namespace Google\Service\Compute;

class PreservedStatePreservedDisk extends \Google\Model
{
  public const AUTO_DELETE_NEVER = 'NEVER';
  public const AUTO_DELETE_ON_PERMANENT_INSTANCE_DELETION = 'ON_PERMANENT_INSTANCE_DELETION';
  /**
   * Attaches this disk in read-only mode. Multiple VM instances can use a disk
   * in READ_ONLY mode at a time.
   */
  public const MODE_READ_ONLY = 'READ_ONLY';
  /**
   * *[Default]* Attaches this disk in READ_WRITE mode. Only one VM instance at
   * a time can be attached to a disk inREAD_WRITE mode.
   */
  public const MODE_READ_WRITE = 'READ_WRITE';
  /**
   * These stateful disks will never be deleted during autohealing, update,
   * instance recreate operations. This flag is used to configure if the disk
   * should be deleted after it is no longer used by the group, e.g. when the
   * given instance or the whole MIG is deleted. Note: disks attached in
   * READ_ONLY mode cannot be auto-deleted.
   *
   * @var string
   */
  public $autoDelete;
  /**
   * The mode in which to attach this disk, either READ_WRITE orREAD_ONLY. If
   * not specified, the default is to attach the disk in READ_WRITE mode.
   *
   * @var string
   */
  public $mode;
  /**
   * The URL of the disk resource that is stateful and should be attached to the
   * VM instance.
   *
   * @var string
   */
  public $source;

  /**
   * These stateful disks will never be deleted during autohealing, update,
   * instance recreate operations. This flag is used to configure if the disk
   * should be deleted after it is no longer used by the group, e.g. when the
   * given instance or the whole MIG is deleted. Note: disks attached in
   * READ_ONLY mode cannot be auto-deleted.
   *
   * Accepted values: NEVER, ON_PERMANENT_INSTANCE_DELETION
   *
   * @param self::AUTO_DELETE_* $autoDelete
   */
  public function setAutoDelete($autoDelete)
  {
    $this->autoDelete = $autoDelete;
  }
  /**
   * @return self::AUTO_DELETE_*
   */
  public function getAutoDelete()
  {
    return $this->autoDelete;
  }
  /**
   * The mode in which to attach this disk, either READ_WRITE orREAD_ONLY. If
   * not specified, the default is to attach the disk in READ_WRITE mode.
   *
   * Accepted values: READ_ONLY, READ_WRITE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The URL of the disk resource that is stateful and should be attached to the
   * VM instance.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreservedStatePreservedDisk::class, 'Google_Service_Compute_PreservedStatePreservedDisk');
