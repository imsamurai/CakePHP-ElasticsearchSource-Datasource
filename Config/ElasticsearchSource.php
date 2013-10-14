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
				->path(':index/_search')
				->table('_search')
				->readParams(array(
					'size' => 'limit',
					'from' => 'offset',
					'sort' => 'order.0',
					'fields' => 'fields'
				))
		)
		->add(
				$CF->endpoint()
				->id(2)
				->methodUpdate()
				->table('index_create')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->length(100)->required())
				->addCondition($CF->condition()->name('timestamp')->sendInQuery())
				->addCondition($CF->condition()->name('ttl')->sendInQuery())
		)
		->result($CF->result()->map(function($result) {
					return Hash::extract($result, 'hits.hits.{n}._source') + Hash::extract($result, 'hits.hits.{n}.fields');
				}))



;


$config['ElasticsearchSource']['config'] = $Config;
