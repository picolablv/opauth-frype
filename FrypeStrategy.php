<?php
/**
 * Frype strategy for Opauth
 * based on https://www.draugiem.lv/applications/dev/docs/passport/
 *
 * More information on Opauth: http://opauth.org
 *
 * @copyright    Copyright © 2014 SIA Rixtellab (http://rixtellab.com)
 * @link         http://rixtellab.com
 * @license      MIT License
 */

class FrypeStrategy extends OpauthStrategy{

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('app_key', 'app_id');

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'action' => 'userdata',
		'data_format' => 'json',
		'redirect_uri' => '{complete_url_to_strategy}int_callback'
	);

	/**
	 * Auth request
	 * Autorizācijas lapas saites adrese veidojas šādā formā:
	 * http://api.draugiem.lv/authorize/?app=[app]&hash=[hash]&redirect=[redirect]
	 */
	public function request(){
		$url = 'http://api.draugiem.lv/authorize/';
		$params = array(
			'redirect' => $this->strategy['redirect_uri'],
			'app' => $this->strategy['app_id'],
			'hash' => md5($this->strategy['app_key'].$this->strategy['redirect_uri'])
		);

		$this->clientGet($url, $params);
	}



	/**
	 * Internal callback - frype auth request
	 */
	public function int_callback()
	{
		if(array_key_exists('dr_auth_status', $_GET) and $_GET['dr_auth_status']!='failed')
		{
			if(array_key_exists('dr_auth_code', $_GET))
			{
				$url = 'http://api.draugiem.lv/'.$this->strategy['data_format'];

				// Authorize user and get user API key
				$params = array(
					'action' => 'authorize',
					'app' =>  $this->strategy['app_key'],
					'code' =>  trim($_GET['dr_auth_code'])
				);

				$response = $this->serverGet($url, $params, null, $headers);

				$results = $this->prepare($response);

				if(!empty($results) and !empty($results['users']))
				{
					$user = reset($results['users']);
					$this->auth = array(
						'provider' => 'Frype',
						'uid' => $user['uid'],
						'apikey' => $results['apikey'],
						'language' => $results['language'],
						'info' => array(
							'uid' => $user['uid'],
							'name' => $user['name'].' '.$user['surname'],
							'url' => $user['url'],
							'first_name' => $user['name'],
							'last_name' => $user['surname'],
							'image' =>  $user['img'],
							'imgi' =>  $user['imgi'],
							'imgm' =>  $user['imgm'],
							'imgl' =>  $user['imgl'],
							'urls' => array(
								'frype' => 'http://frype.com/'.$user['url'],
							),
						),
						'raw' => $user
					);
					if (!empty($user['nick'])) $this->auth['info']['nick'] = $user['nick'];
					if (!empty($user['emailHash'])) $this->auth['info']['emailHash'] = $user['emailHash'];
					if (!empty($user['place'])) $this->auth['info']['location'] = $user['place'];
					if (!empty($user['birthday'])) $this->auth['info']['birthday'] = $user['birthday'];
					if (!empty($user['sex'])) $this->auth['info']['sex'] = $user['sex'];
					if (!empty($user['age'])) $this->auth['info']['age'] = $user['age'];
					if (!empty($user['adult'])) $this->auth['info']['adult'] = $user['adult'];
					if (!empty($user['type'])) $this->auth['info']['type'] = $user['type'];
					if (!empty($user['deleted'])) $this->auth['info']['deleted'] = $user['deleted'];
					if (!empty($user['place'])) $this->auth['info']['place'] = $user['place'];

					$this->callback();
				}
				else
				{
					$error = array(
						'provider' => 'Frype',
						'code' => $results['error']['code'],
						'message' => $results['error']['description'],
						'raw' => $headers
					);

					$this->errorCallback($error);

				}

			}
		}
		else
		{
			$error = array(
				'provider' => 'Frype',
				'code' => $_GET['dr_auth_status'],
				'message' => 'User access denied',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}


	/**
	 * @param $response
	 * @return mixed
	 */
	private function prepare($response)
	{
		$data_format = $this->strategy['data_format'];
		switch($data_format)
		{
			case 'json':
			default:
				return json_decode($response, true);
		}
	}


}