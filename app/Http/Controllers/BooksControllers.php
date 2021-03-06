<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ApiProject\Transformers\BookTransformer;

class BooksController extends ApiController
{
	protected $bookTransformer;

	/**
	 * __construct
	 *
	 * @param BookTransformer $bookTransformer
	 * @return void
	 */
	public function __construct(BookTransformer $bookTransformer)
	{
		$this->bookTransformer = $bookTransformer;
		$this->middleware('check-param', ['only' => ['show', 'update', 'destroy']]);
		$this->middleware('jwt.auth', ['only' => ['store', 'update', 'destroy']]);
	}

	/**
	 * Retrieve all books.
	 *
	 * @return json response
	 */
	public function index()
	{
		$books   = Book::all();
		$message = ($books->isEmpty()) ? 'No books found!.' : 'Display all books.';

		return $this->respond([
			'message' => $message,
			'data'    => $this->bookTransformer->transformCollection($books->toArray())
		]);
	}

	/**
	 * show GET('api/books/$bookId)
	 *
	 * @param mixed $bookId
	 * @return void
	 */
	public function show($bookId)
	{
		$book = Book::find($bookId);

		if ( ! $book) {
			return $this->respondNotFound('Book does not exist.');
		}

		return $this->respond([
			'data'    => $this->bookTransformer->transform($book),
			'message' => "Information about Book ID: {$bookId}."
		]);
	}

	/**
	 * store POST('api/books')
	 *
	 * @param Request $request
	 * @return void
	 */
	public function store(Request $request)
	{
		$validation = Validator::make($request->all(), Book::rules(), Book::messages());

		if ($validation->fails()) {
			return $this->respondUnprocessableEntity($validation->errors());
		}

		$newBook = Book::create($request->all());

		return $this->setStatusCode(201)->respond([
			'data'    => $this->bookTransformer->transform($newBook),
			'message' => "Book ID: {$newBook->id} successfully created.!"
		]);
	}

	/**
	 * update PUT('api/books/bookId')
	 *
	 * @param Request $request
	 * @param mixed $bookId
	 * @return void
	 */
	public function update(Request $request, $bookId)
	{
		$book = Book::find($bookId);

		if ( ! $book) {
			return $this->respondNotFound('Book does not exist.');
		}

		$validation = Validator::make($request->all(), Book::rules(), Book::messages());

		if ($validation->fails()) {
			return $this->respondUnprocessableEntity($validation->errors());
		}

		$book->fill($request->all());
		$book->save();

		return $this->setStatusCode(200)->respond([
			'data'    => $this->bookTransformer->transform($book),
			'message' => "Book ID: {$book->id} successfully updated.!"
		]);
	}

	/**
	 * destroy DELETE('api/books/bookId')
	 *
	 * @param mixed $bookId
	 * @return void
	 */
	public function destroy($bookId)
	{
		$book = Book::find($bookId);

		if ( ! $book) {
			return $this->respondNotFound('Book does not exist.');
		}

		$book->delete();

		return $this->respond([
			'message' => "Book ID: {$bookId} successfully deleted!."
		]);
	}
}
