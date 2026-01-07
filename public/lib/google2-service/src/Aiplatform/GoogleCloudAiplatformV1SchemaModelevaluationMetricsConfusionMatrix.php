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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $annotationSpecsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrixAnnotationSpecRef::class;
  protected $annotationSpecsDataType = 'array';
  /**
   * Rows in the confusion matrix. The number of rows is equal to the size of
   * `annotationSpecs`. `rowsi` is the number of DataItems that have ground
   * truth of the `annotationSpecs[i]` and are predicted as `annotationSpecs[j]`
   * by the Model being evaluated. For Text Extraction, when
   * `annotationSpecs[i]` is the last element in `annotationSpecs`, i.e. the
   * special negative AnnotationSpec, `rowsi` is the number of predicted
   * entities of `annoatationSpec[j]` that are not labeled as any of the ground
   * truth AnnotationSpec. When annotationSpecs[j] is the special negative
   * AnnotationSpec, `rowsi` is the number of entities have ground truth of
   * `annotationSpec[i]` that are not predicted as an entity by the Model. The
   * value of the last cell, i.e. `rowi` where i == j and `annotationSpec[i]` is
   * the special negative AnnotationSpec, is always 0.
   *
   * @var array[]
   */
  public $rows;

  /**
   * AnnotationSpecs used in the confusion matrix. For AutoML Text Extraction, a
   * special negative AnnotationSpec with empty `id` and `displayName` of "NULL"
   * will be added as the last element.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrixAnnotationSpecRef[] $annotationSpecs
   */
  public function setAnnotationSpecs($annotationSpecs)
  {
    $this->annotationSpecs = $annotationSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrixAnnotationSpecRef[]
   */
  public function getAnnotationSpecs()
  {
    return $this->annotationSpecs;
  }
  /**
   * Rows in the confusion matrix. The number of rows is equal to the size of
   * `annotationSpecs`. `rowsi` is the number of DataItems that have ground
   * truth of the `annotationSpecs[i]` and are predicted as `annotationSpecs[j]`
   * by the Model being evaluated. For Text Extraction, when
   * `annotationSpecs[i]` is the last element in `annotationSpecs`, i.e. the
   * special negative AnnotationSpec, `rowsi` is the number of predicted
   * entities of `annoatationSpec[j]` that are not labeled as any of the ground
   * truth AnnotationSpec. When annotationSpecs[j] is the special negative
   * AnnotationSpec, `rowsi` is the number of entities have ground truth of
   * `annotationSpec[i]` that are not predicted as an entity by the Model. The
   * value of the last cell, i.e. `rowi` where i == j and `annotationSpec[i]` is
   * the special negative AnnotationSpec, is always 0.
   *
   * @param array[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return array[]
   */
  public function getRows()
  {
    return $this->rows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix');
