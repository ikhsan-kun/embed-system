<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class MonitorController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        $sensors = $this->database->getReference('sensors')->getValue();
        return view('monitor', compact('sensors'));
    }
}