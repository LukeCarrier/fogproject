<?php
/**
 * Boot page for pxe/iPXE
 *
 * PHP version 5
 *
 * @category Boot
 * @package  FOGProject
 * @author   Tom Elliott <tommygunsster@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Boot page for pxe/iPXE
 *
 * @category Boot
 * @package  FOGProject
 * @author   Tom Elliott <tommygunsster@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
require '../../commons/base.inc.php';
header("Content-type: text/plain");
$mac = array_merge(
    explode('|', $_REQUEST['mac']),
    explode('|', $_REQUEST['mac0']),
    explode('|', $_REQUEST['mac1']),
    explode('|', $_REQUEST['mac2'])
);
$mac = array_filter($mac);
$mac = array_unique($mac);
$mac = array_values($mac);
$_REQUEST['mac'] = implode('|', (array)$mac);
$Host = $FOGCore->getHostItem(false, false, true);
FOGCore::getClass('BootMenu', $Host);
