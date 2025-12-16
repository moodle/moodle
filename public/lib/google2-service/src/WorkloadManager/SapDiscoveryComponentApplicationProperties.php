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

namespace Google\Service\WorkloadManager;

class SapDiscoveryComponentApplicationProperties extends \Google\Model
{
  /**
   * Unspecified application type
   */
  public const APPLICATION_TYPE_APPLICATION_TYPE_UNSPECIFIED = 'APPLICATION_TYPE_UNSPECIFIED';
  /**
   * SAP Netweaver
   */
  public const APPLICATION_TYPE_NETWEAVER = 'NETWEAVER';
  /**
   * SAP Netweaver ABAP
   */
  public const APPLICATION_TYPE_NETWEAVER_ABAP = 'NETWEAVER_ABAP';
  /**
   * SAP Netweaver Java
   */
  public const APPLICATION_TYPE_NETWEAVER_JAVA = 'NETWEAVER_JAVA';
  /**
   * Optional. Deprecated: ApplicationType now tells you whether this is ABAP or
   * Java.
   *
   * @deprecated
   * @var bool
   */
  public $abap;
  /**
   * Optional. Instance number of the SAP application instance.
   *
   * @var string
   */
  public $appInstanceNumber;
  /**
   * Required. Type of the application. Netweaver, etc.
   *
   * @var string
   */
  public $applicationType;
  /**
   * Optional. Instance number of the ASCS instance.
   *
   * @var string
   */
  public $ascsInstanceNumber;
  /**
   * Optional. Resource URI of the recognized ASCS host of the application.
   *
   * @var string
   */
  public $ascsUri;
  /**
   * Optional. Instance number of the ERS instance.
   *
   * @var string
   */
  public $ersInstanceNumber;
  /**
   * Optional. Kernel version for Netweaver running in the system.
   *
   * @var string
   */
  public $kernelVersion;
  /**
   * Optional. Resource URI of the recognized shared NFS of the application. May
   * be empty if the application server has only a single node.
   *
   * @var string
   */
  public $nfsUri;

  /**
   * Optional. Deprecated: ApplicationType now tells you whether this is ABAP or
   * Java.
   *
   * @deprecated
   * @param bool $abap
   */
  public function setAbap($abap)
  {
    $this->abap = $abap;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getAbap()
  {
    return $this->abap;
  }
  /**
   * Optional. Instance number of the SAP application instance.
   *
   * @param string $appInstanceNumber
   */
  public function setAppInstanceNumber($appInstanceNumber)
  {
    $this->appInstanceNumber = $appInstanceNumber;
  }
  /**
   * @return string
   */
  public function getAppInstanceNumber()
  {
    return $this->appInstanceNumber;
  }
  /**
   * Required. Type of the application. Netweaver, etc.
   *
   * Accepted values: APPLICATION_TYPE_UNSPECIFIED, NETWEAVER, NETWEAVER_ABAP,
   * NETWEAVER_JAVA
   *
   * @param self::APPLICATION_TYPE_* $applicationType
   */
  public function setApplicationType($applicationType)
  {
    $this->applicationType = $applicationType;
  }
  /**
   * @return self::APPLICATION_TYPE_*
   */
  public function getApplicationType()
  {
    return $this->applicationType;
  }
  /**
   * Optional. Instance number of the ASCS instance.
   *
   * @param string $ascsInstanceNumber
   */
  public function setAscsInstanceNumber($ascsInstanceNumber)
  {
    $this->ascsInstanceNumber = $ascsInstanceNumber;
  }
  /**
   * @return string
   */
  public function getAscsInstanceNumber()
  {
    return $this->ascsInstanceNumber;
  }
  /**
   * Optional. Resource URI of the recognized ASCS host of the application.
   *
   * @param string $ascsUri
   */
  public function setAscsUri($ascsUri)
  {
    $this->ascsUri = $ascsUri;
  }
  /**
   * @return string
   */
  public function getAscsUri()
  {
    return $this->ascsUri;
  }
  /**
   * Optional. Instance number of the ERS instance.
   *
   * @param string $ersInstanceNumber
   */
  public function setErsInstanceNumber($ersInstanceNumber)
  {
    $this->ersInstanceNumber = $ersInstanceNumber;
  }
  /**
   * @return string
   */
  public function getErsInstanceNumber()
  {
    return $this->ersInstanceNumber;
  }
  /**
   * Optional. Kernel version for Netweaver running in the system.
   *
   * @param string $kernelVersion
   */
  public function setKernelVersion($kernelVersion)
  {
    $this->kernelVersion = $kernelVersion;
  }
  /**
   * @return string
   */
  public function getKernelVersion()
  {
    return $this->kernelVersion;
  }
  /**
   * Optional. Resource URI of the recognized shared NFS of the application. May
   * be empty if the application server has only a single node.
   *
   * @param string $nfsUri
   */
  public function setNfsUri($nfsUri)
  {
    $this->nfsUri = $nfsUri;
  }
  /**
   * @return string
   */
  public function getNfsUri()
  {
    return $this->nfsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryComponentApplicationProperties::class, 'Google_Service_WorkloadManager_SapDiscoveryComponentApplicationProperties');
