<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

/**
 * Net-related utility methods.
 *
 * @package SimpleSAMLphp
 */
class Net
{
    /**
     * Check whether an IP address is part of a CIDR.
     *
     * @param string $cidr The network CIDR address.
     * @param string $ip The IP address to check. Optional. Current remote address will be used if none specified. Do
     * not rely on default parameter if running behind load balancers.
     *
     * @return boolean True if the IP address belongs to the specified CIDR, false otherwise.
     *
     * @author Andreas Åkre Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     * @author Brook Schofield, GÉANT
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function ipCIDRcheck($cidr, $ip = null)
    {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (strpos($cidr, '/') === false) {
            return false;
        }

        list ($net, $mask) = explode('/', $cidr);
        $mask = intval($mask);

        $ip_ip = [];
        $ip_net = [];
        if (strstr($ip, ':') || strstr($net, ':')) {
            // Validate IPv6 with inet_pton, convert to hex with bin2hex
            // then store as a long with hexdec

            $ip_pack = @inet_pton($ip);
            $net_pack = @inet_pton($net);

            if ($ip_pack === false || $net_pack === false) {
                // not valid IPv6 address (warning silenced)
                return false;
            }

            $ip_ip = str_split(bin2hex($ip_pack), 8);
            foreach ($ip_ip as &$value) {
                $value = hexdec($value);
            }

            $ip_net = str_split(bin2hex($net_pack), 8);
            foreach ($ip_net as &$value) {
                $value = hexdec($value);
            }
        } else {
            $ip_ip[0] = ip2long($ip);
            $ip_net[0] = ip2long($net);
        }

        for ($i = 0; $mask > 0 && $i < sizeof($ip_ip); $i++) {
            if ($mask > 32) {
                $iteration_mask = 32;
            } else {
                $iteration_mask = $mask;
            }
            $mask -= 32;

            $ip_mask = ~((1 << (32 - $iteration_mask)) - 1);

            $ip_net_mask = intval($ip_net[$i]) & $ip_mask;
            $ip_ip_mask = intval($ip_ip[$i]) & $ip_mask;

            if ($ip_ip_mask != $ip_net_mask) {
                return false;
            }
        }
        return true;
    }
}
