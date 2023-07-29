<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoHeader extends Model
{
  protected $connection   = 'bjgmas01';
  protected $table        = 'Trans_POHD202305';
  protected $primaryKey   = 'NoBukti';
  public $timestamps      = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'NoBukti', 'POParent', 'TGL', 'Tgl_Needed', 'ShipmentNotes', 'TGL_JatuhTempo',
    'isImport', 'isAsset', 'idBDP', 'Status', 'NoContract', 'SupplierID', 'ShipmentTo',
    'Term', 'NilaiTukar', 'ConditionID', 'PaymentID', 'ConsigneeID', 'PelabuhanID',
    'TipePPN', 'PPN', 'MataUang', 'Discount', 'Fee', 'isWIP', 'FPrint', 'InvID', 'JurnalID',
    'OnBoardDate', 'Keterangan', 'KeteranganJasa', 'ExportWeb', 'CreateDate', 'CreateBy',
    'CompanyCode'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    //'scan_update_date', 'scan_update_by'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];
}
