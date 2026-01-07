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

class GoogleDatastoreAdminV1MigrationProgressEvent extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STEP_MIGRATION_STEP_UNSPECIFIED = 'MIGRATION_STEP_UNSPECIFIED';
  /**
   * Pre-migration: the database is prepared for migration.
   */
  public const STEP_PREPARE = 'PREPARE';
  /**
   * Start of migration.
   */
  public const STEP_START = 'START';
  /**
   * Writes are applied synchronously to at least one replica.
   */
  public const STEP_APPLY_WRITES_SYNCHRONOUSLY = 'APPLY_WRITES_SYNCHRONOUSLY';
  /**
   * Data is copied to Cloud Firestore and then verified to match the data in
   * Cloud Datastore.
   */
  public const STEP_COPY_AND_VERIFY = 'COPY_AND_VERIFY';
  /**
   * Eventually-consistent reads are redirected to Cloud Firestore.
   */
  public const STEP_REDIRECT_EVENTUALLY_CONSISTENT_READS = 'REDIRECT_EVENTUALLY_CONSISTENT_READS';
  /**
   * Strongly-consistent reads are redirected to Cloud Firestore.
   */
  public const STEP_REDIRECT_STRONGLY_CONSISTENT_READS = 'REDIRECT_STRONGLY_CONSISTENT_READS';
  /**
   * Writes are redirected to Cloud Firestore.
   */
  public const STEP_REDIRECT_WRITES = 'REDIRECT_WRITES';
  protected $prepareStepDetailsType = GoogleDatastoreAdminV1PrepareStepDetails::class;
  protected $prepareStepDetailsDataType = '';
  protected $redirectWritesStepDetailsType = GoogleDatastoreAdminV1RedirectWritesStepDetails::class;
  protected $redirectWritesStepDetailsDataType = '';
  /**
   * The step that is starting. An event with step set to `START` indicates that
   * the migration has been reverted back to the initial pre-migration state.
   *
   * @var string
   */
  public $step;

  /**
   * Details for the `PREPARE` step.
   *
   * @param GoogleDatastoreAdminV1PrepareStepDetails $prepareStepDetails
   */
  public function setPrepareStepDetails(GoogleDatastoreAdminV1PrepareStepDetails $prepareStepDetails)
  {
    $this->prepareStepDetails = $prepareStepDetails;
  }
  /**
   * @return GoogleDatastoreAdminV1PrepareStepDetails
   */
  public function getPrepareStepDetails()
  {
    return $this->prepareStepDetails;
  }
  /**
   * Details for the `REDIRECT_WRITES` step.
   *
   * @param GoogleDatastoreAdminV1RedirectWritesStepDetails $redirectWritesStepDetails
   */
  public function setRedirectWritesStepDetails(GoogleDatastoreAdminV1RedirectWritesStepDetails $redirectWritesStepDetails)
  {
    $this->redirectWritesStepDetails = $redirectWritesStepDetails;
  }
  /**
   * @return GoogleDatastoreAdminV1RedirectWritesStepDetails
   */
  public function getRedirectWritesStepDetails()
  {
    return $this->redirectWritesStepDetails;
  }
  /**
   * The step that is starting. An event with step set to `START` indicates that
   * the migration has been reverted back to the initial pre-migration state.
   *
   * Accepted values: MIGRATION_STEP_UNSPECIFIED, PREPARE, START,
   * APPLY_WRITES_SYNCHRONOUSLY, COPY_AND_VERIFY,
   * REDIRECT_EVENTUALLY_CONSISTENT_READS, REDIRECT_STRONGLY_CONSISTENT_READS,
   * REDIRECT_WRITES
   *
   * @param self::STEP_* $step
   */
  public function setStep($step)
  {
    $this->step = $step;
  }
  /**
   * @return self::STEP_*
   */
  public function getStep()
  {
    return $this->step;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1MigrationProgressEvent::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1MigrationProgressEvent');
