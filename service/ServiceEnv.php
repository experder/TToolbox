<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

use tt\debug\Error;

class ServiceEnv {

	/**
	 * @var bool $request_is_ajax Otherwise HTML
	 */
	public static $response_is_expected_to_be_json = false;

	/**
	 * instanceof without instanziation
	 * @param string $subclass
	 * @param string $superclass
	 * @return bool
	 */
	public static function reflectionInstanceof($subclass, $superclass){
		try {
			$ref_sub = new \ReflectionClass($subclass);
			$ref_super = new \ReflectionClass($superclass);
		} catch (\ReflectionException $e) {
			Error::fromException($e);
			exit;
		}
		return $ref_sub->isSubclassOf($ref_super);
	}

	public static function isSapiCLI(){
		return php_sapi_name() == 'cli';
	}

	/**
	 * @return bool Output buffer is not empty. Returns false if no buffering is active.
	 */
	public static function responseSent(){
		return ob_get_length()!=0;
	}

}