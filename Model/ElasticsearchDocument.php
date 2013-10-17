<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2013
 * Time: 18:19:36
 * Format: http://book.cakephp.org/2.0/en/models.html
 *
 * @package ElasticsearchSource
 * @subpackage Document
 */
App::uses('HttpSourceModel', 'HttpSource.Model');

class ElasticsearchDocument extends HttpSourceModel {

	public $name = 'ElasticsearchDocument';
	public $useTable = 'document';

}
