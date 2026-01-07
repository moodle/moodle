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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Export extends \Google\Model
{
  /**
   * Output only. Time the export job was created.
   *
   * @var string
   */
  public $created;
  /**
   * Name of the datastore that is the destination of the export job [datastore]
   *
   * @var string
   */
  public $datastoreName;
  /**
   * Description of the export job.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Error is set when export fails
   *
   * @var string
   */
  public $error;
  /**
   * Output only. Execution time for this export job. If the job is still in
   * progress, it will be set to the amount of time that has elapsed
   * since`created`, in seconds. Else, it will set to (`updated` - `created`),
   * in seconds.
   *
   * @var string
   */
  public $executionTime;
  /**
   * Display name of the export job.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Self link of the export job. A URI that can be used to
   * retrieve the status of an export job. Example: `/organizations/myorg/enviro
   * nments/myenv/analytics/exports/9cfc0d85-0f30-46d6-ae6f-318d0cb961bd`
   *
   * @var string
   */
  public $self;
  /**
   * Output only. Status of the export job. Valid values include `enqueued`,
   * `running`, `completed`, and `failed`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Time the export job was last updated.
   *
   * @var string
   */
  public $updated;

  /**
   * Output only. Time the export job was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Name of the datastore that is the destination of the export job [datastore]
   *
   * @param string $datastoreName
   */
  public function setDatastoreName($datastoreName)
  {
    $this->datastoreName = $datastoreName;
  }
  /**
   * @return string
   */
  public function getDatastoreName()
  {
    return $this->datastoreName;
  }
  /**
   * Description of the export job.
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
   * Output only. Error is set when export fails
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. Execution time for this export job. If the job is still in
   * progress, it will be set to the amount of time that has elapsed
   * since`created`, in seconds. Else, it will set to (`updated` - `created`),
   * in seconds.
   *
   * @param string $executionTime
   */
  public function setExecutionTime($executionTime)
  {
    $this->executionTime = $executionTime;
  }
  /**
   * @return string
   */
  public function getExecutionTime()
  {
    return $this->executionTime;
  }
  /**
   * Display name of the export job.
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
   * Output only. Self link of the export job. A URI that can be used to
   * retrieve the status of an export job. Example: `/organizations/myorg/enviro
   * nments/myenv/analytics/exports/9cfc0d85-0f30-46d6-ae6f-318d0cb961bd`
   *
   * @param string $self
   */
  public function setSelf($self)
  {
    $this->self = $self;
  }
  /**
   * @return string
   */
  public function getSelf()
  {
    return $this->self;
  }
  /**
   * Output only. Status of the export job. Valid values include `enqueued`,
   * `running`, `completed`, and `failed`.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Time the export job was last updated.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Export::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Export');
