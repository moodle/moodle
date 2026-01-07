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

class IosXcTest extends \Google\Model
{
  /**
   * Output only. The bundle id for the application under test.
   *
   * @var string
   */
  public $appBundleId;
  /**
   * The option to test special app entitlements. Setting this would re-sign the
   * app having special entitlements with an explicit application-identifier.
   * Currently supports testing aps-environment entitlement.
   *
   * @var bool
   */
  public $testSpecialEntitlements;
  protected $testsZipType = FileReference::class;
  protected $testsZipDataType = '';
  /**
   * The Xcode version that should be used for the test. Use the
   * TestEnvironmentDiscoveryService to get supported options. Defaults to the
   * latest Xcode version Firebase Test Lab supports.
   *
   * @var string
   */
  public $xcodeVersion;
  protected $xctestrunType = FileReference::class;
  protected $xctestrunDataType = '';

  /**
   * Output only. The bundle id for the application under test.
   *
   * @param string $appBundleId
   */
  public function setAppBundleId($appBundleId)
  {
    $this->appBundleId = $appBundleId;
  }
  /**
   * @return string
   */
  public function getAppBundleId()
  {
    return $this->appBundleId;
  }
  /**
   * The option to test special app entitlements. Setting this would re-sign the
   * app having special entitlements with an explicit application-identifier.
   * Currently supports testing aps-environment entitlement.
   *
   * @param bool $testSpecialEntitlements
   */
  public function setTestSpecialEntitlements($testSpecialEntitlements)
  {
    $this->testSpecialEntitlements = $testSpecialEntitlements;
  }
  /**
   * @return bool
   */
  public function getTestSpecialEntitlements()
  {
    return $this->testSpecialEntitlements;
  }
  /**
   * Required. The .zip containing the .xctestrun file and the contents of the
   * DerivedData/Build/Products directory. The .xctestrun file in this zip is
   * ignored if the xctestrun field is specified.
   *
   * @param FileReference $testsZip
   */
  public function setTestsZip(FileReference $testsZip)
  {
    $this->testsZip = $testsZip;
  }
  /**
   * @return FileReference
   */
  public function getTestsZip()
  {
    return $this->testsZip;
  }
  /**
   * The Xcode version that should be used for the test. Use the
   * TestEnvironmentDiscoveryService to get supported options. Defaults to the
   * latest Xcode version Firebase Test Lab supports.
   *
   * @param string $xcodeVersion
   */
  public function setXcodeVersion($xcodeVersion)
  {
    $this->xcodeVersion = $xcodeVersion;
  }
  /**
   * @return string
   */
  public function getXcodeVersion()
  {
    return $this->xcodeVersion;
  }
  /**
   * An .xctestrun file that will override the .xctestrun file in the tests zip.
   * Because the .xctestrun file contains environment variables along with test
   * methods to run and/or ignore, this can be useful for sharding tests.
   * Default is taken from the tests zip.
   *
   * @param FileReference $xctestrun
   */
  public function setXctestrun(FileReference $xctestrun)
  {
    $this->xctestrun = $xctestrun;
  }
  /**
   * @return FileReference
   */
  public function getXctestrun()
  {
    return $this->xctestrun;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosXcTest::class, 'Google_Service_Testing_IosXcTest');
