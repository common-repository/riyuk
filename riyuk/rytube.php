<?php
final class rYtube {
	
	private static $_instance = null;
	protected $_data = array();
	protected $_jquery = false;
	protected $_js = false;
	
	public static function getInstance() {
		if( self::$_instance === null ) self::$_instance = new self();
		return self::$_instance;
	}
	
	public function __call( $name, $args ) {
		self::getInstance()->_handleCalls( $name, $args, 'this' );
	}
	
	public static function __callStatic( $name, $args ) {
		self::getInstance()->_handleCalls( $name, $args, 'static' );
	}
	
	private function _handleCalls( $name, $args, $type ) {
		switch( $name ) {
			case 'filterContent':
				
				if( !$this->_jquery ) break;
				
				$content = current( $args );
				
				preg_match_all( '#<a href="http://www.youtube.com/watch\?v=([^"& ]+)[^>]*>([^<]+)</a>#', $content, $matches );
				if( is_array( $matches[0] ) && count( $matches[0] ) ) {
					for( $i = 0; $i < count( $matches[0] ); $i++ ) {
						if( strstr( $content, '<p>' . $matches[0][$i] . '</p>' ) ) {
						
							$newStr = sprintf( '<div class="youtube"><div class="preview" style="float: left; width: 140px;"><img src="http://img.youtube.com/vi/%s/2.jpg" /></div><div style="float: left;" class="link">%s</div><br clear="all"/><div class="media" style="display: none;"><embed type="application/x-shockwave-flash" src="http://www.youtube.com/v/%s&amp;autoplay=0" width="460" height="259" style="" bgcolor="#FFFFFF" quality="high" allowfullscreen="false" allowscriptaccess="never" salign="tl" scale="noscale" wmode="opaque" flashvars="width=460&amp;height=259"></div></div>', $matches[1][$i], $matches[0][$i], $matches[1][$i] );
							$content = str_replace( '<p>' . $matches[0][$i] . '</p>', $newStr, $content );
						
							$jquery = true;
						}
					}
					
				}
				
				echo $content;
				
				if( !$this->_js ) {
					// only load once
					$this->_js = true;
					
					$effect = get_option('ryt-effect');
					if( !strlen( $effect ) ) $effect = 'toggle';
					else $effect .= 'Toggle';
					
					// falls videos gefunden wurden brauchen wir den js für die youtube divs
					?><script type="text/javascript">
					jQuery.fn.fadeToggle = function(speed, easing, callback) { 
					    return this.animate({opacity: 'toggle'}, speed, easing, callback); 
					};
					jQuery(document).ready( function($) {
						$('div.youtube a').click( function( event ) {
							event.preventDefault();
							
							youtube = $(this).parent().parent();
							
							mediadiv = youtube.find( 'div.media' );
							//divs = youtube.find( 'div.preview, div.link');
							previewdivs = youtube.find( 'div.preview');
							
							previewdivs.fadeToggle();
							mediadiv.<?= $effect ?>();
							
						});
					});
					</script>
					<?php
				
					/*
					$short_url = @get_post_meta( get_the_ID(), '_short_url', true );
					// OR
					$short_url = $post->short_url;
					if( strlen( $short_url ) ) {
						echo '<p><span style="font-size: 10px;">Short URL: <a href="' . $short_url . '">' . $short_url . '</a></span></p>';
					}
					*/
					
				}
				return true;
				break;
			case 'actionInit':
				#wp_deregister_script( 'jquery' );
			    #wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', array(), '1.4.2' );
				wp_enqueue_script( 'jquery' );
				$this->_jquery = true;
				
				return;
				break;
			case 'actionPost':
				$post = $this->_bitly( current( $args ) );
				return $post;
				break;
			case 'actionAdmin':
			
				add_menu_page( 'RIYUK Options', 'RIYUK', 'administrator', 'riyuk', array( 'rYtube', 'adminMenu' ) );
				add_submenu_page( 'riyuk', 'RIYUK Options', 'Settings', 'administrator', 'riyuk', array( 'rYtube', 'adminMenu' ) );
				
				add_action( 'admin_init', array( 'rYtube', 'registerOptions' ) );
				
				return;
				break;
			case 'registerOptions':
				
				register_setting( 'ryt-settings-group', 'ryt-youtube' );  
				register_setting( 'ryt-settings-group', 'ryt-effect' );  
				register_setting( 'ryt-settings-group', 'ryt-bitly-username' );
				register_setting( 'ryt-settings-group', 'ryt-bitly-api' );
				
				return;
				break;
		}
		
		throw new Exception( 'invalid call: ' . $name );

	}
	
	
	public function __get( $name ) {
		if( isset( $this->_data[ $name ] ) ) return $this->_data[ $name ];
		
		return null;
	}
	
	public function __isset( $name ) {
		return isset( $this->_data[ $name ] );
	}
	
	public function __unset( $name ) {
		if( isset( $this->_data[ $name ] ) ) unset( $this->_data[ $name ] );
	}
	
	public function __set( $name, $value ) {
		$this->_data[ $name ] = $value;		
	}
	
	/**
	 * sprintfn
	 *
	 * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
	 *
	 * with sprintfn: sprintfn('second: %(second)s ; first: %(first)s', array(
	 *  'first' => '1st',
	 *  'second'=> '2nd'
	 * ));
	 *
	 * @see http://de.php.net/manual/de/function.sprintf.php#94608
	 * @param string $format sprintf format string, with any number of named arguments
	 * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
	 * @return string|false result of sprintf call, or bool false on error
	 * @throws Exception
	 **/
	public static function sprintfn( $format, array $args ) {
	    // map of argument names to their corresponding sprintf numeric argument value
	    $arg_nums = array_slice( array_flip( array_keys( array( 0 => 0 ) + $args ) ), 1 );
	
	    // find the next named argument. each search starts at the end of the previous replacement.
	    for( $pos = 0; preg_match( '/(?<=%)\(([a-zA-Z_]\w*)\)/', $format, $match, PREG_OFFSET_CAPTURE, $pos ); ) {
	        $arg_pos = $match[0][1];
	        $arg_len = strlen( $match[0][0] );
	        $arg_key = $match[1][0];
	
	        // programmer did not supply a value for the named argument found in the format string
	        if ( !array_key_exists( $arg_key, $arg_nums ) ) {
	            throw new Exception( self::__( "rytube::sprintfn(): Missing argument '%(arg_key)s'", array( 'arg_key' => $arg_key ) ) );
	            return false;
	        }
	
	        // replace the named argument with the corresponding numeric one
	        $format = substr_replace( $format, $replace = $arg_nums[$arg_key] . '$', $arg_pos, $arg_len );
	        $pos = $arg_pos + strlen( $replace ); // skip to end of replacement for next iteration
	    }
	
	    return vsprintf( $format, array_values( $args ) );
	}
	
	
	public static function __( $str, array $args = array() ) {
		return __( self::sprintfn( $str, $args ), 'riyuk' );
	}
	
	public static function adminMenu() {
	
		if ( !current_user_can( 'manage_options' ) )  {
	    	wp_die( self::__( "You do not have sufficient permissions to access this page.", array() ) );
	  	}
		
		include_once( R_PLUGIN_DIR . 'admin.php' );
	}
	
	public function createBitlyUrl( $url, $format = 'xml', $version = '2.0.1' ) {
		
		$bitlyAccount = get_option( 'ryt-bitly-username' );
		$bitlyApi = get_option( 'ryt-bitly-api' );
		if( !strlen( $bitlyAccount ) || !strlen( $bitlyApi ) ) return '';
		
		$bitly = 'http://api.bit.ly/shorten?version=' . $version . '&longUrl=' . urlencode( $url );
		$bitly .= '&login=' . $bitlyAccount . '&apiKey=' . $bitlyApi . '&format=' . $format;  
		
		$response = file_get_contents( $bitly );  
		
		//parse depending on desired format  
		if( strtolower( $format ) == 'json' ) {  
			$json = @json_decode( $response, true );
		    return $json['results'][ $url ]['shortUrl'];
		} else {  
		    $xml = @simplexml_load_string( $response );
		    if( $xml->statusCode == 'OK' && isset( $xml->results->nodeKeyVal->shortUrl ) ) {
		    	return current( $xml->results->nodeKeyVal->shortUrl );	
		    }
		}
		 return ''; 
	}
	
	private function _bitly( $post ) {
		if( !is_object( $post ) || !isset( $post->ID ) ) throw new Exception( self::__( 'Invalid call of bitly! Post is not a obj.', array() ) );
		
		if( get_post_meta( $post->ID, '_short_url', true ) != '' ) {   
	        //Short URL already exists, pull from post meta
	        $bitly = get_post_meta( $post->ID, '_short_url', true );  
	    } else {
	        //No short URL has been made yet  
	        $link = get_permalink();
	        $bitly = $this->createBitlyUrl( $link );
	        
	        if( is_string( $bitly ) && strlen( $bitly ) ) {
		        //Save generated short url for future views  
		        add_post_meta( $post->ID, '_short_url', $bitly, true) or update_post_meta( $post->ID, '_short_url', $bitly );
		    }
	    }
	    
	    if( !isset( $post->short_url ) || !strlen( $post->short_url ) ) $post->short_url = $bitly;
		
		return $post;
	}
	
		
}
