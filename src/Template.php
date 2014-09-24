<?php

class Template {
	const BINDING_PATTERN = "/(?:{{\s*)([\d\s\w][^{}]*)(?:\s*}})/i";

	private $bindings = array();
	private $data = array();
	private $filename = "";
	public $isFile = false;
	public $templateSet = false;
	public $dataSet = false;
	private $output = "";
	public $template = "";

	public function __construct() {
		$args = func_get_args();

		if ( empty( $args ) ) {
			return $this;
		}

		foreach ( $args as $key => $value ) {

			// If argument is a string
			if ( isset( $args[$key] ) && is_string( $args[$key] ) ) {
				$this->setTemplate( $args[$key] );
			}

			// If argument is an array
			if ( isset( $args[$key] ) && is_array( $args[$key] ) ) {
				$this->setData( $args[$key] );
			}

		}
		

	}

	private function getBindings () {
		preg_match_all( self::BINDING_PATTERN, $this->template, $matches );
		foreach( $matches[1] as $key => $val ) {
			array_push( $this->bindings, trim( $val ) );
		}
		return $this->bindings;
	}

	public function getOutput () {
		if ( $this->templateSet && $this->dataSet ) {
			$this->output = $this->template;
			foreach( $this->bindings as $str ) {
				if ( isset( $this->data[$str] ) ) {
					$this->output = preg_replace( "/{{\s*" . preg_quote( $str ) . "*\s*}}/i", $this->data[$str], $this->output );
				}
			}
			return $this->output;
		}

		if ( !$this->templateSet && !$this->dataSet ) {	$exception = "Warning: getOutput() requires a template and data, neither given"; }
		else if ( !$this->templateSet && $this->dataSet ) { $exception = "Warning: getOutput() requires a template, template not given"; }
		else if ( $this->templateSet && !$this->dataSet ) { $exception = "Warning: getOutput() requires data, data not given"; }
		if ( isset( $exception ) ) { throw new TemplateException ( $exception ); }

		return false;
	}

	public function setData ( $data ) {
		if ( is_array( $data ) ) {
			if ( is_associative_array( $data ) ) {
				$this->dataSet = true;
				$this->data = $data;
			}
		}
	}

	public function setTemplate ( $str ) {
		if ( is_string( $str ) ) {
			if ( is_readable( $str ) ) {
				$this->isFile = true;
				$this->filename = $str;
				$fileContents = file_get_contents( $this->filename );
				// Regular expression searches for bindings
				if ( preg_match( self::BINDING_PATTERN, $fileContents ) ) {
					$this->templateSet = true;
					$this->template = $fileContents;
				}
			} else if ( preg_match( self::BINDING_PATTERN, $str ) ) {
				$this->templateSet = true;
				$this->template = $str;
			}
		}

		if ( !empty( $this->template ) ) {
			$this->getBindings();
			return $this->template;
		}

		return false;
	}
}

// Create custom exception for template errors
class TemplateException extends Exception {
	public function __construct( $message, $code = 0, Exception $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}

function is_associative_array ( $arr ) {
	return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
}

?>
