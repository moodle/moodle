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

namespace Google\Service\CloudNaturalLanguage;

class XPSTablesEvaluationMetrics extends \Google\Model
{
  protected $classificationMetricsType = XPSTablesClassificationMetrics::class;
  protected $classificationMetricsDataType = '';
  protected $regressionMetricsType = XPSTablesRegressionMetrics::class;
  protected $regressionMetricsDataType = '';

  /**
   * Classification metrics.
   *
   * @param XPSTablesClassificationMetrics $classificationMetrics
   */
  public function setClassificationMetrics(XPSTablesClassificationMetrics $classificationMetrics)
  {
    $this->classificationMetrics = $classificationMetrics;
  }
  /**
   * @return XPSTablesClassificationMetrics
   */
  public function getClassificationMetrics()
  {
    return $this->classificationMetrics;
  }
  /**
   * Regression metrics.
   *
   * @param XPSTablesRegressionMetrics $regressionMetrics
   */
  public function setRegressionMetrics(XPSTablesRegressionMetrics $regressionMetrics)
  {
    $this->regressionMetrics = $regressionMetrics;
  }
  /**
   * @return XPSTablesRegressionMetrics
   */
  public function getRegressionMetrics()
  {
    return $this->regressionMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTablesEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSTablesEvaluationMetrics');
