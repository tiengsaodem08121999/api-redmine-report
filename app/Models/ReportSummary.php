<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSummary extends Model
{
    protected $table = 'report_summaries';
    protected $fillable = ['cr_font', 'bug_font', 'cr_cms', 'bug_cms', 'cr_api', 'bug_api'];
}
