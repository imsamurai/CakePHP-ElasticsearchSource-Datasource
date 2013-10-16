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
				->path(':index/_search')
				->addCondition($CF->condition()->name('query'))
				->addCondition($CF->condition()->name('filter'))
				->addCondition($CF->condition()->name('facets'))
				->addCondition($CF->condition()->name('hobbies'))
				->addCondition($CF->condition()->name('terms'))
				->addCondition($CF->condition()->name('fields'))
				->addCondition($CF->condition()->name('size'))
				->addCondition($CF->condition()->name('from'))
				->addCondition($CF->condition()->name('sort'))
				->addCondition($CF->condition()->name('fields'))
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
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
		->add(
				$CF->endpoint()
				->id(2)
				->methodUpdate()
				->table('search')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->length(100)->required())
				->addCondition($CF->condition()->name('timestamp')->sendInQuery())
				->addCondition($CF->condition()->name('ttl')->sendInQuery())
		)




;


$config['ElasticsearchSource']['config'] = $Config;
