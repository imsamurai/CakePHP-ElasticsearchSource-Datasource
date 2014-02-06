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

}
