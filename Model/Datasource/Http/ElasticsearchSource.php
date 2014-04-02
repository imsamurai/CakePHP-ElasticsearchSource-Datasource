<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: https://github.com/imsamurai/cakephp-httpsource-datasource
 *
 * @package ElasticsearchSource
 * @subpackage Datasource
 */
App::uses('HttpSource', 'HttpSource.Model/Datasource');
App::uses('ElasticsearchConnection', 'ElasticsearchSource.Model/Datasource');

/**
 * Elasticsearch DataSource
 *
 * @package ElasticsearchSource
 * @subpackage Datasource
 */
class ElasticsearchSource extends HttpSource {

	/**
	 * Http methods constants
	 */
	const HTTP_METHOD_CREATE = 'POST';
	const HTTP_METHOD_UPDATE = 'PUT';

	/**
	 * Maximum log length
	 */
	const LOG_MAX_LENGTH = 1000;

	/**
	 * Elasticsearch API Datasource
	 *
	 * @var string
	 */
	public $description = 'ElasticsearchSource DataSource';

	/**
	 * last request status
	 *
	 * @var string
	 */
	protected $_requestStatus = array();

	/**
	 * {@inheritdoc}
	 *
	 * @param array $config
	 * @param HttpSourceConnection $Connection
	 */
	public function __construct($config = array(), HttpSourceConnection $Connection = null) {
		parent::__construct($config, new ElasticsearchConnection($config));
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Model $Model
	 * @param array $result
	 */
	protected function _emulateOrder(Model $Model, array &$result) {
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Model $Model
	 * @param array $result
	 */
	protected function _emulateLimit(Model $Model, array &$result) {
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Model $model
	 * @param array $result
	 * @param string $requestMethod
	 * @param bool $force
	 * @return array
	 */
	protected function _extractResult(Model $model, array $result, $requestMethod, $force = false) {
		//save read info: score, total
		$this->_requestStatus[$requestMethod] = array();
		if (static::METHOD_READ == $requestMethod) {
			$getinfo = array('took', 'timed_out', '_shards');
			$gethits = array('total', 'max_score');
			foreach ($getinfo as $indexName)
				if (isset($result[$indexName])) {
					$this->_requestStatus[$requestMethod][$indexName] = $result[$indexName];
				}
			if (isset($result['hits'])) {
				if(isset($result['hits']['hits'])){
					$this->affected = count($result['hits']['hits']);
				}
				foreach ($gethits as $indexName)
					if (isset($result['hits'][$indexName])) {
						$this->_requestStatus[$requestMethod][$indexName] = $result['hits'][$indexName];
					}
			}
		} else {
			//other info like delete, update
			$this->_requestStatus[$requestMethod] = $result;
		}

		return parent::_extractResult($model, $result, $requestMethod, true);
	}

	/**
	 * Get total hits from search result.
	 *
	 * @return int
	 */
	public function lastCandidates() {
		return (int)isset($this->_requestStatus['read']['total']) ? $this->_requestStatus['read']['total'] : 0;
	}

	/**
	 * Get search time.
	 *
	 * @return int
	 */
	public function timeTook() {
		return (int)isset($this->_requestStatus['read']['took']) ? $this->_requestStatus['read']['took'] : 0;
	}

	/**
	 * Log given query.
	 *
	 * @return void
	 */
	public function logRequest() {
		$this->numRows = $this->lastCandidates();
		parent::logRequest();
	}

	/**
	 * Log given query.
	 *
	 * @return void
	 */
	public function logPrepare() {
		$log = array(
			'query' => strlen($this->query) > static::LOG_MAX_LENGTH ? substr($this->query, 0, static::LOG_MAX_LENGTH) . ' ' . static::LOG_TRUNCATED : $this->query,
			'affected' => $this->affected,
			'numRows' => $this->numRows,
			'took' => $this->took,
		);
		$http_parts = explode('HTTP/', $this->query);

		$get_parts = explode('GET ', $http_parts[0]);
		if(isset($get_parts[1]) && isset($http_parts[1])){
			$host_parts = explode('Host:', $http_parts[1]);
			$request_parts = explode('Content-Length: ', $http_parts[1]);
			$http_request = '';
			if(isset($request_parts[1]) && false !== strpos($request_parts[1], '{')){
				$http_request = strstr($request_parts[1], '{');
			}
			$connection_parts = explode('Connection:', $host_parts[1]);
			$get_request = trim($get_parts[1]);

			$http_request = $get_request.(false !== strpos($get_request, '?') ? '&source=' : '?source=').htmlentities($http_request);
			$explain_request = $http_request.'&explain=true';

			//$log['took'] .= '<br/><a href="http://'.trim($connection_parts[0]).$explain_request.'"><b>Explain</b></a>';
			//$log['took'] .= '<br/><a href="http://'.trim($connection_parts[0]).$http_request.'"><b>Request</b></a>';
		}
		$this->_logRow = $log;
	}
}
