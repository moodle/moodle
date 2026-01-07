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

namespace Google\Service\DatabaseMigrationService;

class ImportRulesJobDetails extends \Google\Collection
{
  /**
   * Unspecified rules format.
   */
  public const FILE_FORMAT_IMPORT_RULES_FILE_FORMAT_UNSPECIFIED = 'IMPORT_RULES_FILE_FORMAT_UNSPECIFIED';
  /**
   * HarbourBridge session file.
   */
  public const FILE_FORMAT_IMPORT_RULES_FILE_FORMAT_HARBOUR_BRIDGE_SESSION_FILE = 'IMPORT_RULES_FILE_FORMAT_HARBOUR_BRIDGE_SESSION_FILE';
  /**
   * Ora2Pg configuration file.
   */
  public const FILE_FORMAT_IMPORT_RULES_FILE_FORMAT_ORATOPG_CONFIG_FILE = 'IMPORT_RULES_FILE_FORMAT_ORATOPG_CONFIG_FILE';
  protected $collection_key = 'files';
  /**
   * Output only. The requested file format.
   *
   * @var string
   */
  public $fileFormat;
  /**
   * Output only. File names used for the import rules job.
   *
   * @var string[]
   */
  public $files;

  /**
   * Output only. The requested file format.
   *
   * Accepted values: IMPORT_RULES_FILE_FORMAT_UNSPECIFIED,
   * IMPORT_RULES_FILE_FORMAT_HARBOUR_BRIDGE_SESSION_FILE,
   * IMPORT_RULES_FILE_FORMAT_ORATOPG_CONFIG_FILE
   *
   * @param self::FILE_FORMAT_* $fileFormat
   */
  public function setFileFormat($fileFormat)
  {
    $this->fileFormat = $fileFormat;
  }
  /**
   * @return self::FILE_FORMAT_*
   */
  public function getFileFormat()
  {
    return $this->fileFormat;
  }
  /**
   * Output only. File names used for the import rules job.
   *
   * @param string[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return string[]
   */
  public function getFiles()
  {
    return $this->files;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportRulesJobDetails::class, 'Google_Service_DatabaseMigrationService_ImportRulesJobDetails');
