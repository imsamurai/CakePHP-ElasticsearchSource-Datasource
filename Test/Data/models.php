<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/models.html
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */

App::uses('HttpSourceModel', 'HttpSource.Model');

class Elasticsearch extends HttpSourceModel {
    public $name = 'Elasticsearch';
	public $useDbConfig = 'testElasticsearchSource';
	public $useTable = 'search';

}