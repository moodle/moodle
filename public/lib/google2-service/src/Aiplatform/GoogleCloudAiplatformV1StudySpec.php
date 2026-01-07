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

class GoogleCloudAiplatformV1StudySpec extends \Google\Collection
{
  /**
   * The default algorithm used by Vertex AI for [hyperparameter
   * tuning](https://cloud.google.com/vertex-ai/docs/training/hyperparameter-
   * tuning-overview) and [Vertex AI Vizier](https://cloud.google.com/vertex-
   * ai/docs/vizier).
   */
  public const ALGORITHM_ALGORITHM_UNSPECIFIED = 'ALGORITHM_UNSPECIFIED';
  /**
   * Simple grid search within the feasible space. To use grid search, all
   * parameters must be `INTEGER`, `CATEGORICAL`, or `DISCRETE`.
   */
  public const ALGORITHM_GRID_SEARCH = 'GRID_SEARCH';
  /**
   * Simple random search within the feasible space.
   */
  public const ALGORITHM_RANDOM_SEARCH = 'RANDOM_SEARCH';
  /**
   * Will be treated as LAST_MEASUREMENT.
   */
  public const MEASUREMENT_SELECTION_TYPE_MEASUREMENT_SELECTION_TYPE_UNSPECIFIED = 'MEASUREMENT_SELECTION_TYPE_UNSPECIFIED';
  /**
   * Use the last measurement reported.
   */
  public const MEASUREMENT_SELECTION_TYPE_LAST_MEASUREMENT = 'LAST_MEASUREMENT';
  /**
   * Use the best measurement reported.
   */
  public const MEASUREMENT_SELECTION_TYPE_BEST_MEASUREMENT = 'BEST_MEASUREMENT';
  /**
   * The default noise level chosen by Vertex AI.
   */
  public const OBSERVATION_NOISE_OBSERVATION_NOISE_UNSPECIFIED = 'OBSERVATION_NOISE_UNSPECIFIED';
  /**
   * Vertex AI assumes that the objective function is (nearly) perfectly
   * reproducible, and will never repeat the same Trial parameters.
   */
  public const OBSERVATION_NOISE_LOW = 'LOW';
  /**
   * Vertex AI will estimate the amount of noise in metric evaluations, it may
   * repeat the same Trial parameters more than once.
   */
  public const OBSERVATION_NOISE_HIGH = 'HIGH';
  protected $collection_key = 'parameters';
  /**
   * The search algorithm specified for the Study.
   *
   * @var string
   */
  public $algorithm;
  protected $convexAutomatedStoppingSpecType = GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec::class;
  protected $convexAutomatedStoppingSpecDataType = '';
  protected $decayCurveStoppingSpecType = GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec::class;
  protected $decayCurveStoppingSpecDataType = '';
  /**
   * Describe which measurement selection type will be used
   *
   * @var string
   */
  public $measurementSelectionType;
  protected $medianAutomatedStoppingSpecType = GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec::class;
  protected $medianAutomatedStoppingSpecDataType = '';
  protected $metricsType = GoogleCloudAiplatformV1StudySpecMetricSpec::class;
  protected $metricsDataType = 'array';
  /**
   * The observation noise level of the study. Currently only supported by the
   * Vertex AI Vizier service. Not supported by HyperparameterTuningJob or
   * TrainingPipeline.
   *
   * @var string
   */
  public $observationNoise;
  protected $parametersType = GoogleCloudAiplatformV1StudySpecParameterSpec::class;
  protected $parametersDataType = 'array';
  protected $studyStoppingConfigType = GoogleCloudAiplatformV1StudySpecStudyStoppingConfig::class;
  protected $studyStoppingConfigDataType = '';

  /**
   * The search algorithm specified for the Study.
   *
   * Accepted values: ALGORITHM_UNSPECIFIED, GRID_SEARCH, RANDOM_SEARCH
   *
   * @param self::ALGORITHM_* $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @return self::ALGORITHM_*
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * The automated early stopping spec using convex stopping rule.
   *
   * @param GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec $convexAutomatedStoppingSpec
   */
  public function setConvexAutomatedStoppingSpec(GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec $convexAutomatedStoppingSpec)
  {
    $this->convexAutomatedStoppingSpec = $convexAutomatedStoppingSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec
   */
  public function getConvexAutomatedStoppingSpec()
  {
    return $this->convexAutomatedStoppingSpec;
  }
  /**
   * The automated early stopping spec using decay curve rule.
   *
   * @param GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec $decayCurveStoppingSpec
   */
  public function setDecayCurveStoppingSpec(GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec $decayCurveStoppingSpec)
  {
    $this->decayCurveStoppingSpec = $decayCurveStoppingSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec
   */
  public function getDecayCurveStoppingSpec()
  {
    return $this->decayCurveStoppingSpec;
  }
  /**
   * Describe which measurement selection type will be used
   *
   * Accepted values: MEASUREMENT_SELECTION_TYPE_UNSPECIFIED, LAST_MEASUREMENT,
   * BEST_MEASUREMENT
   *
   * @param self::MEASUREMENT_SELECTION_TYPE_* $measurementSelectionType
   */
  public function setMeasurementSelectionType($measurementSelectionType)
  {
    $this->measurementSelectionType = $measurementSelectionType;
  }
  /**
   * @return self::MEASUREMENT_SELECTION_TYPE_*
   */
  public function getMeasurementSelectionType()
  {
    return $this->measurementSelectionType;
  }
  /**
   * The automated early stopping spec using median rule.
   *
   * @param GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec $medianAutomatedStoppingSpec
   */
  public function setMedianAutomatedStoppingSpec(GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec $medianAutomatedStoppingSpec)
  {
    $this->medianAutomatedStoppingSpec = $medianAutomatedStoppingSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec
   */
  public function getMedianAutomatedStoppingSpec()
  {
    return $this->medianAutomatedStoppingSpec;
  }
  /**
   * Required. Metric specs for the Study.
   *
   * @param GoogleCloudAiplatformV1StudySpecMetricSpec[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecMetricSpec[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The observation noise level of the study. Currently only supported by the
   * Vertex AI Vizier service. Not supported by HyperparameterTuningJob or
   * TrainingPipeline.
   *
   * Accepted values: OBSERVATION_NOISE_UNSPECIFIED, LOW, HIGH
   *
   * @param self::OBSERVATION_NOISE_* $observationNoise
   */
  public function setObservationNoise($observationNoise)
  {
    $this->observationNoise = $observationNoise;
  }
  /**
   * @return self::OBSERVATION_NOISE_*
   */
  public function getObservationNoise()
  {
    return $this->observationNoise;
  }
  /**
   * Required. The set of parameters to tune.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpec[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpec[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Conditions for automated stopping of a Study. Enable automated stopping by
   * configuring at least one condition.
   *
   * @param GoogleCloudAiplatformV1StudySpecStudyStoppingConfig $studyStoppingConfig
   */
  public function setStudyStoppingConfig(GoogleCloudAiplatformV1StudySpecStudyStoppingConfig $studyStoppingConfig)
  {
    $this->studyStoppingConfig = $studyStoppingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecStudyStoppingConfig
   */
  public function getStudyStoppingConfig()
  {
    return $this->studyStoppingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpec');
