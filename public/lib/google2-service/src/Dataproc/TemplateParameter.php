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

namespace Google\Service\Dataproc;

class TemplateParameter extends \Google\Collection
{
  protected $collection_key = 'fields';
  /**
   * Optional. Brief description of the parameter. Must not exceed 1024
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Paths to all fields that the parameter replaces. A field is
   * allowed to appear in at most one parameter's list of field paths.A field
   * path is similar in syntax to a google.protobuf.FieldMask. For example, a
   * field path that references the zone field of a workflow template's cluster
   * selector would be specified as placement.clusterSelector.zone.Also, field
   * paths can reference fields using the following syntax: Values in maps can
   * be referenced by key: labels'key'
   * placement.clusterSelector.clusterLabels'key'
   * placement.managedCluster.labels'key'
   * placement.clusterSelector.clusterLabels'key' jobs'step-id'.labels'key' Jobs
   * in the jobs list can be referenced by step-id: jobs'step-
   * id'.hadoopJob.mainJarFileUri jobs'step-id'.hiveJob.queryFileUri jobs'step-
   * id'.pySparkJob.mainPythonFileUri jobs'step-id'.hadoopJob.jarFileUris0
   * jobs'step-id'.hadoopJob.archiveUris0 jobs'step-id'.hadoopJob.fileUris0
   * jobs'step-id'.pySparkJob.pythonFileUris0 Items in repeated fields can be
   * referenced by a zero-based index: jobs'step-id'.sparkJob.args0 Other
   * examples: jobs'step-id'.hadoopJob.properties'key' jobs'step-
   * id'.hadoopJob.args0 jobs'step-id'.hiveJob.scriptVariables'key' jobs'step-
   * id'.hadoopJob.mainJarFileUri placement.clusterSelector.zoneIt may not be
   * possible to parameterize maps and repeated fields in their entirety since
   * only individual map values and individual items in repeated fields can be
   * referenced. For example, the following field paths are invalid:
   * placement.clusterSelector.clusterLabels jobs'step-id'.sparkJob.args
   *
   * @var string[]
   */
  public $fields;
  /**
   * Required. Parameter name. The parameter name is used as the key, and paired
   * with the parameter value, which are passed to the template when the
   * template is instantiated. The name must contain only capital letters (A-Z),
   * numbers (0-9), and underscores (_), and must not start with a number. The
   * maximum length is 40 characters.
   *
   * @var string
   */
  public $name;
  protected $validationType = ParameterValidation::class;
  protected $validationDataType = '';

  /**
   * Optional. Brief description of the parameter. Must not exceed 1024
   * characters.
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
   * Required. Paths to all fields that the parameter replaces. A field is
   * allowed to appear in at most one parameter's list of field paths.A field
   * path is similar in syntax to a google.protobuf.FieldMask. For example, a
   * field path that references the zone field of a workflow template's cluster
   * selector would be specified as placement.clusterSelector.zone.Also, field
   * paths can reference fields using the following syntax: Values in maps can
   * be referenced by key: labels'key'
   * placement.clusterSelector.clusterLabels'key'
   * placement.managedCluster.labels'key'
   * placement.clusterSelector.clusterLabels'key' jobs'step-id'.labels'key' Jobs
   * in the jobs list can be referenced by step-id: jobs'step-
   * id'.hadoopJob.mainJarFileUri jobs'step-id'.hiveJob.queryFileUri jobs'step-
   * id'.pySparkJob.mainPythonFileUri jobs'step-id'.hadoopJob.jarFileUris0
   * jobs'step-id'.hadoopJob.archiveUris0 jobs'step-id'.hadoopJob.fileUris0
   * jobs'step-id'.pySparkJob.pythonFileUris0 Items in repeated fields can be
   * referenced by a zero-based index: jobs'step-id'.sparkJob.args0 Other
   * examples: jobs'step-id'.hadoopJob.properties'key' jobs'step-
   * id'.hadoopJob.args0 jobs'step-id'.hiveJob.scriptVariables'key' jobs'step-
   * id'.hadoopJob.mainJarFileUri placement.clusterSelector.zoneIt may not be
   * possible to parameterize maps and repeated fields in their entirety since
   * only individual map values and individual items in repeated fields can be
   * referenced. For example, the following field paths are invalid:
   * placement.clusterSelector.clusterLabels jobs'step-id'.sparkJob.args
   *
   * @param string[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Required. Parameter name. The parameter name is used as the key, and paired
   * with the parameter value, which are passed to the template when the
   * template is instantiated. The name must contain only capital letters (A-Z),
   * numbers (0-9), and underscores (_), and must not start with a number. The
   * maximum length is 40 characters.
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
   * Optional. Validation rules to be applied to this parameter's value.
   *
   * @param ParameterValidation $validation
   */
  public function setValidation(ParameterValidation $validation)
  {
    $this->validation = $validation;
  }
  /**
   * @return ParameterValidation
   */
  public function getValidation()
  {
    return $this->validation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TemplateParameter::class, 'Google_Service_Dataproc_TemplateParameter');
