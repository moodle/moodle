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

namespace Google\Service\Testing;

class TestMatrix extends \Google\Collection
{
  /**
   * Do not use. For proto versioning only.
   */
  public const INVALID_MATRIX_DETAILS_INVALID_MATRIX_DETAILS_UNSPECIFIED = 'INVALID_MATRIX_DETAILS_UNSPECIFIED';
  /**
   * The matrix is INVALID, but there are no further details available.
   */
  public const INVALID_MATRIX_DETAILS_DETAILS_UNAVAILABLE = 'DETAILS_UNAVAILABLE';
  /**
   * The input app APK could not be parsed.
   */
  public const INVALID_MATRIX_DETAILS_MALFORMED_APK = 'MALFORMED_APK';
  /**
   * The input test APK could not be parsed.
   */
  public const INVALID_MATRIX_DETAILS_MALFORMED_TEST_APK = 'MALFORMED_TEST_APK';
  /**
   * The AndroidManifest.xml could not be found.
   */
  public const INVALID_MATRIX_DETAILS_NO_MANIFEST = 'NO_MANIFEST';
  /**
   * The APK manifest does not declare a package name.
   */
  public const INVALID_MATRIX_DETAILS_NO_PACKAGE_NAME = 'NO_PACKAGE_NAME';
  /**
   * The APK application ID (aka package name) is invalid. See also
   * https://developer.android.com/studio/build/application-id
   */
  public const INVALID_MATRIX_DETAILS_INVALID_PACKAGE_NAME = 'INVALID_PACKAGE_NAME';
  /**
   * The test package and app package are the same.
   */
  public const INVALID_MATRIX_DETAILS_TEST_SAME_AS_APP = 'TEST_SAME_AS_APP';
  /**
   * The test apk does not declare an instrumentation.
   */
  public const INVALID_MATRIX_DETAILS_NO_INSTRUMENTATION = 'NO_INSTRUMENTATION';
  /**
   * The input app apk does not have a signature.
   */
  public const INVALID_MATRIX_DETAILS_NO_SIGNATURE = 'NO_SIGNATURE';
  /**
   * The test runner class specified by user or in the test APK's manifest file
   * is not compatible with Android Test Orchestrator. Orchestrator is only
   * compatible with AndroidJUnitRunner version 1.1 or higher. Orchestrator can
   * be disabled by using DO_NOT_USE_ORCHESTRATOR OrchestratorOption.
   */
  public const INVALID_MATRIX_DETAILS_INSTRUMENTATION_ORCHESTRATOR_INCOMPATIBLE = 'INSTRUMENTATION_ORCHESTRATOR_INCOMPATIBLE';
  /**
   * The test APK does not contain the test runner class specified by the user
   * or in the manifest file. This can be caused by one of the following
   * reasons: - the user provided a runner class name that's incorrect, or - the
   * test runner isn't built into the test APK (might be in the app APK
   * instead).
   */
  public const INVALID_MATRIX_DETAILS_NO_TEST_RUNNER_CLASS = 'NO_TEST_RUNNER_CLASS';
  /**
   * A main launcher activity could not be found.
   */
  public const INVALID_MATRIX_DETAILS_NO_LAUNCHER_ACTIVITY = 'NO_LAUNCHER_ACTIVITY';
  /**
   * The app declares one or more permissions that are not allowed.
   */
  public const INVALID_MATRIX_DETAILS_FORBIDDEN_PERMISSIONS = 'FORBIDDEN_PERMISSIONS';
  /**
   * There is a conflict in the provided robo_directives.
   */
  public const INVALID_MATRIX_DETAILS_INVALID_ROBO_DIRECTIVES = 'INVALID_ROBO_DIRECTIVES';
  /**
   * There is at least one invalid resource name in the provided robo directives
   */
  public const INVALID_MATRIX_DETAILS_INVALID_RESOURCE_NAME = 'INVALID_RESOURCE_NAME';
  /**
   * Invalid definition of action in the robo directives (e.g. a click or ignore
   * action includes an input text field)
   */
  public const INVALID_MATRIX_DETAILS_INVALID_DIRECTIVE_ACTION = 'INVALID_DIRECTIVE_ACTION';
  /**
   * There is no test loop intent filter, or the one that is given is not
   * formatted correctly.
   */
  public const INVALID_MATRIX_DETAILS_TEST_LOOP_INTENT_FILTER_NOT_FOUND = 'TEST_LOOP_INTENT_FILTER_NOT_FOUND';
  /**
   * The request contains a scenario label that was not declared in the
   * manifest.
   */
  public const INVALID_MATRIX_DETAILS_SCENARIO_LABEL_NOT_DECLARED = 'SCENARIO_LABEL_NOT_DECLARED';
  /**
   * There was an error when parsing a label's value.
   */
  public const INVALID_MATRIX_DETAILS_SCENARIO_LABEL_MALFORMED = 'SCENARIO_LABEL_MALFORMED';
  /**
   * The request contains a scenario number that was not declared in the
   * manifest.
   */
  public const INVALID_MATRIX_DETAILS_SCENARIO_NOT_DECLARED = 'SCENARIO_NOT_DECLARED';
  /**
   * Device administrator applications are not allowed.
   */
  public const INVALID_MATRIX_DETAILS_DEVICE_ADMIN_RECEIVER = 'DEVICE_ADMIN_RECEIVER';
  /**
   * The zipped XCTest was malformed. The zip did not contain a single
   * .xctestrun file and the contents of the DerivedData/Build/Products
   * directory.
   */
  public const INVALID_MATRIX_DETAILS_MALFORMED_XC_TEST_ZIP = 'MALFORMED_XC_TEST_ZIP';
  /**
   * The zipped XCTest was built for the iOS simulator rather than for a
   * physical device.
   */
  public const INVALID_MATRIX_DETAILS_BUILT_FOR_IOS_SIMULATOR = 'BUILT_FOR_IOS_SIMULATOR';
  /**
   * The .xctestrun file did not specify any test targets.
   */
  public const INVALID_MATRIX_DETAILS_NO_TESTS_IN_XC_TEST_ZIP = 'NO_TESTS_IN_XC_TEST_ZIP';
  /**
   * One or more of the test targets defined in the .xctestrun file specifies
   * "UseDestinationArtifacts", which is disallowed.
   */
  public const INVALID_MATRIX_DETAILS_USE_DESTINATION_ARTIFACTS = 'USE_DESTINATION_ARTIFACTS';
  /**
   * XC tests which run on physical devices must have "IsAppHostedTestBundle" ==
   * "true" in the xctestrun file.
   */
  public const INVALID_MATRIX_DETAILS_TEST_NOT_APP_HOSTED = 'TEST_NOT_APP_HOSTED';
  /**
   * An Info.plist file in the XCTest zip could not be parsed.
   */
  public const INVALID_MATRIX_DETAILS_PLIST_CANNOT_BE_PARSED = 'PLIST_CANNOT_BE_PARSED';
  /**
   * The APK is marked as "testOnly". Deprecated and not currently used.
   *
   * @deprecated
   */
  public const INVALID_MATRIX_DETAILS_TEST_ONLY_APK = 'TEST_ONLY_APK';
  /**
   * The input IPA could not be parsed.
   */
  public const INVALID_MATRIX_DETAILS_MALFORMED_IPA = 'MALFORMED_IPA';
  /**
   * The application doesn't register the game loop URL scheme.
   */
  public const INVALID_MATRIX_DETAILS_MISSING_URL_SCHEME = 'MISSING_URL_SCHEME';
  /**
   * The iOS application bundle (.app) couldn't be processed.
   */
  public const INVALID_MATRIX_DETAILS_MALFORMED_APP_BUNDLE = 'MALFORMED_APP_BUNDLE';
  /**
   * APK contains no code. See also
   * https://developer.android.com/guide/topics/manifest/application-
   * element.html#code
   */
  public const INVALID_MATRIX_DETAILS_NO_CODE_APK = 'NO_CODE_APK';
  /**
   * Either the provided input APK path was malformed, the APK file does not
   * exist, or the user does not have permission to access the APK file.
   */
  public const INVALID_MATRIX_DETAILS_INVALID_INPUT_APK = 'INVALID_INPUT_APK';
  /**
   * APK is built for a preview SDK which is unsupported
   */
  public const INVALID_MATRIX_DETAILS_INVALID_APK_PREVIEW_SDK = 'INVALID_APK_PREVIEW_SDK';
  /**
   * The matrix expanded to contain too many executions.
   */
  public const INVALID_MATRIX_DETAILS_MATRIX_TOO_LARGE = 'MATRIX_TOO_LARGE';
  /**
   * Not enough test quota to run the executions in this matrix.
   */
  public const INVALID_MATRIX_DETAILS_TEST_QUOTA_EXCEEDED = 'TEST_QUOTA_EXCEEDED';
  /**
   * A required cloud service api is not activated. See:
   * https://firebase.google.com/docs/test-lab/android/continuous#requirements
   */
  public const INVALID_MATRIX_DETAILS_SERVICE_NOT_ACTIVATED = 'SERVICE_NOT_ACTIVATED';
  /**
   * There was an unknown permission issue running this test.
   */
  public const INVALID_MATRIX_DETAILS_UNKNOWN_PERMISSION_ERROR = 'UNKNOWN_PERMISSION_ERROR';
  /**
   * Do not use. For proto versioning only.
   */
  public const OUTCOME_SUMMARY_OUTCOME_SUMMARY_UNSPECIFIED = 'OUTCOME_SUMMARY_UNSPECIFIED';
  /**
   * The test matrix run was successful, for instance: - All the test cases
   * passed. - Robo did not detect a crash of the application under test.
   */
  public const OUTCOME_SUMMARY_SUCCESS = 'SUCCESS';
  /**
   * A run failed, for instance: - One or more test cases failed. - A test timed
   * out. - The application under test crashed.
   */
  public const OUTCOME_SUMMARY_FAILURE = 'FAILURE';
  /**
   * Something unexpected happened. The run should still be considered
   * unsuccessful but this is likely a transient problem and re-running the test
   * might be successful.
   */
  public const OUTCOME_SUMMARY_INCONCLUSIVE = 'INCONCLUSIVE';
  /**
   * All tests were skipped, for instance: - All device configurations were
   * incompatible.
   */
  public const OUTCOME_SUMMARY_SKIPPED = 'SKIPPED';
  /**
   * Do not use. For proto versioning only.
   */
  public const STATE_TEST_STATE_UNSPECIFIED = 'TEST_STATE_UNSPECIFIED';
  /**
   * The execution or matrix is being validated.
   */
  public const STATE_VALIDATING = 'VALIDATING';
  /**
   * The execution or matrix is waiting for resources to become available.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The execution is currently being processed. Can only be set on an
   * execution.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The execution or matrix has terminated normally. On a matrix this means
   * that the matrix level processing completed normally, but individual
   * executions may be in an ERROR state.
   */
  public const STATE_FINISHED = 'FINISHED';
  /**
   * The execution or matrix has stopped because it encountered an
   * infrastructure failure.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The execution was not run because it corresponds to a unsupported
   * environment. Can only be set on an execution.
   */
  public const STATE_UNSUPPORTED_ENVIRONMENT = 'UNSUPPORTED_ENVIRONMENT';
  /**
   * The execution was not run because the provided inputs are incompatible with
   * the requested environment. Example: requested AndroidVersion is lower than
   * APK's minSdkVersion Can only be set on an execution.
   */
  public const STATE_INCOMPATIBLE_ENVIRONMENT = 'INCOMPATIBLE_ENVIRONMENT';
  /**
   * The execution was not run because the provided inputs are incompatible with
   * the requested architecture. Example: requested device does not support
   * running the native code in the supplied APK Can only be set on an
   * execution.
   */
  public const STATE_INCOMPATIBLE_ARCHITECTURE = 'INCOMPATIBLE_ARCHITECTURE';
  /**
   * The user cancelled the execution. Can only be set on an execution.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The execution or matrix was not run because the provided inputs are not
   * valid. Examples: input file is not of the expected type, is
   * malformed/corrupt, or was flagged as malware
   */
  public const STATE_INVALID = 'INVALID';
  protected $collection_key = 'testExecutions';
  protected $clientInfoType = ClientInfo::class;
  protected $clientInfoDataType = '';
  protected $environmentMatrixType = EnvironmentMatrix::class;
  protected $environmentMatrixDataType = '';
  protected $extendedInvalidMatrixDetailsType = MatrixErrorDetail::class;
  protected $extendedInvalidMatrixDetailsDataType = 'array';
  /**
   * If true, only a single attempt at most will be made to run each
   * execution/shard in the matrix. Flaky test attempts are not affected.
   * Normally, 2 or more attempts are made if a potential infrastructure issue
   * is detected. This feature is for latency sensitive workloads. The incidence
   * of execution failures may be significantly greater for fail-fast matrices
   * and support is more limited because of that expectation.
   *
   * @var bool
   */
  public $failFast;
  /**
   * The number of times a TestExecution should be re-attempted if one or more
   * of its test cases fail for any reason. The maximum number of reruns allowed
   * is 10. Default is 0, which implies no reruns.
   *
   * @var int
   */
  public $flakyTestAttempts;
  /**
   * Output only. Describes why the matrix is considered invalid. Only useful
   * for matrices in the INVALID state.
   *
   * @var string
   */
  public $invalidMatrixDetails;
  /**
   * Output Only. The overall outcome of the test. Only set when the test matrix
   * state is FINISHED.
   *
   * @var string
   */
  public $outcomeSummary;
  /**
   * The cloud project that owns the test matrix.
   *
   * @var string
   */
  public $projectId;
  protected $resultStorageType = ResultStorage::class;
  protected $resultStorageDataType = '';
  /**
   * Output only. Indicates the current progress of the test matrix.
   *
   * @var string
   */
  public $state;
  protected $testExecutionsType = TestExecution::class;
  protected $testExecutionsDataType = 'array';
  /**
   * Output only. Unique id set by the service.
   *
   * @var string
   */
  public $testMatrixId;
  protected $testSpecificationType = TestSpecification::class;
  protected $testSpecificationDataType = '';
  /**
   * Output only. The time this test matrix was initially created.
   *
   * @var string
   */
  public $timestamp;

  /**
   * Information about the client which invoked the test.
   *
   * @param ClientInfo $clientInfo
   */
  public function setClientInfo(ClientInfo $clientInfo)
  {
    $this->clientInfo = $clientInfo;
  }
  /**
   * @return ClientInfo
   */
  public function getClientInfo()
  {
    return $this->clientInfo;
  }
  /**
   * Required. The devices the tests are being executed on.
   *
   * @param EnvironmentMatrix $environmentMatrix
   */
  public function setEnvironmentMatrix(EnvironmentMatrix $environmentMatrix)
  {
    $this->environmentMatrix = $environmentMatrix;
  }
  /**
   * @return EnvironmentMatrix
   */
  public function getEnvironmentMatrix()
  {
    return $this->environmentMatrix;
  }
  /**
   * Output only. Details about why a matrix was deemed invalid. If multiple
   * checks can be safely performed, they will be reported but no assumptions
   * should be made about the length of this list.
   *
   * @param MatrixErrorDetail[] $extendedInvalidMatrixDetails
   */
  public function setExtendedInvalidMatrixDetails($extendedInvalidMatrixDetails)
  {
    $this->extendedInvalidMatrixDetails = $extendedInvalidMatrixDetails;
  }
  /**
   * @return MatrixErrorDetail[]
   */
  public function getExtendedInvalidMatrixDetails()
  {
    return $this->extendedInvalidMatrixDetails;
  }
  /**
   * If true, only a single attempt at most will be made to run each
   * execution/shard in the matrix. Flaky test attempts are not affected.
   * Normally, 2 or more attempts are made if a potential infrastructure issue
   * is detected. This feature is for latency sensitive workloads. The incidence
   * of execution failures may be significantly greater for fail-fast matrices
   * and support is more limited because of that expectation.
   *
   * @param bool $failFast
   */
  public function setFailFast($failFast)
  {
    $this->failFast = $failFast;
  }
  /**
   * @return bool
   */
  public function getFailFast()
  {
    return $this->failFast;
  }
  /**
   * The number of times a TestExecution should be re-attempted if one or more
   * of its test cases fail for any reason. The maximum number of reruns allowed
   * is 10. Default is 0, which implies no reruns.
   *
   * @param int $flakyTestAttempts
   */
  public function setFlakyTestAttempts($flakyTestAttempts)
  {
    $this->flakyTestAttempts = $flakyTestAttempts;
  }
  /**
   * @return int
   */
  public function getFlakyTestAttempts()
  {
    return $this->flakyTestAttempts;
  }
  /**
   * Output only. Describes why the matrix is considered invalid. Only useful
   * for matrices in the INVALID state.
   *
   * Accepted values: INVALID_MATRIX_DETAILS_UNSPECIFIED, DETAILS_UNAVAILABLE,
   * MALFORMED_APK, MALFORMED_TEST_APK, NO_MANIFEST, NO_PACKAGE_NAME,
   * INVALID_PACKAGE_NAME, TEST_SAME_AS_APP, NO_INSTRUMENTATION, NO_SIGNATURE,
   * INSTRUMENTATION_ORCHESTRATOR_INCOMPATIBLE, NO_TEST_RUNNER_CLASS,
   * NO_LAUNCHER_ACTIVITY, FORBIDDEN_PERMISSIONS, INVALID_ROBO_DIRECTIVES,
   * INVALID_RESOURCE_NAME, INVALID_DIRECTIVE_ACTION,
   * TEST_LOOP_INTENT_FILTER_NOT_FOUND, SCENARIO_LABEL_NOT_DECLARED,
   * SCENARIO_LABEL_MALFORMED, SCENARIO_NOT_DECLARED, DEVICE_ADMIN_RECEIVER,
   * MALFORMED_XC_TEST_ZIP, BUILT_FOR_IOS_SIMULATOR, NO_TESTS_IN_XC_TEST_ZIP,
   * USE_DESTINATION_ARTIFACTS, TEST_NOT_APP_HOSTED, PLIST_CANNOT_BE_PARSED,
   * TEST_ONLY_APK, MALFORMED_IPA, MISSING_URL_SCHEME, MALFORMED_APP_BUNDLE,
   * NO_CODE_APK, INVALID_INPUT_APK, INVALID_APK_PREVIEW_SDK, MATRIX_TOO_LARGE,
   * TEST_QUOTA_EXCEEDED, SERVICE_NOT_ACTIVATED, UNKNOWN_PERMISSION_ERROR
   *
   * @param self::INVALID_MATRIX_DETAILS_* $invalidMatrixDetails
   */
  public function setInvalidMatrixDetails($invalidMatrixDetails)
  {
    $this->invalidMatrixDetails = $invalidMatrixDetails;
  }
  /**
   * @return self::INVALID_MATRIX_DETAILS_*
   */
  public function getInvalidMatrixDetails()
  {
    return $this->invalidMatrixDetails;
  }
  /**
   * Output Only. The overall outcome of the test. Only set when the test matrix
   * state is FINISHED.
   *
   * Accepted values: OUTCOME_SUMMARY_UNSPECIFIED, SUCCESS, FAILURE,
   * INCONCLUSIVE, SKIPPED
   *
   * @param self::OUTCOME_SUMMARY_* $outcomeSummary
   */
  public function setOutcomeSummary($outcomeSummary)
  {
    $this->outcomeSummary = $outcomeSummary;
  }
  /**
   * @return self::OUTCOME_SUMMARY_*
   */
  public function getOutcomeSummary()
  {
    return $this->outcomeSummary;
  }
  /**
   * The cloud project that owns the test matrix.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. Where the results for the matrix are written.
   *
   * @param ResultStorage $resultStorage
   */
  public function setResultStorage(ResultStorage $resultStorage)
  {
    $this->resultStorage = $resultStorage;
  }
  /**
   * @return ResultStorage
   */
  public function getResultStorage()
  {
    return $this->resultStorage;
  }
  /**
   * Output only. Indicates the current progress of the test matrix.
   *
   * Accepted values: TEST_STATE_UNSPECIFIED, VALIDATING, PENDING, RUNNING,
   * FINISHED, ERROR, UNSUPPORTED_ENVIRONMENT, INCOMPATIBLE_ENVIRONMENT,
   * INCOMPATIBLE_ARCHITECTURE, CANCELLED, INVALID
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The list of test executions that the service creates for this
   * matrix.
   *
   * @param TestExecution[] $testExecutions
   */
  public function setTestExecutions($testExecutions)
  {
    $this->testExecutions = $testExecutions;
  }
  /**
   * @return TestExecution[]
   */
  public function getTestExecutions()
  {
    return $this->testExecutions;
  }
  /**
   * Output only. Unique id set by the service.
   *
   * @param string $testMatrixId
   */
  public function setTestMatrixId($testMatrixId)
  {
    $this->testMatrixId = $testMatrixId;
  }
  /**
   * @return string
   */
  public function getTestMatrixId()
  {
    return $this->testMatrixId;
  }
  /**
   * Required. How to run the test.
   *
   * @param TestSpecification $testSpecification
   */
  public function setTestSpecification(TestSpecification $testSpecification)
  {
    $this->testSpecification = $testSpecification;
  }
  /**
   * @return TestSpecification
   */
  public function getTestSpecification()
  {
    return $this->testSpecification;
  }
  /**
   * Output only. The time this test matrix was initially created.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestMatrix::class, 'Google_Service_Testing_TestMatrix');
