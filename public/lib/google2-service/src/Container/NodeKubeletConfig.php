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

namespace Google\Service\Container;

class NodeKubeletConfig extends \Google\Collection
{
  protected $collection_key = 'allowedUnsafeSysctls';
  /**
   * Optional. Defines a comma-separated allowlist of unsafe sysctls or sysctl
   * patterns (ending in `*`). The unsafe namespaced sysctl groups are
   * `kernel.shm*`, `kernel.msg*`, `kernel.sem`, `fs.mqueue.*`, and `net.*`.
   * Leaving this allowlist empty means they cannot be set on Pods. To allow
   * certain sysctls or sysctl patterns to be set on Pods, list them separated
   * by commas. For example: `kernel.msg*,net.ipv4.route.min_pmtu`. See
   * https://kubernetes.io/docs/tasks/administer-cluster/sysctl-cluster/ for
   * more details.
   *
   * @var string[]
   */
  public $allowedUnsafeSysctls;
  /**
   * Optional. Defines the maximum number of container log files that can be
   * present for a container. See https://kubernetes.io/docs/concepts/cluster-
   * administration/logging/#log-rotation The value must be an integer between 2
   * and 10, inclusive. The default value is 5 if unspecified.
   *
   * @var int
   */
  public $containerLogMaxFiles;
  /**
   * Optional. Defines the maximum size of the container log file before it is
   * rotated. See https://kubernetes.io/docs/concepts/cluster-
   * administration/logging/#log-rotation Valid format is positive number +
   * unit, e.g. 100Ki, 10Mi. Valid units are Ki, Mi, Gi. The value must be
   * between 10Mi and 500Mi, inclusive. Note that the total container log size
   * (container_log_max_size * container_log_max_files) cannot exceed 1% of the
   * total storage of the node, to avoid disk pressure caused by log files. The
   * default value is 10Mi if unspecified.
   *
   * @var string
   */
  public $containerLogMaxSize;
  /**
   * Enable CPU CFS quota enforcement for containers that specify CPU limits.
   * This option is enabled by default which makes kubelet use CFS quota
   * (https://www.kernel.org/doc/Documentation/scheduler/sched-bwc.txt) to
   * enforce container CPU limits. Otherwise, CPU limits will not be enforced at
   * all. Disable this option to mitigate CPU throttling problems while still
   * having your pods to be in Guaranteed QoS class by specifying the CPU
   * limits. The default value is 'true' if unspecified.
   *
   * @var bool
   */
  public $cpuCfsQuota;
  /**
   * Set the CPU CFS quota period value 'cpu.cfs_period_us'. The string must be
   * a sequence of decimal numbers, each with optional fraction and a unit
   * suffix, such as "300ms". Valid time units are "ns", "us" (or "µs"), "ms",
   * "s", "m", "h". The value must be a positive duration between 1ms and 1
   * second, inclusive.
   *
   * @var string
   */
  public $cpuCfsQuotaPeriod;
  /**
   * Control the CPU management policy on the node. See
   * https://kubernetes.io/docs/tasks/administer-cluster/cpu-management-
   * policies/ The following values are allowed. * "none": the default, which
   * represents the existing scheduling behavior. * "static": allows pods with
   * certain resource characteristics to be granted increased CPU affinity and
   * exclusivity on the node. The default value is 'none' if unspecified.
   *
   * @var string
   */
  public $cpuManagerPolicy;
  /**
   * Optional. eviction_max_pod_grace_period_seconds is the maximum allowed
   * grace period (in seconds) to use when terminating pods in response to a
   * soft eviction threshold being met. This value effectively caps the Pod's
   * terminationGracePeriodSeconds value during soft evictions. Default: 0.
   * Range: [0, 300].
   *
   * @var int
   */
  public $evictionMaxPodGracePeriodSeconds;
  protected $evictionMinimumReclaimType = EvictionMinimumReclaim::class;
  protected $evictionMinimumReclaimDataType = '';
  protected $evictionSoftType = EvictionSignals::class;
  protected $evictionSoftDataType = '';
  protected $evictionSoftGracePeriodType = EvictionGracePeriod::class;
  protected $evictionSoftGracePeriodDataType = '';
  /**
   * Optional. Defines the percent of disk usage after which image garbage
   * collection is always run. The percent is calculated as this field value out
   * of 100. The value must be between 10 and 85, inclusive and greater than
   * image_gc_low_threshold_percent. The default value is 85 if unspecified.
   *
   * @var int
   */
  public $imageGcHighThresholdPercent;
  /**
   * Optional. Defines the percent of disk usage before which image garbage
   * collection is never run. Lowest disk usage to garbage collect to. The
   * percent is calculated as this field value out of 100. The value must be
   * between 10 and 85, inclusive and smaller than
   * image_gc_high_threshold_percent. The default value is 80 if unspecified.
   *
   * @var int
   */
  public $imageGcLowThresholdPercent;
  /**
   * Optional. Defines the maximum age an image can be unused before it is
   * garbage collected. The string must be a sequence of decimal numbers, each
   * with optional fraction and a unit suffix, such as "300s", "1.5h", and
   * "2h45m". Valid time units are "ns", "us" (or "µs"), "ms", "s", "m", "h".
   * The value must be a positive duration greater than image_minimum_gc_age or
   * "0s". The default value is "0s" if unspecified, which disables this field,
   * meaning images won't be garbage collected based on being unused for too
   * long.
   *
   * @var string
   */
  public $imageMaximumGcAge;
  /**
   * Optional. Defines the minimum age for an unused image before it is garbage
   * collected. The string must be a sequence of decimal numbers, each with
   * optional fraction and a unit suffix, such as "300s", "1.5h", and "2h45m".
   * Valid time units are "ns", "us" (or "µs"), "ms", "s", "m", "h". The value
   * must be a positive duration less than or equal to 2 minutes. The default
   * value is "2m0s" if unspecified.
   *
   * @var string
   */
  public $imageMinimumGcAge;
  /**
   * Enable or disable Kubelet read only port.
   *
   * @var bool
   */
  public $insecureKubeletReadonlyPortEnabled;
  /**
   * Optional. Defines the maximum number of image pulls in parallel. The range
   * is 2 to 5, inclusive. The default value is 2 or 3 depending on the disk
   * type. See https://kubernetes.io/docs/concepts/containers/images/#maximum-
   * parallel-image-pulls for more details.
   *
   * @var int
   */
  public $maxParallelImagePulls;
  protected $memoryManagerType = MemoryManager::class;
  protected $memoryManagerDataType = '';
  /**
   * Set the Pod PID limits. See https://kubernetes.io/docs/concepts/policy/pid-
   * limiting/#pod-pid-limits Controls the maximum number of processes allowed
   * to run in a pod. The value must be greater than or equal to 1024 and less
   * than 4194304.
   *
   * @var string
   */
  public $podPidsLimit;
  /**
   * Optional. Defines whether to enable single process OOM killer. If true,
   * will prevent the memory.oom.group flag from being set for container cgroups
   * in cgroups v2. This causes processes in the container to be OOM killed
   * individually instead of as a group.
   *
   * @var bool
   */
  public $singleProcessOomKill;
  protected $topologyManagerType = TopologyManager::class;
  protected $topologyManagerDataType = '';

  /**
   * Optional. Defines a comma-separated allowlist of unsafe sysctls or sysctl
   * patterns (ending in `*`). The unsafe namespaced sysctl groups are
   * `kernel.shm*`, `kernel.msg*`, `kernel.sem`, `fs.mqueue.*`, and `net.*`.
   * Leaving this allowlist empty means they cannot be set on Pods. To allow
   * certain sysctls or sysctl patterns to be set on Pods, list them separated
   * by commas. For example: `kernel.msg*,net.ipv4.route.min_pmtu`. See
   * https://kubernetes.io/docs/tasks/administer-cluster/sysctl-cluster/ for
   * more details.
   *
   * @param string[] $allowedUnsafeSysctls
   */
  public function setAllowedUnsafeSysctls($allowedUnsafeSysctls)
  {
    $this->allowedUnsafeSysctls = $allowedUnsafeSysctls;
  }
  /**
   * @return string[]
   */
  public function getAllowedUnsafeSysctls()
  {
    return $this->allowedUnsafeSysctls;
  }
  /**
   * Optional. Defines the maximum number of container log files that can be
   * present for a container. See https://kubernetes.io/docs/concepts/cluster-
   * administration/logging/#log-rotation The value must be an integer between 2
   * and 10, inclusive. The default value is 5 if unspecified.
   *
   * @param int $containerLogMaxFiles
   */
  public function setContainerLogMaxFiles($containerLogMaxFiles)
  {
    $this->containerLogMaxFiles = $containerLogMaxFiles;
  }
  /**
   * @return int
   */
  public function getContainerLogMaxFiles()
  {
    return $this->containerLogMaxFiles;
  }
  /**
   * Optional. Defines the maximum size of the container log file before it is
   * rotated. See https://kubernetes.io/docs/concepts/cluster-
   * administration/logging/#log-rotation Valid format is positive number +
   * unit, e.g. 100Ki, 10Mi. Valid units are Ki, Mi, Gi. The value must be
   * between 10Mi and 500Mi, inclusive. Note that the total container log size
   * (container_log_max_size * container_log_max_files) cannot exceed 1% of the
   * total storage of the node, to avoid disk pressure caused by log files. The
   * default value is 10Mi if unspecified.
   *
   * @param string $containerLogMaxSize
   */
  public function setContainerLogMaxSize($containerLogMaxSize)
  {
    $this->containerLogMaxSize = $containerLogMaxSize;
  }
  /**
   * @return string
   */
  public function getContainerLogMaxSize()
  {
    return $this->containerLogMaxSize;
  }
  /**
   * Enable CPU CFS quota enforcement for containers that specify CPU limits.
   * This option is enabled by default which makes kubelet use CFS quota
   * (https://www.kernel.org/doc/Documentation/scheduler/sched-bwc.txt) to
   * enforce container CPU limits. Otherwise, CPU limits will not be enforced at
   * all. Disable this option to mitigate CPU throttling problems while still
   * having your pods to be in Guaranteed QoS class by specifying the CPU
   * limits. The default value is 'true' if unspecified.
   *
   * @param bool $cpuCfsQuota
   */
  public function setCpuCfsQuota($cpuCfsQuota)
  {
    $this->cpuCfsQuota = $cpuCfsQuota;
  }
  /**
   * @return bool
   */
  public function getCpuCfsQuota()
  {
    return $this->cpuCfsQuota;
  }
  /**
   * Set the CPU CFS quota period value 'cpu.cfs_period_us'. The string must be
   * a sequence of decimal numbers, each with optional fraction and a unit
   * suffix, such as "300ms". Valid time units are "ns", "us" (or "µs"), "ms",
   * "s", "m", "h". The value must be a positive duration between 1ms and 1
   * second, inclusive.
   *
   * @param string $cpuCfsQuotaPeriod
   */
  public function setCpuCfsQuotaPeriod($cpuCfsQuotaPeriod)
  {
    $this->cpuCfsQuotaPeriod = $cpuCfsQuotaPeriod;
  }
  /**
   * @return string
   */
  public function getCpuCfsQuotaPeriod()
  {
    return $this->cpuCfsQuotaPeriod;
  }
  /**
   * Control the CPU management policy on the node. See
   * https://kubernetes.io/docs/tasks/administer-cluster/cpu-management-
   * policies/ The following values are allowed. * "none": the default, which
   * represents the existing scheduling behavior. * "static": allows pods with
   * certain resource characteristics to be granted increased CPU affinity and
   * exclusivity on the node. The default value is 'none' if unspecified.
   *
   * @param string $cpuManagerPolicy
   */
  public function setCpuManagerPolicy($cpuManagerPolicy)
  {
    $this->cpuManagerPolicy = $cpuManagerPolicy;
  }
  /**
   * @return string
   */
  public function getCpuManagerPolicy()
  {
    return $this->cpuManagerPolicy;
  }
  /**
   * Optional. eviction_max_pod_grace_period_seconds is the maximum allowed
   * grace period (in seconds) to use when terminating pods in response to a
   * soft eviction threshold being met. This value effectively caps the Pod's
   * terminationGracePeriodSeconds value during soft evictions. Default: 0.
   * Range: [0, 300].
   *
   * @param int $evictionMaxPodGracePeriodSeconds
   */
  public function setEvictionMaxPodGracePeriodSeconds($evictionMaxPodGracePeriodSeconds)
  {
    $this->evictionMaxPodGracePeriodSeconds = $evictionMaxPodGracePeriodSeconds;
  }
  /**
   * @return int
   */
  public function getEvictionMaxPodGracePeriodSeconds()
  {
    return $this->evictionMaxPodGracePeriodSeconds;
  }
  /**
   * Optional. eviction_minimum_reclaim is a map of signal names to quantities
   * that defines minimum reclaims, which describe the minimum amount of a given
   * resource the kubelet will reclaim when performing a pod eviction while that
   * resource is under pressure.
   *
   * @param EvictionMinimumReclaim $evictionMinimumReclaim
   */
  public function setEvictionMinimumReclaim(EvictionMinimumReclaim $evictionMinimumReclaim)
  {
    $this->evictionMinimumReclaim = $evictionMinimumReclaim;
  }
  /**
   * @return EvictionMinimumReclaim
   */
  public function getEvictionMinimumReclaim()
  {
    return $this->evictionMinimumReclaim;
  }
  /**
   * Optional. eviction_soft is a map of signal names to quantities that defines
   * soft eviction thresholds. Each signal is compared to its corresponding
   * threshold to determine if a pod eviction should occur.
   *
   * @param EvictionSignals $evictionSoft
   */
  public function setEvictionSoft(EvictionSignals $evictionSoft)
  {
    $this->evictionSoft = $evictionSoft;
  }
  /**
   * @return EvictionSignals
   */
  public function getEvictionSoft()
  {
    return $this->evictionSoft;
  }
  /**
   * Optional. eviction_soft_grace_period is a map of signal names to quantities
   * that defines grace periods for each soft eviction signal. The grace period
   * is the amount of time that a pod must be under pressure before an eviction
   * occurs.
   *
   * @param EvictionGracePeriod $evictionSoftGracePeriod
   */
  public function setEvictionSoftGracePeriod(EvictionGracePeriod $evictionSoftGracePeriod)
  {
    $this->evictionSoftGracePeriod = $evictionSoftGracePeriod;
  }
  /**
   * @return EvictionGracePeriod
   */
  public function getEvictionSoftGracePeriod()
  {
    return $this->evictionSoftGracePeriod;
  }
  /**
   * Optional. Defines the percent of disk usage after which image garbage
   * collection is always run. The percent is calculated as this field value out
   * of 100. The value must be between 10 and 85, inclusive and greater than
   * image_gc_low_threshold_percent. The default value is 85 if unspecified.
   *
   * @param int $imageGcHighThresholdPercent
   */
  public function setImageGcHighThresholdPercent($imageGcHighThresholdPercent)
  {
    $this->imageGcHighThresholdPercent = $imageGcHighThresholdPercent;
  }
  /**
   * @return int
   */
  public function getImageGcHighThresholdPercent()
  {
    return $this->imageGcHighThresholdPercent;
  }
  /**
   * Optional. Defines the percent of disk usage before which image garbage
   * collection is never run. Lowest disk usage to garbage collect to. The
   * percent is calculated as this field value out of 100. The value must be
   * between 10 and 85, inclusive and smaller than
   * image_gc_high_threshold_percent. The default value is 80 if unspecified.
   *
   * @param int $imageGcLowThresholdPercent
   */
  public function setImageGcLowThresholdPercent($imageGcLowThresholdPercent)
  {
    $this->imageGcLowThresholdPercent = $imageGcLowThresholdPercent;
  }
  /**
   * @return int
   */
  public function getImageGcLowThresholdPercent()
  {
    return $this->imageGcLowThresholdPercent;
  }
  /**
   * Optional. Defines the maximum age an image can be unused before it is
   * garbage collected. The string must be a sequence of decimal numbers, each
   * with optional fraction and a unit suffix, such as "300s", "1.5h", and
   * "2h45m". Valid time units are "ns", "us" (or "µs"), "ms", "s", "m", "h".
   * The value must be a positive duration greater than image_minimum_gc_age or
   * "0s". The default value is "0s" if unspecified, which disables this field,
   * meaning images won't be garbage collected based on being unused for too
   * long.
   *
   * @param string $imageMaximumGcAge
   */
  public function setImageMaximumGcAge($imageMaximumGcAge)
  {
    $this->imageMaximumGcAge = $imageMaximumGcAge;
  }
  /**
   * @return string
   */
  public function getImageMaximumGcAge()
  {
    return $this->imageMaximumGcAge;
  }
  /**
   * Optional. Defines the minimum age for an unused image before it is garbage
   * collected. The string must be a sequence of decimal numbers, each with
   * optional fraction and a unit suffix, such as "300s", "1.5h", and "2h45m".
   * Valid time units are "ns", "us" (or "µs"), "ms", "s", "m", "h". The value
   * must be a positive duration less than or equal to 2 minutes. The default
   * value is "2m0s" if unspecified.
   *
   * @param string $imageMinimumGcAge
   */
  public function setImageMinimumGcAge($imageMinimumGcAge)
  {
    $this->imageMinimumGcAge = $imageMinimumGcAge;
  }
  /**
   * @return string
   */
  public function getImageMinimumGcAge()
  {
    return $this->imageMinimumGcAge;
  }
  /**
   * Enable or disable Kubelet read only port.
   *
   * @param bool $insecureKubeletReadonlyPortEnabled
   */
  public function setInsecureKubeletReadonlyPortEnabled($insecureKubeletReadonlyPortEnabled)
  {
    $this->insecureKubeletReadonlyPortEnabled = $insecureKubeletReadonlyPortEnabled;
  }
  /**
   * @return bool
   */
  public function getInsecureKubeletReadonlyPortEnabled()
  {
    return $this->insecureKubeletReadonlyPortEnabled;
  }
  /**
   * Optional. Defines the maximum number of image pulls in parallel. The range
   * is 2 to 5, inclusive. The default value is 2 or 3 depending on the disk
   * type. See https://kubernetes.io/docs/concepts/containers/images/#maximum-
   * parallel-image-pulls for more details.
   *
   * @param int $maxParallelImagePulls
   */
  public function setMaxParallelImagePulls($maxParallelImagePulls)
  {
    $this->maxParallelImagePulls = $maxParallelImagePulls;
  }
  /**
   * @return int
   */
  public function getMaxParallelImagePulls()
  {
    return $this->maxParallelImagePulls;
  }
  /**
   * Optional. Controls NUMA-aware Memory Manager configuration on the node. For
   * more information, see: https://kubernetes.io/docs/tasks/administer-
   * cluster/memory-manager/
   *
   * @param MemoryManager $memoryManager
   */
  public function setMemoryManager(MemoryManager $memoryManager)
  {
    $this->memoryManager = $memoryManager;
  }
  /**
   * @return MemoryManager
   */
  public function getMemoryManager()
  {
    return $this->memoryManager;
  }
  /**
   * Set the Pod PID limits. See https://kubernetes.io/docs/concepts/policy/pid-
   * limiting/#pod-pid-limits Controls the maximum number of processes allowed
   * to run in a pod. The value must be greater than or equal to 1024 and less
   * than 4194304.
   *
   * @param string $podPidsLimit
   */
  public function setPodPidsLimit($podPidsLimit)
  {
    $this->podPidsLimit = $podPidsLimit;
  }
  /**
   * @return string
   */
  public function getPodPidsLimit()
  {
    return $this->podPidsLimit;
  }
  /**
   * Optional. Defines whether to enable single process OOM killer. If true,
   * will prevent the memory.oom.group flag from being set for container cgroups
   * in cgroups v2. This causes processes in the container to be OOM killed
   * individually instead of as a group.
   *
   * @param bool $singleProcessOomKill
   */
  public function setSingleProcessOomKill($singleProcessOomKill)
  {
    $this->singleProcessOomKill = $singleProcessOomKill;
  }
  /**
   * @return bool
   */
  public function getSingleProcessOomKill()
  {
    return $this->singleProcessOomKill;
  }
  /**
   * Optional. Controls Topology Manager configuration on the node. For more
   * information, see: https://kubernetes.io/docs/tasks/administer-
   * cluster/topology-manager/
   *
   * @param TopologyManager $topologyManager
   */
  public function setTopologyManager(TopologyManager $topologyManager)
  {
    $this->topologyManager = $topologyManager;
  }
  /**
   * @return TopologyManager
   */
  public function getTopologyManager()
  {
    return $this->topologyManager;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeKubeletConfig::class, 'Google_Service_Container_NodeKubeletConfig');
