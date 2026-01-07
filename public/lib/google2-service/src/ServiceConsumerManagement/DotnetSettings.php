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

namespace Google\Service\ServiceConsumerManagement;

class DotnetSettings extends \Google\Collection
{
  protected $collection_key = 'ignoredResources';
  protected $commonType = CommonLanguageSettings::class;
  protected $commonDataType = '';
  /**
   * Namespaces which must be aliased in snippets due to a known (but non-
   * generator-predictable) naming collision
   *
   * @var string[]
   */
  public $forcedNamespaceAliases;
  /**
   * Method signatures (in the form "service.method(signature)") which are
   * provided separately, so shouldn't be generated. Snippets *calling* these
   * methods are still generated, however.
   *
   * @var string[]
   */
  public $handwrittenSignatures;
  /**
   * List of full resource types to ignore during generation. This is typically
   * used for API-specific Location resources, which should be handled by the
   * generator as if they were actually the common Location resources. Example
   * entry: "documentai.googleapis.com/Location"
   *
   * @var string[]
   */
  public $ignoredResources;
  /**
   * Map from full resource types to the effective short name for the resource.
   * This is used when otherwise resource named from different services would
   * cause naming collisions. Example entry:
   * "datalabeling.googleapis.com/Dataset": "DataLabelingDataset"
   *
   * @var string[]
   */
  public $renamedResources;
  /**
   * Map from original service names to renamed versions. This is used when the
   * default generated types would cause a naming conflict. (Neither name is
   * fully-qualified.) Example: Subscriber to SubscriberServiceApi.
   *
   * @var string[]
   */
  public $renamedServices;

  /**
   * Some settings.
   *
   * @param CommonLanguageSettings $common
   */
  public function setCommon(CommonLanguageSettings $common)
  {
    $this->common = $common;
  }
  /**
   * @return CommonLanguageSettings
   */
  public function getCommon()
  {
    return $this->common;
  }
  /**
   * Namespaces which must be aliased in snippets due to a known (but non-
   * generator-predictable) naming collision
   *
   * @param string[] $forcedNamespaceAliases
   */
  public function setForcedNamespaceAliases($forcedNamespaceAliases)
  {
    $this->forcedNamespaceAliases = $forcedNamespaceAliases;
  }
  /**
   * @return string[]
   */
  public function getForcedNamespaceAliases()
  {
    return $this->forcedNamespaceAliases;
  }
  /**
   * Method signatures (in the form "service.method(signature)") which are
   * provided separately, so shouldn't be generated. Snippets *calling* these
   * methods are still generated, however.
   *
   * @param string[] $handwrittenSignatures
   */
  public function setHandwrittenSignatures($handwrittenSignatures)
  {
    $this->handwrittenSignatures = $handwrittenSignatures;
  }
  /**
   * @return string[]
   */
  public function getHandwrittenSignatures()
  {
    return $this->handwrittenSignatures;
  }
  /**
   * List of full resource types to ignore during generation. This is typically
   * used for API-specific Location resources, which should be handled by the
   * generator as if they were actually the common Location resources. Example
   * entry: "documentai.googleapis.com/Location"
   *
   * @param string[] $ignoredResources
   */
  public function setIgnoredResources($ignoredResources)
  {
    $this->ignoredResources = $ignoredResources;
  }
  /**
   * @return string[]
   */
  public function getIgnoredResources()
  {
    return $this->ignoredResources;
  }
  /**
   * Map from full resource types to the effective short name for the resource.
   * This is used when otherwise resource named from different services would
   * cause naming collisions. Example entry:
   * "datalabeling.googleapis.com/Dataset": "DataLabelingDataset"
   *
   * @param string[] $renamedResources
   */
  public function setRenamedResources($renamedResources)
  {
    $this->renamedResources = $renamedResources;
  }
  /**
   * @return string[]
   */
  public function getRenamedResources()
  {
    return $this->renamedResources;
  }
  /**
   * Map from original service names to renamed versions. This is used when the
   * default generated types would cause a naming conflict. (Neither name is
   * fully-qualified.) Example: Subscriber to SubscriberServiceApi.
   *
   * @param string[] $renamedServices
   */
  public function setRenamedServices($renamedServices)
  {
    $this->renamedServices = $renamedServices;
  }
  /**
   * @return string[]
   */
  public function getRenamedServices()
  {
    return $this->renamedServices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DotnetSettings::class, 'Google_Service_ServiceConsumerManagement_DotnetSettings');
