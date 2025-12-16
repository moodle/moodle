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

namespace Google\Service\ContainerAnalysis;

class Note extends \Google\Collection
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
  protected $collection_key = 'relatedUrl';
  protected $attestationType = AttestationNote::class;
  protected $attestationDataType = '';
  protected $buildType = BuildNote::class;
  protected $buildDataType = '';
  protected $complianceType = ComplianceNote::class;
  protected $complianceDataType = '';
  /**
   * Output only. The time this note was created. This field can be used as a
   * filter in list requests.
   *
   * @var string
   */
  public $createTime;
  protected $deploymentType = DeploymentNote::class;
  protected $deploymentDataType = '';
  protected $discoveryType = DiscoveryNote::class;
  protected $discoveryDataType = '';
  protected $dsseAttestationType = DSSEAttestationNote::class;
  protected $dsseAttestationDataType = '';
  /**
   * Time of expiration for this note. Empty if note does not expire.
   *
   * @var string
   */
  public $expirationTime;
  protected $imageType = ImageNote::class;
  protected $imageDataType = '';
  /**
   * Output only. The type of analysis. This field can be used as a filter in
   * list requests.
   *
   * @var string
   */
  public $kind;
  /**
   * A detailed description of this note.
   *
   * @var string
   */
  public $longDescription;
  /**
   * Output only. The name of the note in the form of
   * `projects/[PROVIDER_ID]/notes/[NOTE_ID]`.
   *
   * @var string
   */
  public $name;
  protected $packageType = PackageNote::class;
  protected $packageDataType = '';
  /**
   * Other notes related to this note.
   *
   * @var string[]
   */
  public $relatedNoteNames;
  protected $relatedUrlType = RelatedUrl::class;
  protected $relatedUrlDataType = 'array';
  protected $sbomReferenceType = SBOMReferenceNote::class;
  protected $sbomReferenceDataType = '';
  protected $secretType = SecretNote::class;
  protected $secretDataType = '';
  /**
   * A one sentence description of this note.
   *
   * @var string
   */
  public $shortDescription;
  /**
   * Output only. The time this note was last updated. This field can be used as
   * a filter in list requests.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeType = UpgradeNote::class;
  protected $upgradeDataType = '';
  protected $vulnerabilityType = VulnerabilityNote::class;
  protected $vulnerabilityDataType = '';
  protected $vulnerabilityAssessmentType = VulnerabilityAssessmentNote::class;
  protected $vulnerabilityAssessmentDataType = '';

  /**
   * A note describing an attestation role.
   *
   * @param AttestationNote $attestation
   */
  public function setAttestation(AttestationNote $attestation)
  {
    $this->attestation = $attestation;
  }
  /**
   * @return AttestationNote
   */
  public function getAttestation()
  {
    return $this->attestation;
  }
  /**
   * A note describing build provenance for a verifiable build.
   *
   * @param BuildNote $build
   */
  public function setBuild(BuildNote $build)
  {
    $this->build = $build;
  }
  /**
   * @return BuildNote
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * A note describing a compliance check.
   *
   * @param ComplianceNote $compliance
   */
  public function setCompliance(ComplianceNote $compliance)
  {
    $this->compliance = $compliance;
  }
  /**
   * @return ComplianceNote
   */
  public function getCompliance()
  {
    return $this->compliance;
  }
  /**
   * Output only. The time this note was created. This field can be used as a
   * filter in list requests.
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
   * A note describing something that can be deployed.
   *
   * @param DeploymentNote $deployment
   */
  public function setDeployment(DeploymentNote $deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return DeploymentNote
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * A note describing the initial analysis of a resource.
   *
   * @param DiscoveryNote $discovery
   */
  public function setDiscovery(DiscoveryNote $discovery)
  {
    $this->discovery = $discovery;
  }
  /**
   * @return DiscoveryNote
   */
  public function getDiscovery()
  {
    return $this->discovery;
  }
  /**
   * A note describing a dsse attestation note.
   *
   * @param DSSEAttestationNote $dsseAttestation
   */
  public function setDsseAttestation(DSSEAttestationNote $dsseAttestation)
  {
    $this->dsseAttestation = $dsseAttestation;
  }
  /**
   * @return DSSEAttestationNote
   */
  public function getDsseAttestation()
  {
    return $this->dsseAttestation;
  }
  /**
   * Time of expiration for this note. Empty if note does not expire.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * A note describing a base image.
   *
   * @param ImageNote $image
   */
  public function setImage(ImageNote $image)
  {
    $this->image = $image;
  }
  /**
   * @return ImageNote
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Output only. The type of analysis. This field can be used as a filter in
   * list requests.
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
   * A detailed description of this note.
   *
   * @param string $longDescription
   */
  public function setLongDescription($longDescription)
  {
    $this->longDescription = $longDescription;
  }
  /**
   * @return string
   */
  public function getLongDescription()
  {
    return $this->longDescription;
  }
  /**
   * Output only. The name of the note in the form of
   * `projects/[PROVIDER_ID]/notes/[NOTE_ID]`.
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
   * A note describing a package hosted by various package managers.
   *
   * @param PackageNote $package
   */
  public function setPackage(PackageNote $package)
  {
    $this->package = $package;
  }
  /**
   * @return PackageNote
   */
  public function getPackage()
  {
    return $this->package;
  }
  /**
   * Other notes related to this note.
   *
   * @param string[] $relatedNoteNames
   */
  public function setRelatedNoteNames($relatedNoteNames)
  {
    $this->relatedNoteNames = $relatedNoteNames;
  }
  /**
   * @return string[]
   */
  public function getRelatedNoteNames()
  {
    return $this->relatedNoteNames;
  }
  /**
   * URLs associated with this note.
   *
   * @param RelatedUrl[] $relatedUrl
   */
  public function setRelatedUrl($relatedUrl)
  {
    $this->relatedUrl = $relatedUrl;
  }
  /**
   * @return RelatedUrl[]
   */
  public function getRelatedUrl()
  {
    return $this->relatedUrl;
  }
  /**
   * A note describing an SBOM reference.
   *
   * @param SBOMReferenceNote $sbomReference
   */
  public function setSbomReference(SBOMReferenceNote $sbomReference)
  {
    $this->sbomReference = $sbomReference;
  }
  /**
   * @return SBOMReferenceNote
   */
  public function getSbomReference()
  {
    return $this->sbomReference;
  }
  /**
   * A note describing a secret.
   *
   * @param SecretNote $secret
   */
  public function setSecret(SecretNote $secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return SecretNote
   */
  public function getSecret()
  {
    return $this->secret;
  }
  /**
   * A one sentence description of this note.
   *
   * @param string $shortDescription
   */
  public function setShortDescription($shortDescription)
  {
    $this->shortDescription = $shortDescription;
  }
  /**
   * @return string
   */
  public function getShortDescription()
  {
    return $this->shortDescription;
  }
  /**
   * Output only. The time this note was last updated. This field can be used as
   * a filter in list requests.
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
   * A note describing available package upgrades.
   *
   * @param UpgradeNote $upgrade
   */
  public function setUpgrade(UpgradeNote $upgrade)
  {
    $this->upgrade = $upgrade;
  }
  /**
   * @return UpgradeNote
   */
  public function getUpgrade()
  {
    return $this->upgrade;
  }
  /**
   * A note describing a package vulnerability.
   *
   * @param VulnerabilityNote $vulnerability
   */
  public function setVulnerability(VulnerabilityNote $vulnerability)
  {
    $this->vulnerability = $vulnerability;
  }
  /**
   * @return VulnerabilityNote
   */
  public function getVulnerability()
  {
    return $this->vulnerability;
  }
  /**
   * A note describing a vulnerability assessment.
   *
   * @param VulnerabilityAssessmentNote $vulnerabilityAssessment
   */
  public function setVulnerabilityAssessment(VulnerabilityAssessmentNote $vulnerabilityAssessment)
  {
    $this->vulnerabilityAssessment = $vulnerabilityAssessment;
  }
  /**
   * @return VulnerabilityAssessmentNote
   */
  public function getVulnerabilityAssessment()
  {
    return $this->vulnerabilityAssessment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Note::class, 'Google_Service_ContainerAnalysis_Note');
