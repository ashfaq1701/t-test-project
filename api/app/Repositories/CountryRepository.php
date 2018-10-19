<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository {
    public function searchCountries($request) {
        $query = Country::where('name', 'LIKE', $request->input('query') . '%')
            ->orWhere('alpha_2', 'LIKE', $request->input('query') . '%');
        if ($request->has('page')) {
            $countries = $query->paginate();
        } else {
            $countries = $query->get();
        }
        return $countries;
    }

    public function getAllCountries($request) {
        if ($request->has('page')) {
            return Country::query()->paginate();
        } else {
            return Country::all();
        }
    }
}