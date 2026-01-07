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

class LinuxNodeConfig extends \Google\Model
{
  /**
   * CGROUP_MODE_UNSPECIFIED is when unspecified cgroup configuration is used.
   * The default for the GKE node OS image will be used.
   */
  public const CGROUP_MODE_CGROUP_MODE_UNSPECIFIED = 'CGROUP_MODE_UNSPECIFIED';
  /**
   * CGROUP_MODE_V1 specifies to use cgroupv1 for the cgroup configuration on
   * the node image.
   */
  public const CGROUP_MODE_CGROUP_MODE_V1 = 'CGROUP_MODE_V1';
  /**
   * CGROUP_MODE_V2 specifies to use cgroupv2 for the cgroup configuration on
   * the node image.
   */
  public const CGROUP_MODE_CGROUP_MODE_V2 = 'CGROUP_MODE_V2';
  /**
   * Default value. GKE will not modify the kernel configuration.
   */
  public const TRANSPARENT_HUGEPAGE_DEFRAG_TRANSPARENT_HUGEPAGE_DEFRAG_UNSPECIFIED = 'TRANSPARENT_HUGEPAGE_DEFRAG_UNSPECIFIED';
  /**
   * It means that an application requesting THP will stall on allocation
   * failure and directly reclaim pages and compact memory in an effort to
   * allocate a THP immediately.
   */
  public const TRANSPARENT_HUGEPAGE_DEFRAG_TRANSPARENT_HUGEPAGE_DEFRAG_ALWAYS = 'TRANSPARENT_HUGEPAGE_DEFRAG_ALWAYS';
  /**
   * It means that an application will wake kswapd in the background to reclaim
   * pages and wake kcompactd to compact memory so that THP is available in the
   * near future. It's the responsibility of khugepaged to then install the THP
   * pages later.
   */
  public const TRANSPARENT_HUGEPAGE_DEFRAG_TRANSPARENT_HUGEPAGE_DEFRAG_DEFER = 'TRANSPARENT_HUGEPAGE_DEFRAG_DEFER';
  /**
   * It means that an application will enter direct reclaim and compaction like
   * always, but only for regions that have used madvise(MADV_HUGEPAGE); all
   * other regions will wake kswapd in the background to reclaim pages and wake
   * kcompactd to compact memory so that THP is available in the near future.
   */
  public const TRANSPARENT_HUGEPAGE_DEFRAG_TRANSPARENT_HUGEPAGE_DEFRAG_DEFER_WITH_MADVISE = 'TRANSPARENT_HUGEPAGE_DEFRAG_DEFER_WITH_MADVISE';
  /**
   * It means that an application will enter direct reclaim like always but only
   * for regions that are have used madvise(MADV_HUGEPAGE). This is the default
   * kernel configuration.
   */
  public const TRANSPARENT_HUGEPAGE_DEFRAG_TRANSPARENT_HUGEPAGE_DEFRAG_MADVISE = 'TRANSPARENT_HUGEPAGE_DEFRAG_MADVISE';
  /**
   * It means that an application will never enter direct reclaim or compaction.
   */
  public const TRANSPARENT_HUGEPAGE_DEFRAG_TRANSPARENT_HUGEPAGE_DEFRAG_NEVER = 'TRANSPARENT_HUGEPAGE_DEFRAG_NEVER';
  /**
   * Default value. GKE will not modify the kernel configuration.
   */
  public const TRANSPARENT_HUGEPAGE_ENABLED_TRANSPARENT_HUGEPAGE_ENABLED_UNSPECIFIED = 'TRANSPARENT_HUGEPAGE_ENABLED_UNSPECIFIED';
  /**
   * Transparent hugepage support for anonymous memory is enabled system wide.
   */
  public const TRANSPARENT_HUGEPAGE_ENABLED_TRANSPARENT_HUGEPAGE_ENABLED_ALWAYS = 'TRANSPARENT_HUGEPAGE_ENABLED_ALWAYS';
  /**
   * Transparent hugepage support for anonymous memory is enabled inside
   * MADV_HUGEPAGE regions. This is the default kernel configuration.
   */
  public const TRANSPARENT_HUGEPAGE_ENABLED_TRANSPARENT_HUGEPAGE_ENABLED_MADVISE = 'TRANSPARENT_HUGEPAGE_ENABLED_MADVISE';
  /**
   * Transparent hugepage support for anonymous memory is disabled.
   */
  public const TRANSPARENT_HUGEPAGE_ENABLED_TRANSPARENT_HUGEPAGE_ENABLED_NEVER = 'TRANSPARENT_HUGEPAGE_ENABLED_NEVER';
  /**
   * cgroup_mode specifies the cgroup mode to be used on the node.
   *
   * @var string
   */
  public $cgroupMode;
  protected $hugepagesType = HugepagesConfig::class;
  protected $hugepagesDataType = '';
  protected $nodeKernelModuleLoadingType = NodeKernelModuleLoading::class;
  protected $nodeKernelModuleLoadingDataType = '';
  /**
   * The Linux kernel parameters to be applied to the nodes and all pods running
   * on the nodes. The following parameters are supported. net.core.busy_poll
   * net.core.busy_read net.core.netdev_max_backlog net.core.rmem_max
   * net.core.rmem_default net.core.wmem_default net.core.wmem_max
   * net.core.optmem_max net.core.somaxconn net.ipv4.tcp_rmem net.ipv4.tcp_wmem
   * net.ipv4.tcp_tw_reuse net.ipv4.tcp_mtu_probing net.ipv4.tcp_max_orphans
   * net.ipv4.tcp_max_tw_buckets net.ipv4.tcp_syn_retries net.ipv4.tcp_ecn
   * net.ipv4.tcp_congestion_control net.netfilter.nf_conntrack_max
   * net.netfilter.nf_conntrack_buckets
   * net.netfilter.nf_conntrack_tcp_timeout_close_wait
   * net.netfilter.nf_conntrack_tcp_timeout_time_wait
   * net.netfilter.nf_conntrack_tcp_timeout_established
   * net.netfilter.nf_conntrack_acct kernel.shmmni kernel.shmmax kernel.shmall
   * kernel.perf_event_paranoid kernel.sched_rt_runtime_us
   * kernel.softlockup_panic kernel.yama.ptrace_scope kernel.kptr_restrict
   * kernel.dmesg_restrict kernel.sysrq fs.aio-max-nr fs.file-max
   * fs.inotify.max_user_instances fs.inotify.max_user_watches fs.nr_open
   * vm.dirty_background_ratio vm.dirty_background_bytes
   * vm.dirty_expire_centisecs vm.dirty_ratio vm.dirty_bytes
   * vm.dirty_writeback_centisecs vm.max_map_count vm.overcommit_memory
   * vm.overcommit_ratio vm.vfs_cache_pressure vm.swappiness
   * vm.watermark_scale_factor vm.min_free_kbytes
   *
   * @var string[]
   */
  public $sysctls;
  /**
   * Optional. Defines the transparent hugepage defrag configuration on the
   * node. VM hugepage allocation can be managed by either limiting
   * defragmentation for delayed allocation or skipping it entirely for
   * immediate allocation only. See https://docs.kernel.org/admin-
   * guide/mm/transhuge.html for more details.
   *
   * @var string
   */
  public $transparentHugepageDefrag;
  /**
   * Optional. Transparent hugepage support for anonymous memory can be entirely
   * disabled (mostly for debugging purposes) or only enabled inside
   * MADV_HUGEPAGE regions (to avoid the risk of consuming more memory
   * resources) or enabled system wide. See https://docs.kernel.org/admin-
   * guide/mm/transhuge.html for more details.
   *
   * @var string
   */
  public $transparentHugepageEnabled;

  /**
   * cgroup_mode specifies the cgroup mode to be used on the node.
   *
   * Accepted values: CGROUP_MODE_UNSPECIFIED, CGROUP_MODE_V1, CGROUP_MODE_V2
   *
   * @param self::CGROUP_MODE_* $cgroupMode
   */
  public function setCgroupMode($cgroupMode)
  {
    $this->cgroupMode = $cgroupMode;
  }
  /**
   * @return self::CGROUP_MODE_*
   */
  public function getCgroupMode()
  {
    return $this->cgroupMode;
  }
  /**
   * Optional. Amounts for 2M and 1G hugepages
   *
   * @param HugepagesConfig $hugepages
   */
  public function setHugepages(HugepagesConfig $hugepages)
  {
    $this->hugepages = $hugepages;
  }
  /**
   * @return HugepagesConfig
   */
  public function getHugepages()
  {
    return $this->hugepages;
  }
  /**
   * Optional. Configuration for kernel module loading on nodes. When enabled,
   * the node pool will be provisioned with a Container-Optimized OS image that
   * enforces kernel module signature verification.
   *
   * @param NodeKernelModuleLoading $nodeKernelModuleLoading
   */
  public function setNodeKernelModuleLoading(NodeKernelModuleLoading $nodeKernelModuleLoading)
  {
    $this->nodeKernelModuleLoading = $nodeKernelModuleLoading;
  }
  /**
   * @return NodeKernelModuleLoading
   */
  public function getNodeKernelModuleLoading()
  {
    return $this->nodeKernelModuleLoading;
  }
  /**
   * The Linux kernel parameters to be applied to the nodes and all pods running
   * on the nodes. The following parameters are supported. net.core.busy_poll
   * net.core.busy_read net.core.netdev_max_backlog net.core.rmem_max
   * net.core.rmem_default net.core.wmem_default net.core.wmem_max
   * net.core.optmem_max net.core.somaxconn net.ipv4.tcp_rmem net.ipv4.tcp_wmem
   * net.ipv4.tcp_tw_reuse net.ipv4.tcp_mtu_probing net.ipv4.tcp_max_orphans
   * net.ipv4.tcp_max_tw_buckets net.ipv4.tcp_syn_retries net.ipv4.tcp_ecn
   * net.ipv4.tcp_congestion_control net.netfilter.nf_conntrack_max
   * net.netfilter.nf_conntrack_buckets
   * net.netfilter.nf_conntrack_tcp_timeout_close_wait
   * net.netfilter.nf_conntrack_tcp_timeout_time_wait
   * net.netfilter.nf_conntrack_tcp_timeout_established
   * net.netfilter.nf_conntrack_acct kernel.shmmni kernel.shmmax kernel.shmall
   * kernel.perf_event_paranoid kernel.sched_rt_runtime_us
   * kernel.softlockup_panic kernel.yama.ptrace_scope kernel.kptr_restrict
   * kernel.dmesg_restrict kernel.sysrq fs.aio-max-nr fs.file-max
   * fs.inotify.max_user_instances fs.inotify.max_user_watches fs.nr_open
   * vm.dirty_background_ratio vm.dirty_background_bytes
   * vm.dirty_expire_centisecs vm.dirty_ratio vm.dirty_bytes
   * vm.dirty_writeback_centisecs vm.max_map_count vm.overcommit_memory
   * vm.overcommit_ratio vm.vfs_cache_pressure vm.swappiness
   * vm.watermark_scale_factor vm.min_free_kbytes
   *
   * @param string[] $sysctls
   */
  public function setSysctls($sysctls)
  {
    $this->sysctls = $sysctls;
  }
  /**
   * @return string[]
   */
  public function getSysctls()
  {
    return $this->sysctls;
  }
  /**
   * Optional. Defines the transparent hugepage defrag configuration on the
   * node. VM hugepage allocation can be managed by either limiting
   * defragmentation for delayed allocation or skipping it entirely for
   * immediate allocation only. See https://docs.kernel.org/admin-
   * guide/mm/transhuge.html for more details.
   *
   * Accepted values: TRANSPARENT_HUGEPAGE_DEFRAG_UNSPECIFIED,
   * TRANSPARENT_HUGEPAGE_DEFRAG_ALWAYS, TRANSPARENT_HUGEPAGE_DEFRAG_DEFER,
   * TRANSPARENT_HUGEPAGE_DEFRAG_DEFER_WITH_MADVISE,
   * TRANSPARENT_HUGEPAGE_DEFRAG_MADVISE, TRANSPARENT_HUGEPAGE_DEFRAG_NEVER
   *
   * @param self::TRANSPARENT_HUGEPAGE_DEFRAG_* $transparentHugepageDefrag
   */
  public function setTransparentHugepageDefrag($transparentHugepageDefrag)
  {
    $this->transparentHugepageDefrag = $transparentHugepageDefrag;
  }
  /**
   * @return self::TRANSPARENT_HUGEPAGE_DEFRAG_*
   */
  public function getTransparentHugepageDefrag()
  {
    return $this->transparentHugepageDefrag;
  }
  /**
   * Optional. Transparent hugepage support for anonymous memory can be entirely
   * disabled (mostly for debugging purposes) or only enabled inside
   * MADV_HUGEPAGE regions (to avoid the risk of consuming more memory
   * resources) or enabled system wide. See https://docs.kernel.org/admin-
   * guide/mm/transhuge.html for more details.
   *
   * Accepted values: TRANSPARENT_HUGEPAGE_ENABLED_UNSPECIFIED,
   * TRANSPARENT_HUGEPAGE_ENABLED_ALWAYS, TRANSPARENT_HUGEPAGE_ENABLED_MADVISE,
   * TRANSPARENT_HUGEPAGE_ENABLED_NEVER
   *
   * @param self::TRANSPARENT_HUGEPAGE_ENABLED_* $transparentHugepageEnabled
   */
  public function setTransparentHugepageEnabled($transparentHugepageEnabled)
  {
    $this->transparentHugepageEnabled = $transparentHugepageEnabled;
  }
  /**
   * @return self::TRANSPARENT_HUGEPAGE_ENABLED_*
   */
  public function getTransparentHugepageEnabled()
  {
    return $this->transparentHugepageEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinuxNodeConfig::class, 'Google_Service_Container_LinuxNodeConfig');
