<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 03.09.2014
 * Time: 13:37:27
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('Security', 'Utility');
App::uses('ElasticsearchController', 'ElasticsearchSource.Controller');
App::uses('ElasticsearchHelper', 'ElasticsearchSource.View/Helper');

/**
 * ElasticsearchControllerTest
 * 
 * @package ElasticsearchSourceTest
 * @subpackage Controller
 */
class ElasticsearchControllerTest extends ControllerTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test explain() action
	 * 
	 * @param string $method
	 * @param array $logData
	 * @param int $debugLevel
	 * @param string $exception
	 * 
	 * @dataProvider explainProvider
	 */
	public function testExplain($method, array $logData, $debugLevel, $exception) {
		$explaination = 'this is explaination';
		if ($exception) {
			$this->expectException($exception);
		}
		Configure::write('debug', $debugLevel);

		$Controller = $this->generate('ElasticsearchSource.Elasticsearch', array(
			'models' => array(
				'ElasticsearchSource.ElasticsearchDocument' => array('explainQuery')
			),
			'methods' => array('_getViewObject')
		));

		$Controller
				->expects($this->any())
				->method('_getViewObject')
				->willReturnCallback(function() use (&$Controller, $exception, $explaination) {
					$View = new View($Controller);
					$View->loadHelpers();
					$Helper = $this->getMockBuilder('ElasticsearchHelper')
							->setConstructorArgs(array(new View()))
							->setMethods(array('explain'))
							->getMock();

					$Helper->expects($this->exactly($exception ? 0 : 1))
					->method('explain')
					->with($explaination)
					->willReturn($explaination . $explaination);

					$View->Elasticsearch = $Helper;
					return $View;
				});

		$Controller->ElasticsearchDocument
				->expects($this->exactly($exception ? 0 : 1))
				->method('explainQuery')
				->with($logData['ds'], $logData['sql'])
				->willReturn($explaination);

		$view = $this->testAction('/elasticsearch/', array(
			'method' => $method,
			'data' => array(
				'log' => $logData
			),
			'return' => 'view'
		));
		
		$this->assertStringMatchesFormat('%w<table class="sql-log-query-explain debug-table">%w' . $explaination . $explaination . '%w</table>%w', $view);
	}

	/**
	 * Data provider for testExplain
	 * 
	 * @return array
	 */
	public function explainProvider() {
		return array(
			//set #0
			array(
				//method
				'GET',
				//logData
				array(
					'sql' => 'sql',
					'ds' => 'ds',
					'hash' => 'hash'
				),
				//debugLevel
				1,
				//exception
				'BadRequestException'
			),
			//set #1
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => '',
					'ds' => 'ds',
					'hash' => 'hash'
				),
				//debugLevel
				1,
				//exception
				'BadRequestException'
			),
			//set #2
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => 'sql',
					'ds' => '',
					'hash' => 'hash'
				),
				//debugLevel
				1,
				//exception
				'BadRequestException'
			),
			//set #3
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => 'sql',
					'ds' => 'ds',
					'hash' => ''
				),
				//debugLevel
				1,
				//exception
				'BadRequestException'
			),
			//set #4
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => 'sql',
					'ds' => 'ds',
					'hash' => 'hash'
				),
				//debugLevel
				0,
				//exception
				'BadRequestException'
			),
			//set #5
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => 'sql',
					'ds' => 'ds',
					'hash' => Security::hash('sql' . 'ds', 'sha1', true)
				),
				//debugLevel
				0,
				//exception
				'BadRequestException'
			),
			//set #6
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => 'sql',
					'ds' => 'ds',
					'hash' => 'hash'
				),
				//debugLevel
				1,
				//exception
				'BadRequestException'
			),
			//set #7
			array(
				//method
				'POST',
				//logData
				array(
					'sql' => 'sql',
					'ds' => 'ds',
					'hash' => Security::hash('sql' . 'ds', 'sha1', true)
				),
				//debugLevel
				1,
				//exception
				''
			),
		);
	}

}
