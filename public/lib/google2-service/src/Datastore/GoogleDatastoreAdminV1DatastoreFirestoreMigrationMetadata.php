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

namespace Google\Service\Datastore;

class GoogleDatastoreAdminV1DatastoreFirestoreMigrationMetadata extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const MIGRATION_STATE_MIGRATION_STATE_UNSPECIFIED = 'MIGRATION_STATE_UNSPECIFIED';
  /**
   * The migration is running.
   */
  public const MIGRATION_STATE_RUNNING = 'RUNNING';
  /**
   * The migration is paused.
   */
  public const MIGRATION_STATE_PAUSED = 'PAUSED';
  /**
   * The migration is complete.
   */
  public const MIGRATION_STATE_COMPLETE = 'COMPLETE';
  /**
   * Unspecified.
   */
  public const MIGRATION_STEP_MIGRATION_STEP_UNSPECIFIED = 'MIGRATION_STEP_UNSPECIFIED';
  /**
   * Pre-migration: the database is prepared for migration.
   */
  public const MIGRATION_STEP_PREPARE = 'PREPARE';
  /**
   * Start of migration.
   */
  public const MIGRATION_STEP_START = 'START';
  /**
   * Writes are applied synchronously to at least one replica.
   */
  public const MIGRATION_STEP_APPLY_WRITES_SYNCHRONOUSLY = 'APPLY_WRITES_SYNCHRONOUSLY';
  /**
   * Data is copied to Cloud Firestore and then verified to match the data in
   * Cloud Datastore.
   */
  public const MIGRATION_STEP_COPY_AND_VERIFY = 'COPY_AND_VERIFY';
  /**
   * Eventually-consistent reads are redirected to Cloud Firestore.
   */
  public const MIGRATION_STEP_REDIRECT_EVENTUALLY_CONSISTENT_READS = 'REDIRECT_EVENTUALLY_CONSISTENT_READS';
  /**
   * Strongly-consistent reads are redirected to Cloud Firestore.
   */
  public const MIGRATION_STEP_REDIRECT_STRONGLY_CONSISTENT_READS = 'REDIRECT_STRONGLY_CONSISTENT_READS';
  /**
   * Writes are redirected to Cloud Firestore.
   */
  public const MIGRATION_STEP_REDIRECT_WRITES = 'REDIRECT_WRITES';
  /**
   * The current state of migration from Cloud Datastore to Cloud Firestore in
   * Datastore mode.
   *
   * @var string
   */
  public $migrationState;
  /**
   * The current step of migration from Cloud Datastore to Cloud Firestore in
   * Datastore mode.
   *
   * @var string
   */
  public $migrationStep;

  /**
   * The current state of migration from Cloud Datastore to Cloud Firestore in
   * Datastore mode.
   *
   * Accepted values: MIGRATION_STATE_UNSPECIFIED, RUNNING, PAUSED, COMPLETE
   *
   * @param self::MIGRATION_STATE_* $migrationState
   */
  public function setMigrationState($migrationState)
  {
    $this->migrationState = $migrationState;
  }
  /**
   * @return self::MIGRATION_STATE_*
   */
  public function getMigrationState()
  {
    return $this->migrationState;
  }
  /**
   * The current step of migration from Cloud Datastore to Cloud Firestore in
   * Datastore mode.
   *
   * Accepted values: MIGRATION_STEP_UNSPECIFIED, PREPARE, START,
   * APPLY_WRITES_SYNCHRONOUSLY, COPY_AND_VERIFY,
   * REDIRECT_EVENTUALLY_CONSISTENT_READS, REDIRECT_STRONGLY_CONSISTENT_READS,
   * REDIRECT_WRITES
   *
   * @param self::MIGRATION_STEP_* $migrationStep
   */
  public function setMigrationStep($migrationStep)
  {
    $this->migrationStep = $migrationStep;
  }
  /**
   * @return self::MIGRATION_STEP_*
   */
  public function getMigrationStep()
  {
    return $this->migrationStep;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1DatastoreFirestoreMigrationMetadata::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1DatastoreFirestoreMigrationMetadata');
