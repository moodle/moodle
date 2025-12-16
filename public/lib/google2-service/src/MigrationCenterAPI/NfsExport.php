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

class NfsExport extends \Google\Collection
{
  protected $collection_key = 'hosts';
  /**
   * The directory being exported.
   *
   * @var string
   */
  public $exportDirectory;
  /**
   * The hosts or networks to which the export is being shared.
   *
   * @var string[]
   */
  public $hosts;

  /**
   * The directory being exported.
   *
   * @param string $exportDirectory
   */
  public function setExportDirectory($exportDirectory)
  {
    $this->exportDirectory = $exportDirectory;
  }
  /**
   * @return string
   */
  public function getExportDirectory()
  {
    return $this->exportDirectory;
  }
  /**
   * The hosts or networks to which the export is being shared.
   *
   * @param string[] $hosts
   */
  public function setHosts($hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return string[]
   */
  public function getHosts()
  {
    return $this->hosts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NfsExport::class, 'Google_Service_MigrationCenterAPI_NfsExport');
