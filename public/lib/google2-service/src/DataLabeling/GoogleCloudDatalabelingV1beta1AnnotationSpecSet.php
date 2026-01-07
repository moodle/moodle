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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1AnnotationSpecSet extends \Google\Collection
{
  protected $collection_key = 'blockingResources';
  protected $annotationSpecsType = GoogleCloudDatalabelingV1beta1AnnotationSpec::class;
  protected $annotationSpecsDataType = 'array';
  /**
   * Output only. The names of any related resources that are blocking changes
   * to the annotation spec set.
   *
   * @var string[]
   */
  public $blockingResources;
  /**
   * Optional. User-provided description of the annotation specification set.
   * The description can be up to 10,000 characters long.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name for AnnotationSpecSet that you define when you
   * create it. Maximum of 64 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The AnnotationSpecSet resource name in the following format:
   * "projects/{project_id}/annotationSpecSets/{annotation_spec_set_id}"
   *
   * @var string
   */
  public $name;

  /**
   * Required. The array of AnnotationSpecs that you define when you create the
   * AnnotationSpecSet. These are the possible labels for the labeling task.
   *
   * @param GoogleCloudDatalabelingV1beta1AnnotationSpec[] $annotationSpecs
   */
  public function setAnnotationSpecs($annotationSpecs)
  {
    $this->annotationSpecs = $annotationSpecs;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1AnnotationSpec[]
   */
  public function getAnnotationSpecs()
  {
    return $this->annotationSpecs;
  }
  /**
   * Output only. The names of any related resources that are blocking changes
   * to the annotation spec set.
   *
   * @param string[] $blockingResources
   */
  public function setBlockingResources($blockingResources)
  {
    $this->blockingResources = $blockingResources;
  }
  /**
   * @return string[]
   */
  public function getBlockingResources()
  {
    return $this->blockingResources;
  }
  /**
   * Optional. User-provided description of the annotation specification set.
   * The description can be up to 10,000 characters long.
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
   * Required. The display name for AnnotationSpecSet that you define when you
   * create it. Maximum of 64 characters.
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
   * Output only. The AnnotationSpecSet resource name in the following format:
   * "projects/{project_id}/annotationSpecSets/{annotation_spec_set_id}"
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
class_alias(GoogleCloudDatalabelingV1beta1AnnotationSpecSet::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1AnnotationSpecSet');
