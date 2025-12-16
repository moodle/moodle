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

class GoogleCloudDialogflowCxV3PlaybookImportStrategy extends \Google\Model
{
  /**
   * Unspecified. Treated as 'CREATE_NEW'.
   */
  public const MAIN_PLAYBOOK_IMPORT_STRATEGY_IMPORT_STRATEGY_UNSPECIFIED = 'IMPORT_STRATEGY_UNSPECIFIED';
  /**
   * Create a new resource with a numeric suffix appended to the end of the
   * existing display name.
   */
  public const MAIN_PLAYBOOK_IMPORT_STRATEGY_IMPORT_STRATEGY_CREATE_NEW = 'IMPORT_STRATEGY_CREATE_NEW';
  /**
   * Replace existing resource with incoming resource in the content to be
   * imported.
   */
  public const MAIN_PLAYBOOK_IMPORT_STRATEGY_IMPORT_STRATEGY_REPLACE = 'IMPORT_STRATEGY_REPLACE';
  /**
   * Keep existing resource and discard incoming resource in the content to be
   * imported.
   */
  public const MAIN_PLAYBOOK_IMPORT_STRATEGY_IMPORT_STRATEGY_KEEP = 'IMPORT_STRATEGY_KEEP';
  /**
   * Combine existing and incoming resources when a conflict is encountered.
   */
  public const MAIN_PLAYBOOK_IMPORT_STRATEGY_IMPORT_STRATEGY_MERGE = 'IMPORT_STRATEGY_MERGE';
  /**
   * Throw error if a conflict is encountered.
   */
  public const MAIN_PLAYBOOK_IMPORT_STRATEGY_IMPORT_STRATEGY_THROW_ERROR = 'IMPORT_STRATEGY_THROW_ERROR';
  /**
   * Unspecified. Treated as 'CREATE_NEW'.
   */
  public const NESTED_RESOURCE_IMPORT_STRATEGY_IMPORT_STRATEGY_UNSPECIFIED = 'IMPORT_STRATEGY_UNSPECIFIED';
  /**
   * Create a new resource with a numeric suffix appended to the end of the
   * existing display name.
   */
  public const NESTED_RESOURCE_IMPORT_STRATEGY_IMPORT_STRATEGY_CREATE_NEW = 'IMPORT_STRATEGY_CREATE_NEW';
  /**
   * Replace existing resource with incoming resource in the content to be
   * imported.
   */
  public const NESTED_RESOURCE_IMPORT_STRATEGY_IMPORT_STRATEGY_REPLACE = 'IMPORT_STRATEGY_REPLACE';
  /**
   * Keep existing resource and discard incoming resource in the content to be
   * imported.
   */
  public const NESTED_RESOURCE_IMPORT_STRATEGY_IMPORT_STRATEGY_KEEP = 'IMPORT_STRATEGY_KEEP';
  /**
   * Combine existing and incoming resources when a conflict is encountered.
   */
  public const NESTED_RESOURCE_IMPORT_STRATEGY_IMPORT_STRATEGY_MERGE = 'IMPORT_STRATEGY_MERGE';
  /**
   * Throw error if a conflict is encountered.
   */
  public const NESTED_RESOURCE_IMPORT_STRATEGY_IMPORT_STRATEGY_THROW_ERROR = 'IMPORT_STRATEGY_THROW_ERROR';
  /**
   * Unspecified. Treated as 'CREATE_NEW'.
   */
  public const TOOL_IMPORT_STRATEGY_IMPORT_STRATEGY_UNSPECIFIED = 'IMPORT_STRATEGY_UNSPECIFIED';
  /**
   * Create a new resource with a numeric suffix appended to the end of the
   * existing display name.
   */
  public const TOOL_IMPORT_STRATEGY_IMPORT_STRATEGY_CREATE_NEW = 'IMPORT_STRATEGY_CREATE_NEW';
  /**
   * Replace existing resource with incoming resource in the content to be
   * imported.
   */
  public const TOOL_IMPORT_STRATEGY_IMPORT_STRATEGY_REPLACE = 'IMPORT_STRATEGY_REPLACE';
  /**
   * Keep existing resource and discard incoming resource in the content to be
   * imported.
   */
  public const TOOL_IMPORT_STRATEGY_IMPORT_STRATEGY_KEEP = 'IMPORT_STRATEGY_KEEP';
  /**
   * Combine existing and incoming resources when a conflict is encountered.
   */
  public const TOOL_IMPORT_STRATEGY_IMPORT_STRATEGY_MERGE = 'IMPORT_STRATEGY_MERGE';
  /**
   * Throw error if a conflict is encountered.
   */
  public const TOOL_IMPORT_STRATEGY_IMPORT_STRATEGY_THROW_ERROR = 'IMPORT_STRATEGY_THROW_ERROR';
  /**
   * Optional. Specifies the import strategy used when resolving conflicts with
   * the main playbook. If not specified, 'CREATE_NEW' is assumed.
   *
   * @var string
   */
  public $mainPlaybookImportStrategy;
  /**
   * Optional. Specifies the import strategy used when resolving referenced
   * playbook/flow conflicts. If not specified, 'CREATE_NEW' is assumed.
   *
   * @var string
   */
  public $nestedResourceImportStrategy;
  /**
   * Optional. Specifies the import strategy used when resolving tool conflicts.
   * If not specified, 'CREATE_NEW' is assumed. This will be applied after the
   * main playbook and nested resource import strategies, meaning if the
   * playbook that references the tool is skipped, the tool will also be
   * skipped.
   *
   * @var string
   */
  public $toolImportStrategy;

  /**
   * Optional. Specifies the import strategy used when resolving conflicts with
   * the main playbook. If not specified, 'CREATE_NEW' is assumed.
   *
   * Accepted values: IMPORT_STRATEGY_UNSPECIFIED, IMPORT_STRATEGY_CREATE_NEW,
   * IMPORT_STRATEGY_REPLACE, IMPORT_STRATEGY_KEEP, IMPORT_STRATEGY_MERGE,
   * IMPORT_STRATEGY_THROW_ERROR
   *
   * @param self::MAIN_PLAYBOOK_IMPORT_STRATEGY_* $mainPlaybookImportStrategy
   */
  public function setMainPlaybookImportStrategy($mainPlaybookImportStrategy)
  {
    $this->mainPlaybookImportStrategy = $mainPlaybookImportStrategy;
  }
  /**
   * @return self::MAIN_PLAYBOOK_IMPORT_STRATEGY_*
   */
  public function getMainPlaybookImportStrategy()
  {
    return $this->mainPlaybookImportStrategy;
  }
  /**
   * Optional. Specifies the import strategy used when resolving referenced
   * playbook/flow conflicts. If not specified, 'CREATE_NEW' is assumed.
   *
   * Accepted values: IMPORT_STRATEGY_UNSPECIFIED, IMPORT_STRATEGY_CREATE_NEW,
   * IMPORT_STRATEGY_REPLACE, IMPORT_STRATEGY_KEEP, IMPORT_STRATEGY_MERGE,
   * IMPORT_STRATEGY_THROW_ERROR
   *
   * @param self::NESTED_RESOURCE_IMPORT_STRATEGY_* $nestedResourceImportStrategy
   */
  public function setNestedResourceImportStrategy($nestedResourceImportStrategy)
  {
    $this->nestedResourceImportStrategy = $nestedResourceImportStrategy;
  }
  /**
   * @return self::NESTED_RESOURCE_IMPORT_STRATEGY_*
   */
  public function getNestedResourceImportStrategy()
  {
    return $this->nestedResourceImportStrategy;
  }
  /**
   * Optional. Specifies the import strategy used when resolving tool conflicts.
   * If not specified, 'CREATE_NEW' is assumed. This will be applied after the
   * main playbook and nested resource import strategies, meaning if the
   * playbook that references the tool is skipped, the tool will also be
   * skipped.
   *
   * Accepted values: IMPORT_STRATEGY_UNSPECIFIED, IMPORT_STRATEGY_CREATE_NEW,
   * IMPORT_STRATEGY_REPLACE, IMPORT_STRATEGY_KEEP, IMPORT_STRATEGY_MERGE,
   * IMPORT_STRATEGY_THROW_ERROR
   *
   * @param self::TOOL_IMPORT_STRATEGY_* $toolImportStrategy
   */
  public function setToolImportStrategy($toolImportStrategy)
  {
    $this->toolImportStrategy = $toolImportStrategy;
  }
  /**
   * @return self::TOOL_IMPORT_STRATEGY_*
   */
  public function getToolImportStrategy()
  {
    return $this->toolImportStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3PlaybookImportStrategy::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3PlaybookImportStrategy');
