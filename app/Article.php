<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'subtitle', 'content', 'send_date', 'news_link',
        'preview_img', 'preview_content'];

    public function newsHistory()
    {
        return $this->hasOne(NewsHistory::class);
    }
}
