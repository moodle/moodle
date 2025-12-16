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

namespace Google\Service\CloudAlloyDBAdmin;

class BackupDrInfo extends \Google\Collection
{
  protected $collection_key = 'previousWindows';
  protected $currentWindowType = BackupDrEnabledWindow::class;
  protected $currentWindowDataType = '';
  protected $previousWindowsType = BackupDrEnabledWindow::class;
  protected $previousWindowsDataType = 'array';

  /**
   * The current BackupDR configuration for this cluster. If BackupDR protection
   * is not enabled for this cluster, this field will be empty.
   *
   * @param BackupDrEnabledWindow $currentWindow
   */
  public function setCurrentWindow(BackupDrEnabledWindow $currentWindow)
  {
    $this->currentWindow = $currentWindow;
  }
  /**
   * @return BackupDrEnabledWindow
   */
  public function getCurrentWindow()
  {
    return $this->currentWindow;
  }
  /**
   * Windows during which BackupDR was enabled for this cluster, along with
   * associated configuration for that window. These are used to determine
   * points-in-time for which restores can be performed. The windows are ordered
   * with the most recent window last. Windows are mutally exclusive. Windows
   * which closed more than 1 year ago will be removed from this list.
   *
   * @param BackupDrEnabledWindow[] $previousWindows
   */
  public function setPreviousWindows($previousWindows)
  {
    $this->previousWindows = $previousWindows;
  }
  /**
   * @return BackupDrEnabledWindow[]
   */
  public function getPreviousWindows()
  {
    return $this->previousWindows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupDrInfo::class, 'Google_Service_CloudAlloyDBAdmin_BackupDrInfo');
