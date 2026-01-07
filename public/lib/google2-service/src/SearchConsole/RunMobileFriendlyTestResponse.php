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

namespace Google\Service\SearchConsole;

class RunMobileFriendlyTestResponse extends \Google\Collection
{
  /**
   * Internal error when running this test. Please try running the test again.
   */
  public const MOBILE_FRIENDLINESS_MOBILE_FRIENDLY_TEST_RESULT_UNSPECIFIED = 'MOBILE_FRIENDLY_TEST_RESULT_UNSPECIFIED';
  /**
   * The page is mobile friendly.
   */
  public const MOBILE_FRIENDLINESS_MOBILE_FRIENDLY = 'MOBILE_FRIENDLY';
  /**
   * The page is not mobile friendly.
   */
  public const MOBILE_FRIENDLINESS_NOT_MOBILE_FRIENDLY = 'NOT_MOBILE_FRIENDLY';
  protected $collection_key = 'resourceIssues';
  /**
   * Test verdict, whether the page is mobile friendly or not.
   *
   * @var string
   */
  public $mobileFriendliness;
  protected $mobileFriendlyIssuesType = MobileFriendlyIssue::class;
  protected $mobileFriendlyIssuesDataType = 'array';
  protected $resourceIssuesType = ResourceIssue::class;
  protected $resourceIssuesDataType = 'array';
  protected $screenshotType = Image::class;
  protected $screenshotDataType = '';
  protected $testStatusType = TestStatus::class;
  protected $testStatusDataType = '';

  /**
   * Test verdict, whether the page is mobile friendly or not.
   *
   * Accepted values: MOBILE_FRIENDLY_TEST_RESULT_UNSPECIFIED, MOBILE_FRIENDLY,
   * NOT_MOBILE_FRIENDLY
   *
   * @param self::MOBILE_FRIENDLINESS_* $mobileFriendliness
   */
  public function setMobileFriendliness($mobileFriendliness)
  {
    $this->mobileFriendliness = $mobileFriendliness;
  }
  /**
   * @return self::MOBILE_FRIENDLINESS_*
   */
  public function getMobileFriendliness()
  {
    return $this->mobileFriendliness;
  }
  /**
   * List of mobile-usability issues.
   *
   * @param MobileFriendlyIssue[] $mobileFriendlyIssues
   */
  public function setMobileFriendlyIssues($mobileFriendlyIssues)
  {
    $this->mobileFriendlyIssues = $mobileFriendlyIssues;
  }
  /**
   * @return MobileFriendlyIssue[]
   */
  public function getMobileFriendlyIssues()
  {
    return $this->mobileFriendlyIssues;
  }
  /**
   * Information about embedded resources issues.
   *
   * @param ResourceIssue[] $resourceIssues
   */
  public function setResourceIssues($resourceIssues)
  {
    $this->resourceIssues = $resourceIssues;
  }
  /**
   * @return ResourceIssue[]
   */
  public function getResourceIssues()
  {
    return $this->resourceIssues;
  }
  /**
   * Screenshot of the requested URL.
   *
   * @param Image $screenshot
   */
  public function setScreenshot(Image $screenshot)
  {
    $this->screenshot = $screenshot;
  }
  /**
   * @return Image
   */
  public function getScreenshot()
  {
    return $this->screenshot;
  }
  /**
   * Final state of the test, can be either complete or an error.
   *
   * @param TestStatus $testStatus
   */
  public function setTestStatus(TestStatus $testStatus)
  {
    $this->testStatus = $testStatus;
  }
  /**
   * @return TestStatus
   */
  public function getTestStatus()
  {
    return $this->testStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunMobileFriendlyTestResponse::class, 'Google_Service_SearchConsole_RunMobileFriendlyTestResponse');
