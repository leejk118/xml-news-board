<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['send_date', 'article_id', 'view_count'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
