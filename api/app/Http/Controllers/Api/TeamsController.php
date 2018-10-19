<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Team as TeamResource;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamsController extends Controller {
    public $teamRepository;

    public function __construct(TeamRepository $teamRepository) {
        $this->teamRepository = $teamRepository;
    }

    public function index(Request $request) {
        if ($request->has('query')) {
            $teams = $this->teamRepository->searchTeams($request);
        } else {
            $teams = $this->teamRepository->getAllTeams($request);
        }
        return TeamResource::collection($teams);
    }
}