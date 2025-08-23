<?php

namespace App\Controllers;

use App\Models\Rembesan\DataGabunganModel;
use App\Models\Rembesan\PerhitunganSRModel;
use App\Models\Rembesan\PerhitunganBocoranModel;
use App\Models\Rembesan\PerhitunganIntiGaleryModel;
use App\Models\Rembesan\PerhitunganSpillwayModel;
use App\Models\Rembesan\TebingKananModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MenuController extends BaseController
{
    public function index()
    {
        return view('menu');
    }
}