<?php
/**
 * Chronolabs Fonting Repository Services REST FONTIER FONTIER
*
* You may not change or alter any portion of this comment or credits
* of supporting developers from this source code or any supporting source code
* which is considered copyrighted (c) material of the original comment or credit authors.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* @copyright       Chronolabs Cooperative http://labs.coop
* @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
* @package         fonts
* @since           2.1.9
* @author          Simon Roberts <wishcraft@users.sourceforge.net>
* @subpackage		FONTIER
* @description		Fonting Repository Services REST FONTIER
* @link			http://sourceforge.net/projects/chronolabsFONTIERs
* @link			http://cipher.labs.coop
*/
	
	error_reporting(E_ERROR);
	ini_set('display_errors', true);
	
	global $source, $ipid, $fontnames, $salter;
	$fontnames = array();
	
	/**
	 * Opens Access Origin Via networking Route NPN
	*/
	header('Access-Control-Allow-Origin: *');
	header('Origin: *');
	
	/**
	 * Turns of GZ Lib Compression for Document Incompatibility
	 */
	ini_set("zlib.output_compression", 'Off');
	ini_set("zlib.output_compression_level", -1);
	
	require_once XOOPS_PATH.'/modules/fontier/include/functions.php';
	
	$parts = explode(".", microtime(true));
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	$salter = ((float)(mt_rand(0,1)==1?'':'-').$parts[1].'.'.$parts[0]) / sqrt((float)$parts[1].'.'.intval(cosh($parts[0])))*tanh($parts[1]) * mt_rand(1, intval($parts[0] / $parts[1]));
	header('Blowfish-salt: '. $salter);
	
	define('FONTS_CACHE', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'cache');
	if (!is_dir(FONTS_CACHE))
		mkdirSecure(FONTS_CACHE, 0777, true);
	
	/**
	 * URI Path Finding of FONTIER URL Source Locality
	 * @var unknown_type
	 */
	$pu = parse_url($_SERVER['REQUEST_URI']);
	$source = (isset($_SERVER['HTTPS'])?'https://':'http://').strtolower($_SERVER['HTTP_HOST']);
	
	/**
	 * URI Path Finding of FONTIER URL Source Locality
	 * @var unknown_type
	 */
	$ipid = getIPIdentity(whitelistGetIP(true));