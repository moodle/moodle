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

namespace Google\Service\GKEHub;

class PolicyControllerPolicyContentSpec extends \Google\Model
{
  protected $bundlesType = PolicyControllerBundleInstallSpec::class;
  protected $bundlesDataType = 'map';
  protected $templateLibraryType = PolicyControllerTemplateLibraryConfig::class;
  protected $templateLibraryDataType = '';

  /**
   * map of bundle name to BundleInstallSpec. The bundle name maps to the
   * `bundleName` key in the `policycontroller.gke.io/constraintData` annotation
   * on a constraint.
   *
   * @param PolicyControllerBundleInstallSpec[] $bundles
   */
  public function setBundles($bundles)
  {
    $this->bundles = $bundles;
  }
  /**
   * @return PolicyControllerBundleInstallSpec[]
   */
  public function getBundles()
  {
    return $this->bundles;
  }
  /**
   * Configures the installation of the Template Library.
   *
   * @param PolicyControllerTemplateLibraryConfig $templateLibrary
   */
  public function setTemplateLibrary(PolicyControllerTemplateLibraryConfig $templateLibrary)
  {
    $this->templateLibrary = $templateLibrary;
  }
  /**
   * @return PolicyControllerTemplateLibraryConfig
   */
  public function getTemplateLibrary()
  {
    return $this->templateLibrary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyControllerPolicyContentSpec::class, 'Google_Service_GKEHub_PolicyControllerPolicyContentSpec');
