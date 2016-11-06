<?php

namespace DanBovey\LinkHeaderPaginator;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator as BasePaginator;

class LengthAwarePaginator extends BasePaginator {

	public function __construct(BasePaginator $paginator, array $options = []) {
		parent::__construct($paginator->items(), $paginator->total(), $paginator->perPage(), $paginator->currentPage(), $options);
	}

	/**
	 * The Paginator instance returns only the items
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->items->toArray();
	}

	/**
	 * Build the Link headers
	 * Can be attached to the response using `withHeaders`
	 *
	 * @return array
	 */
	public function getHeaders() {
		$links = [
			'current' => $this->url($this->currentPage()),
			'last' => $this->url($this->lastPage()),
			'next' => $this->nextPageUrl(),
			'prev' => $this->previousPageUrl(),
		];

		$headers = [];

		foreach($links as $rel => $url) {
			if($url != null) {
				$headers[] = (new Link($url, $rel))->toString();
			}
		}

		return [
			'Link' => implode(', ', $headers),
			'X-Items-Total' => $this->total(),
			'X-Items-Per-Page' => $this->perPage(),
			'X-Items-From' => $this->firstItem(),
			'X-Items-To' => $this->lastItem(),
		];
	}

	/**
	 * Create a Laravel Response that sends the items in the body and
	 * pagination info in the headers
	 *
	 * @return JsonResponse
	 */
	public function toResponse() {
		$response = new JsonResponse($this->toArray());

		return $response->withHeaders($this->getHeaders());
	}

}