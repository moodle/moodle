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

class ImportMappingRulesRequest extends \Google\Collection
{
  /**
   * Unspecified rules format.
   */
  public const RULES_FORMAT_IMPORT_RULES_FILE_FORMAT_UNSPECIFIED = 'IMPORT_RULES_FILE_FORMAT_UNSPECIFIED';
  /**
   * HarbourBridge session file.
   */
  public const RULES_FORMAT_IMPORT_RULES_FILE_FORMAT_HARBOUR_BRIDGE_SESSION_FILE = 'IMPORT_RULES_FILE_FORMAT_HARBOUR_BRIDGE_SESSION_FILE';
  /**
   * Ora2Pg configuration file.
   */
  public const RULES_FORMAT_IMPORT_RULES_FILE_FORMAT_ORATOPG_CONFIG_FILE = 'IMPORT_RULES_FILE_FORMAT_ORATOPG_CONFIG_FILE';
  protected $collection_key = 'rulesFiles';
  /**
   * Required. Should the conversion workspace be committed automatically after
   * the import operation.
   *
   * @var bool
   */
  public $autoCommit;
  protected $rulesFilesType = RulesFile::class;
  protected $rulesFilesDataType = 'array';
  /**
   * Required. The format of the rules content file.
   *
   * @var string
   */
  public $rulesFormat;

  /**
   * Required. Should the conversion workspace be committed automatically after
   * the import operation.
   *
   * @param bool $autoCommit
   */
  public function setAutoCommit($autoCommit)
  {
    $this->autoCommit = $autoCommit;
  }
  /**
   * @return bool
   */
  public function getAutoCommit()
  {
    return $this->autoCommit;
  }
  /**
   * Required. One or more rules files.
   *
   * @param RulesFile[] $rulesFiles
   */
  public function setRulesFiles($rulesFiles)
  {
    $this->rulesFiles = $rulesFiles;
  }
  /**
   * @return RulesFile[]
   */
  public function getRulesFiles()
  {
    return $this->rulesFiles;
  }
  /**
   * Required. The format of the rules content file.
   *
   * Accepted values: IMPORT_RULES_FILE_FORMAT_UNSPECIFIED,
   * IMPORT_RULES_FILE_FORMAT_HARBOUR_BRIDGE_SESSION_FILE,
   * IMPORT_RULES_FILE_FORMAT_ORATOPG_CONFIG_FILE
   *
   * @param self::RULES_FORMAT_* $rulesFormat
   */
  public function setRulesFormat($rulesFormat)
  {
    $this->rulesFormat = $rulesFormat;
  }
  /**
   * @return self::RULES_FORMAT_*
   */
  public function getRulesFormat()
  {
    return $this->rulesFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportMappingRulesRequest::class, 'Google_Service_DatabaseMigrationService_ImportMappingRulesRequest');
