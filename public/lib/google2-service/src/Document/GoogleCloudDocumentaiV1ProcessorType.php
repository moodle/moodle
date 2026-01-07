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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessorType extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'sampleDocumentUris';
  /**
   * Whether the processor type allows creation. If true, users can create a
   * processor of this processor type. Otherwise, users need to request access.
   *
   * @var bool
   */
  public $allowCreation;
  protected $availableLocationsType = GoogleCloudDocumentaiV1ProcessorTypeLocationInfo::class;
  protected $availableLocationsDataType = 'array';
  /**
   * The processor category, used by UI to group processor types.
   *
   * @var string
   */
  public $category;
  /**
   * Launch stage of the processor type
   *
   * @var string
   */
  public $launchStage;
  /**
   * The resource name of the processor type. Format:
   * `projects/{project}/processorTypes/{processor_type}`
   *
   * @var string
   */
  public $name;
  /**
   * A set of Cloud Storage URIs of sample documents for this processor.
   *
   * @var string[]
   */
  public $sampleDocumentUris;
  /**
   * The processor type, such as: `OCR_PROCESSOR`, `INVOICE_PROCESSOR`.
   *
   * @var string
   */
  public $type;

  /**
   * Whether the processor type allows creation. If true, users can create a
   * processor of this processor type. Otherwise, users need to request access.
   *
   * @param bool $allowCreation
   */
  public function setAllowCreation($allowCreation)
  {
    $this->allowCreation = $allowCreation;
  }
  /**
   * @return bool
   */
  public function getAllowCreation()
  {
    return $this->allowCreation;
  }
  /**
   * The locations in which this processor is available.
   *
   * @param GoogleCloudDocumentaiV1ProcessorTypeLocationInfo[] $availableLocations
   */
  public function setAvailableLocations($availableLocations)
  {
    $this->availableLocations = $availableLocations;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorTypeLocationInfo[]
   */
  public function getAvailableLocations()
  {
    return $this->availableLocations;
  }
  /**
   * The processor category, used by UI to group processor types.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Launch stage of the processor type
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * The resource name of the processor type. Format:
   * `projects/{project}/processorTypes/{processor_type}`
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
   * A set of Cloud Storage URIs of sample documents for this processor.
   *
   * @param string[] $sampleDocumentUris
   */
  public function setSampleDocumentUris($sampleDocumentUris)
  {
    $this->sampleDocumentUris = $sampleDocumentUris;
  }
  /**
   * @return string[]
   */
  public function getSampleDocumentUris()
  {
    return $this->sampleDocumentUris;
  }
  /**
   * The processor type, such as: `OCR_PROCESSOR`, `INVOICE_PROCESSOR`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ProcessorType::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessorType');
