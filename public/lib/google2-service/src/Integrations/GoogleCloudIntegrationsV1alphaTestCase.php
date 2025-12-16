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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaTestCase extends \Google\Collection
{
  /**
   * Enables persistence for all execution data.
   */
  public const DATABASE_PERSISTENCE_POLICY_DATABASE_PERSISTENCE_POLICY_UNSPECIFIED = 'DATABASE_PERSISTENCE_POLICY_UNSPECIFIED';
  /**
   * Disables persistence for all execution data.
   */
  public const DATABASE_PERSISTENCE_POLICY_DATABASE_PERSISTENCE_DISABLED = 'DATABASE_PERSISTENCE_DISABLED';
  /**
   * Asynchronously persist all execution data.
   */
  public const DATABASE_PERSISTENCE_POLICY_DATABASE_PERSISTENCE_ASYNC = 'DATABASE_PERSISTENCE_ASYNC';
  protected $collection_key = 'testTaskConfigs';
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The creator's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $creatorEmail;
  /**
   * Optional. Various policies for how to persist the test execution info
   * including execution info, execution export info, execution metadata index
   * and execution param index..
   *
   * @var string
   */
  public $databasePersistencePolicy;
  /**
   * Optional. Description of the test case.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of test case.
   *
   * @var string
   */
  public $displayName;
  /**
   * The last modifier's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $lastModifierEmail;
  /**
   * Optional. The edit lock holder's email address. Generated based on the End
   * User Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $lockHolderEmail;
  /**
   * Output only. Auto-generated primary key.
   *
   * @var string
   */
  public $name;
  protected $testInputParametersType = GoogleCloudIntegrationsV1alphaIntegrationParameter::class;
  protected $testInputParametersDataType = 'array';
  protected $testTaskConfigsType = GoogleCloudIntegrationsV1alphaTestTaskConfig::class;
  protected $testTaskConfigsDataType = 'array';
  protected $triggerConfigType = GoogleCloudIntegrationsV1alphaTriggerConfig::class;
  protected $triggerConfigDataType = '';
  /**
   * Required. This defines the trigger ID in workflow which is considered to be
   * executed as starting point of the test case
   *
   * @var string
   */
  public $triggerId;
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Auto-generated.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The creator's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @param string $creatorEmail
   */
  public function setCreatorEmail($creatorEmail)
  {
    $this->creatorEmail = $creatorEmail;
  }
  /**
   * @return string
   */
  public function getCreatorEmail()
  {
    return $this->creatorEmail;
  }
  /**
   * Optional. Various policies for how to persist the test execution info
   * including execution info, execution export info, execution metadata index
   * and execution param index..
   *
   * Accepted values: DATABASE_PERSISTENCE_POLICY_UNSPECIFIED,
   * DATABASE_PERSISTENCE_DISABLED, DATABASE_PERSISTENCE_ASYNC
   *
   * @param self::DATABASE_PERSISTENCE_POLICY_* $databasePersistencePolicy
   */
  public function setDatabasePersistencePolicy($databasePersistencePolicy)
  {
    $this->databasePersistencePolicy = $databasePersistencePolicy;
  }
  /**
   * @return self::DATABASE_PERSISTENCE_POLICY_*
   */
  public function getDatabasePersistencePolicy()
  {
    return $this->databasePersistencePolicy;
  }
  /**
   * Optional. Description of the test case.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of test case.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The last modifier's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @param string $lastModifierEmail
   */
  public function setLastModifierEmail($lastModifierEmail)
  {
    $this->lastModifierEmail = $lastModifierEmail;
  }
  /**
   * @return string
   */
  public function getLastModifierEmail()
  {
    return $this->lastModifierEmail;
  }
  /**
   * Optional. The edit lock holder's email address. Generated based on the End
   * User Credentials/LOAS role of the user making the call.
   *
   * @param string $lockHolderEmail
   */
  public function setLockHolderEmail($lockHolderEmail)
  {
    $this->lockHolderEmail = $lockHolderEmail;
  }
  /**
   * @return string
   */
  public function getLockHolderEmail()
  {
    return $this->lockHolderEmail;
  }
  /**
   * Output only. Auto-generated primary key.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Parameters that are expected to be passed to the test case when
   * the test case is triggered. This gives the user the ability to provide
   * default values. This should include all the output variables of the trigger
   * as input variables.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationParameter[] $testInputParameters
   */
  public function setTestInputParameters($testInputParameters)
  {
    $this->testInputParameters = $testInputParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationParameter[]
   */
  public function getTestInputParameters()
  {
    return $this->testInputParameters;
  }
  /**
   * Optional. However, the test case doesn't mock or assert anything without
   * test_task_configs.
   *
   * @param GoogleCloudIntegrationsV1alphaTestTaskConfig[] $testTaskConfigs
   */
  public function setTestTaskConfigs($testTaskConfigs)
  {
    $this->testTaskConfigs = $testTaskConfigs;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTestTaskConfig[]
   */
  public function getTestTaskConfigs()
  {
    return $this->testTaskConfigs;
  }
  /**
   * Optional. Auto-generated.
   *
   * @param GoogleCloudIntegrationsV1alphaTriggerConfig $triggerConfig
   */
  public function setTriggerConfig(GoogleCloudIntegrationsV1alphaTriggerConfig $triggerConfig)
  {
    $this->triggerConfig = $triggerConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTriggerConfig
   */
  public function getTriggerConfig()
  {
    return $this->triggerConfig;
  }
  /**
   * Required. This defines the trigger ID in workflow which is considered to be
   * executed as starting point of the test case
   *
   * @param string $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
  /**
   * Auto-generated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaTestCase::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTestCase');
