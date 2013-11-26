<?PHP

/////////////////////////////////////////
///////// PHP Useful Stuff library //////
/// (c) Internet Media Group Ltd 2006 ///
/////////////////////////////////////////



define ("PHP_TRUE",  1);
define ("PHP_FALSE", 0); 

class utils
{

	/** 
	 * Converts a DB date format to an array with variety of format options
	 * @param myDate  - A DB date
	 * @return - An array with a variety of date formats
	 */
	function formatDate($myDate)
	{
		$myDate = strtotime($myDate);			
		//Formats the Date
		$dateArray[0] = date('jS F Y', $myDate);
		$dateArray[1] = date('j/m/Y', $myDate);
		$dateArray[2] = date('jS M Y g:i a', $myDate);	
		$dateArray[3] = date('j/m/Y g:i a', $myDate);
		$dateArray[4] = date('jS M Y', $myDate);			
		
		return $dateArray;
	}
	
	
	
	function getCurrentDate()
	{
		$date = date("Y-m-d H:i:s");
		return $date;
	}
	/**
	Validate an email address.
	Provide email address (raw input)
	Returns true if the email address has the email 
	address format and the domain exists.
	*/
	function validEmail($email)
	{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)
	   {
		  $isValid = false;
	   }
	   else
	   {
		  $domain = substr($email, $atIndex+1);
		  $local = substr($email, 0, $atIndex);
		  $localLen = strlen($local);
		  $domainLen = strlen($domain);
		  if ($localLen < 1 || $localLen > 64)
		  {
			 // local part length exceeded
			 $isValid = false;
		  }
		  else if ($domainLen < 1 || $domainLen > 255)
		  {
			 // domain part length exceeded
			 $isValid = false;
		  }
		  else if ($local[0] == '.' || $local[$localLen-1] == '.')
		  {
			 // local part starts or ends with '.'
			 $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $local))
		  {
			 // local part has two consecutive dots
			 $isValid = false;
		  }
		  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		  {
			 // character not valid in domain part
			 $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $domain))
		  {
			 // domain part has two consecutive dots
			 $isValid = false;
		  }
		  else if
	(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
					 str_replace("\\\\","",$local)))
		  {
			 // character not valid in local part unless 
			 // local part is quoted
			 if (!preg_match('/^"(\\\\"|[^"])+"$/',
				 str_replace("\\\\","",$local)))
			 {
				$isValid = false;
			 }
		  }
		  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
		  {
			 // domain not found in DNS
			 $isValid = false;
		  }
	   }
	   return $isValid;
	}
	
	function br2nl($text, $tags="br")
	{
		$tags = explode(" ", $tags);
		
			foreach($tags as $tag)
			{
				$text = eregi_replace("<" . $tag . "[^>]*>", "\n", $text);
				$text = eregi_replace("]*>", "\n", $text);
			}
		
		return($text);
	}
	
	
	function truncateText($string, $max = 20, $replacement = '')
	{
		if (strlen($string) <= $max)
		{
			return $string;
		}
		$leave = $max - strlen ($replacement);
		return substr_replace($string, $replacement, $leave);
	}
	
	function limitWords($str, $limit = 100, $end_char = '&#8230;') {
		
		if (trim($str) == '')
			return $str;
		
		preg_match('/\s*(?:\S*\s*){'. (int) $limit .'}/', $str, $matches);
	
		if (strlen($matches[0]) == strlen($str))
			$end_char = '';
	
		return rtrim($matches[0]) . $end_char;
	}
	
	
	
	
	function convertTextFromDB($input)
	{
		$input = stripslashes($input);
		$input = html_entity_decode($input, ENT_QUOTES, 'ISO-8859-1');	
		
		return $input;
	}
	
	function dateDiff($startDate, $endDate)
	{
		$secondsSinceResponse = $endDate-$startDate;
		
		$daysResponse = $secondsSinceResponse / 86400;
		$daysResponse = number_format($daysResponse, 0);
		
		$daysResponse = $secondsSinceResponse / 86400;
		$daysResponse = floor($daysResponse);
		
		$temp_remainder = $secondsSinceResponse - ($daysResponse * 86400);
		$hours = floor($temp_remainder / 3600);
		
		$temp_remainder = $temp_remainder - ($hours * 3600);
		$minutes = round($temp_remainder / 60, 0);
		
		if($daysResponse==0)
		{
			if($hours==0)
			{
				$dateDiff= '< 1 hour';
			}
			else
			{
				$dateDiff= $hours.' hour(s)';									
			}
	
		}
		else
		{
			$dateDiff=$daysResponse.' day(s)';
		}
		
		return $dateDiff;
	}
	
	/**
	 * Returns a random string of numbers and letters [0-9][A-Z]
	 * Note: This is not secure random
	 * @param $chars The number of characters in the random string
	 * @return String containing random numbers and letters
	 */
	function randomString ($chars)
	{
		$randStr = "";
		
		while ($chars > 0) {
			$ord = rand(48, 90);
			if (($ord >= 48 && $ord <=57) || ($ord >= 65 && $ord <=90)) {
				$randStr .= chr($ord);
				$chars -= 1;
			}
		}
	
		return $randStr;
	}
	
	

	function getCurrentUsername() {
		// Get the current user's info 
		$current_user = wp_get_current_user(); 
	
		if ( !($current_user instanceof WP_User) ) 
			return; 
	
		return $current_user->user_login; 
	}	
	

	/**
	 * Returns the file extension of the given filename
	 * E.g. 'Test.php' --> 'php'
	 * @param $fname The filename to chop
	 * @return String containing the file extension
	 */
	function getFileExtension ($fname)
	{
			//$i = strpos($fname, '.'); 
			//return (substr($fname, $i+1));	
	
			return strtolower(substr(strrchr($fname, "."), 1));
	}	
	
	

}
?>