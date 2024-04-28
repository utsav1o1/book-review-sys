<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Book extends Model
{
    use HasFactory;


    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withCount(['review' =>fn(Builder $q)=>$this->dateRangeFilter($q, $from, $to)])->orderBy('review_count', 'desc');
    }

    public function scopeHighestRated(Builder $query,  $from = null, $to = null): Builder
    {
        return $query->withAvg(['review' =>fn(Builder $q)=>$this->dateRangeFilter($q, $from, $to)], 'rating')->orderBy('review_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReview):Builder
    {
        return $query->having('review_count','>=','$minReview');
    }

    private function dateRangerFilter(Builder $query, $from = null, $to = null)
    {

        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }
}
