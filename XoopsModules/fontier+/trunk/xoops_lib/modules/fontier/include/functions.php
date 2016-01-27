<?php
/**
 * Chronolabs Cooperatiev Fontier+
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
 * @package         fontier+
 * @since           1.0.1
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		fontier+
 * @description		XOOPS Client for Fonts REST API - http://fonts.labs.coop
 * @link			http://fonts.labs.coop
 * @link			http://sourceforge.net/projects/chronolabs
 * @link			http://cipher.labs.coop
 */

require_once __DIR__.'/constants.php';

if (!function_exists("fontsUseragentSupportedArray")) {
	function fontsUseragentSupportedArray()
	{
		$return = array();
		if (isset($_GET['version']) && !empty($_GET['version']))
			$version = (string)$_GET['version'];
		else 
			$version = (string)"v2";
		$ua = explode( " " , str_replace(array("\"","'",";",":","(",")","\\","/"), " ", $_SERVER['HTTP_USER_AGENT']) );
		$fontlist = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'default-useragent-'.$version.'.diz';
		if (!isset($ua[0]) && empty($ua[0]) && !isset($ua[1]) && empty($ua[1]) && !file_exists($fontlist = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . strtolower($ua[0]).'-'.strtolower($ua[1]).'-useragent-'.$version.'.diz'))
		{
			foreach(cleanWhitespaces(file($fontlist)) as $out)
			{
				$puts = explode("||", $out);
				$return[$puts[0]]=$puts[1];
			}
		}
		if (empty($return))
			foreach(cleanWhitespaces(file($fontlist)) as $out)
			{
				$puts = explode("||", $out);
				$return[$puts[0]]=$puts[1];
			}
		return $return;
	}
}


if (!function_exists("setCallBackURI")) {

	/* function getURIData()
	 *
	 * 	cURL Routine
	 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
	 *
	 * @return 		float()
	 */
	function setCallBackURI($uri = '', $timeout = 65, $connectout = 65, $data = array(), $queries = array())
	{
		list($when) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['trackerDB']->queryF("SELECT `when` from `callbacks` ORDER BY `when` DESC LIMIT 1"));
		if ($when<time())
			$when = $time();
			$when = $when + mt_rand(3, 14);
			return $GLOBALS['FontsDB']->queryF("INSERT INTO `callbacks` (`when`, `uri`, `timeout`, `connection`, `data`, `queries`) VALUES(\"$when\", \"$uri\", \"$timeout\", \"$connectout\", \"" . mysql_real_escape_string(json_encode($data)) . "\",\"" . mysql_real_escape_string(json_encode($queries)) . "\")");
	}
}

if (!function_exists("putRawFile")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function putRawFile($file = '', $data = '')
	{
		$lineBreak = "\n";
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$lineBreak = "\r\n";
		}
		if (!is_dir(dirname($file)))
			if (strpos(' '.$file, FONTS_CACHE))
				mkdirSecure(dirname($file), 0777, true);
			else
				mkdir(dirname($file), 0777, true);
		elseif (strpos(' '.$file, FONTS_CACHE) && !file_exists(FONTS_CACHE . DIRECTORY_SEPARATOR . '.htaccess'))
			putRawFile(FONTS_CACHE . DIRECTORY_SEPARATOR . '.htaccess', "<Files ~ \"^.*$\">\n\tdeny from all\n</Files>");
		if (is_file($file))
			unlink($file);
		$data = str_replace("\n", $lineBreak, $data);
		$ff = fopen($file, 'w');
		fwrite($ff, $data, strlen($data));
		fclose($ff);
	}
}

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIPAddy()
	 *
	* 	provides an associative array of whitelisted IP Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function whitelistGetIPAddy() {
		return array_merge(whitelistGetNetBIOSIP(), file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist.txt'));
	}
}

if (!function_exists("whitelistGetNetBIOSIP")) {

	/* function whitelistGetNetBIOSIP()
	 *
	* 	provides an associative array of whitelisted IP Addresses base on TLD and NetBIOS Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function whitelistGetNetBIOSIP() {
		$ret = array();
		foreach(file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist-domains.txt') as $domain) {
			$ip = gethostbyname($domain);
			$ret[$ip] = $ip;
		}
		return $ret;
	}
}

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIP()
	 *
	* 	get the True IPv4/IPv6 address of the client using the API
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @param		$asString	boolean		Whether to return an address or network long integer
	*
	* @return 		mixed
	*/
	function whitelistGetIP($asString = true){
		// Gets the proxy ip sent by the user
		$proxy_ip = '';
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
		if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
		} else
		if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
		} else
		if (!empty($_SERVER['HTTP_FORWARDED'])) {
			$proxy_ip = $_SERVER['HTTP_FORWARDED'];
		} else
		if (!empty($_SERVER['HTTP_VIA'])) {
			$proxy_ip = $_SERVER['HTTP_VIA'];
		} else
		if (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
			$proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
		} else
		if (!empty($_SERVER['HTTP_COMING_FROM'])) {
			$proxy_ip = $_SERVER['HTTP_COMING_FROM'];
		}
		if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0)  {
			$the_IP = $regs[0];
		} else {
			$the_IP = $_SERVER['REMOTE_ADDR'];
		}
			
		$the_IP = ($asString) ? $the_IP : ip2long($the_IP);
		return $the_IP;
	}
}


if (!function_exists("getIPIdentity")) {
	/**
	 *
	 * @param string $ip
	 * @return string
	 */
	function getIPIdentity($ip = '', $sarray = false)
	{
		$sql = array();
		
		if (empty($ip))
			$ip = whitelistGetIP(true);
		
		if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
			$sql['selecta'] = "SELECT * from `networking` WHERE `ipaddy` LIKE '" . $ip . "' AND `type` = 'ipv6'";
		else
			$sql['selecta'] = "SELECT * from `networking` WHERE `ipaddy` LIKE '" . $ip . "' AND `type` = 'ipv4'";
		if (!$row = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql['selecta'])))
			if (($ipaddypart[0] ===  $serverpart[0] && $ipaddypart[1] ===  $serverpart[1]) )
			{
				$uris = cleanWhitespaces(file($file = __DIR__ . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "lookups.diz"));
				shuffle($uris); shuffle($uris); shuffle($uris); shuffle($uris);
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE || FILTER_FLAG_NO_RES_RANGE) === false)
				{
					$data = array();
					foreach($uris as $uri)
					{
						if ($data['ip']==$ip || $data['country']['iso'] == "-" || empty($data))
							$data = json_decode(getURIData(sprintf($uri, 'myself', 'json'), 120, 120), true);
						if (count($data) > 0 &&  $data['country']['iso'] != "-")
							continue;
					}
				} else{
					foreach($uris as $uri)
					{
						if ($data['ip']!=$ip || $data['country']['iso'] == "-" || empty($data))
							$data = json_decode(getURIData(sprintf($uri, $ip, 'json'), 120, 120), true);
						if (count($data) > 0 &&  $data['country']['iso'] != "-")
							continue;
					}
				}
				if (!isset($data['ip']) && empty($data['ip']))
					$data['ip'] = $ip;
				if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
					$sql['selectb'] = "SELECT * from `networking` WHERE `ipaddy` LIKE '" . $data['ip'] . "' AND `type` = 'ipv6'";
				else
					$sql['selectb'] = "SELECT * from `networking` WHERE `ipaddy` LIKE '" . $data['ip'] . "' AND `type` = 'ipv4'";
				if (!$row = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql['selectb'])))
				{
					$row = array();
					$row['ipaddy'] = $data['ip'];
					if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
						$row['type'] = 'ipv6';
					else 
						$row['type'] = 'ipv4';
					$row['netbios'] = gethostbyaddr($row['ipaddy']);
					$row['data'] = array('ipstack' => gethostbynamel($row['netbios']));
					$row['domain'] = getBaseDomain("http://".$row['netbios']);
					$row['country'] = $data['country']['iso'];
					$row['region'] = $data['location']['region'];
					$row['city'] = $data['location']['city'];
					$row['postcode'] = $data['location']['postcode'];
					$row['timezone'] = "GMT " . $data['location']['gmt'];
					$row['longitude'] = $data['location']['coordinates']['longitude'];
					$row['latitude'] = $data['location']['coordinates']['latitude'];
					$row['last'] = $row['created'] = time();
					$row['downloads'] = 0;
					$row['uploads'] = 0;
			
					$row['fonts'] = 0;
					$row['surveys'] = 0;
					$whois = array();
					$whoisuris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "whois.diz"));
					shuffle($whoisuris); shuffle($whoisuris); shuffle($whoisuris); shuffle($whoisuris);
					foreach($whoisuris as $uri)
					{
						if (empty($whois[$row['type']]) || !isset($whois[$row['type']]))
						{
							$whois[$row['type']] = json_decode(getURIData(sprintf($uri, $row['ipaddy'], 'json'), 120, 120), true);
						} elseif (empty($whois['domain']) || !isset($whois['domain']))
						{
							$whois['domain'] = json_decode(getURIData(sprintf($uri, $row['domain'], 'json'), 120, 120), true);
						} else
							continue;
					}
					$sql = "SELECT count(*) FROM `whois` WHERE `id` = '".$wsid = md5(json_encode($whois))."'";
					list($countb) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
					if ($countb == 0)
					{
						$wsdata = array();
						$wsdata['id'] = $wsid;
						$wsdata['whois'] = mysql_real_escape_string(json_encode($whois));
						$wsdata['created'] = time();
						$wsdata['last'] = time();
						$wsdata['instances'] = 1;
						if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `whois` (`" . implode('`, `', array_keys($whois)) . "`) VALUES ('" . implode("', '", $whois) . "')"))
							die("SQL Failed: $sql;");
						else
							echo ".";
					} else {
						if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `whois` SET `instances` = `instances` + 1, `last` = unix_timestamp() WHERE `id` =  '$wsid'"))
							die("SQL Failed: $sql;");
						else
							echo ".";
					}
					$row['whois'] = $wsid;
					$row['ip_id'] = md5(json_encode($row));
					$data = array();
					foreach($row as $key => $value)
						if (is_array($value))
							$data[$key] = mysql_real_escape_string(json_encode($value));
						else
							$data[$key] = mysql_real_escape_string($value);
					$sql['inserta'] = "INSERT INTO `networking` (`" . implode("`, `", array_keys($data)) . "`) VALUES ('" . implode("', '", $data) . "')";
					$GLOBALS['FontsDB']->queryF($sql['inserta']);
				} 
			} 
		$sql['updatea'] = "UPDATE `networking` SET `last` = '". time() . '\' WHERE `ip_id` = "' . $row['ip_id'] .'"';
		$GLOBALS['FontsDB']->queryF($sql['updatea']);
		if ($sarray == false)
			return $row['ip_id'];
		else
			return $row;
	}
}


if (!function_exists("getBaseDomain")) {
	/**
	 * getBaseDomain
	 *
	 * @param string $url
	 * @return string|unknown
	 */
	function getBaseDomain($url)
	{

		static $fallout, $stratauris, $classes;

		if (empty($classes))
		{
			if (empty($stratauris)) {
				$stratauris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "stratas.diz"));
				shuffle($stratauris); shuffle($stratauris); shuffle($stratauris); shuffle($stratauris);
			}
			shuffle($stratauris);
			$attempts = 0;
			while(empty($classes) || $attempts <= (count($stratauris) * 1.65))
			{
				$attempts++;
				$classes = array_keys(unserialize(getURIData($stratauris[mt_rand(0, count($stratauris)-1)] ."/v1/strata/serial.api", 120, 120)));
			}
		}
		if (empty($fallout))
		{
			if (empty($stratauris)) {
				$stratauris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "stratas.diz"));
				shuffle($stratauris); shuffle($stratauris); shuffle($stratauris); shuffle($stratauris);
			}
			shuffle($stratauris);
			$attempts = 0;
			while(empty($fallout) || $attempts <= (count($stratauris) * 1.65))
			{
				$attempts++;
				$fallout = array_keys(unserialize(getURIData($stratauris[mt_rand(0, count($stratauris)-1)] ."/v1/fallout/serial.api", 120, 120)));
			}
		}
		
		// Get Full Hostname
		$url = strtolower($url);
		$hostname = parse_url($url, PHP_URL_HOST);
		if (!filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 || FILTER_FLAG_IPV4) === false)
			return $hostname;

		// break up domain, reverse
		$elements = explode('.', $hostname);
		$elements = array_reverse($elements);

		// Returns Base Domain
		if (in_array($elements[0], $classes))
			return $elements[1] . '.' . $elements[0];
		elseif (in_array($elements[0], $fallout) && in_array($elements[1], $classes))
			return $elements[2] . '.' . $elements[1] . '.' . $elements[0];
		elseif (in_array($elements[0], $fallout))
			return  $elements[1] . '.' . $elements[0];
		else
			return  $elements[1] . '.' . $elements[0];
	}
}

if (!function_exists("generateCSS")) {
	function generateCSS($fonts = array(), $name = '', $normal = 'no', $bold = 'no', $italic = 'no', $version = "v2")
	{
		if ($bold == 'yes')
			$name .= ' Bold';
		if ($italic == 'yes')
			$name .= ' Italic';
		$name = trim($name);
		$typals = fontsUseragentSupportedArray();
		$buff = array();
		$keys = array_keys($fonts);
		sort($keys);
		foreach($keys as $type)
			$buff[] = "local('".$fonts[$type]."') format('".$typals[$type]."')";
		foreach($keys as $type)
			$buff[] = "url('".$fonts[$type]."') format('".$typals[$type]."')";
		$css = array();
		$css[] = "";
		$css[] = "/** Font: $name **/";
		$css[] = "@font-face {";
		$css[] = "\tfont-family: '$name';";
		$css[] = "\tsrc: url('".$fonts['woff']."');";
		$css[] = "\tsrc: ".implode(", ", $buff) .";";
		$css[] = "\tfont-weight: ".($bold=='yes'?'900':'normal') . ";";
		$css[] = "\tfont-style: ".($italic=='yes'?'italic':'normal') . ";";
		$css[] = "}";
		return implode("\n", $css);
	}
}

if (!function_exists("getRegionalFontName")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function getRegionalFontName($fontid = '', $latitude = 0, $longitude = 0, $getGistance = false)
	{
		static $variables = array();
		if (!isset($variables[$fontid]))
		{
			if ($latitude==0 && $longitude == 0)
			{
				if (empty($iparray)) 
					$iparray = getIPIdentity(whitelistGetIP(true), true);
				$latitude = $iparray['latitude'];
				$longitude = $iparray['longitude'];
			}
			list($name, $distance) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT `name`, 3956 * 2 * ASIN(SQRT(POWER(SIN((" . abs($latitude) . " - abs(`latitude`)) * pi() / 180 / 2), 2) + COS(" . abs($latitude) . " * pi() / 180 ) * COS(abs(`latitude`) *  pi() / 180) * POWER(SIN((" . $longitude . " - `longitude`) *  pi() / 180 / 2), 2) )) as distance FROM `fonts_names` WHERE `font_id` = '$fontid' ORDER BY `distance` LIMIT 1"));
			$variables[$fontid]['name'] = empty($name)?$fontid:$name;
			$variables[$fontid]['distance'] = $distance;
		}
		return (!isset($variables[$fontid]['name'])||empty($variables[$fontid]['name'])?$fontid:($getGistance == false?$variables[$fontid]['name']:$variables[$fontid]['distance']));
	}
}

if (!function_exists("getMimetype")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function getMimetype($extension = '-=-')
	{
		$mimetypes = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mimetypes.diz'));
		foreach($mimetypes as $mimetype)
		{
			$parts = explode("||", $mimetype);
			if (strtolower($extension) == strtolower($parts[0]))
				return $parts[1];
			if (strtolower("-=-") == strtolower($parts[0]))
				$final = $parts[1];
		}
		return $final;
	}
}

if (!function_exists("mkdirSecure")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function mkdirSecure($path = '', $perm = 0777, $secure = true)
	{
		if (!is_dir($path))
		{
			mkdir($path, $perm, true);
			if ($secure == true)
			{
				writeRawFile($path . DIRECTORY_SEPARATOR . '.htaccess', "<Files ~ \"^.*$\">\n\tdeny from all\n</Files>");
			}
			return true;
		}
		return false;
	}
}

if (!function_exists("cleanWhitespaces")) {
	/**
	 *
	 * @param array $array
	 */
	function cleanWhitespaces($array = array())
	{
		foreach($array as $key => $value)
		{
			if (is_array($value))
				$array[$key] = cleanWhitespaces($value);
			else {
				$array[$key] = trim(str_replace(array("\n", "\r", "\t"), "", $value));
			}
		}
		return $array;
	}
}

if (!function_exists("getURIData")) {

	/* function getURIData()
	 *
	* 	cURL Routine
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
   	*
	* @return 		float()
	*/
	function getURIData($uri = '', $timeout = 65, $connectout = 65, $post_data = array())
	{
		if (!function_exists("curl_init"))
		{
			return file_get_contents($uri);
		}
		if (!$btt = curl_init($uri)) {
			return false;
		}
		curl_setopt($btt, CURLOPT_HEADER, 0);
		curl_setopt($btt, CURLOPT_POST, (count($posts)==0?false:true));
		if (count($posts)!=0)
			curl_setopt($btt, CURLOPT_POSTFIELDS, http_build_query($post_data));
		curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
		curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($btt, CURLOPT_VERBOSE, false);
		curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($btt);
		curl_close($btt);
		return $data;
	}
}


if (!function_exists("getFontUFORawData")) {
	function getFontUFORawData($mode = '', $clause = '', $state = '', $output = '', $ufofile = '')
	{
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $row['font_id'] . "'");
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			switch($font['medium'])
			{
				case FONT_RESOURCES_CACHE:
				case FONT_RESOURCES_RESOURCE:
					if ($font['medium'] == FONT_RESOURCES_CACHE)
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprint(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					}
					$json = json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
				case FONT_RESOURCES_PEER:
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, mysql_real_escape_string($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprint($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					$json = json_decode(getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
			}
			$filez = array();
			if ($state=='')
			{
				$filez['parent'] = API_URL;
				foreach($json['Files'] as $key => $files)
				{
					if (substr($key, strlen($key)-4) == '.ufo' && is_array($files))
					{
						$folder = $key;
						$filez['root'] = API_URL."/v2/font/$clause/ufo.api";
						$filez['title'] = "$clause/ufo.api";
						foreach($files as $file)
						{
							$filez['files'][md5($file)]['name'] = $file;
							$filez['files'][md5($file)]['bytes'] = number_format(strlen(getArchivedZIPFile($zip, basename($file), true)),0);
						}
					}
					elseif (!empty($folder) && substr($key, 0, strlen($folder)) == $folder && is_array($files))
					{
						$filez['folder'][md5($file)] = basename($key);
					}
				}
			} elseif (substr($state, strlen($state)-1, 1) == "/")
			{
				$state = substr($state,0, strlen($state)-1);
				$filez['parent'] = API_URL."/v2/font/$clause/ufo.api";
				foreach($json['Files'] as $key => $files)
				{
					if (substr($key, strlen($key)-4) == '.ufo' && is_array($files))
					{
						$folder = $key;
					}
					elseif (!empty($folder) && substr($key, strlen($key)-strlen($state)) == $state && is_array($files))
					{
						$filez['root'] = API_URL."/v2/font/$clause/ufo.api/$state";
						$filez['title'] = "ufo.api/$state";
						foreach($files as $file)
						{
							$filez['files'][md5($file)]['name'] = $file;
							$filez['files'][md5($file)]['bytes'] = number_format(strlen(getArchivedZIPFile($zip, basename($file), true)),0);
						}
					}
				}
			} elseif (substr($state, strlen($state)-1, 1) != "/" && strlen($state)) {
				return getArchivedZIPFile($zip, basename($state), $row['font_id']);
			}
		}
		$html = "<h1>Index of ".$filez['title']."</h1>\n";
		$html .= "<table>\n";
		$html .= "<tbody>";
		$html .= "<tr><th colspan=\"5\"><hr></th></tr>";
		$html .= "<tr><td valign=\"top\"><img src=\"".API_URL."/images/back.gif\" alt=\"[PARENTDIR]\"></td><td><a href=\"".$filez['parent']."\">Parent Directory</a></td><td>&nbsp;</td><td align=\"right\">  - </td><td>&nbsp;</td></tr>\n";
		if (isset($filez['folder']))
		{
			foreach($filez['folder'] as $md5 => $folder)
				$html .= "<tr><td valign=\"top\"><img src=\"".API_URL."/images/folder.gif\" alt=\"[DIR]\"></td><td><a href=\"".$filez['root']."/$folder/\">$folder/</a></td><td align=\"right\">".date("Y-m-d H:i:s")."</td><td align=\"right\">  - </td><td>&nbsp;</td></tr>\n";
		}
		if (isset($filez['files']))
		{
			foreach($filez['files'] as $md5 => $file)
				$html .= "<tr><td valign=\"top\"><img src=\"".API_URL."/images/text.gif\" alt=\"[FILE]\"></td><td><a href=\"".$filez['root']."/".$file['name']."\">".$file['name']."</a></td><td align=\"right\">".date("Y-m-d H:i:s")."</td><td align=\"right\">".$file['bytes']." bytes</td><td>&nbsp;</td></tr>\n";
		}
		$html .= "<tr><th colspan=\"5\"><hr></th></tr></tbody></table>\n";
		$html .= "<address>Fonts API/".API_VERSION." (".PHP_VERSION.") Server at ".parse_url("http://".$_SERVER["HTTP_HOST"], PHP_URL_HOST). " Port ".$_SERVER["SERVER_PORT"]."</address>\n";
		return $html;
	}
}

if (!function_exists("getCSSListArray")) {
	function getCSSListArray($mode = '', $clause = '', $state = '', $name = '', $output = '', $version = "v2")
	{
	$styles = array();
	switch($mode)
	{
		case "font":
			$sql = "SELECT * from `fonts` WHERE `id` = '$clause'";
			$result = $GLOBALS['FontsDB']->queryF($sql);
			while($font = $GLOBALS['FontsDB']->fetchArray($result))
			{
				foreach(getArchivingShellExec() as $type => $exec)
					$GLOBALS['downloaduris'][$font['name']][$type] = API_URL . '/v2/data/' .  $font['id'] . '/' . $type . '/download.api';
				$fonts = array();
				$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $clause . "'");
				foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
				{
					$fonts[$fonttype] = API_URL . "/".$version."/font/$clause/$fonttype.api";
				}
				//die(getRegionalFontName($clause));
				$GLOBALS['fontnames'][getRegionalFontName($clause)] = getRegionalFontName($clause);
				$styles[getRegionalFontName($clause)] = generateCSS($fonts, getRegionalFontName($clause), $font['normal'], $font['bold'], $font['italics']);
				if ($state!='preview')
				{
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fonts[$clause][$fonttype] = API_URL . "/".$version."/font/$clause/$fonttype.api";
					}
					$GLOBALS['fontnames'][$clause] = $clause;
					foreach(getArchivingShellExec() as $type => $exec)
						$GLOBALS['downloaduris'][$clause][$type] = API_URL . '/v2/data/' .  $clause . '/' . $type . '/download.api';
					$styles[$clause] = generateCSS($fonts[$clause], $clause, $font['normal'], $font['bold'], $font['italics']);
				}
			}
			break;
		case "fonts":
			$names = array();
			foreach(getFontsListArray($clause, $output) as $key => $font)
			{
				$fonts = array();
				$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $key . "'");
				foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
				{
					$fonts[getRegionalFontName($key)][$fonttype] = API_URL . "/".$version."/font/$key/$fonttype.api";
				}
				$GLOBALS['fontnames'][getRegionalFontName($key)] = getRegionalFontName($key);
				foreach(getArchivingShellExec() as $type => $exec)
					$GLOBALS['downloaduris'][getRegionalFontName($key)][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
				$sql = "SELECT * from `fonts_names` WHERE `font_id` = '$key'";
				$result = $GLOBALS['FontsDB']->queryF($sql);
				while($fontname = $GLOBALS['FontsDB']->fetchArray($result))
				{
					$styles[md5($key.$fontname['name'])] = generateCSS($fonts[getRegionalFontName($key)], $fontname['name'], $font['normal'], $font['bold'], $font['italics']);
				}
				$GLOBALS['fontnames'][$key] = $key;
				foreach(getArchivingShellExec() as $type => $exec)
					$GLOBALS['downloaduris'][$key][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
				$styles[$key] = generateCSS($fonts[getRegionalFontName($key)], getRegionalFontName($key), $font['normal'], $font['bold'], $font['italics']);
				
			}
			break;
		case "sites":
			break;
		case "random":
			$fonts = array();
			$fonts['normal'] = getRandomFontsFromStringList($clause, 'yes', '', '', '');
			$fonts['bold'] = getRandomFontsFromStringList($clause, '', 'yes', '', '');
			$fonts['italic'] = getRandomFontsFromStringList($clause, '', '', 'yes', '');
			$fonts['condensed'] = getRandomFontsFromStringList($clause, '', '', '', 'yes');
			$fontooo = array();
			foreach($fonts as $key => $font)
			{
				if (!empty($font))
				{
					$font['name'] = trim(ucwords(str_replace('-', ' ', $state)));
					$fontooo[$font['id']] = $font;
					if (count($fontooo)>=2)
						$fontooo[$font['id']]['name'] . " " . ucfirst($key);
				}
			}
			$GLOBALS['fontnames'][] = $font['name'];
			foreach($fontooo as $key => $font)
			{
				if (!empty($font))
				{
					$fonter = array();
					$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $key . "'");
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fonter[$fonttype] = API_URL . "/".$version."/font/$key/$fonttype.api";
					}
					$styles[$key] = generateCSS($fonter, trim($font['name']) . ($names[$font['name']]!="AA"?" ".$names[$font['name']]:""), $font['normal'], $font['bold'], $font['italics']);
				}
			}
			break;
		}
		foreach($GLOBALS['fontnames'] as $key => $value)
			if (empty($value)||empty($key))
				unset($GLOBALS['fontnames'][$key]);
			
		return $styles;
	}
}



if (!function_exists("getFontRawData")) {
	function getFontRawData($mode = '', $clause = '', $output = '', $ufofile = '')
	{
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		if (!$result = $GLOBALS['FontsDB']->queryF($sql))
			die("SQL Failed: $sql;");
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $row['font_id'] . "'");
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			switch($font['medium'])
			{
				case FONT_RESOURCES_CACHE:
				case FONT_RESOURCES_RESOURCE:
					if ($font['medium'] == FONT_RESOURCES_CACHE)
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprint(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					}
					$json = json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
				case FONT_RESOURCES_PEER:
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, mysql_real_escape_string($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprint($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					$json = json_decode(getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
			}
			$fontfiles = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts_files` WHERE `font_id` = '" . $row['font_id'] . "' AND `type` = '$output'"));
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['font_id'] . "'");
			$resultb = $GLOBALS['FontsDB']->queryF($sql = "SELECT * FROM `fonts_callbacks` WHERE `failed` <= unix_timestamp() - (3600 * 6) AND LENGTH(`uri`) > 0 AND `type` IN ('fonthit') AND `font_id` = '" . $row['font_id'] . "'");
			while($callback = $GLOBALS['FontsDB']->fetchArray($resultb))
			{
				@setCallBackURI($callback['uri'], 145, 145, array_merge(array('type' => $output, 'hits' => $fontfiles['hits']+1, 'font-key' => $row['font_id'], 'ipid' => getIPIdentity('', true))), array("success"=>"UPDATE `fonts_callbacks` SET `calls` = `calls` + 1, `last` = UNIX_TIMESTAMP() WHERE `id` = '" . $callback['id'] . "'", "failed" => "UPDATE `fonts_callbacks` SET `calls` = `calls` + 1, `last` = UNIX_TIMESTAMP(), `failed` = UNIX_TIMESTAMP() WHERE `id` = '" . $callback['id'] . "'"));
			}
			foreach(getArchivedZIPContentsArray($zip) as $md5 => $values)
				if ((integer)$output == (integer)$values['type'])
					switch($output)
					{
						default:
							return getArchivedZIPFile($zip, $row['font_id']);
							break;
						case "ufo":
							return getArchivedZIPFile($zip, $ufofile, $row['font_id']);
							break;
					}
			
		}
		return '';
	}
}


if (!function_exists("cleanResourcesCache")) {
	function cleanResourcesCache()
	{
		$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
		foreach($sessions as $key => $values)
		{
			if ($values['dropped']<microtime(true))
			{
				unlink(FONT_RESOURCES_CACHE.$values['resource']);
				$path = constant("FONT_RESOURCES_CACHE") . ($subpath = dirname($values['resource']));
				foreach(explode(DIRECTORY_SEPARATOR, $subpath) as $folder)
				{
					rmdir($path);
					$path = dirname($path);
				}
				unset($sessions[$key]);
			}
		}
		writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
		return true;
	}
}

if (!function_exists("getMimetype")) {
	function getMimetype($type = '')
	{
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `mimetypes` WHERE `type` LIKE '$type'");
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
			return $row['mimetype'];
		return 'text/html';
	}
}

if (!function_exists("getArchivedZIPFile")) {
	function getArchivedZIPFile($zip_resource = '', $zip_file = '', $fontid = '')
	{
		if (!empty($fontid))
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `hits` = `hits` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `font_id` = '" . $fontid . "' AND `filename` = '$zip_file'");
		$data = '';
 		$zip = zip_open($zip_resource);
        if ($zip) {
        	while ($zip_entry = zip_read($zip)) {
            	if (strpos('  '.strtolower(zip_entry_name($zip_entry)), strtolower($zip_file)))
                	if (zip_entry_open($zip, $zip_entry, "r")) {
                    	$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                        zip_entry_close($zip_entry);
                        continue;
                        continue;
                    }
            }
            zip_close($zip);
         }
         return $data;
		
	}
}

if (!function_exists('sef'))
{

	/**
	 * Safe encoded paths elements
	 *
	 * @param unknown $datab
	 * @param string $char
	 * @return string
	 */
	function sef($value = '', $stripe ='-')
	{
		$value = str_replace('&', 'and', $value);
		$value = str_replace(array("'", '"', "`"), 'tick', $value);
		$replacement_chars = array();
		$accepted = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","m","o","p","q",
				"r","s","t","u","v","w","x","y","z","0","9","8","7","6","5","4","3","2","1");
		for($i=0;$i<256;$i++){
			if (!in_array(strtolower(chr($i)),$accepted))
				$replacement_chars[] = chr($i);
		}
		$result = (str_replace($replacement_chars, $stripe, ($value)));
		while(substr($result, 0, strlen($stripe)) == $stripe)
			$result = substr($result, strlen($stripe), strlen($result) - strlen($stripe));
		while(substr($result, strlen($result) - strlen($stripe), strlen($stripe)) == $stripe)
			$result = substr($result, 0, strlen($result) - strlen($stripe));
		while(strpos($result, $stripe . $stripe))
			$result = str_replace($stripe . $stripe, $stripe, $result);
		return(strtolower($result));
	}
}

if (!function_exists("writeRawFile")) {
	function writeRawFile($file = '', $data = '')
	{
		if (!is_dir(dirname($file)))
			mkdir(dirname($file), 0777, true);
		if (is_file($file))
			unlink($file);
		file_put_contents($file, $data);
		if (!strpos($file, 'caches-files-sessioning.serial') && strpos($file, '.serial'))
		{
			
			if (file_exists(FONTS_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial'))
				$sessions = unserialize(file_get_contents(FONTS_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial'));
			else
				$sessions = array();
			if (!isset($sessions[basename($file)]))
				$sessions[basename($file)] = array('file' => $file, 'till' =>microtime(true) + mt_rand(3600*24*7.35,3600*24*14*8.75));
			foreach($sessions as $file => $values)
				if ($values['till']<time() && isset($values['till']))
				{
					if (file_exists($values['file']))
						unlink($values['file'])	;
					unset($sessions[$file]);
				}
			file_put_contents(FONTS_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial', serialize($sessions));
		}
	}
}

if (!function_exists("getArchivedZIPContentsArray")) {
	function getArchivedZIPContentsArray($zip_file = '')
	{
		$zip = zip_open($zip_file);
		$files = array();
		if ($zip) {
			while ($zip_entry = zip_read($zip)) {
				if (zip_entry_open($zip, $zip_entry, "r")) {
					$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					$type = '';
					$parts = explode(".", basename(zip_entry_name($zip_entry)));
					$type = $parts[count($parts)-1];
					$files[md5($data)] = array('filename' => basename(zip_entry_name($zip_entry)), 'path' => dirname(zip_entry_name($zip_entry)), 'bytes' => strlen($data), 'type' => $type);
					zip_entry_close($zip_entry);
				}
			}
			zip_close($zip);
		}
		return $files;
	}
}

if (!function_exists("getCompleteFilesListAsArray")) {
	function getCompleteFilesListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
			foreach(getFileListAsArray($path) as $file)
				$result[$path.DIRECTORY_SEPARATOR.$file] = $path.DIRECTORY_SEPARATOR.$file;
		return $result;
	}

}


if (!function_exists("getCompleteDirListAsArray")) {
	function getCompleteDirListAsArray($dirname, $result = array())
	{
		$result[$dirname] = $dirname;
		foreach(getDirListAsArray($dirname) as $path)
		{
			$result[$dirname . DIRECTORY_SEPARATOR . $path] = $dirname . DIRECTORY_SEPARATOR . $path;
			$result = getCompleteDirListAsArray($dirname . DIRECTORY_SEPARATOR . $path, $result);
		}
		return $result;
	}
	
}

if (!function_exists("getCompleteZipListAsArray")) {
	function getCompleteZipListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
		{
			foreach(getZipListAsArray($path) as $file)
				$result[md5_file($path . DIRECTORY_SEPARATOR . $file)] =  $path . DIRECTORY_SEPARATOR . $file;
		}
		return $result;
	}
}


if (!function_exists("getCompletePacksListAsArray")) {
	function getCompletePacksListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
		{
			foreach(getPacksListAsArray($path) as $file=>$values)
				$result[$values['type']][md5_file( $path . DIRECTORY_SEPARATOR . $values['file'])] =  $path . DIRECTORY_SEPARATOR . $values['file'];
		}
		return $result;
	}
}

if (!function_exists("getCompleteFontsListAsArray")) {
	function getCompleteFontsListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
		{
			foreach(getFontsListAsArray($path) as $file=>$values)
				$result[$values['type']][md5_file($path . DIRECTORY_SEPARATOR . $values['file'])] = $path . DIRECTORY_SEPARATOR . $values['file'];
		}
		return $result;
	}
}

if (!function_exists("getDirListAsArray")) {
        function getDirListAsArray($dirname)
        {
            $ignored = array(
                'cvs' ,
                '_darcs');
            $list = array();
            if (substr($dirname, - 1) != '/') {
                $dirname .= '/';
            }
            if ($handle = opendir($dirname)) {
                while ($file = readdir($handle)) {
                    if (substr($file, 0, 1) == '.' || in_array(strtolower($file), $ignored))
                        continue;
                    if (is_dir($dirname . $file)) {
                        $list[$file] = $file;
                    }
                }
                closedir($handle);
                asort($list);
                reset($list);
            }

            return $list;
        }
}

if (!function_exists("getFileListAsArray")) {
        function getFileListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if (substr($dirname, - 1) == '/') {
                $dirname = substr($dirname, 0, - 1);
            }
            if (is_dir($dirname) && $handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (! preg_match('/^[\.]{1,2}$/', $file) && is_file($dirname . '/' . $file)) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }
}

if (!function_exists("getZipListAsArray")) {
        function getZipListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if ($handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (preg_match('/(\.zip)$/i', $file)) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }
}


if (!function_exists("getPacksListAsArray")) {
	function getPacksListAsArray($dirname, $prefix = '')
	{
		$packs = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-converted.diz'));
		$filelist = array();
		if ($handle = opendir($dirname)) {
			while (false !== ($file = readdir($handle))) {
				foreach($packs as $pack)
					if (substr(strtolower($file), strlen($file)-strlen(".".$pack)) == strtolower(".".$pack)) {
						$file = $prefix . $file;
						$filelist[$file] = array('file'=>$file, 'type'=>$pack);
					}
			}
			closedir($handle);
		}
		return $filelist;
	}
}


if (!function_exists("getFontsListAsArray")) {
	function getFontsListAsArray($dirname, $prefix = '')
	{
		$formats = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-converted.diz'));
		$filelist = array();
		
		if ($handle = opendir($dirname)) {
			while (false !== ($file = readdir($handle))) {
				foreach($formats as $format)
					if (substr(strtolower($file), strlen($file)-strlen(".".$format)) == strtolower(".".$format)) {
						$file = $prefix . $file;
						$filelist[$file] = array('file'=>$file, 'type'=>$format);
					}
			}
			closedir($handle);
		}
		return $filelist;
	}
}


if (!function_exists("getArchivingShellExec")) {
	function getArchivingShellExec()
	{
		$ret = array();
		foreach(cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-archiving.diz')) as $values)
		{
			$parts = explode("||", $values);
			$ret[$parts[0]] = $parts[1];
		}
		return $ret;
	}
}

if (!function_exists("getExtractionShellExec")) {
	function getExtractionShellExec()
	{
		$ret = array();
		foreach(cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-extracting.diz')) as $values)
		{
			$parts = explode("||", $values);
			$ret[$parts[0]] = $parts[1];
		}
		return $ret;
	}
}

?>