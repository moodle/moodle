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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SpecialistPool extends \Google\Collection
{
  protected $collection_key = 'specialistWorkerEmails';
  /**
   * Required. The user-defined name of the SpecialistPool. The name can be up
   * to 128 characters long and can consist of any UTF-8 characters. This field
   * should be unique on project-level.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The resource name of the SpecialistPool.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the pending data labeling jobs.
   *
   * @var string[]
   */
  public $pendingDataLabelingJobs;
  /**
   * The email addresses of the managers in the SpecialistPool.
   *
   * @var string[]
   */
  public $specialistManagerEmails;
  /**
   * Output only. The number of managers in this SpecialistPool.
   *
   * @var int
   */
  public $specialistManagersCount;
  /**
   * The email addresses of workers in the SpecialistPool.
   *
   * @var string[]
   */
  public $specialistWorkerEmails;

  /**
   * Required. The user-defined name of the SpecialistPool. The name can be up
   * to 128 characters long and can consist of any UTF-8 characters. This field
   * should be unique on project-level.
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
   * Required. The resource name of the SpecialistPool.
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
   * Output only. The resource name of the pending data labeling jobs.
   *
   * @param string[] $pendingDataLabelingJobs
   */
  public function setPendingDataLabelingJobs($pendingDataLabelingJobs)
  {
    $this->pendingDataLabelingJobs = $pendingDataLabelingJobs;
  }
  /**
   * @return string[]
   */
  public function getPendingDataLabelingJobs()
  {
    return $this->pendingDataLabelingJobs;
  }
  /**
   * The email addresses of the managers in the SpecialistPool.
   *
   * @param string[] $specialistManagerEmails
   */
  public function setSpecialistManagerEmails($specialistManagerEmails)
  {
    $this->specialistManagerEmails = $specialistManagerEmails;
  }
  /**
   * @return string[]
   */
  public function getSpecialistManagerEmails()
  {
    return $this->specialistManagerEmails;
  }
  /**
   * Output only. The number of managers in this SpecialistPool.
   *
   * @param int $specialistManagersCount
   */
  public function setSpecialistManagersCount($specialistManagersCount)
  {
    $this->specialistManagersCount = $specialistManagersCount;
  }
  /**
   * @return int
   */
  public function getSpecialistManagersCount()
  {
    return $this->specialistManagersCount;
  }
  /**
   * The email addresses of workers in the SpecialistPool.
   *
   * @param string[] $specialistWorkerEmails
   */
  public function setSpecialistWorkerEmails($specialistWorkerEmails)
  {
    $this->specialistWorkerEmails = $specialistWorkerEmails;
  }
  /**
   * @return string[]
   */
  public function getSpecialistWorkerEmails()
  {
    return $this->specialistWorkerEmails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SpecialistPool::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SpecialistPool');
