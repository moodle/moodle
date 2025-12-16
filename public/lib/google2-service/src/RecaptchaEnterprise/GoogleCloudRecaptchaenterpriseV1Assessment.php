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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1Assessment extends \Google\Model
{
  protected $accountDefenderAssessmentType = GoogleCloudRecaptchaenterpriseV1AccountDefenderAssessment::class;
  protected $accountDefenderAssessmentDataType = '';
  protected $accountVerificationType = GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo::class;
  protected $accountVerificationDataType = '';
  protected $assessmentEnvironmentType = GoogleCloudRecaptchaenterpriseV1AssessmentEnvironment::class;
  protected $assessmentEnvironmentDataType = '';
  protected $eventType = GoogleCloudRecaptchaenterpriseV1Event::class;
  protected $eventDataType = '';
  protected $firewallPolicyAssessmentType = GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment::class;
  protected $firewallPolicyAssessmentDataType = '';
  protected $fraudPreventionAssessmentType = GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessment::class;
  protected $fraudPreventionAssessmentDataType = '';
  protected $fraudSignalsType = GoogleCloudRecaptchaenterpriseV1FraudSignals::class;
  protected $fraudSignalsDataType = '';
  /**
   * Output only. Identifier. The resource name for the Assessment in the format
   * `projects/{project}/assessments/{assessment}`.
   *
   * @var string
   */
  public $name;
  protected $phoneFraudAssessmentType = GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment::class;
  protected $phoneFraudAssessmentDataType = '';
  protected $privatePasswordLeakVerificationType = GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification::class;
  protected $privatePasswordLeakVerificationDataType = '';
  protected $riskAnalysisType = GoogleCloudRecaptchaenterpriseV1RiskAnalysis::class;
  protected $riskAnalysisDataType = '';
  protected $tokenPropertiesType = GoogleCloudRecaptchaenterpriseV1TokenProperties::class;
  protected $tokenPropertiesDataType = '';

  /**
   * Output only. Assessment returned by account defender when an account
   * identifier is provided.
   *
   * @param GoogleCloudRecaptchaenterpriseV1AccountDefenderAssessment $accountDefenderAssessment
   */
  public function setAccountDefenderAssessment(GoogleCloudRecaptchaenterpriseV1AccountDefenderAssessment $accountDefenderAssessment)
  {
    $this->accountDefenderAssessment = $accountDefenderAssessment;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1AccountDefenderAssessment
   */
  public function getAccountDefenderAssessment()
  {
    return $this->accountDefenderAssessment;
  }
  /**
   * Optional. Account verification information for identity verification. The
   * assessment event must include a token and site key to use this feature.
   *
   * @param GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo $accountVerification
   */
  public function setAccountVerification(GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo $accountVerification)
  {
    $this->accountVerification = $accountVerification;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo
   */
  public function getAccountVerification()
  {
    return $this->accountVerification;
  }
  /**
   * Optional. The environment creating the assessment. This describes your
   * environment (the system invoking CreateAssessment), NOT the environment of
   * your user.
   *
   * @param GoogleCloudRecaptchaenterpriseV1AssessmentEnvironment $assessmentEnvironment
   */
  public function setAssessmentEnvironment(GoogleCloudRecaptchaenterpriseV1AssessmentEnvironment $assessmentEnvironment)
  {
    $this->assessmentEnvironment = $assessmentEnvironment;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1AssessmentEnvironment
   */
  public function getAssessmentEnvironment()
  {
    return $this->assessmentEnvironment;
  }
  /**
   * Optional. The event being assessed.
   *
   * @param GoogleCloudRecaptchaenterpriseV1Event $event
   */
  public function setEvent(GoogleCloudRecaptchaenterpriseV1Event $event)
  {
    $this->event = $event;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1Event
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * Output only. Assessment returned when firewall policies belonging to the
   * project are evaluated using the field firewall_policy_evaluation.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment $firewallPolicyAssessment
   */
  public function setFirewallPolicyAssessment(GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment $firewallPolicyAssessment)
  {
    $this->firewallPolicyAssessment = $firewallPolicyAssessment;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallPolicyAssessment
   */
  public function getFirewallPolicyAssessment()
  {
    return $this->firewallPolicyAssessment;
  }
  /**
   * Output only. Assessment returned by Fraud Prevention when TransactionData
   * is provided.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessment $fraudPreventionAssessment
   */
  public function setFraudPreventionAssessment(GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessment $fraudPreventionAssessment)
  {
    $this->fraudPreventionAssessment = $fraudPreventionAssessment;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessment
   */
  public function getFraudPreventionAssessment()
  {
    return $this->fraudPreventionAssessment;
  }
  /**
   * Output only. Fraud Signals specific to the users involved in a payment
   * transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FraudSignals $fraudSignals
   */
  public function setFraudSignals(GoogleCloudRecaptchaenterpriseV1FraudSignals $fraudSignals)
  {
    $this->fraudSignals = $fraudSignals;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FraudSignals
   */
  public function getFraudSignals()
  {
    return $this->fraudSignals;
  }
  /**
   * Output only. Identifier. The resource name for the Assessment in the format
   * `projects/{project}/assessments/{assessment}`.
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
   * Output only. Assessment returned when a site key, a token, and a phone
   * number as `user_id` are provided. Account defender and SMS toll fraud
   * protection need to be enabled.
   *
   * @param GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment $phoneFraudAssessment
   */
  public function setPhoneFraudAssessment(GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment $phoneFraudAssessment)
  {
    $this->phoneFraudAssessment = $phoneFraudAssessment;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment
   */
  public function getPhoneFraudAssessment()
  {
    return $this->phoneFraudAssessment;
  }
  /**
   * Optional. The private password leak verification field contains the
   * parameters that are used to to check for leaks privately without sharing
   * user credentials.
   *
   * @param GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification $privatePasswordLeakVerification
   */
  public function setPrivatePasswordLeakVerification(GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification $privatePasswordLeakVerification)
  {
    $this->privatePasswordLeakVerification = $privatePasswordLeakVerification;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification
   */
  public function getPrivatePasswordLeakVerification()
  {
    return $this->privatePasswordLeakVerification;
  }
  /**
   * Output only. The risk analysis result for the event being assessed.
   *
   * @param GoogleCloudRecaptchaenterpriseV1RiskAnalysis $riskAnalysis
   */
  public function setRiskAnalysis(GoogleCloudRecaptchaenterpriseV1RiskAnalysis $riskAnalysis)
  {
    $this->riskAnalysis = $riskAnalysis;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1RiskAnalysis
   */
  public function getRiskAnalysis()
  {
    return $this->riskAnalysis;
  }
  /**
   * Output only. Properties of the provided event token.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TokenProperties $tokenProperties
   */
  public function setTokenProperties(GoogleCloudRecaptchaenterpriseV1TokenProperties $tokenProperties)
  {
    $this->tokenProperties = $tokenProperties;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TokenProperties
   */
  public function getTokenProperties()
  {
    return $this->tokenProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1Assessment::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1Assessment');
