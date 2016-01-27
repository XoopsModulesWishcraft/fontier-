<?php
/**
 * Chronolabs Entitiesing Repository Services REST API API
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
 * @package         entities
 * @since           2.1.9
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Entitiesing Repository Services REST API
 * @link			http://sourceforge.net/projects/chronolabsapis
 * @link			http://cipher.labs.coop
 */


	require_once  __DIR__ . DIRECTORY_SEPARATOR . "header.php";

	$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
	if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, mysql_real_escape_string($GLOBALS['peer-id']))))==1)
	{
		$peer = $GLOBALS['FontsDB']->fetchArray($results);
	}
	
	$mode = !isset($_REQUEST['mode'])?md5(NULL):$_REQUEST['mode'];
	
	switch ($mode)
	{
		case "register":
			$required = array('peer-id', 'api-uri', 'api-uri-callback', 'api-uri-zip', 'api-uri-fonts', 'version', 'polinating');
			foreach($required as $field)
				if (!in_array($field, array_keys($_POST)))
					die("Field \$_POST[$field] is required to operate this function!");
			
			$sql = "INSERT INTO `peers` (`peer-id`, 'api-uri', 'api-uri-callback', 'api-uri-zip', 'api-uri-fonts', `version`, `polinating`, `created`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
			if ($GLOBALS['FontsDB']->queryF(sprintf($sql, mysql_real_escape_string($_POST['peer-id']), mysql_real_escape_string($_POST['api-uri']), mysql_real_escape_string($_POST['api-uri-callback']), mysql_real_escape_string($_POST['api-uri-zip']), mysql_real_escape_string($_POST['api-uri-fonts']), mysql_real_escape_string($_POST['version']), ($_POST['polinating']==true?'Yes':'No'), time())))
			{
				if ($_POST['polinating']==true)
				{
					if (API_URL === API_ROOT_NODE)
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` NOT LIKE '%s' AND  `peer-id` NOT LIKE '%s' AND `polinating` = 'Yes'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, mysql_real_escape_string($GLOBALS['peer-id']), mysql_real_escape_string($_POST['peer-id']))))>=1)
						{
							while($other = $GLOBALS['FontsDB']->fetchArray($results))
							{
								@setCallBackURI(sprintf($other['api-uri'].$other['api-uri-callback'], $mode), 145, 145, $_POST, array());
								@setCallBackURI(sprintf($_POST['api-uri'].$_POST['api-uri-callback'], $mode), 145, 145, array('peer-id'=>$other['peer-id'], 'api-uri'=>$other['api-uri'], 'api-uri-callback'=>$other['api-uri-callback'], 'api-uri-zip'=>$other['api-uri-zip'], 'api-uri-fonts'=>$other['api-uri-fonts'], 'version'=>$other['version'], 'polinating'=>$other['polinating']), array());
							}
						}
					}
				}
				
			}
			break;
		case "fingering":
			$required = array('fingerprint');
			foreach($required as $field)
				if (!in_array($field, array_keys($_POST)))
					die("Field \$_POST[$field] is required to operate this function!");
			$sql = "SELECT COUNT(*) as RC from `fonts_fingering` where `fingerprint` LIKE '%s'";
			list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $_POST['fingerprint'])));
			die(json_encode(array('count'=>$count)));
			break;
		default:
			
			break;
	}
	exit(0);
?>