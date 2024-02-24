<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadModel extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = ['employee_id','domain', 'year_founded', 'industry', 'size_range', 'locality', 'country', 'linkedin_url', 'current_employee_estimate', 'total_employee_estimate'];

}
