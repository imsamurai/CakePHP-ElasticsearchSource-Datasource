<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: https://github.com/imsamurai/cakephp-httpsource-datasource
 *
 * @package ElasticsearchSource
 * @subpackage Config
 */
$config['ElasticsearchSource']['config_version'] = 2;

$CF = HttpSourceConfigFactory::instance();
$Config = $CF->config();

$Config
		/*
		 * Search apis
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search.html
		 */
		->add(
				$CF->endpoint()
				->id(1)
				->methodRead()
				->table('search')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->required())
				->result($CF->result()->map(function($data) {
							return array((array) Hash::get($data, '_source') + array(
							'id' => $data['_id'],
							'type' => $data['_type'],
							'index' => $data['_index']
							));
						}))
		)
		->add(
				$CF->endpoint()
				->id(2)
				->methodRead()
				->table('search')
				->path(':index/:type/_search')
				->addCondition($CF->condition()->name('query')->sendInBody())
				->addCondition($CF->condition()->name('filter')->sendInBody())
				->addCondition($CF->condition()->name('facets')->sendInBody())
				->addCondition($CF->condition()->name('hobbies')->sendInBody())
				->addCondition($CF->condition()->name('terms')->sendInBody())
				->addCondition($CF->condition()->name('fields')->sendInBody())
				->addCondition($CF->condition()->name('size')->sendInBody())
				->addCondition($CF->condition()->name('from')->sendInBody())
				->addCondition($CF->condition()->name('sort')->sendInBody())
				->addCondition($CF->condition()->name('fields')->sendInBody())
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults(''))
				->addCondition($CF->condition()->name('type')->sendInQuery()->defaults(''))
				->readParams(array(
					'size' => 'limit',
					'from' => 'offset',
					'sort' => 'order.0',
					'fields' => 'fields'
				))
				->result($CF->result()->map(function($data) {
							$result = array();
							foreach ((array) Hash::get($data, 'hits.hits') as $item) {
								$result[] = (array) Hash::get($item, '_source') + (array) Hash::get($item, 'fields') + array(
									'id' => $item['_id'],
									'type' => $item['_type'],
									'index' => $item['_index'],
									'score' => $item['_score'],
								);
							}
							return $result;
						}))
		)

		/**
		 * Indexing
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-index_.html
		 */
		->add(
				$CF->endpoint()
				->id(3)
				->methodUpdate()
				->table('search')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->length(100)->required())
				->addCondition($CF->condition()->name('version')->sendInQuery())
				->addCondition($CF->condition()->name('op_type')->sendInQuery())
				->addCondition($CF->condition()->name('routing')->sendInQuery())
				->addCondition($CF->condition()->name('parent')->sendInQuery())
				->addCondition($CF->condition()->name('timestamp')->sendInQuery())
				->addCondition($CF->condition()->name('ttl')->sendInQuery())
				->addCondition($CF->condition()->name('percolate')->sendInQuery())
				->addCondition($CF->condition()->name('distributed')->sendInQuery())
				->addCondition($CF->condition()->name('consistency')->sendInQuery())
				->addCondition($CF->condition()->name('replication')->sendInQuery())
				->addCondition($CF->condition()->name('refresh')->sendInQuery())
				->addCondition($CF->condition()->name('timeout')->sendInQuery())
		)
		->add(
				$CF->endpoint()
				->id(4)
				->methodCreate()
				->table('search')
				->path(':index/:type')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->length(100))
				->addCondition($CF->condition()->name('routing')->sendInQuery())
				->addCondition($CF->condition()->name('parent')->sendInQuery())
				->addCondition($CF->condition()->name('timestamp')->sendInQuery())
				->addCondition($CF->condition()->name('ttl')->sendInQuery())
				->addCondition($CF->condition()->name('percolate')->sendInQuery())
				->addCondition($CF->condition()->name('distributed')->sendInQuery())
				->addCondition($CF->condition()->name('consistency')->sendInQuery())
				->addCondition($CF->condition()->name('replication')->sendInQuery())
				->addCondition($CF->condition()->name('refresh')->sendInQuery())
				->addCondition($CF->condition()->name('timeout')->sendInQuery())
				->result($CF->result()->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								$Model->id = $data['_id'];
								return $data;
							} else {
								return false;
							}

						}))
		)




;


$config['ElasticsearchSource']['config'] = $Config;
