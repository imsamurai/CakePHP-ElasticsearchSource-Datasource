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

/**
 * Elasticsearch Connection
 *
 * @package ElasticsearchSource
 * @subpackage Config
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
	 * Candidates count
	 *
	 * @var int 
	 */
	protected $_candidates;

	/**
	 * {@inheritdoc}
	 *
	 * @return int
	 */
	public function getCandidates() {
		return $this->_candidates;
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @param mixed $result
	 * @return int|string
	 */
	public function getNumRows($result) {
		return parent::getNumRows($result) . " ({$this->getCandidates()})";
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

		$response = parent::request($request);
		$this->_affected = count((array)Hash::get((array)$response, 'hits.hits'));
		$this->_took .= " (" . (float)Hash::get((array)$response, 'took') . ")";
		$this->_candidates = (int)Hash::get((array)$response, 'hits.total');
		return $response;
	}

	/**
	 * Reset state variables
	 */
	public function reset() {
		parent::reset();
		$this->_candidates = null;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function _extractRemoteError() {
		return Hash::get((array)$this->_lastResponse, 'error');
	}

}
