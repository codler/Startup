<?php 
class SU_Ui {
	function happy_date($date) {
		//$time = strtotime($date);
		$ago = time() - $date;
		
		// less then 5 seconds
		if ($ago < 5) {
			return "just now";
		}
		// less then 1 minute
		if ($ago < 60) {
			return $ago . " seconds ago";
		}
		// less then 1 hour
		if ($ago < 60*60) {
			return ceil($ago / 60) . " minutes ago";
		}
		// less then 1 day
		if ($ago < 24*60*60) {
			return ceil($ago / 60 / 60) . " hours ago";
		}
		// less then 1 week
		if ($ago < 7*24*60*60) {
			return ceil($ago / 24 / 60 / 60) . " days ago";
		}
		
		return date('Y-m-d',$date);
	}

	function table ($options) {

	/* 	$options['id'] // (required) source int
		$options['data'] // (required) source function($offset, $limit)
		$options['header'] // (required) source array
		$options['map'] // source function($row)
		$options['entries'] // (required) source int */
		$options['page'] = (isset($options['page'])) ? $options['page'] : 1; // default 1
		$options['limit'] = (isset($options['limit'])) ? $options['limit'] : 10; // default 10
		$options['no_records'] = (isset($options['no_records'])) ? $options['no_records'] : 'No records was found'; // default

		$data = r::parse($options['id'].':int:'.$options['page']);
		$options['page'] = $data[$options['id']];

		pager::set($options['entries'], $options['page'], $options['limit']);

		$rows = $options['data'](pager::db(), $options['limit']);
		if (is_callable($options['map']) && count($rows) > 0) {
			$rows = array_map($options['map'], $rows, range(0, count($rows)-1));
		}

		$html = '';
		if (count($rows) == 0) { 
			$html .= $options['no_records'];
		} else { 
			$html .= '<table id="' . $options['id'] . '"><thead><tr>';
			foreach($options['header'] AS $header) {
				$html .= '<th>' . $header . '</th>';
			}
			$html .= '</tr></thead>';
			if (pager::count() > 1) { 
				$html .= '<tfoot><tr><td colspan="' . count($options['header']) . '">';
				$query = $_GET;
				if (!pager::is_first()) { 
					$query[$options['id']] = pager::previous();
					$html .= '<a href="?' . http_build_query($query) . '">&lt;</a>';
				}
			
				for ($i = 1; $i <= pager::count(); $i++) {
					if (pager::get() == $i) {
						$html .= pager::get();
					} else { 
						$query[$options['id']] = $i;
						$html .= '<a href="?' . http_build_query($query) . '">' . $i . '</a>';
					}
				}
				
				if (!pager::is_last()) { 
					$query[$options['id']] = pager::next();
					$html .= '<a href="?' . http_build_query($query) . '">&gt;</a>';
				}
				
				$html .= '</td></tr></tfoot>';
			}

			$html .= '<tbody>';
			
			foreach($rows AS $row) { 
				$html .= '<tr>';
				foreach($options['header'] AS $k => $v) {
					$html .= '<td>' . $row[$k] . '</td>';
				}
			} 
			$html .= '</tbody></table>';
		}
		return $html;
	}


	// add media files like css or js
	function add_external($url,$type=null) {
		global $externalResource;
		
		if (file_exists(BASE_DIR . $url)) {
			$url .= '?' . filemtime(BASE_DIR . $url);
		}
		
		if ($type==null) {
			$type = a::last(explode('.', $url));
		}
		
		switch ($type) {
			case 'css':
				$externalResource[$type][] = '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
				break;
			case 'js':
				$externalResource[$type][] = '<script src="'.$url.'"></script>';
		}
	}

	function load_external($type='all') {
		global $externalResource;
		if (!is_array($externalResource)) return;
		$htmlExternal = '';
		if ($type=='all') {
			if (array_key_exists('css',$externalResource)) {
				// css first
				$htmlExternal .= "\n\t<!-- Assets - CSS -->\n\t";
				$htmlExternal .= implode("\n\t",$externalResource['css']);
				$htmlExternal .= "\n\t";
			}
			if (array_key_exists('js',$externalResource)) {
				// js second
				$htmlExternal .= "\n\t<!-- Assets - Javascript -->\n\t";
				$htmlExternal .= implode("\n\t",$externalResource['js']);
				$htmlExternal .= "\n\t";
			}
			// remove from array
			unset($externalResource['js']);
			unset($externalResource['css']);
			
			// others
			foreach ($externalResource AS $key => $value) {
				$htmlExternal .= "\n\t<!-- Assets - ".$key." -->\n\t";
				$htmlExternal .= implode("\n\t",$externalResource[$key]);
				$htmlExternal .= "\n\t";
			}
		} else {
			// user specifik type
			$htmlExternal .= "\n\t<!-- Assets - ".$type." -->\n\t";
			$htmlExternal .= implode("\n\t",$externalResource[$type]);
			$htmlExternal .= "\n\t";
		}
		echo $htmlExternal;
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	function gravatar( $email, $s = 80, $d = 'identicon', $r = 'pg', $img = false, $atts = array() ) {
		$url = 'https://secure.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}


	function number($number) {
		return number_format($number, 0, ',', ' ');
	}
	
	function yesno($string) {
		return ($string) ? 'Yes' : 'No';
	}
	
	/**
	 * Converts a value in php.ini to bytes
	 *
	 * @param string $val Input example 10M, 1G, 128K
	 * @return int Number of bytes
	 */
	function to_bytes($val) {
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}
	
}
?>