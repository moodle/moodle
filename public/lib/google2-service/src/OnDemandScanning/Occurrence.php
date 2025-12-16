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

namespace Google\Service\OnDemandScanning;

class Occurrence extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const KIND_NOTE_KIND_UNSPECIFIED = 'NOTE_KIND_UNSPECIFIED';
  /**
   * The note and occurrence represent a package vulnerability.
   */
  public const KIND_VULNERABILITY = 'VULNERABILITY';
  /**
   * The note and occurrence assert build provenance.
   */
  public const KIND_BUILD = 'BUILD';
  /**
   * This represents an image basis relationship.
   */
  public const KIND_IMAGE = 'IMAGE';
  /**
   * This represents a package installed via a package manager.
   */
  public const KIND_PACKAGE = 'PACKAGE';
  /**
   * The note and occurrence track deployment events.
   */
  public const KIND_DEPLOYMENT = 'DEPLOYMENT';
  /**
   * The note and occurrence track the initial discovery status of a resource.
   */
  public const KIND_DISCOVERY = 'DISCOVERY';
  /**
   * This represents a logical "role" that can attest to artifacts.
   */
  public const KIND_ATTESTATION = 'ATTESTATION';
  /**
   * This represents an available package upgrade.
   */
  public const KIND_UPGRADE = 'UPGRADE';
  /**
   * This represents a Compliance Note
   */
  public const KIND_COMPLIANCE = 'COMPLIANCE';
  /**
   * This represents a DSSE attestation Note
   */
  public const KIND_DSSE_ATTESTATION = 'DSSE_ATTESTATION';
  /**
   * This represents a Vulnerability Assessment.
   */
  public const KIND_VULNERABILITY_ASSESSMENT = 'VULNERABILITY_ASSESSMENT';
  /**
   * This represents an SBOM Reference.
   */
  public const KIND_SBOM_REFERENCE = 'SBOM_REFERENCE';
  /**
   * This represents a secret.
   */
  public const KIND_SECRET = 'SECRET';
  protected $attestationType = AttestationOccurrence::class;
  protected $attestationDataType = '';
  protected $buildType = BuildOccurrence::class;
  protected $buildDataType = '';
  protected $complianceType = ComplianceOccurrence::class;
  protected $complianceDataType = '';
  /**
   * Output only. The time this occurrence was created.
   *
   * @var string
   */
  public $createTime;
  protected $deploymentType = DeploymentOccurrence::class;
  protected $deploymentDataType = '';
  protected $discoveryType = DiscoveryOccurrence::class;
  protected $discoveryDataType = '';
  protected $dsseAttestationType = DSSEAttestationOccurrence::class;
  protected $dsseAttestationDataType = '';
  protected $envelopeType = Envelope::class;
  protected $envelopeDataType = '';
  protected $imageType = ImageOccurrence::class;
  protected $imageDataType = '';
  /**
   * Output only. This explicitly denotes which of the occurrence details are
   * specified. This field can be used as a filter in list requests.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. The name of the occurrence in the form of
   * `projects/[PROJECT_ID]/occurrences/[OCCURRENCE_ID]`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The analysis note associated with this occurrence, in
   * the form of `projects/[PROVIDER_ID]/notes/[NOTE_ID]`. This field can be
   * used as a filter in list requests.
   *
   * @var string
   */
  public $noteName;
  protected $packageType = PackageOccurrence::class;
  protected $packageDataType = '';
  /**
   * A description of actions that can be taken to remedy the note.
   *
   * @var string
   */
  public $remediation;
  /**
   * Required. Immutable. A URI that represents the resource for which the
   * occurrence applies. For example,
   * `https://gcr.io/project/image@sha256:123abc` for a Docker image.
   *
   * @var string
   */
  public $resourceUri;
  protected $sbomReferenceType = SBOMReferenceOccurrence::class;
  protected $sbomReferenceDataType = '';
  protected $secretType = SecretOccurrence::class;
  protected $secretDataType = '';
  /**
   * Output only. The time this occurrence was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeType = UpgradeOccurrence::class;
  protected $upgradeDataType = '';
  protected $vulnerabilityType = VulnerabilityOccurrence::class;
  protected $vulnerabilityDataType = '';

  /**
   * Describes an attestation of an artifact.
   *
   * @param AttestationOccurrence $attestation
   */
  public function setAttestation(AttestationOccurrence $attestation)
  {
    $this->attestation = $attestation;
  }
  /**
   * @return AttestationOccurrence
   */
  public function getAttestation()
  {
    return $this->attestation;
  }
  /**
   * Describes a verifiable build.
   *
   * @param BuildOccurrence $build
   */
  public function setBuild(BuildOccurrence $build)
  {
    $this->build = $build;
  }
  /**
   * @return BuildOccurrence
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Describes a compliance violation on a linked resource.
   *
   * @param ComplianceOccurrence $compliance
   */
  public function setCompliance(ComplianceOccurrence $compliance)
  {
    $this->compliance = $compliance;
  }
  /**
   * @return ComplianceOccurrence
   */
  public function getCompliance()
  {
    return $this->compliance;
  }
  /**
   * Output only. The time this occurrence was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Describes the deployment of an artifact on a runtime.
   *
   * @param DeploymentOccurrence $deployment
   */
  public function setDeployment(DeploymentOccurrence $deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return DeploymentOccurrence
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Describes when a resource was discovered.
   *
   * @param DiscoveryOccurrence $discovery
   */
  public function setDiscovery(DiscoveryOccurrence $discovery)
  {
    $this->discovery = $discovery;
  }
  /**
   * @return DiscoveryOccurrence
   */
  public function getDiscovery()
  {
    return $this->discovery;
  }
  /**
   * Describes an attestation of an artifact using dsse.
   *
   * @param DSSEAttestationOccurrence $dsseAttestation
   */
  public function setDsseAttestation(DSSEAttestationOccurrence $dsseAttestation)
  {
    $this->dsseAttestation = $dsseAttestation;
  }
  /**
   * @return DSSEAttestationOccurrence
   */
  public function getDsseAttestation()
  {
    return $this->dsseAttestation;
  }
  /**
   * https://github.com/secure-systems-lab/dsse
   *
   * @param Envelope $envelope
   */
  public function setEnvelope(Envelope $envelope)
  {
    $this->envelope = $envelope;
  }
  /**
   * @return Envelope
   */
  public function getEnvelope()
  {
    return $this->envelope;
  }
  /**
   * Describes how this resource derives from the basis in the associated note.
   *
   * @param ImageOccurrence $image
   */
  public function setImage(ImageOccurrence $image)
  {
    $this->image = $image;
  }
  /**
   * @return ImageOccurrence
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Output only. This explicitly denotes which of the occurrence details are
   * specified. This field can be used as a filter in list requests.
   *
   * Accepted values: NOTE_KIND_UNSPECIFIED, VULNERABILITY, BUILD, IMAGE,
   * PACKAGE, DEPLOYMENT, DISCOVERY, ATTESTATION, UPGRADE, COMPLIANCE,
   * DSSE_ATTESTATION, VULNERABILITY_ASSESSMENT, SBOM_REFERENCE, SECRET
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. The name of the occurrence in the form of
   * `projects/[PROJECT_ID]/occurrences/[OCCURRENCE_ID]`.
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
   * Required. Immutable. The analysis note associated with this occurrence, in
   * the form of `projects/[PROVIDER_ID]/notes/[NOTE_ID]`. This field can be
   * used as a filter in list requests.
   *
   * @param string $noteName
   */
  public function setNoteName($noteName)
  {
    $this->noteName = $noteName;
  }
  /**
   * @return string
   */
  public function getNoteName()
  {
    return $this->noteName;
  }
  /**
   * Describes the installation of a package on the linked resource.
   *
   * @param PackageOccurrence $package
   */
  public function setPackage(PackageOccurrence $package)
  {
    $this->package = $package;
  }
  /**
   * @return PackageOccurrence
   */
  public function getPackage()
  {
    return $this->package;
  }
  /**
   * A description of actions that can be taken to remedy the note.
   *
   * @param string $remediation
   */
  public function setRemediation($remediation)
  {
    $this->remediation = $remediation;
  }
  /**
   * @return string
   */
  public function getRemediation()
  {
    return $this->remediation;
  }
  /**
   * Required. Immutable. A URI that represents the resource for which the
   * occurrence applies. For example,
   * `https://gcr.io/project/image@sha256:123abc` for a Docker image.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * Describes a specific SBOM reference occurrences.
   *
   * @param SBOMReferenceOccurrence $sbomReference
   */
  public function setSbomReference(SBOMReferenceOccurrence $sbomReference)
  {
    $this->sbomReference = $sbomReference;
  }
  /**
   * @return SBOMReferenceOccurrence
   */
  public function getSbomReference()
  {
    return $this->sbomReference;
  }
  /**
   * Describes a secret.
   *
   * @param SecretOccurrence $secret
   */
  public function setSecret(SecretOccurrence $secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return SecretOccurrence
   */
  public function getSecret()
  {
    return $this->secret;
  }
  /**
   * Output only. The time this occurrence was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Describes an available package upgrade on the linked resource.
   *
   * @param UpgradeOccurrence $upgrade
   */
  public function setUpgrade(UpgradeOccurrence $upgrade)
  {
    $this->upgrade = $upgrade;
  }
  /**
   * @return UpgradeOccurrence
   */
  public function getUpgrade()
  {
    return $this->upgrade;
  }
  /**
   * Describes a security vulnerability.
   *
   * @param VulnerabilityOccurrence $vulnerability
   */
  public function setVulnerability(VulnerabilityOccurrence $vulnerability)
  {
    $this->vulnerability = $vulnerability;
  }
  /**
   * @return VulnerabilityOccurrence
   */
  public function getVulnerability()
  {
    return $this->vulnerability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Occurrence::class, 'Google_Service_OnDemandScanning_Occurrence');
