<?php
// app/Models/Policy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $fillable = ['name', 'cost'];
}
