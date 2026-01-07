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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2ExecutionTemplate extends \Google\Model
{
  /**
   * Unstructured key value map that may be set by external tools to store and
   * arbitrary metadata. They are not queryable and should be preserved when
   * modifying objects. Cloud Run API v2 does not support annotations with
   * `run.googleapis.com`, `cloud.googleapis.com`, `serving.knative.dev`, or
   * `autoscaling.knative.dev` namespaces, and they will be rejected. All system
   * annotations in v1 now have a corresponding field in v2 ExecutionTemplate.
   * This field follows Kubernetes annotations' namespacing, limits, and rules.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Unstructured key value map that can be used to organize and categorize
   * objects. User-provided labels are shared with Google's billing system, so
   * they can be used to filter, or break down billing charges by team,
   * component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 ExecutionTemplate.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Specifies the maximum desired number of tasks the execution
   * should run at given time. When the job is run, if this field is 0 or unset,
   * the maximum possible value will be used for that execution. The actual
   * number of tasks running in steady state will be less than this number when
   * there are fewer tasks waiting to be completed remaining, i.e. when the work
   * left to do is less than max parallelism.
   *
   * @var int
   */
  public $parallelism;
  /**
   * Specifies the desired number of tasks the execution should run. Setting to
   * 1 means that parallelism is limited to 1 and the success of that task
   * signals the success of the execution. Defaults to 1.
   *
   * @var int
   */
  public $taskCount;
  protected $templateType = GoogleCloudRunV2TaskTemplate::class;
  protected $templateDataType = '';

  /**
   * Unstructured key value map that may be set by external tools to store and
   * arbitrary metadata. They are not queryable and should be preserved when
   * modifying objects. Cloud Run API v2 does not support annotations with
   * `run.googleapis.com`, `cloud.googleapis.com`, `serving.knative.dev`, or
   * `autoscaling.knative.dev` namespaces, and they will be rejected. All system
   * annotations in v1 now have a corresponding field in v2 ExecutionTemplate.
   * This field follows Kubernetes annotations' namespacing, limits, and rules.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Unstructured key value map that can be used to organize and categorize
   * objects. User-provided labels are shared with Google's billing system, so
   * they can be used to filter, or break down billing charges by team,
   * component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 ExecutionTemplate.
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
   * Optional. Specifies the maximum desired number of tasks the execution
   * should run at given time. When the job is run, if this field is 0 or unset,
   * the maximum possible value will be used for that execution. The actual
   * number of tasks running in steady state will be less than this number when
   * there are fewer tasks waiting to be completed remaining, i.e. when the work
   * left to do is less than max parallelism.
   *
   * @param int $parallelism
   */
  public function setParallelism($parallelism)
  {
    $this->parallelism = $parallelism;
  }
  /**
   * @return int
   */
  public function getParallelism()
  {
    return $this->parallelism;
  }
  /**
   * Specifies the desired number of tasks the execution should run. Setting to
   * 1 means that parallelism is limited to 1 and the success of that task
   * signals the success of the execution. Defaults to 1.
   *
   * @param int $taskCount
   */
  public function setTaskCount($taskCount)
  {
    $this->taskCount = $taskCount;
  }
  /**
   * @return int
   */
  public function getTaskCount()
  {
    return $this->taskCount;
  }
  /**
   * Required. Describes the task(s) that will be created when executing an
   * execution.
   *
   * @param GoogleCloudRunV2TaskTemplate $template
   */
  public function setTemplate(GoogleCloudRunV2TaskTemplate $template)
  {
    $this->template = $template;
  }
  /**
   * @return GoogleCloudRunV2TaskTemplate
   */
  public function getTemplate()
  {
    return $this->template;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ExecutionTemplate::class, 'Google_Service_CloudRun_GoogleCloudRunV2ExecutionTemplate');
