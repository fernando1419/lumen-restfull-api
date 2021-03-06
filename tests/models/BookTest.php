<?php

namespace tests\models;

use App\Book;
use TestCase; // this uses tests/TestCase.php that implements createApplication() of Laravel\Lumen\Testing\TestCase.
use Laravel\Lumen\Testing\DatabaseTransactions;

class BookTest extends TestCase
{
	use DatabaseTransactions;

	protected $required_attributes = ['title', 'author_id'];
	protected $optional_attributes = ['description', 'isbn'];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();
		Book::truncate();
	}

	/** @test */
	public function a_book_has_a_title()
	{
		$book = new Book(['title' => 'New brand Book']);

		$this->assertEquals('New brand Book', $book->title);
		$this->assertNotEquals('New old Book', $book->title);
	}

	/** @test */
	public function a_book_object_can_only_be_persisted_if_its_required_attributes_are_specified()
	{
		$book = Book::create([
			'title'     => 'This is a title',
			'author_id' => 10
		]);

		$this->assertEquals(1, $book->id);
		foreach ($this->required_attributes as $attribute) {
			$this->assertNotNull($book->$attribute);
			$this->assertNotEmpty($book->$attribute);
		}
	}

	/** @test */
	public function a_book_object_can_be_persisted_if_its_optional_attributes_are_specified()
	{
		$book = Book::create([
			'title'       => 'This is a title of a book',
			'description' => 'This is a description of a book',
			'isbn'        => 2344124124,
			'author_id'   => 10
		]);

		$this->assertEquals($book->description, 'This is a description of a book');
		$this->assertEquals($book->isbn, 2344124124);
	}

	/** @test */
	public function a_book_object_can_be_persisted_if_its_optional_attributes_are_null_or_empty()
	{
		$book1 = Book::create([
			'title'       => 'This book has an emtpy description and isbn',
			'description' => '',
			'isbn'        => '',
			'author_id'   => 10
		]);

		$book2 = Book::create([
			'title'       => 'This book has a NULL description and isbn',
			'description' => null,
			'isbn'        => null,
			'author_id'   => 10
		]);

		foreach ($this->optional_attributes as $attribute) {
			$this->assertEmpty($book1->$attribute);
			$this->assertNull($book2->$attribute);
		}
	}
}
