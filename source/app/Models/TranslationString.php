<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationString extends Model
{
    protected $fillable = ['group', 'key', 'locale', 'value'];
}
