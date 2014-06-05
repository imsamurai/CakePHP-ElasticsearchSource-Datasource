<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 05.06.2014
 * Time: 11:25:37
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */
App::uses('ElasticsearchSourceAppController', 'ElasticsearchSource.Controller');

/**
 * ElasticsearchController
 * 
 * @property ElasticsearchDocument $ElasticsearchDocument ElasticsearchDocument Model
 * 
 * @package ElasticsearchSource
 * @subpackage Controller
 */
class ElasticsearchController extends ElasticsearchSourceAppController {

	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $uses = array('ElasticsearchSource.ElasticsearchDocument');

	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $helpers = array('ElasticsearchSource.Elasticsearch');

	/**
	 * Run explain/profiling on queries. Checks the hash + the hashed queries,
	 * if there is mismatch a 404 will be rendered. If debug == 0 a 404 will also be
	 * rendered. No explain will be run if a 404 is made.
	 *
	 * @throws BadRequestException
	 * @return void
	 */
	public function explain() {
		if (
				!$this->request->is('post') ||
				empty($this->request->data['log']['sql']) ||
				empty($this->request->data['log']['ds']) ||
				empty($this->request->data['log']['hash']) ||
				Configure::read('debug') == 0
		) {
			throw new BadRequestException('Invalid parameters');
		}
		$hash = Security::hash($this->request->data['log']['sql'] . $this->request->data['log']['ds'], 'sha1', true);
		if ($hash !== $this->request->data['log']['hash']) {
			throw new BadRequestException('Invalid parameters');
		}
		$result = $this->ElasticsearchDocument->explainQuery($this->request->data['log']['ds'], $this->request->data['log']['sql']);
		$this->set(compact('result'));
	}

}
