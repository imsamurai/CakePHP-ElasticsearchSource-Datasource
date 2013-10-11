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
	const HTTP_METHOD_READ = 'POST';

	/**
	 * Elasticsearch API Datasource
	 *
	 * @var string
	 */
	public $description = 'ElasticsearchSource DataSource';

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
	 * Single request
	 *
	 * @param array $request
	 * @param string $request_method
	 * @param Model $model
	 * @return array|bool
	 */
	protected function _singleRequest(array $request, $request_method, Model $model = null) {
		if (!empty($request['uri']['path'])) {
			$request['uri']['path'] = (string) Hash::get($this->config, 'path') . '/' . $request['uri']['path'];
		}

		return parent::_singleRequest($request, $request_method, $model);
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
	protected function _emulateFields(Model $Model, array &$result) {

	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Model $Model
	 * @param array $result
	 */
	protected function _emulateLimit(Model $Model, array &$result) {

	}

}
