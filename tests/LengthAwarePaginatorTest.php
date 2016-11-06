<?php

use PHPUnit\Framework\TestCase;
use DanBovey\LinkHeaderPaginator\LengthAwarePaginator;

class LengthAwarePaginatorTest extends TestCase {

	private $items = [
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
		'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
	];
	private $page = 2;
	private $per_page = 5;

	private $queryBuilderPaginator;

	public function setUp() {
		$items = collect($this->items)->forPage($this->page, $this->per_page);
		$this->queryBuilderPaginator = new \Illuminate\Pagination\LengthAwarePaginator($items, count($this->items), $this->per_page, $this->page);
	}

	public function testLink() {
		$paginator = new LengthAwarePaginator($this->queryBuilderPaginator);
		$link = $paginator->getHeaders()['Link'];
		$parsed = \phpish\link_header\parse($link);

		$this->assertEquals($parsed['current'][0]['uri'], '/?page=' . $this->page);
		$this->assertEquals($parsed['next'][0]['uri'], '/?page=' . ($this->page + 1));
		$this->assertEquals($parsed['last'][0]['uri'], '/?page=' . ceil(count($this->items) / $this->per_page));
	}

	public function testResponse() {
		$paginator = new LengthAwarePaginator($this->queryBuilderPaginator);
		$response = $paginator->toResponse();

		$this->assertTrue($response->headers->has('Link'));
		$this->assertEquals($response->content(), json_encode(array_slice($this->items, ($this->page - 1) * $this->per_page, $this->per_page)));
	}

}