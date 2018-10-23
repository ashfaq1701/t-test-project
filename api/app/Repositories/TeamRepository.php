<?php

namespace App\Repositories;

use App\Models\Team;
use App\User;
use Illuminate\Support\Facades\Auth;
use Dotenv\Exception\ValidationException;

class TeamRepository {
    public function getQuery($request) {
        $query = Team::query();
        if ($request->has('country') && !empty($request->input('country'))) {
            $query = $query->where('country_id', '=', $request->input('country'));
        }
        if ($request->has('user') && !empty($request->input('user'))) {
            $query = $query->where('user_id', '=', $request->input('user'));
        }
        if ($request->has('user_name') && !empty($request->input('user_name'))) {
            $query = $query->whereHas('user', function ($innerQuery) use($request) {
               $innerQuery->where('name', 'LIKE', $request->input('user_name') . '%');
            });
        }
        return $query;
    }

    public function searchTeams($request) {
        $query = $this->getQuery($request);
        $query = $query->where('name', 'LIKE', $request->input('query') . '%');
        if ($request->has('page')) {
            $teams = $query->paginate(10);
        } else {
            $teams = $query->get();
        }
        return $teams;
    }

    public function getAllTeams($request) {
        $query = $this->getQuery($request);
        if ($request->has('page')) {
            return $query->paginate(10);
        } else {
            return $query->get();
        }
    }

    public function updateTeam($request, $id) {
        $data = $request->only(['name', 'fund', 'country_id']);
        $team = Team::find($id);
        $team->update($data);
        return $team;
    }

    public function deleteTeam($id) {
        if (is_object($id)) {
            $team = $id;
        } else {
            $team = Team::find($id);
        }
        $playerRepository = new PlayerRepository();
        foreach ($team->players as $player) {
            $playerRepository->deletePlayer($player);
        }
        $team->delete();
        return '';
    }

    public function getTeam($id) {
        $team = Team::find($id);
        return $team;
    }
}