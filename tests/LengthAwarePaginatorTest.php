<?php

use PHPUnit\Framework\TestCase;
use DanBovey\LinkHeaderPaginator\LengthAwarePaginator;

class LengthAwarePaginatorTest extends TestCase {

	private $items = [
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
		'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
	];
	private $page = 1;
	private $per_page = 5;

	public function testLink() {
		$paginator = new LengthAwarePaginator($this->items, count($this->items), $this->per_page, $this->page);
		$link = $paginator->getHeaders()['Link'];
		$parsed = \phpish\link_header\parse($link);

		$this->assertEquals($parsed['current'][0]['uri'], '/?page=' . $this->page);
		$this->assertEquals($parsed['next'][0]['uri'], '/?page=' . ($this->page + 1));
		$this->assertEquals($parsed['last'][0]['uri'], '/?page=' . ceil(count($this->items) / $this->per_page));
	}

	public function testResponse() {
		$items = collect($this->items);
		$paginator = new LengthAwarePaginator($items->forPage($this->page, $this->per_page), $items->count(), $this->per_page, $this->page);
		$response = $paginator->toResponse();

		$this->assertTrue($response->headers->has('Link'));
		$this->assertEquals($response->content(), json_encode(array_slice($this->items, 0, $this->per_page)));
	}

}