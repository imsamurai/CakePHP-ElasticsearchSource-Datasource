<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 *
 * @package ElasticsearchSource
 * @subpackage Config
 */
App::uses('HttpSourceEndpoint', 'HttpSource.Lib/Config');

/**
 * Elasticsearch source endpoint
 *
 * @package ElasticsearchSource
 * @subpackage Config
 */
class ElasticsearchSourceEndpoint extends HttpSourceEndpoint {

	/**
	 * {@inheritdoc}
	 *
	 * @param Model $Model
	 * @param array $usedConditions List of conditions that must present in query
	 * @param array $queryData Query data: conditions, limit, etc
	 */
	protected function _buildQuery(Model $Model, array $usedConditions, array $queryData) {
		parent::_buildQuery($Model, $usedConditions, $queryData);

		if (!empty($queryData['limit'])) {
			$Model->request['body']['size'] = $queryData['limit'];
			unset($queryData['limit']);
		}

		if (!empty($queryData['offset'])) {
			$Model->request['body']['from'] = $queryData['offset'];
			unset($queryData['offset']);
		}

		if (!empty($queryData['order'][0])) {
			$Model->request['body']['sort'] = $queryData['order'][0];
			unset($queryData['order']);
		}

		if (!empty($queryData['fields'])) {
			$Model->request['body']['fields'] = $queryData['fields'];
			unset($queryData['fields']);
		}
	}

}
