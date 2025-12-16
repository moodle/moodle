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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3FlowImportStrategy extends \Google\Model
{
  /**
   * Unspecified. Treated as 'CREATE_NEW'.
   */
  public const GLOBAL_IMPORT_STRATEGY_IMPORT_STRATEGY_UNSPECIFIED = 'IMPORT_STRATEGY_UNSPECIFIED';
  /**
   * Create a new resource with a numeric suffix appended to the end of the
   * existing display name.
   */
  public const GLOBAL_IMPORT_STRATEGY_IMPORT_STRATEGY_CREATE_NEW = 'IMPORT_STRATEGY_CREATE_NEW';
  /**
   * Replace existing resource with incoming resource in the content to be
   * imported.
   */
  public const GLOBAL_IMPORT_STRATEGY_IMPORT_STRATEGY_REPLACE = 'IMPORT_STRATEGY_REPLACE';
  /**
   * Keep existing resource and discard incoming resource in the content to be
   * imported.
   */
  public const GLOBAL_IMPORT_STRATEGY_IMPORT_STRATEGY_KEEP = 'IMPORT_STRATEGY_KEEP';
  /**
   * Combine existing and incoming resources when a conflict is encountered.
   */
  public const GLOBAL_IMPORT_STRATEGY_IMPORT_STRATEGY_MERGE = 'IMPORT_STRATEGY_MERGE';
  /**
   * Throw error if a conflict is encountered.
   */
  public const GLOBAL_IMPORT_STRATEGY_IMPORT_STRATEGY_THROW_ERROR = 'IMPORT_STRATEGY_THROW_ERROR';
  /**
   * Optional. Import strategy for resource conflict resolution, applied
   * globally throughout the flow. It will be applied for all display name
   * conflicts in the imported content. If not specified, 'CREATE_NEW' is
   * assumed.
   *
   * @var string
   */
  public $globalImportStrategy;

  /**
   * Optional. Import strategy for resource conflict resolution, applied
   * globally throughout the flow. It will be applied for all display name
   * conflicts in the imported content. If not specified, 'CREATE_NEW' is
   * assumed.
   *
   * Accepted values: IMPORT_STRATEGY_UNSPECIFIED, IMPORT_STRATEGY_CREATE_NEW,
   * IMPORT_STRATEGY_REPLACE, IMPORT_STRATEGY_KEEP, IMPORT_STRATEGY_MERGE,
   * IMPORT_STRATEGY_THROW_ERROR
   *
   * @param self::GLOBAL_IMPORT_STRATEGY_* $globalImportStrategy
   */
  public function setGlobalImportStrategy($globalImportStrategy)
  {
    $this->globalImportStrategy = $globalImportStrategy;
  }
  /**
   * @return self::GLOBAL_IMPORT_STRATEGY_*
   */
  public function getGlobalImportStrategy()
  {
    return $this->globalImportStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3FlowImportStrategy::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3FlowImportStrategy');
