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

namespace Google\Service\Spanner;

class UpdateDatabaseDdlMetadata extends \Google\Collection
{
  protected $collection_key = 'statements';
  protected $actionsType = DdlStatementActionInfo::class;
  protected $actionsDataType = 'array';
  /**
   * Reports the commit timestamps of all statements that have succeeded so far,
   * where `commit_timestamps[i]` is the commit timestamp for the statement
   * `statements[i]`.
   *
   * @var string[]
   */
  public $commitTimestamps;
  /**
   * The database being modified.
   *
   * @var string
   */
  public $database;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = 'array';
  /**
   * For an update this list contains all the statements. For an individual
   * statement, this list contains only that statement.
   *
   * @var string[]
   */
  public $statements;
  /**
   * Output only. When true, indicates that the operation is throttled, for
   * example, due to resource constraints. When resources become available the
   * operation will resume and this field will be false again.
   *
   * @var bool
   */
  public $throttled;

  /**
   * The brief action info for the DDL statements. `actions[i]` is the brief
   * info for `statements[i]`.
   *
   * @param DdlStatementActionInfo[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return DdlStatementActionInfo[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Reports the commit timestamps of all statements that have succeeded so far,
   * where `commit_timestamps[i]` is the commit timestamp for the statement
   * `statements[i]`.
   *
   * @param string[] $commitTimestamps
   */
  public function setCommitTimestamps($commitTimestamps)
  {
    $this->commitTimestamps = $commitTimestamps;
  }
  /**
   * @return string[]
   */
  public function getCommitTimestamps()
  {
    return $this->commitTimestamps;
  }
  /**
   * The database being modified.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * The progress of the UpdateDatabaseDdl operations. All DDL statements will
   * have continuously updating progress, and `progress[i]` is the operation
   * progress for `statements[i]`. Also, `progress[i]` will have start time and
   * end time populated with commit timestamp of operation, as well as a
   * progress of 100% once the operation has completed.
   *
   * @param OperationProgress[] $progress
   */
  public function setProgress($progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return OperationProgress[]
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * For an update this list contains all the statements. For an individual
   * statement, this list contains only that statement.
   *
   * @param string[] $statements
   */
  public function setStatements($statements)
  {
    $this->statements = $statements;
  }
  /**
   * @return string[]
   */
  public function getStatements()
  {
    return $this->statements;
  }
  /**
   * Output only. When true, indicates that the operation is throttled, for
   * example, due to resource constraints. When resources become available the
   * operation will resume and this field will be false again.
   *
   * @param bool $throttled
   */
  public function setThrottled($throttled)
  {
    $this->throttled = $throttled;
  }
  /**
   * @return bool
   */
  public function getThrottled()
  {
    return $this->throttled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDatabaseDdlMetadata::class, 'Google_Service_Spanner_UpdateDatabaseDdlMetadata');
