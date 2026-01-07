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

namespace Google\Service\Transcoder;

class JobTemplate extends \Google\Model
{
  protected $configType = JobConfig::class;
  protected $configDataType = '';
  /**
   * The labels associated with this job template. You can use these to organize
   * and group your job templates.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The resource name of the job template. Format: `projects/{project_number}/l
   * ocations/{location}/jobTemplates/{job_template}`
   *
   * @var string
   */
  public $name;

  /**
   * The configuration for this template.
   *
   * @param JobConfig $config
   */
  public function setConfig(JobConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return JobConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * The labels associated with this job template. You can use these to organize
   * and group your job templates.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The resource name of the job template. Format: `projects/{project_number}/l
   * ocations/{location}/jobTemplates/{job_template}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobTemplate::class, 'Google_Service_Transcoder_JobTemplate');
