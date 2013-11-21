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
	private $request_status = array();

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
	 * @param string $request_method
	 * @param bool $force
	 * @return array
	 */
	protected function _extractResult(Model $model, array $result, $request_method, $force = false) {
		//save read info: score, total
		$this->request_status[$request_method] = array();
		if(static::METHOD_READ == $request_method){
			$getinfo = array('took', 'timed_out', '_shards');
			$gethits = array('total', 'max_score');
			foreach($getinfo as $index_name)
				if(isset($result[$index_name])){
					$this->request_status[$request_method][$index_name] = $result[$index_name];
			}
			if(isset($result['hits'])){
				foreach($gethits as $index_name)
					if(isset($result['hits'][$index_name])){
						$this->request_status[$request_method][$index_name] = $result['hits'][$index_name];
				}
			}
		}else{  //other info like delete, update
			$this->request_status[$request_method] = $result;
		}

		return parent::_extractResult($model, $result, $request_method, true);
	}

	/**
	 * Get total hits from search result.
	 *
	 * @return int
	 */
	public function lastCandidates() {
		return (int)isset($this->request_status['read']['total']) ? $this->request_status['read']['total'] : 0;
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
