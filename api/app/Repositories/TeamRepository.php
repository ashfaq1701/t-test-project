<?php

namespace App\Repositories;

use App\Models\Team;
use App\User;
use Illuminate\Support\Facades\Auth;
use Dotenv\Exception\ValidationException;

class TeamRepository {
    public function getQuery($request) {
        $query = Team::query();
        if ($request->has('country')) {
            $query = $query->where('country_id', '=', $request->input('country'));
        }
        if ($request->has('user')) {
            $query = $query->where('user_id', '=', $request->input('user'));
        }
        if ($request->has('user_name')) {
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
            $teams = $query->paginate();
        } else {
            $teams = $query->get();
        }
        return $teams;
    }

    public function getAllTeams($request) {
        $query = $this->getQuery($request);
        if ($request->has('page')) {
            return $query->paginate();
        } else {
            return $query->get();
        }
    }

    public function storeTeam($request) {
        $data = $request->only(['name', 'fund', 'country_id', 'user_id']);
        if ($request->has('user_id')) {
            $user = User::find($request->input('user_id'));
            if (!empty($user->team)) {
                throw new ValidationException('Target user already has an assigned team. Can not assign ' .
                    'new team to this user.');
            }
        }
        $team = Team::create($data);
        return $team;
    }

    public function updateTeam($request, $id) {
        $currentUser = Auth::user();
        if ((!$currentUser->hasPermissionTo('change_team_ownership')) && $request->has('user_id')) {
            throw new ValidationException('Current user is not permitted to change team ownership');
        }
        if ($request->has('user_id')) {
            $user = User::find($request->input('user_id'));
            if (!empty($user->team)) {
                throw new ValidationException('Target user already has an assigned team. Can not assign ' .
                    'new team to this user.');
            }
        }
        $data = $request->only(['name', 'fund', 'country_id', 'user_id']);
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