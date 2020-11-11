<?php

namespace App\Http\Controllers\Game;

use App\Base\Controllers\Controller;
use App\Services\Game\GamePackageService;
use Illuminate\Http\Request;

class GamePackageController extends Controller
{
    protected $service ;

    /**
     * GamePackageController constructor.
     * @param GamePackageService $service
     */
    public function __construct(GamePackageService $service)
    {
        $this->service = $service;
    }


    public function index()
    {
        return $this->service->index();
    }
}
