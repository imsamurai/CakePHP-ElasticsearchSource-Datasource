<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 03.09.2014
 * Time: 14:22:17
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('ElasticsearchSource', 'ElasticsearchSource.Model/Datasource/Http');
App::uses('ElasticsearchConnection', 'ElasticsearchSource.Model/Datasource');
App::uses('HttpSourceConfigFactory', 'ElasticsearchSource.Lib/Config');
App::uses('HttpSourceEndpoint', 'HttpSource.Lib/Config');
App::uses('HttpSourceConfigFactory', 'HttpSource.Lib/Config');

/**
 * ElasticsearchSourceTest
 * 
 * @package ElasticsearchSourceTest
 * @subpackage Model.Datasource.Http
 */
class ElasticsearchSourceTest extends CakeTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * TEst constructor
	 */
	public function testConstructor() {
		$Source = new ElasticsearchSource(array('datasource' => 'ElasticsearchSource.Http/ElasticsearchSource'));
		$this->assertIsA($Source->getConnection(), 'ElasticsearchConnection');
	}

	/**
	 * Test http methods
	 */
	public function testHttpMethods() {
		$this->assertSame('POST', ElasticsearchSource::HTTP_METHOD_CREATE);
		$this->assertSame('PUT', ElasticsearchSource::HTTP_METHOD_UPDATE);
		$this->assertSame('HEAD', ElasticsearchSource::HTTP_METHOD_CHECK);
		$this->assertSame('DELETE', ElasticsearchSource::HTTP_METHOD_DELETE);
		$this->assertSame('GET', ElasticsearchSource::HTTP_METHOD_READ);
	}

	/**
	 * Test emulate limit that TURNED OFF
	 */
	public function testEmulateLimit() {
		$Source = $this->getMockBuilder('ElasticsearchSource')
				->setConstructorArgs(array(array('datasource' => 'ElasticsearchSource.Http/ElasticsearchSource')))
				->setMethods(array(
					'_getQueryData',
					'_formatResult',
				))
				->getMock();

		$Source->expects($this->any())->method('_formatResult')->willReturnArgument(1);
		$Source->expects($this->any())->method('_getQueryData')->willReturnCallback(function($path) {
			switch ($path) {
				case 'limit': 
					return 3;
				case 'offset': 
					return 2;
				default: 
					return;
			}
		});

		$data = array(
			array('x' => 1),
			array('x' => 2),
			array('x' => 3),
			array('x' => 4),
			array('x' => 5),
			array('x' => 6),
			array('x' => 7),
		);

		$this->assertSame($data, $Source->afterRequest(new Model, $data, ElasticsearchSource::METHOD_READ));
	}

	/**
	 * Test candidates
	 * 
	 * @param int $requestCount
	 * @param int $candidatesAtRequest
	 * @param int $candidatesTotal
	 * 
	 * @dataProvider candidatesProvider
	 */
	public function testCandidates($requestCount, $candidatesAtRequest, $candidatesTotal) {
		$Connection = $this->getMockBuilder('ElasticsearchConnection')
				->setMethods(array(
					'request',
					'getCandidates'
				))
				->getMock();

		$Connection->expects($this->exactly($requestCount))->method('request')->willReturnArgument(0);
		$Connection->expects($this->atLeastOnce())->method('getCandidates')->willReturn($candidatesAtRequest);

		$Source = $this->getMockBuilder('ElasticsearchSource')
				->setConstructorArgs(array(array('datasource' => 'ElasticsearchSource.Http/ElasticsearchSource'), $Connection))
				->setMethods(array(
					'_splitRequest',
				))
				->getMock();
		$Source->expects($this->once())->method('_splitRequest')->willReturnCallback(function($request) use($requestCount) {
			return array_fill(0, $requestCount, $request);
		});
		$Source->request(null, array('method' => ElasticsearchSource::HTTP_METHOD_READ));
		$this->assertSame($candidatesTotal, $Source->lastCandidates());
	}

	/**
	 * Data source for testCandidates
	 * 
	 * @return array
	 */
	public function candidatesProvider() {
		return array(
			//set #0
			array(
				//requestCount
				1,
				//candidatesAtRequest
				5,
				//candidatesTotal
				5
			),
			//set #1
			array(
				//requestCount
				11,
				//candidatesAtRequest
				3,
				//candidatesTotal
				33
			),
		);
	}

	/**
	 * Test time took
	 */
	public function testTimeTook() {
		$took = 234;
		$Source = new ElasticsearchSource(array('datasource' => 'ElasticsearchSource.Http/ElasticsearchSource'));
		$Source->took = $took;
		$this->assertSame($Source->took, $took);
		$this->assertSame($Source->timeTook(), $took);
	}

	/**
	 * Test force extract result
	 * 
	 * @param string $method
	 * @dataProvider extractResultForceProvider
	 */
	public function testExtractResultForce($method) {
		$Model = new Model;
		$Model->request = array(
			'method' => ElasticsearchSource::HTTP_METHOD_READ
		);

		$Connection = $this->getMockBuilder('ElasticsearchConnection')
				->setMethods(array(
					'request'
				))
				->getMock();
		$Connection->expects($this->once())->method('request')->willReturnArgument(0);

		$Endpoint = $this->getMockBuilder('HttpSourceEndpoint')
				->setConstructorArgs(array(HttpSourceConfigFactory::instance()))
				->setMethods(array(
					'processResult',
				))
				->getMock();
		$Endpoint->expects($this->once())->method('processResult')->with($Model, $Model->request)->willReturnArgument(1);

		$Source = $this->getMockBuilder('ElasticsearchSource')
				->setConstructorArgs(array(array('datasource' => 'ElasticsearchSource.Http/ElasticsearchSource'), $Connection))
				->setMethods(array(
					'_getCurrentEndpoint',
					'afterRequest'
				))
				->getMock();

		$Source->expects($this->any())->method('_getCurrentEndpoint')->willReturn($Endpoint);
		$Source->expects($this->once())->method('afterRequest')->willReturnArgument(1);
		$this->assertSame($Model->request, $Source->request($Model, null, $method));
	}

	/**
	 * Data provider for testExtractResultForce
	 * 
	 * @return array
	 */
	public function extractResultForceProvider() {
		return array(
			array_map(function ($v) {
				return array($v);
			}, array_values(ElasticsearchSource::getMethods()))
		);
	}
	
	/**
	 * Test query cache
	 */
	public function testQueryCache() {
		$cacheName = '__test_cache__';
		Cache::config($cacheName, array(
			'engine' => 'File',
			'prefix' => $cacheName,
			'path' => CACHE,
			'serialize' => true,
			'duration' => '1 hour'
		));
		
		$Model = new AppModel;
		$Model->cacheQueries = true;
		$Model->request = array(
			'method' => ElasticsearchSource::HTTP_METHOD_READ
		);
		
		$Connection = $this->getMockBuilder('ElasticsearchConnection')
				->setMethods(array(
					'getCandidates'
				))
				->getMock();
		$Connection->expects($this->any())->method('getCandidates')->willReturn(microtime(true));
		
		$Source = $this->getMockBuilder('ElasticsearchSource')
				->setConstructorArgs(array(array('datasource' => 'ElasticsearchSource.Http/ElasticsearchSource'), $Connection))
				->setMethods(array(
					'request',
					'_buildRequest',
					'_getCurrentEndpoint'
				))
				->getMock();

		$Source->expects($this->once())->method('request')->with($Model, null, HttpSource::METHOD_READ)->will($this->returnValue(array(array('result' => microtime(true)))));
		$Endpoint = new HttpSourceEndpoint(HttpSourceConfigFactory::instance());
		$Endpoint->cacheName($cacheName);
		$Source->expects($this->any())->method('_getCurrentEndpoint')->willReturn($Endpoint);

		$result = $Source->read($Model);
		$candidates = $Source->lastCandidates();
		$this->assertSame($result, $Source->read($Model));
		$this->assertSame($candidates, $Source->lastCandidates());
		$this->assertSame($result, $Source->read($Model));
		$this->assertSame($candidates, $Source->lastCandidates());
		
		Cache::clear(false, $cacheName);
	}

}
