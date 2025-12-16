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

namespace Google\Service\Analytics;

class Experiment extends \Google\Collection
{
  protected $collection_key = 'variations';
  /**
   * Account ID to which this experiment belongs. This field is read-only.
   *
   * @var string
   */
  public $accountId;
  /**
   * Time the experiment was created. This field is read-only.
   *
   * @var string
   */
  public $created;
  /**
   * Notes about this experiment.
   *
   * @var string
   */
  public $description;
  /**
   * If true, the end user will be able to edit the experiment via the Google
   * Analytics user interface.
   *
   * @var bool
   */
  public $editableInGaUi;
  /**
   * The ending time of the experiment (the time the status changed from RUNNING
   * to ENDED). This field is present only if the experiment has ended. This
   * field is read-only.
   *
   * @var string
   */
  public $endTime;
  /**
   * Boolean specifying whether to distribute traffic evenly across all
   * variations. If the value is False, content experiments follows the default
   * behavior of adjusting traffic dynamically based on variation performance.
   * Optional -- defaults to False. This field may not be changed for an
   * experiment whose status is ENDED.
   *
   * @var bool
   */
  public $equalWeighting;
  /**
   * Experiment ID. Required for patch and update. Disallowed for create.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for the web property to which this experiment belongs. This
   * field is read-only.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Resource type for an Analytics experiment. This field is read-only.
   *
   * @var string
   */
  public $kind;
  /**
   * An integer number in [3, 90]. Specifies the minimum length of the
   * experiment. Can be changed for a running experiment. This field may not be
   * changed for an experiments whose status is ENDED.
   *
   * @var int
   */
  public $minimumExperimentLengthInDays;
  /**
   * Experiment name. This field may not be changed for an experiment whose
   * status is ENDED. This field is required when creating an experiment.
   *
   * @var string
   */
  public $name;
  /**
   * The metric that the experiment is optimizing. Valid values:
   * "ga:goal(n)Completions", "ga:adsenseAdsClicks", "ga:adsenseAdsViewed",
   * "ga:adsenseRevenue", "ga:bounces", "ga:pageviews", "ga:sessionDuration",
   * "ga:transactions", "ga:transactionRevenue". This field is required if
   * status is "RUNNING" and servingFramework is one of "REDIRECT" or "API".
   *
   * @var string
   */
  public $objectiveMetric;
  /**
   * Whether the objectiveMetric should be minimized or maximized. Possible
   * values: "MAXIMUM", "MINIMUM". Optional--defaults to "MAXIMUM". Cannot be
   * specified without objectiveMetric. Cannot be modified when status is
   * "RUNNING" or "ENDED".
   *
   * @var string
   */
  public $optimizationType;
  protected $parentLinkType = ExperimentParentLink::class;
  protected $parentLinkDataType = '';
  /**
   * View (Profile) ID to which this experiment belongs. This field is read-
   * only.
   *
   * @var string
   */
  public $profileId;
  /**
   * Why the experiment ended. Possible values: "STOPPED_BY_USER",
   * "WINNER_FOUND", "EXPERIMENT_EXPIRED", "ENDED_WITH_NO_WINNER",
   * "GOAL_OBJECTIVE_CHANGED". "ENDED_WITH_NO_WINNER" means that the experiment
   * didn't expire but no winner was projected to be found. If the experiment
   * status is changed via the API to ENDED this field is set to
   * STOPPED_BY_USER. This field is read-only.
   *
   * @var string
   */
  public $reasonExperimentEnded;
  /**
   * Boolean specifying whether variations URLS are rewritten to match those of
   * the original. This field may not be changed for an experiments whose status
   * is ENDED.
   *
   * @var bool
   */
  public $rewriteVariationUrlsAsOriginal;
  /**
   * Link for this experiment. This field is read-only.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The framework used to serve the experiment variations and evaluate the
   * results. One of: - REDIRECT: Google Analytics redirects traffic to
   * different variation pages, reports the chosen variation and evaluates the
   * results. - API: Google Analytics chooses and reports the variation to serve
   * and evaluates the results; the caller is responsible for serving the
   * selected variation. - EXTERNAL: The variations will be served externally
   * and the chosen variation reported to Google Analytics. The caller is
   * responsible for serving the selected variation and evaluating the results.
   *
   * @var string
   */
  public $servingFramework;
  /**
   * The snippet of code to include on the control page(s). This field is read-
   * only.
   *
   * @var string
   */
  public $snippet;
  /**
   * The starting time of the experiment (the time the status changed from
   * READY_TO_RUN to RUNNING). This field is present only if the experiment has
   * started. This field is read-only.
   *
   * @var string
   */
  public $startTime;
  /**
   * Experiment status. Possible values: "DRAFT", "READY_TO_RUN", "RUNNING",
   * "ENDED". Experiments can be created in the "DRAFT", "READY_TO_RUN" or
   * "RUNNING" state. This field is required when creating an experiment.
   *
   * @var string
   */
  public $status;
  /**
   * A floating-point number in (0, 1]. Specifies the fraction of the traffic
   * that participates in the experiment. Can be changed for a running
   * experiment. This field may not be changed for an experiments whose status
   * is ENDED.
   *
   * @var 
   */
  public $trafficCoverage;
  /**
   * Time the experiment was last modified. This field is read-only.
   *
   * @var string
   */
  public $updated;
  protected $variationsType = ExperimentVariations::class;
  protected $variationsDataType = 'array';
  /**
   * Web property ID to which this experiment belongs. The web property ID is of
   * the form UA-XXXXX-YY. This field is read-only.
   *
   * @var string
   */
  public $webPropertyId;
  /**
   * A floating-point number in (0, 1). Specifies the necessary confidence level
   * to choose a winner. This field may not be changed for an experiments whose
   * status is ENDED.
   *
   * @var 
   */
  public $winnerConfidenceLevel;
  /**
   * Boolean specifying whether a winner has been found for this experiment.
   * This field is read-only.
   *
   * @var bool
   */
  public $winnerFound;

  /**
   * Account ID to which this experiment belongs. This field is read-only.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Time the experiment was created. This field is read-only.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Notes about this experiment.
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
   * If true, the end user will be able to edit the experiment via the Google
   * Analytics user interface.
   *
   * @param bool $editableInGaUi
   */
  public function setEditableInGaUi($editableInGaUi)
  {
    $this->editableInGaUi = $editableInGaUi;
  }
  /**
   * @return bool
   */
  public function getEditableInGaUi()
  {
    return $this->editableInGaUi;
  }
  /**
   * The ending time of the experiment (the time the status changed from RUNNING
   * to ENDED). This field is present only if the experiment has ended. This
   * field is read-only.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Boolean specifying whether to distribute traffic evenly across all
   * variations. If the value is False, content experiments follows the default
   * behavior of adjusting traffic dynamically based on variation performance.
   * Optional -- defaults to False. This field may not be changed for an
   * experiment whose status is ENDED.
   *
   * @param bool $equalWeighting
   */
  public function setEqualWeighting($equalWeighting)
  {
    $this->equalWeighting = $equalWeighting;
  }
  /**
   * @return bool
   */
  public function getEqualWeighting()
  {
    return $this->equalWeighting;
  }
  /**
   * Experiment ID. Required for patch and update. Disallowed for create.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Internal ID for the web property to which this experiment belongs. This
   * field is read-only.
   *
   * @param string $internalWebPropertyId
   */
  public function setInternalWebPropertyId($internalWebPropertyId)
  {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  /**
   * @return string
   */
  public function getInternalWebPropertyId()
  {
    return $this->internalWebPropertyId;
  }
  /**
   * Resource type for an Analytics experiment. This field is read-only.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * An integer number in [3, 90]. Specifies the minimum length of the
   * experiment. Can be changed for a running experiment. This field may not be
   * changed for an experiments whose status is ENDED.
   *
   * @param int $minimumExperimentLengthInDays
   */
  public function setMinimumExperimentLengthInDays($minimumExperimentLengthInDays)
  {
    $this->minimumExperimentLengthInDays = $minimumExperimentLengthInDays;
  }
  /**
   * @return int
   */
  public function getMinimumExperimentLengthInDays()
  {
    return $this->minimumExperimentLengthInDays;
  }
  /**
   * Experiment name. This field may not be changed for an experiment whose
   * status is ENDED. This field is required when creating an experiment.
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
   * The metric that the experiment is optimizing. Valid values:
   * "ga:goal(n)Completions", "ga:adsenseAdsClicks", "ga:adsenseAdsViewed",
   * "ga:adsenseRevenue", "ga:bounces", "ga:pageviews", "ga:sessionDuration",
   * "ga:transactions", "ga:transactionRevenue". This field is required if
   * status is "RUNNING" and servingFramework is one of "REDIRECT" or "API".
   *
   * @param string $objectiveMetric
   */
  public function setObjectiveMetric($objectiveMetric)
  {
    $this->objectiveMetric = $objectiveMetric;
  }
  /**
   * @return string
   */
  public function getObjectiveMetric()
  {
    return $this->objectiveMetric;
  }
  /**
   * Whether the objectiveMetric should be minimized or maximized. Possible
   * values: "MAXIMUM", "MINIMUM". Optional--defaults to "MAXIMUM". Cannot be
   * specified without objectiveMetric. Cannot be modified when status is
   * "RUNNING" or "ENDED".
   *
   * @param string $optimizationType
   */
  public function setOptimizationType($optimizationType)
  {
    $this->optimizationType = $optimizationType;
  }
  /**
   * @return string
   */
  public function getOptimizationType()
  {
    return $this->optimizationType;
  }
  /**
   * Parent link for an experiment. Points to the view (profile) to which this
   * experiment belongs.
   *
   * @param ExperimentParentLink $parentLink
   */
  public function setParentLink(ExperimentParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return ExperimentParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * View (Profile) ID to which this experiment belongs. This field is read-
   * only.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * Why the experiment ended. Possible values: "STOPPED_BY_USER",
   * "WINNER_FOUND", "EXPERIMENT_EXPIRED", "ENDED_WITH_NO_WINNER",
   * "GOAL_OBJECTIVE_CHANGED". "ENDED_WITH_NO_WINNER" means that the experiment
   * didn't expire but no winner was projected to be found. If the experiment
   * status is changed via the API to ENDED this field is set to
   * STOPPED_BY_USER. This field is read-only.
   *
   * @param string $reasonExperimentEnded
   */
  public function setReasonExperimentEnded($reasonExperimentEnded)
  {
    $this->reasonExperimentEnded = $reasonExperimentEnded;
  }
  /**
   * @return string
   */
  public function getReasonExperimentEnded()
  {
    return $this->reasonExperimentEnded;
  }
  /**
   * Boolean specifying whether variations URLS are rewritten to match those of
   * the original. This field may not be changed for an experiments whose status
   * is ENDED.
   *
   * @param bool $rewriteVariationUrlsAsOriginal
   */
  public function setRewriteVariationUrlsAsOriginal($rewriteVariationUrlsAsOriginal)
  {
    $this->rewriteVariationUrlsAsOriginal = $rewriteVariationUrlsAsOriginal;
  }
  /**
   * @return bool
   */
  public function getRewriteVariationUrlsAsOriginal()
  {
    return $this->rewriteVariationUrlsAsOriginal;
  }
  /**
   * Link for this experiment. This field is read-only.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The framework used to serve the experiment variations and evaluate the
   * results. One of: - REDIRECT: Google Analytics redirects traffic to
   * different variation pages, reports the chosen variation and evaluates the
   * results. - API: Google Analytics chooses and reports the variation to serve
   * and evaluates the results; the caller is responsible for serving the
   * selected variation. - EXTERNAL: The variations will be served externally
   * and the chosen variation reported to Google Analytics. The caller is
   * responsible for serving the selected variation and evaluating the results.
   *
   * @param string $servingFramework
   */
  public function setServingFramework($servingFramework)
  {
    $this->servingFramework = $servingFramework;
  }
  /**
   * @return string
   */
  public function getServingFramework()
  {
    return $this->servingFramework;
  }
  /**
   * The snippet of code to include on the control page(s). This field is read-
   * only.
   *
   * @param string $snippet
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The starting time of the experiment (the time the status changed from
   * READY_TO_RUN to RUNNING). This field is present only if the experiment has
   * started. This field is read-only.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Experiment status. Possible values: "DRAFT", "READY_TO_RUN", "RUNNING",
   * "ENDED". Experiments can be created in the "DRAFT", "READY_TO_RUN" or
   * "RUNNING" state. This field is required when creating an experiment.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  public function setTrafficCoverage($trafficCoverage)
  {
    $this->trafficCoverage = $trafficCoverage;
  }
  public function getTrafficCoverage()
  {
    return $this->trafficCoverage;
  }
  /**
   * Time the experiment was last modified. This field is read-only.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Array of variations. The first variation in the array is the original. The
   * number of variations may not change once an experiment is in the RUNNING
   * state. At least two variations are required before status can be set to
   * RUNNING.
   *
   * @param ExperimentVariations[] $variations
   */
  public function setVariations($variations)
  {
    $this->variations = $variations;
  }
  /**
   * @return ExperimentVariations[]
   */
  public function getVariations()
  {
    return $this->variations;
  }
  /**
   * Web property ID to which this experiment belongs. The web property ID is of
   * the form UA-XXXXX-YY. This field is read-only.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
  public function setWinnerConfidenceLevel($winnerConfidenceLevel)
  {
    $this->winnerConfidenceLevel = $winnerConfidenceLevel;
  }
  public function getWinnerConfidenceLevel()
  {
    return $this->winnerConfidenceLevel;
  }
  /**
   * Boolean specifying whether a winner has been found for this experiment.
   * This field is read-only.
   *
   * @param bool $winnerFound
   */
  public function setWinnerFound($winnerFound)
  {
    $this->winnerFound = $winnerFound;
  }
  /**
   * @return bool
   */
  public function getWinnerFound()
  {
    return $this->winnerFound;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Experiment::class, 'Google_Service_Analytics_Experiment');
