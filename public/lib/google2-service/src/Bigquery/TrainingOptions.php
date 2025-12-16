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

namespace Google\Service\Bigquery;

class TrainingOptions extends \Google\Collection
{
  /**
   * Unspecified booster type.
   */
  public const BOOSTER_TYPE_BOOSTER_TYPE_UNSPECIFIED = 'BOOSTER_TYPE_UNSPECIFIED';
  /**
   * Gbtree booster.
   */
  public const BOOSTER_TYPE_GBTREE = 'GBTREE';
  /**
   * Dart booster.
   */
  public const BOOSTER_TYPE_DART = 'DART';
  /**
   * Unspecified encoding method.
   */
  public const CATEGORY_ENCODING_METHOD_ENCODING_METHOD_UNSPECIFIED = 'ENCODING_METHOD_UNSPECIFIED';
  /**
   * Applies one-hot encoding.
   */
  public const CATEGORY_ENCODING_METHOD_ONE_HOT_ENCODING = 'ONE_HOT_ENCODING';
  /**
   * Applies label encoding.
   */
  public const CATEGORY_ENCODING_METHOD_LABEL_ENCODING = 'LABEL_ENCODING';
  /**
   * Applies dummy encoding.
   */
  public const CATEGORY_ENCODING_METHOD_DUMMY_ENCODING = 'DUMMY_ENCODING';
  /**
   * Unspecified color space
   */
  public const COLOR_SPACE_COLOR_SPACE_UNSPECIFIED = 'COLOR_SPACE_UNSPECIFIED';
  /**
   * RGB
   */
  public const COLOR_SPACE_RGB = 'RGB';
  /**
   * HSV
   */
  public const COLOR_SPACE_HSV = 'HSV';
  /**
   * YIQ
   */
  public const COLOR_SPACE_YIQ = 'YIQ';
  /**
   * YUV
   */
  public const COLOR_SPACE_YUV = 'YUV';
  /**
   * GRAYSCALE
   */
  public const COLOR_SPACE_GRAYSCALE = 'GRAYSCALE';
  /**
   * Unspecified dart normalize type.
   */
  public const DART_NORMALIZE_TYPE_DART_NORMALIZE_TYPE_UNSPECIFIED = 'DART_NORMALIZE_TYPE_UNSPECIFIED';
  /**
   * New trees have the same weight of each of dropped trees.
   */
  public const DART_NORMALIZE_TYPE_TREE = 'TREE';
  /**
   * New trees have the same weight of sum of dropped trees.
   */
  public const DART_NORMALIZE_TYPE_FOREST = 'FOREST';
  /**
   * Default value.
   */
  public const DATA_FREQUENCY_DATA_FREQUENCY_UNSPECIFIED = 'DATA_FREQUENCY_UNSPECIFIED';
  /**
   * Automatically inferred from timestamps.
   */
  public const DATA_FREQUENCY_AUTO_FREQUENCY = 'AUTO_FREQUENCY';
  /**
   * Yearly data.
   */
  public const DATA_FREQUENCY_YEARLY = 'YEARLY';
  /**
   * Quarterly data.
   */
  public const DATA_FREQUENCY_QUARTERLY = 'QUARTERLY';
  /**
   * Monthly data.
   */
  public const DATA_FREQUENCY_MONTHLY = 'MONTHLY';
  /**
   * Weekly data.
   */
  public const DATA_FREQUENCY_WEEKLY = 'WEEKLY';
  /**
   * Daily data.
   */
  public const DATA_FREQUENCY_DAILY = 'DAILY';
  /**
   * Hourly data.
   */
  public const DATA_FREQUENCY_HOURLY = 'HOURLY';
  /**
   * Per-minute data.
   */
  public const DATA_FREQUENCY_PER_MINUTE = 'PER_MINUTE';
  /**
   * Default value.
   */
  public const DATA_SPLIT_METHOD_DATA_SPLIT_METHOD_UNSPECIFIED = 'DATA_SPLIT_METHOD_UNSPECIFIED';
  /**
   * Splits data randomly.
   */
  public const DATA_SPLIT_METHOD_RANDOM = 'RANDOM';
  /**
   * Splits data with the user provided tags.
   */
  public const DATA_SPLIT_METHOD_CUSTOM = 'CUSTOM';
  /**
   * Splits data sequentially.
   */
  public const DATA_SPLIT_METHOD_SEQUENTIAL = 'SEQUENTIAL';
  /**
   * Data split will be skipped.
   */
  public const DATA_SPLIT_METHOD_NO_SPLIT = 'NO_SPLIT';
  /**
   * Splits data automatically: Uses NO_SPLIT if the data size is small.
   * Otherwise uses RANDOM.
   */
  public const DATA_SPLIT_METHOD_AUTO_SPLIT = 'AUTO_SPLIT';
  /**
   * Default value.
   */
  public const DISTANCE_TYPE_DISTANCE_TYPE_UNSPECIFIED = 'DISTANCE_TYPE_UNSPECIFIED';
  /**
   * Eculidean distance.
   */
  public const DISTANCE_TYPE_EUCLIDEAN = 'EUCLIDEAN';
  /**
   * Cosine distance.
   */
  public const DISTANCE_TYPE_COSINE = 'COSINE';
  /**
   * Default value.
   */
  public const FEEDBACK_TYPE_FEEDBACK_TYPE_UNSPECIFIED = 'FEEDBACK_TYPE_UNSPECIFIED';
  /**
   * Use weighted-als for implicit feedback problems.
   */
  public const FEEDBACK_TYPE_IMPLICIT = 'IMPLICIT';
  /**
   * Use nonweighted-als for explicit feedback problems.
   */
  public const FEEDBACK_TYPE_EXPLICIT = 'EXPLICIT';
  /**
   * Holiday region unspecified.
   */
  public const HOLIDAY_REGION_HOLIDAY_REGION_UNSPECIFIED = 'HOLIDAY_REGION_UNSPECIFIED';
  /**
   * Global.
   */
  public const HOLIDAY_REGION_GLOBAL = 'GLOBAL';
  /**
   * North America.
   */
  public const HOLIDAY_REGION_NA = 'NA';
  /**
   * Japan and Asia Pacific: Korea, Greater China, India, Australia, and New
   * Zealand.
   */
  public const HOLIDAY_REGION_JAPAC = 'JAPAC';
  /**
   * Europe, the Middle East and Africa.
   */
  public const HOLIDAY_REGION_EMEA = 'EMEA';
  /**
   * Latin America and the Caribbean.
   */
  public const HOLIDAY_REGION_LAC = 'LAC';
  /**
   * United Arab Emirates
   */
  public const HOLIDAY_REGION_AE = 'AE';
  /**
   * Argentina
   */
  public const HOLIDAY_REGION_AR = 'AR';
  /**
   * Austria
   */
  public const HOLIDAY_REGION_AT = 'AT';
  /**
   * Australia
   */
  public const HOLIDAY_REGION_AU = 'AU';
  /**
   * Belgium
   */
  public const HOLIDAY_REGION_BE = 'BE';
  /**
   * Brazil
   */
  public const HOLIDAY_REGION_BR = 'BR';
  /**
   * Canada
   */
  public const HOLIDAY_REGION_CA = 'CA';
  /**
   * Switzerland
   */
  public const HOLIDAY_REGION_CH = 'CH';
  /**
   * Chile
   */
  public const HOLIDAY_REGION_CL = 'CL';
  /**
   * China
   */
  public const HOLIDAY_REGION_CN = 'CN';
  /**
   * Colombia
   */
  public const HOLIDAY_REGION_CO = 'CO';
  /**
   * Czechoslovakia
   */
  public const HOLIDAY_REGION_CS = 'CS';
  /**
   * Czech Republic
   */
  public const HOLIDAY_REGION_CZ = 'CZ';
  /**
   * Germany
   */
  public const HOLIDAY_REGION_DE = 'DE';
  /**
   * Denmark
   */
  public const HOLIDAY_REGION_DK = 'DK';
  /**
   * Algeria
   */
  public const HOLIDAY_REGION_DZ = 'DZ';
  /**
   * Ecuador
   */
  public const HOLIDAY_REGION_EC = 'EC';
  /**
   * Estonia
   */
  public const HOLIDAY_REGION_EE = 'EE';
  /**
   * Egypt
   */
  public const HOLIDAY_REGION_EG = 'EG';
  /**
   * Spain
   */
  public const HOLIDAY_REGION_ES = 'ES';
  /**
   * Finland
   */
  public const HOLIDAY_REGION_FI = 'FI';
  /**
   * France
   */
  public const HOLIDAY_REGION_FR = 'FR';
  /**
   * Great Britain (United Kingdom)
   */
  public const HOLIDAY_REGION_GB = 'GB';
  /**
   * Greece
   */
  public const HOLIDAY_REGION_GR = 'GR';
  /**
   * Hong Kong
   */
  public const HOLIDAY_REGION_HK = 'HK';
  /**
   * Hungary
   */
  public const HOLIDAY_REGION_HU = 'HU';
  /**
   * Indonesia
   */
  public const HOLIDAY_REGION_ID = 'ID';
  /**
   * Ireland
   */
  public const HOLIDAY_REGION_IE = 'IE';
  /**
   * Israel
   */
  public const HOLIDAY_REGION_IL = 'IL';
  /**
   * India
   */
  public const HOLIDAY_REGION_IN = 'IN';
  /**
   * Iran
   */
  public const HOLIDAY_REGION_IR = 'IR';
  /**
   * Italy
   */
  public const HOLIDAY_REGION_IT = 'IT';
  /**
   * Japan
   */
  public const HOLIDAY_REGION_JP = 'JP';
  /**
   * Korea (South)
   */
  public const HOLIDAY_REGION_KR = 'KR';
  /**
   * Latvia
   */
  public const HOLIDAY_REGION_LV = 'LV';
  /**
   * Morocco
   */
  public const HOLIDAY_REGION_MA = 'MA';
  /**
   * Mexico
   */
  public const HOLIDAY_REGION_MX = 'MX';
  /**
   * Malaysia
   */
  public const HOLIDAY_REGION_MY = 'MY';
  /**
   * Nigeria
   */
  public const HOLIDAY_REGION_NG = 'NG';
  /**
   * Netherlands
   */
  public const HOLIDAY_REGION_NL = 'NL';
  /**
   * Norway
   */
  public const HOLIDAY_REGION_NO = 'NO';
  /**
   * New Zealand
   */
  public const HOLIDAY_REGION_NZ = 'NZ';
  /**
   * Peru
   */
  public const HOLIDAY_REGION_PE = 'PE';
  /**
   * Philippines
   */
  public const HOLIDAY_REGION_PH = 'PH';
  /**
   * Pakistan
   */
  public const HOLIDAY_REGION_PK = 'PK';
  /**
   * Poland
   */
  public const HOLIDAY_REGION_PL = 'PL';
  /**
   * Portugal
   */
  public const HOLIDAY_REGION_PT = 'PT';
  /**
   * Romania
   */
  public const HOLIDAY_REGION_RO = 'RO';
  /**
   * Serbia
   */
  public const HOLIDAY_REGION_RS = 'RS';
  /**
   * Russian Federation
   */
  public const HOLIDAY_REGION_RU = 'RU';
  /**
   * Saudi Arabia
   */
  public const HOLIDAY_REGION_SA = 'SA';
  /**
   * Sweden
   */
  public const HOLIDAY_REGION_SE = 'SE';
  /**
   * Singapore
   */
  public const HOLIDAY_REGION_SG = 'SG';
  /**
   * Slovenia
   */
  public const HOLIDAY_REGION_SI = 'SI';
  /**
   * Slovakia
   */
  public const HOLIDAY_REGION_SK = 'SK';
  /**
   * Thailand
   */
  public const HOLIDAY_REGION_TH = 'TH';
  /**
   * Turkey
   */
  public const HOLIDAY_REGION_TR = 'TR';
  /**
   * Taiwan
   */
  public const HOLIDAY_REGION_TW = 'TW';
  /**
   * Ukraine
   */
  public const HOLIDAY_REGION_UA = 'UA';
  /**
   * United States
   */
  public const HOLIDAY_REGION_US = 'US';
  /**
   * Venezuela
   */
  public const HOLIDAY_REGION_VE = 'VE';
  /**
   * Vietnam
   */
  public const HOLIDAY_REGION_VN = 'VN';
  /**
   * South Africa
   */
  public const HOLIDAY_REGION_ZA = 'ZA';
  /**
   * Unspecified initialization method.
   */
  public const KMEANS_INITIALIZATION_METHOD_KMEANS_INITIALIZATION_METHOD_UNSPECIFIED = 'KMEANS_INITIALIZATION_METHOD_UNSPECIFIED';
  /**
   * Initializes the centroids randomly.
   */
  public const KMEANS_INITIALIZATION_METHOD_RANDOM = 'RANDOM';
  /**
   * Initializes the centroids using data specified in
   * kmeans_initialization_column.
   */
  public const KMEANS_INITIALIZATION_METHOD_CUSTOM = 'CUSTOM';
  /**
   * Initializes with kmeans++.
   */
  public const KMEANS_INITIALIZATION_METHOD_KMEANS_PLUS_PLUS = 'KMEANS_PLUS_PLUS';
  /**
   * Default value.
   */
  public const LEARN_RATE_STRATEGY_LEARN_RATE_STRATEGY_UNSPECIFIED = 'LEARN_RATE_STRATEGY_UNSPECIFIED';
  /**
   * Use line search to determine learning rate.
   */
  public const LEARN_RATE_STRATEGY_LINE_SEARCH = 'LINE_SEARCH';
  /**
   * Use a constant learning rate.
   */
  public const LEARN_RATE_STRATEGY_CONSTANT = 'CONSTANT';
  /**
   * Default value.
   */
  public const LOSS_TYPE_LOSS_TYPE_UNSPECIFIED = 'LOSS_TYPE_UNSPECIFIED';
  /**
   * Mean squared loss, used for linear regression.
   */
  public const LOSS_TYPE_MEAN_SQUARED_LOSS = 'MEAN_SQUARED_LOSS';
  /**
   * Mean log loss, used for logistic regression.
   */
  public const LOSS_TYPE_MEAN_LOG_LOSS = 'MEAN_LOG_LOSS';
  /**
   * Default value.
   */
  public const MODEL_REGISTRY_MODEL_REGISTRY_UNSPECIFIED = 'MODEL_REGISTRY_UNSPECIFIED';
  /**
   * Vertex AI.
   */
  public const MODEL_REGISTRY_VERTEX_AI = 'VERTEX_AI';
  /**
   * Default value.
   */
  public const OPTIMIZATION_STRATEGY_OPTIMIZATION_STRATEGY_UNSPECIFIED = 'OPTIMIZATION_STRATEGY_UNSPECIFIED';
  /**
   * Uses an iterative batch gradient descent algorithm.
   */
  public const OPTIMIZATION_STRATEGY_BATCH_GRADIENT_DESCENT = 'BATCH_GRADIENT_DESCENT';
  /**
   * Uses a normal equation to solve linear regression problem.
   */
  public const OPTIMIZATION_STRATEGY_NORMAL_EQUATION = 'NORMAL_EQUATION';
  /**
   * Default value.
   */
  public const PCA_SOLVER_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Full eigen-decoposition.
   */
  public const PCA_SOLVER_FULL = 'FULL';
  /**
   * Randomized SVD.
   */
  public const PCA_SOLVER_RANDOMIZED = 'RANDOMIZED';
  /**
   * Auto.
   */
  public const PCA_SOLVER_AUTO = 'AUTO';
  /**
   * Default value.
   */
  public const RESERVATION_AFFINITY_TYPE_RESERVATION_AFFINITY_TYPE_UNSPECIFIED = 'RESERVATION_AFFINITY_TYPE_UNSPECIFIED';
  /**
   * No reservation.
   */
  public const RESERVATION_AFFINITY_TYPE_NO_RESERVATION = 'NO_RESERVATION';
  /**
   * Any reservation.
   */
  public const RESERVATION_AFFINITY_TYPE_ANY_RESERVATION = 'ANY_RESERVATION';
  /**
   * Specific reservation.
   */
  public const RESERVATION_AFFINITY_TYPE_SPECIFIC_RESERVATION = 'SPECIFIC_RESERVATION';
  /**
   * Unspecified tree method.
   */
  public const TREE_METHOD_TREE_METHOD_UNSPECIFIED = 'TREE_METHOD_UNSPECIFIED';
  /**
   * Use heuristic to choose the fastest method.
   */
  public const TREE_METHOD_AUTO = 'AUTO';
  /**
   * Exact greedy algorithm.
   */
  public const TREE_METHOD_EXACT = 'EXACT';
  /**
   * Approximate greedy algorithm using quantile sketch and gradient histogram.
   */
  public const TREE_METHOD_APPROX = 'APPROX';
  /**
   * Fast histogram optimized approximate greedy algorithm.
   */
  public const TREE_METHOD_HIST = 'HIST';
  protected $collection_key = 'vertexAiModelVersionAliases';
  /**
   * Activation function of the neural nets.
   *
   * @var string
   */
  public $activationFn;
  /**
   * If true, detect step changes and make data adjustment in the input time
   * series.
   *
   * @var bool
   */
  public $adjustStepChanges;
  /**
   * Whether to use approximate feature contribution method in XGBoost model
   * explanation for global explain.
   *
   * @var bool
   */
  public $approxGlobalFeatureContrib;
  /**
   * Whether to enable auto ARIMA or not.
   *
   * @var bool
   */
  public $autoArima;
  /**
   * The max value of the sum of non-seasonal p and q.
   *
   * @var string
   */
  public $autoArimaMaxOrder;
  /**
   * The min value of the sum of non-seasonal p and q.
   *
   * @var string
   */
  public $autoArimaMinOrder;
  /**
   * Whether to calculate class weights automatically based on the popularity of
   * each label.
   *
   * @var bool
   */
  public $autoClassWeights;
  /**
   * Batch size for dnn models.
   *
   * @var string
   */
  public $batchSize;
  /**
   * Booster type for boosted tree models.
   *
   * @var string
   */
  public $boosterType;
  /**
   * Budget in hours for AutoML training.
   *
   * @var 
   */
  public $budgetHours;
  /**
   * Whether or not p-value test should be computed for this model. Only
   * available for linear and logistic regression models.
   *
   * @var bool
   */
  public $calculatePValues;
  /**
   * Categorical feature encoding method.
   *
   * @var string
   */
  public $categoryEncodingMethod;
  /**
   * If true, clean spikes and dips in the input time series.
   *
   * @var bool
   */
  public $cleanSpikesAndDips;
  /**
   * Enums for color space, used for processing images in Object Table. See more
   * details at https://www.tensorflow.org/io/tutorials/colorspace.
   *
   * @var string
   */
  public $colorSpace;
  /**
   * Subsample ratio of columns for each level for boosted tree models.
   *
   * @var 
   */
  public $colsampleBylevel;
  /**
   * Subsample ratio of columns for each node(split) for boosted tree models.
   *
   * @var 
   */
  public $colsampleBynode;
  /**
   * Subsample ratio of columns when constructing each tree for boosted tree
   * models.
   *
   * @var 
   */
  public $colsampleBytree;
  /**
   * The contribution metric. Applies to contribution analysis models. Allowed
   * formats supported are for summable and summable ratio contribution metrics.
   * These include expressions such as `SUM(x)` or `SUM(x)/SUM(y)`, where x and
   * y are column names from the base table.
   *
   * @var string
   */
  public $contributionMetric;
  /**
   * Type of normalization algorithm for boosted tree models using dart booster.
   *
   * @var string
   */
  public $dartNormalizeType;
  /**
   * The data frequency of a time series.
   *
   * @var string
   */
  public $dataFrequency;
  /**
   * The column to split data with. This column won't be used as a feature. 1.
   * When data_split_method is CUSTOM, the corresponding column should be
   * boolean. The rows with true value tag are eval data, and the false are
   * training data. 2. When data_split_method is SEQ, the first
   * DATA_SPLIT_EVAL_FRACTION rows (from smallest to largest) in the
   * corresponding column are used as training data, and the rest are eval data.
   * It respects the order in Orderable data types:
   * https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * types#data_type_properties
   *
   * @var string
   */
  public $dataSplitColumn;
  /**
   * The fraction of evaluation data over the whole input data. The rest of data
   * will be used as training data. The format should be double. Accurate to two
   * decimal places. Default value is 0.2.
   *
   * @var 
   */
  public $dataSplitEvalFraction;
  /**
   * The data split type for training and evaluation, e.g. RANDOM.
   *
   * @var string
   */
  public $dataSplitMethod;
  /**
   * If true, perform decompose time series and save the results.
   *
   * @var bool
   */
  public $decomposeTimeSeries;
  /**
   * Optional. Names of the columns to slice on. Applies to contribution
   * analysis models.
   *
   * @var string[]
   */
  public $dimensionIdColumns;
  /**
   * Distance type for clustering models.
   *
   * @var string
   */
  public $distanceType;
  /**
   * Dropout probability for dnn models.
   *
   * @var 
   */
  public $dropout;
  /**
   * Whether to stop early when the loss doesn't improve significantly any more
   * (compared to min_relative_progress). Used only for iterative training
   * algorithms.
   *
   * @var bool
   */
  public $earlyStop;
  /**
   * If true, enable global explanation during training.
   *
   * @var bool
   */
  public $enableGlobalExplain;
  /**
   * The idle TTL of the endpoint before the resources get destroyed. The
   * default value is 6.5 hours.
   *
   * @var string
   */
  public $endpointIdleTtl;
  /**
   * Feedback type that specifies which algorithm to run for matrix
   * factorization.
   *
   * @var string
   */
  public $feedbackType;
  /**
   * Whether the model should include intercept during model training.
   *
   * @var bool
   */
  public $fitIntercept;
  /**
   * The forecast limit lower bound that was used during ARIMA model training
   * with limits. To see more details of the algorithm:
   * https://otexts.com/fpp2/limits.html
   *
   * @var 
   */
  public $forecastLimitLowerBound;
  /**
   * The forecast limit upper bound that was used during ARIMA model training
   * with limits.
   *
   * @var 
   */
  public $forecastLimitUpperBound;
  /**
   * Hidden units for dnn models.
   *
   * @var string[]
   */
  public $hiddenUnits;
  /**
   * The geographical region based on which the holidays are considered in time
   * series modeling. If a valid value is specified, then holiday effects
   * modeling is enabled.
   *
   * @var string
   */
  public $holidayRegion;
  /**
   * A list of geographical regions that are used for time series modeling.
   *
   * @var string[]
   */
  public $holidayRegions;
  /**
   * The number of periods ahead that need to be forecasted.
   *
   * @var string
   */
  public $horizon;
  /**
   * The target evaluation metrics to optimize the hyperparameters for.
   *
   * @var string[]
   */
  public $hparamTuningObjectives;
  /**
   * The id of a Hugging Face model. For example, `google/gemma-2-2b-it`.
   *
   * @var string
   */
  public $huggingFaceModelId;
  /**
   * Include drift when fitting an ARIMA model.
   *
   * @var bool
   */
  public $includeDrift;
  /**
   * Specifies the initial learning rate for the line search learn rate
   * strategy.
   *
   * @var 
   */
  public $initialLearnRate;
  /**
   * Name of input label columns in training data.
   *
   * @var string[]
   */
  public $inputLabelColumns;
  /**
   * Name of the instance weight column for training data. This column isn't be
   * used as a feature.
   *
   * @var string
   */
  public $instanceWeightColumn;
  /**
   * Number of integral steps for the integrated gradients explain method.
   *
   * @var string
   */
  public $integratedGradientsNumSteps;
  /**
   * Name of the column used to determine the rows corresponding to control and
   * test. Applies to contribution analysis models.
   *
   * @var string
   */
  public $isTestColumn;
  /**
   * Item column specified for matrix factorization models.
   *
   * @var string
   */
  public $itemColumn;
  /**
   * The column used to provide the initial centroids for kmeans algorithm when
   * kmeans_initialization_method is CUSTOM.
   *
   * @var string
   */
  public $kmeansInitializationColumn;
  /**
   * The method used to initialize the centroids for kmeans algorithm.
   *
   * @var string
   */
  public $kmeansInitializationMethod;
  /**
   * L1 regularization coefficient to activations.
   *
   * @var 
   */
  public $l1RegActivation;
  /**
   * L1 regularization coefficient.
   *
   * @var 
   */
  public $l1Regularization;
  /**
   * L2 regularization coefficient.
   *
   * @var 
   */
  public $l2Regularization;
  /**
   * Weights associated with each label class, for rebalancing the training
   * data. Only applicable for classification models.
   *
   * @var []
   */
  public $labelClassWeights;
  /**
   * Learning rate in training. Used only for iterative training algorithms.
   *
   * @var 
   */
  public $learnRate;
  /**
   * The strategy to determine learn rate for the current iteration.
   *
   * @var string
   */
  public $learnRateStrategy;
  /**
   * Type of loss function used during training run.
   *
   * @var string
   */
  public $lossType;
  /**
   * The type of the machine used to deploy and serve the model.
   *
   * @var string
   */
  public $machineType;
  /**
   * The maximum number of iterations in training. Used only for iterative
   * training algorithms.
   *
   * @var string
   */
  public $maxIterations;
  /**
   * Maximum number of trials to run in parallel.
   *
   * @var string
   */
  public $maxParallelTrials;
  /**
   * The maximum number of machine replicas that will be deployed on an
   * endpoint. The default value is equal to min_replica_count.
   *
   * @var string
   */
  public $maxReplicaCount;
  /**
   * The maximum number of time points in a time series that can be used in
   * modeling the trend component of the time series. Don't use this option with
   * the `timeSeriesLengthFraction` or `minTimeSeriesLength` options.
   *
   * @var string
   */
  public $maxTimeSeriesLength;
  /**
   * Maximum depth of a tree for boosted tree models.
   *
   * @var string
   */
  public $maxTreeDepth;
  /**
   * The apriori support minimum. Applies to contribution analysis models.
   *
   * @var 
   */
  public $minAprioriSupport;
  /**
   * When early_stop is true, stops training when accuracy improvement is less
   * than 'min_relative_progress'. Used only for iterative training algorithms.
   *
   * @var 
   */
  public $minRelativeProgress;
  /**
   * The minimum number of machine replicas that will be always deployed on an
   * endpoint. This value must be greater than or equal to 1. The default value
   * is 1.
   *
   * @var string
   */
  public $minReplicaCount;
  /**
   * Minimum split loss for boosted tree models.
   *
   * @var 
   */
  public $minSplitLoss;
  /**
   * The minimum number of time points in a time series that are used in
   * modeling the trend component of the time series. If you use this option you
   * must also set the `timeSeriesLengthFraction` option. This training option
   * ensures that enough time points are available when you use
   * `timeSeriesLengthFraction` in trend modeling. This is particularly
   * important when forecasting multiple time series in a single query using
   * `timeSeriesIdColumn`. If the total number of time points is less than the
   * `minTimeSeriesLength` value, then the query uses all available time points.
   *
   * @var string
   */
  public $minTimeSeriesLength;
  /**
   * Minimum sum of instance weight needed in a child for boosted tree models.
   *
   * @var string
   */
  public $minTreeChildWeight;
  /**
   * The name of a Vertex model garden publisher model. Format is
   * `publishers/{publisher}/models/{model}@{optional_version_id}`.
   *
   * @var string
   */
  public $modelGardenModelName;
  /**
   * The model registry.
   *
   * @var string
   */
  public $modelRegistry;
  /**
   * Google Cloud Storage URI from which the model was imported. Only applicable
   * for imported models.
   *
   * @var string
   */
  public $modelUri;
  protected $nonSeasonalOrderType = ArimaOrder::class;
  protected $nonSeasonalOrderDataType = '';
  /**
   * Number of clusters for clustering models.
   *
   * @var string
   */
  public $numClusters;
  /**
   * Num factors specified for matrix factorization models.
   *
   * @var string
   */
  public $numFactors;
  /**
   * Number of parallel trees constructed during each iteration for boosted tree
   * models.
   *
   * @var string
   */
  public $numParallelTree;
  /**
   * Number of principal components to keep in the PCA model. Must be <= the
   * number of features.
   *
   * @var string
   */
  public $numPrincipalComponents;
  /**
   * Number of trials to run this hyperparameter tuning job.
   *
   * @var string
   */
  public $numTrials;
  /**
   * Optimization strategy for training linear regression models.
   *
   * @var string
   */
  public $optimizationStrategy;
  /**
   * Optimizer used for training the neural nets.
   *
   * @var string
   */
  public $optimizer;
  /**
   * The minimum ratio of cumulative explained variance that needs to be given
   * by the PCA model.
   *
   * @var 
   */
  public $pcaExplainedVarianceRatio;
  /**
   * The solver for PCA.
   *
   * @var string
   */
  public $pcaSolver;
  /**
   * Corresponds to the label key of a reservation resource used by Vertex AI.
   * To target a SPECIFIC_RESERVATION by name, use
   * `compute.googleapis.com/reservation-name` as the key and specify the name
   * of your reservation as its value.
   *
   * @var string
   */
  public $reservationAffinityKey;
  /**
   * Specifies the reservation affinity type used to configure a Vertex AI
   * resource. The default value is `NO_RESERVATION`.
   *
   * @var string
   */
  public $reservationAffinityType;
  /**
   * Corresponds to the label values of a reservation resource used by Vertex
   * AI. This must be the full resource name of the reservation or reservation
   * block.
   *
   * @var string[]
   */
  public $reservationAffinityValues;
  /**
   * Number of paths for the sampled Shapley explain method.
   *
   * @var string
   */
  public $sampledShapleyNumPaths;
  /**
   * If true, scale the feature values by dividing the feature standard
   * deviation. Currently only apply to PCA.
   *
   * @var bool
   */
  public $scaleFeatures;
  /**
   * Whether to standardize numerical features. Default to true.
   *
   * @var bool
   */
  public $standardizeFeatures;
  /**
   * Subsample fraction of the training data to grow tree to prevent overfitting
   * for boosted tree models.
   *
   * @var 
   */
  public $subsample;
  /**
   * Based on the selected TF version, the corresponding docker image is used to
   * train external models.
   *
   * @var string
   */
  public $tfVersion;
  /**
   * Column to be designated as time series data for ARIMA model.
   *
   * @var string
   */
  public $timeSeriesDataColumn;
  /**
   * The time series id column that was used during ARIMA model training.
   *
   * @var string
   */
  public $timeSeriesIdColumn;
  /**
   * The time series id columns that were used during ARIMA model training.
   *
   * @var string[]
   */
  public $timeSeriesIdColumns;
  /**
   * The fraction of the interpolated length of the time series that's used to
   * model the time series trend component. All of the time points of the time
   * series are used to model the non-trend component. This training option
   * accelerates modeling training without sacrificing much forecasting
   * accuracy. You can use this option with `minTimeSeriesLength` but not with
   * `maxTimeSeriesLength`.
   *
   * @var 
   */
  public $timeSeriesLengthFraction;
  /**
   * Column to be designated as time series timestamp for ARIMA model.
   *
   * @var string
   */
  public $timeSeriesTimestampColumn;
  /**
   * Tree construction algorithm for boosted tree models.
   *
   * @var string
   */
  public $treeMethod;
  /**
   * Smoothing window size for the trend component. When a positive value is
   * specified, a center moving average smoothing is applied on the history
   * trend. When the smoothing window is out of the boundary at the beginning or
   * the end of the trend, the first element or the last element is padded to
   * fill the smoothing window before the average is applied.
   *
   * @var string
   */
  public $trendSmoothingWindowSize;
  /**
   * User column specified for matrix factorization models.
   *
   * @var string
   */
  public $userColumn;
  /**
   * The version aliases to apply in Vertex AI model registry. Always overwrite
   * if the version aliases exists in a existing model.
   *
   * @var string[]
   */
  public $vertexAiModelVersionAliases;
  /**
   * Hyperparameter for matrix factoration when implicit feedback type is
   * specified.
   *
   * @var 
   */
  public $walsAlpha;
  /**
   * Whether to train a model from the last checkpoint.
   *
   * @var bool
   */
  public $warmStart;
  /**
   * User-selected XGBoost versions for training of XGBoost models.
   *
   * @var string
   */
  public $xgboostVersion;

  /**
   * Activation function of the neural nets.
   *
   * @param string $activationFn
   */
  public function setActivationFn($activationFn)
  {
    $this->activationFn = $activationFn;
  }
  /**
   * @return string
   */
  public function getActivationFn()
  {
    return $this->activationFn;
  }
  /**
   * If true, detect step changes and make data adjustment in the input time
   * series.
   *
   * @param bool $adjustStepChanges
   */
  public function setAdjustStepChanges($adjustStepChanges)
  {
    $this->adjustStepChanges = $adjustStepChanges;
  }
  /**
   * @return bool
   */
  public function getAdjustStepChanges()
  {
    return $this->adjustStepChanges;
  }
  /**
   * Whether to use approximate feature contribution method in XGBoost model
   * explanation for global explain.
   *
   * @param bool $approxGlobalFeatureContrib
   */
  public function setApproxGlobalFeatureContrib($approxGlobalFeatureContrib)
  {
    $this->approxGlobalFeatureContrib = $approxGlobalFeatureContrib;
  }
  /**
   * @return bool
   */
  public function getApproxGlobalFeatureContrib()
  {
    return $this->approxGlobalFeatureContrib;
  }
  /**
   * Whether to enable auto ARIMA or not.
   *
   * @param bool $autoArima
   */
  public function setAutoArima($autoArima)
  {
    $this->autoArima = $autoArima;
  }
  /**
   * @return bool
   */
  public function getAutoArima()
  {
    return $this->autoArima;
  }
  /**
   * The max value of the sum of non-seasonal p and q.
   *
   * @param string $autoArimaMaxOrder
   */
  public function setAutoArimaMaxOrder($autoArimaMaxOrder)
  {
    $this->autoArimaMaxOrder = $autoArimaMaxOrder;
  }
  /**
   * @return string
   */
  public function getAutoArimaMaxOrder()
  {
    return $this->autoArimaMaxOrder;
  }
  /**
   * The min value of the sum of non-seasonal p and q.
   *
   * @param string $autoArimaMinOrder
   */
  public function setAutoArimaMinOrder($autoArimaMinOrder)
  {
    $this->autoArimaMinOrder = $autoArimaMinOrder;
  }
  /**
   * @return string
   */
  public function getAutoArimaMinOrder()
  {
    return $this->autoArimaMinOrder;
  }
  /**
   * Whether to calculate class weights automatically based on the popularity of
   * each label.
   *
   * @param bool $autoClassWeights
   */
  public function setAutoClassWeights($autoClassWeights)
  {
    $this->autoClassWeights = $autoClassWeights;
  }
  /**
   * @return bool
   */
  public function getAutoClassWeights()
  {
    return $this->autoClassWeights;
  }
  /**
   * Batch size for dnn models.
   *
   * @param string $batchSize
   */
  public function setBatchSize($batchSize)
  {
    $this->batchSize = $batchSize;
  }
  /**
   * @return string
   */
  public function getBatchSize()
  {
    return $this->batchSize;
  }
  /**
   * Booster type for boosted tree models.
   *
   * Accepted values: BOOSTER_TYPE_UNSPECIFIED, GBTREE, DART
   *
   * @param self::BOOSTER_TYPE_* $boosterType
   */
  public function setBoosterType($boosterType)
  {
    $this->boosterType = $boosterType;
  }
  /**
   * @return self::BOOSTER_TYPE_*
   */
  public function getBoosterType()
  {
    return $this->boosterType;
  }
  public function setBudgetHours($budgetHours)
  {
    $this->budgetHours = $budgetHours;
  }
  public function getBudgetHours()
  {
    return $this->budgetHours;
  }
  /**
   * Whether or not p-value test should be computed for this model. Only
   * available for linear and logistic regression models.
   *
   * @param bool $calculatePValues
   */
  public function setCalculatePValues($calculatePValues)
  {
    $this->calculatePValues = $calculatePValues;
  }
  /**
   * @return bool
   */
  public function getCalculatePValues()
  {
    return $this->calculatePValues;
  }
  /**
   * Categorical feature encoding method.
   *
   * Accepted values: ENCODING_METHOD_UNSPECIFIED, ONE_HOT_ENCODING,
   * LABEL_ENCODING, DUMMY_ENCODING
   *
   * @param self::CATEGORY_ENCODING_METHOD_* $categoryEncodingMethod
   */
  public function setCategoryEncodingMethod($categoryEncodingMethod)
  {
    $this->categoryEncodingMethod = $categoryEncodingMethod;
  }
  /**
   * @return self::CATEGORY_ENCODING_METHOD_*
   */
  public function getCategoryEncodingMethod()
  {
    return $this->categoryEncodingMethod;
  }
  /**
   * If true, clean spikes and dips in the input time series.
   *
   * @param bool $cleanSpikesAndDips
   */
  public function setCleanSpikesAndDips($cleanSpikesAndDips)
  {
    $this->cleanSpikesAndDips = $cleanSpikesAndDips;
  }
  /**
   * @return bool
   */
  public function getCleanSpikesAndDips()
  {
    return $this->cleanSpikesAndDips;
  }
  /**
   * Enums for color space, used for processing images in Object Table. See more
   * details at https://www.tensorflow.org/io/tutorials/colorspace.
   *
   * Accepted values: COLOR_SPACE_UNSPECIFIED, RGB, HSV, YIQ, YUV, GRAYSCALE
   *
   * @param self::COLOR_SPACE_* $colorSpace
   */
  public function setColorSpace($colorSpace)
  {
    $this->colorSpace = $colorSpace;
  }
  /**
   * @return self::COLOR_SPACE_*
   */
  public function getColorSpace()
  {
    return $this->colorSpace;
  }
  public function setColsampleBylevel($colsampleBylevel)
  {
    $this->colsampleBylevel = $colsampleBylevel;
  }
  public function getColsampleBylevel()
  {
    return $this->colsampleBylevel;
  }
  public function setColsampleBynode($colsampleBynode)
  {
    $this->colsampleBynode = $colsampleBynode;
  }
  public function getColsampleBynode()
  {
    return $this->colsampleBynode;
  }
  public function setColsampleBytree($colsampleBytree)
  {
    $this->colsampleBytree = $colsampleBytree;
  }
  public function getColsampleBytree()
  {
    return $this->colsampleBytree;
  }
  /**
   * The contribution metric. Applies to contribution analysis models. Allowed
   * formats supported are for summable and summable ratio contribution metrics.
   * These include expressions such as `SUM(x)` or `SUM(x)/SUM(y)`, where x and
   * y are column names from the base table.
   *
   * @param string $contributionMetric
   */
  public function setContributionMetric($contributionMetric)
  {
    $this->contributionMetric = $contributionMetric;
  }
  /**
   * @return string
   */
  public function getContributionMetric()
  {
    return $this->contributionMetric;
  }
  /**
   * Type of normalization algorithm for boosted tree models using dart booster.
   *
   * Accepted values: DART_NORMALIZE_TYPE_UNSPECIFIED, TREE, FOREST
   *
   * @param self::DART_NORMALIZE_TYPE_* $dartNormalizeType
   */
  public function setDartNormalizeType($dartNormalizeType)
  {
    $this->dartNormalizeType = $dartNormalizeType;
  }
  /**
   * @return self::DART_NORMALIZE_TYPE_*
   */
  public function getDartNormalizeType()
  {
    return $this->dartNormalizeType;
  }
  /**
   * The data frequency of a time series.
   *
   * Accepted values: DATA_FREQUENCY_UNSPECIFIED, AUTO_FREQUENCY, YEARLY,
   * QUARTERLY, MONTHLY, WEEKLY, DAILY, HOURLY, PER_MINUTE
   *
   * @param self::DATA_FREQUENCY_* $dataFrequency
   */
  public function setDataFrequency($dataFrequency)
  {
    $this->dataFrequency = $dataFrequency;
  }
  /**
   * @return self::DATA_FREQUENCY_*
   */
  public function getDataFrequency()
  {
    return $this->dataFrequency;
  }
  /**
   * The column to split data with. This column won't be used as a feature. 1.
   * When data_split_method is CUSTOM, the corresponding column should be
   * boolean. The rows with true value tag are eval data, and the false are
   * training data. 2. When data_split_method is SEQ, the first
   * DATA_SPLIT_EVAL_FRACTION rows (from smallest to largest) in the
   * corresponding column are used as training data, and the rest are eval data.
   * It respects the order in Orderable data types:
   * https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * types#data_type_properties
   *
   * @param string $dataSplitColumn
   */
  public function setDataSplitColumn($dataSplitColumn)
  {
    $this->dataSplitColumn = $dataSplitColumn;
  }
  /**
   * @return string
   */
  public function getDataSplitColumn()
  {
    return $this->dataSplitColumn;
  }
  public function setDataSplitEvalFraction($dataSplitEvalFraction)
  {
    $this->dataSplitEvalFraction = $dataSplitEvalFraction;
  }
  public function getDataSplitEvalFraction()
  {
    return $this->dataSplitEvalFraction;
  }
  /**
   * The data split type for training and evaluation, e.g. RANDOM.
   *
   * Accepted values: DATA_SPLIT_METHOD_UNSPECIFIED, RANDOM, CUSTOM, SEQUENTIAL,
   * NO_SPLIT, AUTO_SPLIT
   *
   * @param self::DATA_SPLIT_METHOD_* $dataSplitMethod
   */
  public function setDataSplitMethod($dataSplitMethod)
  {
    $this->dataSplitMethod = $dataSplitMethod;
  }
  /**
   * @return self::DATA_SPLIT_METHOD_*
   */
  public function getDataSplitMethod()
  {
    return $this->dataSplitMethod;
  }
  /**
   * If true, perform decompose time series and save the results.
   *
   * @param bool $decomposeTimeSeries
   */
  public function setDecomposeTimeSeries($decomposeTimeSeries)
  {
    $this->decomposeTimeSeries = $decomposeTimeSeries;
  }
  /**
   * @return bool
   */
  public function getDecomposeTimeSeries()
  {
    return $this->decomposeTimeSeries;
  }
  /**
   * Optional. Names of the columns to slice on. Applies to contribution
   * analysis models.
   *
   * @param string[] $dimensionIdColumns
   */
  public function setDimensionIdColumns($dimensionIdColumns)
  {
    $this->dimensionIdColumns = $dimensionIdColumns;
  }
  /**
   * @return string[]
   */
  public function getDimensionIdColumns()
  {
    return $this->dimensionIdColumns;
  }
  /**
   * Distance type for clustering models.
   *
   * Accepted values: DISTANCE_TYPE_UNSPECIFIED, EUCLIDEAN, COSINE
   *
   * @param self::DISTANCE_TYPE_* $distanceType
   */
  public function setDistanceType($distanceType)
  {
    $this->distanceType = $distanceType;
  }
  /**
   * @return self::DISTANCE_TYPE_*
   */
  public function getDistanceType()
  {
    return $this->distanceType;
  }
  public function setDropout($dropout)
  {
    $this->dropout = $dropout;
  }
  public function getDropout()
  {
    return $this->dropout;
  }
  /**
   * Whether to stop early when the loss doesn't improve significantly any more
   * (compared to min_relative_progress). Used only for iterative training
   * algorithms.
   *
   * @param bool $earlyStop
   */
  public function setEarlyStop($earlyStop)
  {
    $this->earlyStop = $earlyStop;
  }
  /**
   * @return bool
   */
  public function getEarlyStop()
  {
    return $this->earlyStop;
  }
  /**
   * If true, enable global explanation during training.
   *
   * @param bool $enableGlobalExplain
   */
  public function setEnableGlobalExplain($enableGlobalExplain)
  {
    $this->enableGlobalExplain = $enableGlobalExplain;
  }
  /**
   * @return bool
   */
  public function getEnableGlobalExplain()
  {
    return $this->enableGlobalExplain;
  }
  /**
   * The idle TTL of the endpoint before the resources get destroyed. The
   * default value is 6.5 hours.
   *
   * @param string $endpointIdleTtl
   */
  public function setEndpointIdleTtl($endpointIdleTtl)
  {
    $this->endpointIdleTtl = $endpointIdleTtl;
  }
  /**
   * @return string
   */
  public function getEndpointIdleTtl()
  {
    return $this->endpointIdleTtl;
  }
  /**
   * Feedback type that specifies which algorithm to run for matrix
   * factorization.
   *
   * Accepted values: FEEDBACK_TYPE_UNSPECIFIED, IMPLICIT, EXPLICIT
   *
   * @param self::FEEDBACK_TYPE_* $feedbackType
   */
  public function setFeedbackType($feedbackType)
  {
    $this->feedbackType = $feedbackType;
  }
  /**
   * @return self::FEEDBACK_TYPE_*
   */
  public function getFeedbackType()
  {
    return $this->feedbackType;
  }
  /**
   * Whether the model should include intercept during model training.
   *
   * @param bool $fitIntercept
   */
  public function setFitIntercept($fitIntercept)
  {
    $this->fitIntercept = $fitIntercept;
  }
  /**
   * @return bool
   */
  public function getFitIntercept()
  {
    return $this->fitIntercept;
  }
  public function setForecastLimitLowerBound($forecastLimitLowerBound)
  {
    $this->forecastLimitLowerBound = $forecastLimitLowerBound;
  }
  public function getForecastLimitLowerBound()
  {
    return $this->forecastLimitLowerBound;
  }
  public function setForecastLimitUpperBound($forecastLimitUpperBound)
  {
    $this->forecastLimitUpperBound = $forecastLimitUpperBound;
  }
  public function getForecastLimitUpperBound()
  {
    return $this->forecastLimitUpperBound;
  }
  /**
   * Hidden units for dnn models.
   *
   * @param string[] $hiddenUnits
   */
  public function setHiddenUnits($hiddenUnits)
  {
    $this->hiddenUnits = $hiddenUnits;
  }
  /**
   * @return string[]
   */
  public function getHiddenUnits()
  {
    return $this->hiddenUnits;
  }
  /**
   * The geographical region based on which the holidays are considered in time
   * series modeling. If a valid value is specified, then holiday effects
   * modeling is enabled.
   *
   * Accepted values: HOLIDAY_REGION_UNSPECIFIED, GLOBAL, NA, JAPAC, EMEA, LAC,
   * AE, AR, AT, AU, BE, BR, CA, CH, CL, CN, CO, CS, CZ, DE, DK, DZ, EC, EE, EG,
   * ES, FI, FR, GB, GR, HK, HU, ID, IE, IL, IN, IR, IT, JP, KR, LV, MA, MX, MY,
   * NG, NL, NO, NZ, PE, PH, PK, PL, PT, RO, RS, RU, SA, SE, SG, SI, SK, TH, TR,
   * TW, UA, US, VE, VN, ZA
   *
   * @param self::HOLIDAY_REGION_* $holidayRegion
   */
  public function setHolidayRegion($holidayRegion)
  {
    $this->holidayRegion = $holidayRegion;
  }
  /**
   * @return self::HOLIDAY_REGION_*
   */
  public function getHolidayRegion()
  {
    return $this->holidayRegion;
  }
  /**
   * A list of geographical regions that are used for time series modeling.
   *
   * @param string[] $holidayRegions
   */
  public function setHolidayRegions($holidayRegions)
  {
    $this->holidayRegions = $holidayRegions;
  }
  /**
   * @return string[]
   */
  public function getHolidayRegions()
  {
    return $this->holidayRegions;
  }
  /**
   * The number of periods ahead that need to be forecasted.
   *
   * @param string $horizon
   */
  public function setHorizon($horizon)
  {
    $this->horizon = $horizon;
  }
  /**
   * @return string
   */
  public function getHorizon()
  {
    return $this->horizon;
  }
  /**
   * The target evaluation metrics to optimize the hyperparameters for.
   *
   * @param string[] $hparamTuningObjectives
   */
  public function setHparamTuningObjectives($hparamTuningObjectives)
  {
    $this->hparamTuningObjectives = $hparamTuningObjectives;
  }
  /**
   * @return string[]
   */
  public function getHparamTuningObjectives()
  {
    return $this->hparamTuningObjectives;
  }
  /**
   * The id of a Hugging Face model. For example, `google/gemma-2-2b-it`.
   *
   * @param string $huggingFaceModelId
   */
  public function setHuggingFaceModelId($huggingFaceModelId)
  {
    $this->huggingFaceModelId = $huggingFaceModelId;
  }
  /**
   * @return string
   */
  public function getHuggingFaceModelId()
  {
    return $this->huggingFaceModelId;
  }
  /**
   * Include drift when fitting an ARIMA model.
   *
   * @param bool $includeDrift
   */
  public function setIncludeDrift($includeDrift)
  {
    $this->includeDrift = $includeDrift;
  }
  /**
   * @return bool
   */
  public function getIncludeDrift()
  {
    return $this->includeDrift;
  }
  public function setInitialLearnRate($initialLearnRate)
  {
    $this->initialLearnRate = $initialLearnRate;
  }
  public function getInitialLearnRate()
  {
    return $this->initialLearnRate;
  }
  /**
   * Name of input label columns in training data.
   *
   * @param string[] $inputLabelColumns
   */
  public function setInputLabelColumns($inputLabelColumns)
  {
    $this->inputLabelColumns = $inputLabelColumns;
  }
  /**
   * @return string[]
   */
  public function getInputLabelColumns()
  {
    return $this->inputLabelColumns;
  }
  /**
   * Name of the instance weight column for training data. This column isn't be
   * used as a feature.
   *
   * @param string $instanceWeightColumn
   */
  public function setInstanceWeightColumn($instanceWeightColumn)
  {
    $this->instanceWeightColumn = $instanceWeightColumn;
  }
  /**
   * @return string
   */
  public function getInstanceWeightColumn()
  {
    return $this->instanceWeightColumn;
  }
  /**
   * Number of integral steps for the integrated gradients explain method.
   *
   * @param string $integratedGradientsNumSteps
   */
  public function setIntegratedGradientsNumSteps($integratedGradientsNumSteps)
  {
    $this->integratedGradientsNumSteps = $integratedGradientsNumSteps;
  }
  /**
   * @return string
   */
  public function getIntegratedGradientsNumSteps()
  {
    return $this->integratedGradientsNumSteps;
  }
  /**
   * Name of the column used to determine the rows corresponding to control and
   * test. Applies to contribution analysis models.
   *
   * @param string $isTestColumn
   */
  public function setIsTestColumn($isTestColumn)
  {
    $this->isTestColumn = $isTestColumn;
  }
  /**
   * @return string
   */
  public function getIsTestColumn()
  {
    return $this->isTestColumn;
  }
  /**
   * Item column specified for matrix factorization models.
   *
   * @param string $itemColumn
   */
  public function setItemColumn($itemColumn)
  {
    $this->itemColumn = $itemColumn;
  }
  /**
   * @return string
   */
  public function getItemColumn()
  {
    return $this->itemColumn;
  }
  /**
   * The column used to provide the initial centroids for kmeans algorithm when
   * kmeans_initialization_method is CUSTOM.
   *
   * @param string $kmeansInitializationColumn
   */
  public function setKmeansInitializationColumn($kmeansInitializationColumn)
  {
    $this->kmeansInitializationColumn = $kmeansInitializationColumn;
  }
  /**
   * @return string
   */
  public function getKmeansInitializationColumn()
  {
    return $this->kmeansInitializationColumn;
  }
  /**
   * The method used to initialize the centroids for kmeans algorithm.
   *
   * Accepted values: KMEANS_INITIALIZATION_METHOD_UNSPECIFIED, RANDOM, CUSTOM,
   * KMEANS_PLUS_PLUS
   *
   * @param self::KMEANS_INITIALIZATION_METHOD_* $kmeansInitializationMethod
   */
  public function setKmeansInitializationMethod($kmeansInitializationMethod)
  {
    $this->kmeansInitializationMethod = $kmeansInitializationMethod;
  }
  /**
   * @return self::KMEANS_INITIALIZATION_METHOD_*
   */
  public function getKmeansInitializationMethod()
  {
    return $this->kmeansInitializationMethod;
  }
  public function setL1RegActivation($l1RegActivation)
  {
    $this->l1RegActivation = $l1RegActivation;
  }
  public function getL1RegActivation()
  {
    return $this->l1RegActivation;
  }
  public function setL1Regularization($l1Regularization)
  {
    $this->l1Regularization = $l1Regularization;
  }
  public function getL1Regularization()
  {
    return $this->l1Regularization;
  }
  public function setL2Regularization($l2Regularization)
  {
    $this->l2Regularization = $l2Regularization;
  }
  public function getL2Regularization()
  {
    return $this->l2Regularization;
  }
  public function setLabelClassWeights($labelClassWeights)
  {
    $this->labelClassWeights = $labelClassWeights;
  }
  public function getLabelClassWeights()
  {
    return $this->labelClassWeights;
  }
  public function setLearnRate($learnRate)
  {
    $this->learnRate = $learnRate;
  }
  public function getLearnRate()
  {
    return $this->learnRate;
  }
  /**
   * The strategy to determine learn rate for the current iteration.
   *
   * Accepted values: LEARN_RATE_STRATEGY_UNSPECIFIED, LINE_SEARCH, CONSTANT
   *
   * @param self::LEARN_RATE_STRATEGY_* $learnRateStrategy
   */
  public function setLearnRateStrategy($learnRateStrategy)
  {
    $this->learnRateStrategy = $learnRateStrategy;
  }
  /**
   * @return self::LEARN_RATE_STRATEGY_*
   */
  public function getLearnRateStrategy()
  {
    return $this->learnRateStrategy;
  }
  /**
   * Type of loss function used during training run.
   *
   * Accepted values: LOSS_TYPE_UNSPECIFIED, MEAN_SQUARED_LOSS, MEAN_LOG_LOSS
   *
   * @param self::LOSS_TYPE_* $lossType
   */
  public function setLossType($lossType)
  {
    $this->lossType = $lossType;
  }
  /**
   * @return self::LOSS_TYPE_*
   */
  public function getLossType()
  {
    return $this->lossType;
  }
  /**
   * The type of the machine used to deploy and serve the model.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * The maximum number of iterations in training. Used only for iterative
   * training algorithms.
   *
   * @param string $maxIterations
   */
  public function setMaxIterations($maxIterations)
  {
    $this->maxIterations = $maxIterations;
  }
  /**
   * @return string
   */
  public function getMaxIterations()
  {
    return $this->maxIterations;
  }
  /**
   * Maximum number of trials to run in parallel.
   *
   * @param string $maxParallelTrials
   */
  public function setMaxParallelTrials($maxParallelTrials)
  {
    $this->maxParallelTrials = $maxParallelTrials;
  }
  /**
   * @return string
   */
  public function getMaxParallelTrials()
  {
    return $this->maxParallelTrials;
  }
  /**
   * The maximum number of machine replicas that will be deployed on an
   * endpoint. The default value is equal to min_replica_count.
   *
   * @param string $maxReplicaCount
   */
  public function setMaxReplicaCount($maxReplicaCount)
  {
    $this->maxReplicaCount = $maxReplicaCount;
  }
  /**
   * @return string
   */
  public function getMaxReplicaCount()
  {
    return $this->maxReplicaCount;
  }
  /**
   * The maximum number of time points in a time series that can be used in
   * modeling the trend component of the time series. Don't use this option with
   * the `timeSeriesLengthFraction` or `minTimeSeriesLength` options.
   *
   * @param string $maxTimeSeriesLength
   */
  public function setMaxTimeSeriesLength($maxTimeSeriesLength)
  {
    $this->maxTimeSeriesLength = $maxTimeSeriesLength;
  }
  /**
   * @return string
   */
  public function getMaxTimeSeriesLength()
  {
    return $this->maxTimeSeriesLength;
  }
  /**
   * Maximum depth of a tree for boosted tree models.
   *
   * @param string $maxTreeDepth
   */
  public function setMaxTreeDepth($maxTreeDepth)
  {
    $this->maxTreeDepth = $maxTreeDepth;
  }
  /**
   * @return string
   */
  public function getMaxTreeDepth()
  {
    return $this->maxTreeDepth;
  }
  public function setMinAprioriSupport($minAprioriSupport)
  {
    $this->minAprioriSupport = $minAprioriSupport;
  }
  public function getMinAprioriSupport()
  {
    return $this->minAprioriSupport;
  }
  public function setMinRelativeProgress($minRelativeProgress)
  {
    $this->minRelativeProgress = $minRelativeProgress;
  }
  public function getMinRelativeProgress()
  {
    return $this->minRelativeProgress;
  }
  /**
   * The minimum number of machine replicas that will be always deployed on an
   * endpoint. This value must be greater than or equal to 1. The default value
   * is 1.
   *
   * @param string $minReplicaCount
   */
  public function setMinReplicaCount($minReplicaCount)
  {
    $this->minReplicaCount = $minReplicaCount;
  }
  /**
   * @return string
   */
  public function getMinReplicaCount()
  {
    return $this->minReplicaCount;
  }
  public function setMinSplitLoss($minSplitLoss)
  {
    $this->minSplitLoss = $minSplitLoss;
  }
  public function getMinSplitLoss()
  {
    return $this->minSplitLoss;
  }
  /**
   * The minimum number of time points in a time series that are used in
   * modeling the trend component of the time series. If you use this option you
   * must also set the `timeSeriesLengthFraction` option. This training option
   * ensures that enough time points are available when you use
   * `timeSeriesLengthFraction` in trend modeling. This is particularly
   * important when forecasting multiple time series in a single query using
   * `timeSeriesIdColumn`. If the total number of time points is less than the
   * `minTimeSeriesLength` value, then the query uses all available time points.
   *
   * @param string $minTimeSeriesLength
   */
  public function setMinTimeSeriesLength($minTimeSeriesLength)
  {
    $this->minTimeSeriesLength = $minTimeSeriesLength;
  }
  /**
   * @return string
   */
  public function getMinTimeSeriesLength()
  {
    return $this->minTimeSeriesLength;
  }
  /**
   * Minimum sum of instance weight needed in a child for boosted tree models.
   *
   * @param string $minTreeChildWeight
   */
  public function setMinTreeChildWeight($minTreeChildWeight)
  {
    $this->minTreeChildWeight = $minTreeChildWeight;
  }
  /**
   * @return string
   */
  public function getMinTreeChildWeight()
  {
    return $this->minTreeChildWeight;
  }
  /**
   * The name of a Vertex model garden publisher model. Format is
   * `publishers/{publisher}/models/{model}@{optional_version_id}`.
   *
   * @param string $modelGardenModelName
   */
  public function setModelGardenModelName($modelGardenModelName)
  {
    $this->modelGardenModelName = $modelGardenModelName;
  }
  /**
   * @return string
   */
  public function getModelGardenModelName()
  {
    return $this->modelGardenModelName;
  }
  /**
   * The model registry.
   *
   * Accepted values: MODEL_REGISTRY_UNSPECIFIED, VERTEX_AI
   *
   * @param self::MODEL_REGISTRY_* $modelRegistry
   */
  public function setModelRegistry($modelRegistry)
  {
    $this->modelRegistry = $modelRegistry;
  }
  /**
   * @return self::MODEL_REGISTRY_*
   */
  public function getModelRegistry()
  {
    return $this->modelRegistry;
  }
  /**
   * Google Cloud Storage URI from which the model was imported. Only applicable
   * for imported models.
   *
   * @param string $modelUri
   */
  public function setModelUri($modelUri)
  {
    $this->modelUri = $modelUri;
  }
  /**
   * @return string
   */
  public function getModelUri()
  {
    return $this->modelUri;
  }
  /**
   * A specification of the non-seasonal part of the ARIMA model: the three
   * components (p, d, q) are the AR order, the degree of differencing, and the
   * MA order.
   *
   * @param ArimaOrder $nonSeasonalOrder
   */
  public function setNonSeasonalOrder(ArimaOrder $nonSeasonalOrder)
  {
    $this->nonSeasonalOrder = $nonSeasonalOrder;
  }
  /**
   * @return ArimaOrder
   */
  public function getNonSeasonalOrder()
  {
    return $this->nonSeasonalOrder;
  }
  /**
   * Number of clusters for clustering models.
   *
   * @param string $numClusters
   */
  public function setNumClusters($numClusters)
  {
    $this->numClusters = $numClusters;
  }
  /**
   * @return string
   */
  public function getNumClusters()
  {
    return $this->numClusters;
  }
  /**
   * Num factors specified for matrix factorization models.
   *
   * @param string $numFactors
   */
  public function setNumFactors($numFactors)
  {
    $this->numFactors = $numFactors;
  }
  /**
   * @return string
   */
  public function getNumFactors()
  {
    return $this->numFactors;
  }
  /**
   * Number of parallel trees constructed during each iteration for boosted tree
   * models.
   *
   * @param string $numParallelTree
   */
  public function setNumParallelTree($numParallelTree)
  {
    $this->numParallelTree = $numParallelTree;
  }
  /**
   * @return string
   */
  public function getNumParallelTree()
  {
    return $this->numParallelTree;
  }
  /**
   * Number of principal components to keep in the PCA model. Must be <= the
   * number of features.
   *
   * @param string $numPrincipalComponents
   */
  public function setNumPrincipalComponents($numPrincipalComponents)
  {
    $this->numPrincipalComponents = $numPrincipalComponents;
  }
  /**
   * @return string
   */
  public function getNumPrincipalComponents()
  {
    return $this->numPrincipalComponents;
  }
  /**
   * Number of trials to run this hyperparameter tuning job.
   *
   * @param string $numTrials
   */
  public function setNumTrials($numTrials)
  {
    $this->numTrials = $numTrials;
  }
  /**
   * @return string
   */
  public function getNumTrials()
  {
    return $this->numTrials;
  }
  /**
   * Optimization strategy for training linear regression models.
   *
   * Accepted values: OPTIMIZATION_STRATEGY_UNSPECIFIED, BATCH_GRADIENT_DESCENT,
   * NORMAL_EQUATION
   *
   * @param self::OPTIMIZATION_STRATEGY_* $optimizationStrategy
   */
  public function setOptimizationStrategy($optimizationStrategy)
  {
    $this->optimizationStrategy = $optimizationStrategy;
  }
  /**
   * @return self::OPTIMIZATION_STRATEGY_*
   */
  public function getOptimizationStrategy()
  {
    return $this->optimizationStrategy;
  }
  /**
   * Optimizer used for training the neural nets.
   *
   * @param string $optimizer
   */
  public function setOptimizer($optimizer)
  {
    $this->optimizer = $optimizer;
  }
  /**
   * @return string
   */
  public function getOptimizer()
  {
    return $this->optimizer;
  }
  public function setPcaExplainedVarianceRatio($pcaExplainedVarianceRatio)
  {
    $this->pcaExplainedVarianceRatio = $pcaExplainedVarianceRatio;
  }
  public function getPcaExplainedVarianceRatio()
  {
    return $this->pcaExplainedVarianceRatio;
  }
  /**
   * The solver for PCA.
   *
   * Accepted values: UNSPECIFIED, FULL, RANDOMIZED, AUTO
   *
   * @param self::PCA_SOLVER_* $pcaSolver
   */
  public function setPcaSolver($pcaSolver)
  {
    $this->pcaSolver = $pcaSolver;
  }
  /**
   * @return self::PCA_SOLVER_*
   */
  public function getPcaSolver()
  {
    return $this->pcaSolver;
  }
  /**
   * Corresponds to the label key of a reservation resource used by Vertex AI.
   * To target a SPECIFIC_RESERVATION by name, use
   * `compute.googleapis.com/reservation-name` as the key and specify the name
   * of your reservation as its value.
   *
   * @param string $reservationAffinityKey
   */
  public function setReservationAffinityKey($reservationAffinityKey)
  {
    $this->reservationAffinityKey = $reservationAffinityKey;
  }
  /**
   * @return string
   */
  public function getReservationAffinityKey()
  {
    return $this->reservationAffinityKey;
  }
  /**
   * Specifies the reservation affinity type used to configure a Vertex AI
   * resource. The default value is `NO_RESERVATION`.
   *
   * Accepted values: RESERVATION_AFFINITY_TYPE_UNSPECIFIED, NO_RESERVATION,
   * ANY_RESERVATION, SPECIFIC_RESERVATION
   *
   * @param self::RESERVATION_AFFINITY_TYPE_* $reservationAffinityType
   */
  public function setReservationAffinityType($reservationAffinityType)
  {
    $this->reservationAffinityType = $reservationAffinityType;
  }
  /**
   * @return self::RESERVATION_AFFINITY_TYPE_*
   */
  public function getReservationAffinityType()
  {
    return $this->reservationAffinityType;
  }
  /**
   * Corresponds to the label values of a reservation resource used by Vertex
   * AI. This must be the full resource name of the reservation or reservation
   * block.
   *
   * @param string[] $reservationAffinityValues
   */
  public function setReservationAffinityValues($reservationAffinityValues)
  {
    $this->reservationAffinityValues = $reservationAffinityValues;
  }
  /**
   * @return string[]
   */
  public function getReservationAffinityValues()
  {
    return $this->reservationAffinityValues;
  }
  /**
   * Number of paths for the sampled Shapley explain method.
   *
   * @param string $sampledShapleyNumPaths
   */
  public function setSampledShapleyNumPaths($sampledShapleyNumPaths)
  {
    $this->sampledShapleyNumPaths = $sampledShapleyNumPaths;
  }
  /**
   * @return string
   */
  public function getSampledShapleyNumPaths()
  {
    return $this->sampledShapleyNumPaths;
  }
  /**
   * If true, scale the feature values by dividing the feature standard
   * deviation. Currently only apply to PCA.
   *
   * @param bool $scaleFeatures
   */
  public function setScaleFeatures($scaleFeatures)
  {
    $this->scaleFeatures = $scaleFeatures;
  }
  /**
   * @return bool
   */
  public function getScaleFeatures()
  {
    return $this->scaleFeatures;
  }
  /**
   * Whether to standardize numerical features. Default to true.
   *
   * @param bool $standardizeFeatures
   */
  public function setStandardizeFeatures($standardizeFeatures)
  {
    $this->standardizeFeatures = $standardizeFeatures;
  }
  /**
   * @return bool
   */
  public function getStandardizeFeatures()
  {
    return $this->standardizeFeatures;
  }
  public function setSubsample($subsample)
  {
    $this->subsample = $subsample;
  }
  public function getSubsample()
  {
    return $this->subsample;
  }
  /**
   * Based on the selected TF version, the corresponding docker image is used to
   * train external models.
   *
   * @param string $tfVersion
   */
  public function setTfVersion($tfVersion)
  {
    $this->tfVersion = $tfVersion;
  }
  /**
   * @return string
   */
  public function getTfVersion()
  {
    return $this->tfVersion;
  }
  /**
   * Column to be designated as time series data for ARIMA model.
   *
   * @param string $timeSeriesDataColumn
   */
  public function setTimeSeriesDataColumn($timeSeriesDataColumn)
  {
    $this->timeSeriesDataColumn = $timeSeriesDataColumn;
  }
  /**
   * @return string
   */
  public function getTimeSeriesDataColumn()
  {
    return $this->timeSeriesDataColumn;
  }
  /**
   * The time series id column that was used during ARIMA model training.
   *
   * @param string $timeSeriesIdColumn
   */
  public function setTimeSeriesIdColumn($timeSeriesIdColumn)
  {
    $this->timeSeriesIdColumn = $timeSeriesIdColumn;
  }
  /**
   * @return string
   */
  public function getTimeSeriesIdColumn()
  {
    return $this->timeSeriesIdColumn;
  }
  /**
   * The time series id columns that were used during ARIMA model training.
   *
   * @param string[] $timeSeriesIdColumns
   */
  public function setTimeSeriesIdColumns($timeSeriesIdColumns)
  {
    $this->timeSeriesIdColumns = $timeSeriesIdColumns;
  }
  /**
   * @return string[]
   */
  public function getTimeSeriesIdColumns()
  {
    return $this->timeSeriesIdColumns;
  }
  public function setTimeSeriesLengthFraction($timeSeriesLengthFraction)
  {
    $this->timeSeriesLengthFraction = $timeSeriesLengthFraction;
  }
  public function getTimeSeriesLengthFraction()
  {
    return $this->timeSeriesLengthFraction;
  }
  /**
   * Column to be designated as time series timestamp for ARIMA model.
   *
   * @param string $timeSeriesTimestampColumn
   */
  public function setTimeSeriesTimestampColumn($timeSeriesTimestampColumn)
  {
    $this->timeSeriesTimestampColumn = $timeSeriesTimestampColumn;
  }
  /**
   * @return string
   */
  public function getTimeSeriesTimestampColumn()
  {
    return $this->timeSeriesTimestampColumn;
  }
  /**
   * Tree construction algorithm for boosted tree models.
   *
   * Accepted values: TREE_METHOD_UNSPECIFIED, AUTO, EXACT, APPROX, HIST
   *
   * @param self::TREE_METHOD_* $treeMethod
   */
  public function setTreeMethod($treeMethod)
  {
    $this->treeMethod = $treeMethod;
  }
  /**
   * @return self::TREE_METHOD_*
   */
  public function getTreeMethod()
  {
    return $this->treeMethod;
  }
  /**
   * Smoothing window size for the trend component. When a positive value is
   * specified, a center moving average smoothing is applied on the history
   * trend. When the smoothing window is out of the boundary at the beginning or
   * the end of the trend, the first element or the last element is padded to
   * fill the smoothing window before the average is applied.
   *
   * @param string $trendSmoothingWindowSize
   */
  public function setTrendSmoothingWindowSize($trendSmoothingWindowSize)
  {
    $this->trendSmoothingWindowSize = $trendSmoothingWindowSize;
  }
  /**
   * @return string
   */
  public function getTrendSmoothingWindowSize()
  {
    return $this->trendSmoothingWindowSize;
  }
  /**
   * User column specified for matrix factorization models.
   *
   * @param string $userColumn
   */
  public function setUserColumn($userColumn)
  {
    $this->userColumn = $userColumn;
  }
  /**
   * @return string
   */
  public function getUserColumn()
  {
    return $this->userColumn;
  }
  /**
   * The version aliases to apply in Vertex AI model registry. Always overwrite
   * if the version aliases exists in a existing model.
   *
   * @param string[] $vertexAiModelVersionAliases
   */
  public function setVertexAiModelVersionAliases($vertexAiModelVersionAliases)
  {
    $this->vertexAiModelVersionAliases = $vertexAiModelVersionAliases;
  }
  /**
   * @return string[]
   */
  public function getVertexAiModelVersionAliases()
  {
    return $this->vertexAiModelVersionAliases;
  }
  public function setWalsAlpha($walsAlpha)
  {
    $this->walsAlpha = $walsAlpha;
  }
  public function getWalsAlpha()
  {
    return $this->walsAlpha;
  }
  /**
   * Whether to train a model from the last checkpoint.
   *
   * @param bool $warmStart
   */
  public function setWarmStart($warmStart)
  {
    $this->warmStart = $warmStart;
  }
  /**
   * @return bool
   */
  public function getWarmStart()
  {
    return $this->warmStart;
  }
  /**
   * User-selected XGBoost versions for training of XGBoost models.
   *
   * @param string $xgboostVersion
   */
  public function setXgboostVersion($xgboostVersion)
  {
    $this->xgboostVersion = $xgboostVersion;
  }
  /**
   * @return string
   */
  public function getXgboostVersion()
  {
    return $this->xgboostVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrainingOptions::class, 'Google_Service_Bigquery_TrainingOptions');
