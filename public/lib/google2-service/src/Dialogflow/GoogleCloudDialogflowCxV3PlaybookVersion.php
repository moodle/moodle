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

class GoogleCloudDialogflowCxV3PlaybookVersion extends \Google\Collection
{
  protected $collection_key = 'examples';
  /**
   * Optional. The description of the playbook version.
   *
   * @var string
   */
  public $description;
  protected $examplesType = GoogleCloudDialogflowCxV3Example::class;
  protected $examplesDataType = 'array';
  /**
   * The unique identifier of the playbook version. Format:
   * `projects//locations//agents//playbooks//versions/`.
   *
   * @var string
   */
  public $name;
  protected $playbookType = GoogleCloudDialogflowCxV3Playbook::class;
  protected $playbookDataType = '';
  /**
   * Output only. Last time the playbook version was created or modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The description of the playbook version.
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
   * Output only. Snapshot of the examples belonging to the playbook when the
   * playbook version is created.
   *
   * @param GoogleCloudDialogflowCxV3Example[] $examples
   */
  public function setExamples($examples)
  {
    $this->examples = $examples;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Example[]
   */
  public function getExamples()
  {
    return $this->examples;
  }
  /**
   * The unique identifier of the playbook version. Format:
   * `projects//locations//agents//playbooks//versions/`.
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
   * Output only. Snapshot of the playbook when the playbook version is created.
   *
   * @param GoogleCloudDialogflowCxV3Playbook $playbook
   */
  public function setPlaybook(GoogleCloudDialogflowCxV3Playbook $playbook)
  {
    $this->playbook = $playbook;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Playbook
   */
  public function getPlaybook()
  {
    return $this->playbook;
  }
  /**
   * Output only. Last time the playbook version was created or modified.
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
class_alias(GoogleCloudDialogflowCxV3PlaybookVersion::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3PlaybookVersion');
