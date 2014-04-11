<?php 

class sqlite_fulltextsearch {

	var $_againsts_cache = array ();
	
	var $use_against_cache = false;
	var $word_separators_pattern = "/[\s\.,'\"\/\|]+/";
	
	function __construct () {
		$this->_againsts_cache = array ();
		$this->use_against_cache = false;
	}

	function wordspreparation (&$string, &$against) {
		// do nothing here, override in subclasses, see sqlite_fulltextsearchex example
	}
	
	function _wordspreparation ($string, $against, $usecase, &$string_words, &$against_words, &$string_words_count, &$against_words_count) {
		if (!$usecase) {
			$string  = strtolower ($string);
			$against = strtolower ($against);
		}	
		
		$this->wordspreparation	($string, $against);
		
		$string_words  = preg_split ("/ +/", preg_replace ($this->word_separators_pattern, ' ', $string));
		$against_words = null;

		if ($this->use_against_cache && isset ($this->_againsts_cache[$against])) {
			$against_words = $this->_againsts_cache[$against];	
		} else {
			$against_words = preg_split ("/ +/", preg_replace ($this->word_separators_pattern, ' ', $against));
			if ($this->use_against_cache) {
				$this->_againsts_cache[$against] = $against_words;
			}
		}
		
		$string_words_count  = count ($string_words);
		$against_words_count = count ($against_words);
		
		return true;	
	}

	function prominence ($position, $string_words_count, $against_words_count) {
		/* lenear prominence */
		return $position;
	}

	function _internal_wordprominence ($string_words, $against_words, $string_words_count, $against_words_count) {

		if ($string_words_count == 0) {
			return 0;
		}
				
		$global_prominance = 0;
		foreach ($against_words as $against_word) {
			foreach ($string_words as $position => $string_word) {
				$position++;
				if ($string_word == $against_word) {
					$global_prominance += $this->prominence ($position, $string_words_count, $against_words_count);
				}
			}
		}
		
		$divisor = ($string_words_count + 1) * ($string_words_count / 2); // sum of N integer numbers from 1 to N
		
		$result = $global_prominance / $divisor;		
		
		return $result; 		
	}

	function _wordprominence ($string, $against, $usecase) {
		
		$this->_wordspreparation ($string, $against, $usecase, $string_words, $against_words, $string_words_count, $against_words_count);
		
		$string_words = array_reverse ($string_words);

		$result = $this->_internal_wordprominence ($string_words, $against_words, $string_words_count, $against_words_count);
		
		return $result;
	}

	function _reversewordprominence ($string, $against, $usecase) {

		$this->_wordspreparation ($string, $against, $usecase, $string_words, $against_words, $string_words_count, $against_words_count);
		
		$result = $this->_internal_wordprominence ($string_words, $against_words, $string_words_count, $against_words_count);
		
		return $result;
	}
	
	function _centerwordprominence ($string, $against, $usecase) {
		
		$this->_wordspreparation ($string, $against, $usecase, $string_words, $against_words, $string_words_count, $against_words_count);
		
		/* begin centering keywords, from 'a, b, c, d, e' to 'c, d, b, e, a' */
		$start = ceil ($string_words_count / 2) - 1;
		$left = $start;
		$right = $start;
		$centered_string_words = array ();
		$centered_string_words[] = $string_words[$start];
		while (($left > 0) && ($right < $string_words_count)) {
			$left--;
			$right++;
			$centered_string_words[] = $string_words[$right];
			$centered_string_words[] = $string_words[$left];
		}
		
		if ($right < $string_words_count - 1) {
			$centered_string_words[] = $string_words[$string_words_count - 1];
		}
		/* end centering keywords */

		$centered_string_words = array_reverse ($centered_string_words);

		$result = $this->_internal_wordprominence ($centered_string_words, $against_words, $string_words_count, $against_words_count);
		
		return $result;
	}
	
	function _fulltextsearch ($string, $against, $usecase) {
		
		$result = 0;
	
		$this->_wordspreparation ($string, $against, $usecase, $string_words, $against_words, $string_words_count, $against_words_count);
		
		if ($string_words_count == 0) {
			return $result;
		}
				
		$string = ' ' . implode (' ', $string_words) . ' ';
		$against_ex = ' ';
		for ($from = 0; $from < $against_words_count; $from++) {
			$against_ex .= $against_words[$from] . ' ';
			$result += (substr_count ($string, $against_ex) * ($from + 1)) / $string_words_count; 
		}	
	
		return $result;
	}
	
	function register (&$dbhandle) {
		if(is_object($dbhandle)) {
			$dbhandle->create_function ('fulltextsearch',        array (&$this, '_fulltextsearch'),        3); 	
			$dbhandle->create_function ('wordprominence',        array (&$this, '_wordprominence'),        3); 	
			$dbhandle->create_function ('reversewordprominence', array (&$this, '_reversewordprominence'), 3); 	
			$dbhandle->create_function ('centerwordprominence',  array (&$this, '_centerwordprominence'),  3); 	
		}
	}

}

?>