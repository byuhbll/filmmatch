<?php

// NO WARRANTY OR SUPPORT OF ANY KIND IS PROVIDED FOR THIS SOFTWARE.

require_once('../inc/include.php');

class Util {

	/**
	 *
	 */
	public static function fatal($message) {
		exit("<p>" . ERROR_MESSAGE . "</p>\n<p style=\"display: none\">$message</span></p>");
	}

}

?>
