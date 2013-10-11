<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 *
 * @package ElasticsearchSource
 * @subpackage Datasource
 *
 */
class ElasticsearchConnection extends HttpSourceConnection {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $_config = array(
		'maxAttempts' => 1,
		'retryCodes' => array(429),
		'retryDelay' => 5 //in seconds
	);

	/**
	 * {@inheritdoc}
	 *
	 * @return int|string
	 */
	public function getTook() {
		try {
			$response = $this->_decode();
			$tookRemote = (float)Hash::get($response, 'took');
		} catch (Exception $Exception) {
			$tookRemote = 0;
		}
		return parent::getTook() . " ($tookRemote)";
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $request
	 * @return mixed false on error, decoded response array on success
	 */
	public function request($request = array()) {
		if (!empty($request['body'])) {
			$request['body'] = json_encode($request['body']);
		}

		return parent::request($request);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function _extractRemoteError() {
		try {
			$response = $this->_decode();
			return Hash::get($response, 'error');
		} catch (Exception $Exception) {
			return parent::getTook();
		}
	}


}