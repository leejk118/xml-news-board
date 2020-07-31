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

    public function scopeCategory($query, $category, $q)
    {
        switch ($category){
            case 'both':
                $query->orWhere('title', 'like', '%'. $q . '%');
                $query->orWhere('content', 'like', '%'. $q . '%');
                break;
            case 'title':
            case 'content':
                $query->where($category, 'like', '%'. $q . '%');
                break;
            default:
                break;
        }
        return $query;
    }
}
