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

namespace Google\Service\Datastream;

class LogFileDirectories extends \Google\Model
{
  /**
   * Required. Oracle directory for archived logs.
   *
   * @var string
   */
  public $archivedLogDirectory;
  /**
   * Required. Oracle directory for online logs.
   *
   * @var string
   */
  public $onlineLogDirectory;

  /**
   * Required. Oracle directory for archived logs.
   *
   * @param string $archivedLogDirectory
   */
  public function setArchivedLogDirectory($archivedLogDirectory)
  {
    $this->archivedLogDirectory = $archivedLogDirectory;
  }
  /**
   * @return string
   */
  public function getArchivedLogDirectory()
  {
    return $this->archivedLogDirectory;
  }
  /**
   * Required. Oracle directory for online logs.
   *
   * @param string $onlineLogDirectory
   */
  public function setOnlineLogDirectory($onlineLogDirectory)
  {
    $this->onlineLogDirectory = $onlineLogDirectory;
  }
  /**
   * @return string
   */
  public function getOnlineLogDirectory()
  {
    return $this->onlineLogDirectory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogFileDirectories::class, 'Google_Service_Datastream_LogFileDirectories');
