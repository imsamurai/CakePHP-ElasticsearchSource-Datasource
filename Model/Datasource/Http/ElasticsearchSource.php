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
				if (isset($result['hits']['hits'])) {
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
		$httpParts = explode('HTTP/', $this->query);

		$getParts = explode('GET ', $httpParts[0]);
		if (isset($getParts[1]) && isset($httpParts[1])) {
			$hostParts = explode('Host:', $httpParts[1]);
			$requestParts = explode('Content-Length: ', $httpParts[1]);
			$httpRequest = '';
			if (isset($requestParts[1]) && false !== strpos($requestParts[1], '{')) {
				$httpRequest = strstr($requestParts[1], '{');
			}
			$connectionParts = explode('Connection:', $hostParts[1]);
			$getRequest = trim($getParts[1]);

			$httpRequest = $getRequest . (false !== strpos($getRequest, '?') ? '&source=' : '?source=') . htmlentities($httpRequest);
			$explaineRequest = $httpRequest . '&explain=true';

			//$log['took'] .= '<br/><a href="http://'. trim($connectionParts[0]) . $explaineRequest. '"><b>Explain</b></a>';
			//$log['took'] .= '<br/><a href="http://'. trim($connectionParts[0]) . $httpRequest. '"><b>Request</b></a>';
		}
		$this->_logRow = $log;
	}
}
