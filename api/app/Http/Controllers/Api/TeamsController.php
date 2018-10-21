<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Team as TeamResource;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamsController extends Controller {
    public $teamRepository;

    public function __construct(TeamRepository $teamRepository) {
        $this->middleware('permission:get_teams',
            ['only' => ['index']]);
        $this->middleware('permission:create_new_team',
            ['only' => ['store']]);
        $this->middleware('permission:edit_teams|change_team_ownership',
            ['only' => ['update']]);
        $this->middleware('permission:delete_teams',
            ['only' => ['destroy']]);
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'fund' => 'required|numeric|min:1',
            'country_id' => 'required|exists:countries,id',
            'user_id' => 'sometimes|required|exists:users,id'
        ]);
        $team = $this->teamRepository->storeTeam($request);
        return new TeamResource($team);
    }

    public function show($id)
    {
        $team = $this->teamRepository->getTeam($id);
        return new TeamResource($team);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required',
            'fund' => 'sometimes|required|numeric|min:1',
            'country_id' => 'sometimes|required|exists:countries,id',
            'user_id' => 'sometimes|required|exists:users,id'
        ]);
        $team = $this->teamRepository->updateTeam($request, $id);
        return new TeamResource($team);
    }

    public function destroy($id)
    {
        return $this->teamRepository->deleteTeam($id);
    }
}