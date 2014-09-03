<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: https://github.com/imsamurai/cakephp-httpsource-datasource
 */
App::uses('HttpSource', 'HttpSource.Model/Datasource');
App::uses('ElasticsearchConnection', 'ElasticsearchSource.Model/Datasource');

/**
 * Elasticsearch DataSource
 *
 * @package ElasticsearchSource
 * @subpackage Model.Datasource.Http
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
	 * Result candidates
	 *
	 * @var int
	 */
	protected $_candidates = 0;

	/**
	 * {@inheritdoc}
	 *
	 * @param array $config
	 * @param HttpSourceConnection $Connection
	 */
	public function __construct($config = array(), HttpSourceConnection $Connection = null) {
		parent::__construct($config, $Connection ? $Connection : new ElasticsearchConnection($config));
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
	protected function _extractResult(Model $model, array $result, $requestMethod, $force = true) {
		return parent::_extractResult($model, $result, $requestMethod, $force);
	}

	/**
	 * Get total hits from search result.
	 *
	 * @return int
	 */
	public function lastCandidates() {
		return $this->_candidates;
	}

	/**
	 * Get search time.
	 *
	 * @return int
	 */
	public function timeTook() {
		return $this->took;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $request query
	 *
	 * @return mixed results for query if it is cached, false otherwise
	 */
	public function getQueryCache(array $request) {
		$cache = parent::getQueryCache($request);
		if ($cache === false) {
			return false;
		}
		$this->_candidates = $cache['_candidates'];
		return $cache['data'];
	}

	/**
	 * Sends HttpSocket requests. Builds your uri and formats the response too.
	 *
	 * @param Model $model Model object
	 * @param mixed $requestData Array of request or string uri
	 * @param string $requestMethod read, create, update, delete
	 *
	 * @return array|false $response
	 */
	public function request(Model $model = null, $requestData = null, $requestMethod = HttpSource::METHOD_READ) {
		$this->_candidates = 0;
		return parent::request($model, $requestData, $requestMethod);
	}

	/**
	 * Single request
	 *
	 * @param array $request
	 * @param string $requestMethod
	 * @param Model $model
	 * @return array|bool
	 */
	protected function _singleRequest(array $request, $requestMethod, Model $model = null) {
		$response = parent::_singleRequest($request, $requestMethod, $model);
		$this->_candidates += $this->_Connection->getCandidates();
		return $response;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $request Http request
	 * @param mixed $data result of $request query
	 */
	protected function _writeQueryCache(array $request, $data) {
		return parent::_writeQueryCache($request, array(
			'data' => $data,
			'_candidates' => $this->lastCandidates()
		));
	}

}
