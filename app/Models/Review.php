<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Review extends Model
{
     use HasFactory;

     protected $fillable = ['review', 'rating'];

     public function book()
     {
          return $this->belongsTo(Book::class);
     }


     protected static function booted()
     {
          static::updated(
               fn (Book $book) => cache()->forget('book:' . $book->id)
          );
          static::deleted(
               fn (Book $book) => cache()->forget('book:' . $book->id)
          );
          static::created(fn (Review $review) => cache()->forget('book:' . $review->book_id));
     }
}
