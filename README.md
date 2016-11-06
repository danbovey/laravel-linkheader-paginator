# Laravel LinkHeader Paginator

A custom Laravel/Lumen Paginator that uses the [Link header (RFC 5988)](https://tools.ietf.org/html/rfc5988) to send pagination info in the response. Removes the envelope around `data`!

Adds a method called `toResponse` that returns a JSON response with headers. The `getHeaders` method exists if you need different response data.

## Installation

```
$ composer require danbovey/laravel-linkheader-paginator
```

## Usage

Create the pagination with the Eloquent/DB Builder and pass it to the `LengthAwarePaginator`.

```php
$items = User::where('active', 1)->paginate(20);

$paginator = new LengthAwarePaginator($items);

return $paginator->toResponse();
```

**"Simple Pagination"**

The simple paginator does not need to know the total number of items in the result set; however, because of this, the class does not return the URI of the last page.
Ironically, the simple paginator is more work using this library. To save on queries you should skip using the method`simplePaginate`, and implement the `skip`/`take` logic yourself.

```php
$page = $request->get('page');
$perPage = 20;
// Take one more than needed to see if there is a next page
$users = User::skip(($page - 1) * $perPage)
    ->take($perPage + 1);

$paginator = new Paginator($simple, $items);

return $paginator->toResponse();
```