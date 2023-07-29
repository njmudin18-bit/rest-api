<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trans extends Model
{
  protected $connection   = 'bjgmas01';
  protected $table        = 'tbl_scanbarcode_job';
  protected $primaryKey   = 'scan_id';
  public $timestamps      = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'scan_id', 'barcode_no', 'no_job', 'loc_id', 'qty_job', 'qty_box',
    'loc_result', 'scan_status', 'scan_date', 'scan_by'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'scan_update_date', 'scan_update_by'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];
}
