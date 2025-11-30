<?php

namespace App\Controllers\Inclino;

use App\Controllers\BaseController;

class InclinoController extends BaseController
{
    public function index()
    {
        return view('inclino/view');
    }
    
    public function view()
    {
        return view('inclino/view');
    }
    
    public function create()
    {
        return view('inclino/create');
    }
    
    public function edit($id)
    {
        // Logika untuk edit data
        $data['id'] = $id;
        return view('inclino/edit', $data);
    }
}