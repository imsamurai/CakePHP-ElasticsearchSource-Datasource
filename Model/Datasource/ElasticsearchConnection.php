<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 */

App::uses('HttpSourceConnection', 'HttpSource.Model/Datasource');

/**
 * Elasticsearch Connection
 *
 * @package ElasticsearchSource
 * @subpackage Model.Datasource
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
	 * Scroll id
	 *
	 * @var string 
	 */
	protected $_scrollId;

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
	 * @return string
	 */
	public function getScrollId() {
		return $this->_scrollId;
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
		$this->_scrollId = (string)Hash::get((array)$response, '_scroll_id');
		return $response;
	}

	/**
	 * Reset state variables
	 */
	public function reset() {
		parent::reset();
		$this->_candidates = null;
		$this->_scrollId = null;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function _extractRemoteError() {
		try {
			return Hash::get((array)$this->_decode(), 'error');
		} catch (Exception $E) {
			return 'Unknown error, response: ' . $this->_Response;
		}
	}
}
