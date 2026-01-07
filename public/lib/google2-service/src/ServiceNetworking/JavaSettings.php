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

namespace Google\Service\ServiceNetworking;

class JavaSettings extends \Google\Model
{
  protected $commonType = CommonLanguageSettings::class;
  protected $commonDataType = '';
  /**
   * The package name to use in Java. Clobbers the java_package option set in
   * the protobuf. This should be used **only** by APIs who have already set the
   * language_settings.java.package_name" field in gapic.yaml. API teams should
   * use the protobuf java_package option where possible. Example of a YAML
   * configuration:: publishing: library_settings: java_settings:
   * library_package: com.google.cloud.pubsub.v1
   *
   * @var string
   */
  public $libraryPackage;
  /**
   * Configure the Java class name to use instead of the service's for its
   * corresponding generated GAPIC client. Keys are fully-qualified service
   * names as they appear in the protobuf (including the full the
   * language_settings.java.interface_names" field in gapic.yaml. API teams
   * should otherwise use the service name as it appears in the protobuf.
   * Example of a YAML configuration:: publishing: java_settings:
   * service_class_names: - google.pubsub.v1.Publisher: TopicAdmin -
   * google.pubsub.v1.Subscriber: SubscriptionAdmin
   *
   * @var string[]
   */
  public $serviceClassNames;

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
   * The package name to use in Java. Clobbers the java_package option set in
   * the protobuf. This should be used **only** by APIs who have already set the
   * language_settings.java.package_name" field in gapic.yaml. API teams should
   * use the protobuf java_package option where possible. Example of a YAML
   * configuration:: publishing: library_settings: java_settings:
   * library_package: com.google.cloud.pubsub.v1
   *
   * @param string $libraryPackage
   */
  public function setLibraryPackage($libraryPackage)
  {
    $this->libraryPackage = $libraryPackage;
  }
  /**
   * @return string
   */
  public function getLibraryPackage()
  {
    return $this->libraryPackage;
  }
  /**
   * Configure the Java class name to use instead of the service's for its
   * corresponding generated GAPIC client. Keys are fully-qualified service
   * names as they appear in the protobuf (including the full the
   * language_settings.java.interface_names" field in gapic.yaml. API teams
   * should otherwise use the service name as it appears in the protobuf.
   * Example of a YAML configuration:: publishing: java_settings:
   * service_class_names: - google.pubsub.v1.Publisher: TopicAdmin -
   * google.pubsub.v1.Subscriber: SubscriptionAdmin
   *
   * @param string[] $serviceClassNames
   */
  public function setServiceClassNames($serviceClassNames)
  {
    $this->serviceClassNames = $serviceClassNames;
  }
  /**
   * @return string[]
   */
  public function getServiceClassNames()
  {
    return $this->serviceClassNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JavaSettings::class, 'Google_Service_ServiceNetworking_JavaSettings');
