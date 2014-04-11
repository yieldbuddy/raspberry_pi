<?php 

require_once (dirname (__FILE__) . '/sqlite_fulltextsearch.class.php');

class sqlite_fulltextsearchex extends sqlite_fulltextsearch {

	var $striptags = false;

	function __construct () {
		parent::__construct ();
		$this->striptags = false;
	}

	/* override */
	function prominence ($position, $string_words_count, $against_words_count) {
		// exponential prominence
		return (($position * $position) / $string_words_count);
	}

	/* override */
	function wordspreparation (&$string, &$against) {
		if ($this->striptags) {
			$string = preg_replace ('/<script.*?\>.*?<\/script>/si', ' ', $string); 
			$string = preg_replace ('/<style.*?\>.*?<\/style>/si', ' ', $string); 
			$string = preg_replace ('/<.*?\>/si', ' ', $string); 
			$string = html_entity_decode ($string, ENT_NOQUOTES, $GLOBALS['charset']);
		
			$against = preg_replace ('/<script.*?\>.*?<\/script>/si', ' ', $against); 
			$against = preg_replace ('/<style.*?\>.*?<\/style>/si', ' ', $against); 
			$against = preg_replace ('/<.*?\>/si', ' ', $against); 
			$against = html_entity_decode ($against, ENT_NOQUOTES, $GLOBALS['charset']);
		}
	}

}

?>