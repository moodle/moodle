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

namespace Google\Service\Dataform;

class NotebookAction extends \Google\Model
{
  /**
   * Output only. The code contents of a Notebook to be run.
   *
   * @var string
   */
  public $contents;
  /**
   * Output only. The ID of the Vertex job that executed the notebook in
   * contents and also the ID used for the outputs created in Google Cloud
   * Storage buckets. Only set once the job has started to run.
   *
   * @var string
   */
  public $jobId;

  /**
   * Output only. The code contents of a Notebook to be run.
   *
   * @param string $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Output only. The ID of the Vertex job that executed the notebook in
   * contents and also the ID used for the outputs created in Google Cloud
   * Storage buckets. Only set once the job has started to run.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotebookAction::class, 'Google_Service_Dataform_NotebookAction');
