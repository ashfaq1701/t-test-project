<?php

namespace App\Repositories;

use App\Models\Team;

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
}