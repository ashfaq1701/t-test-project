<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Country as CountryResource;
use App\Repositories\CountryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller {
    public $countryRepository;

    public function __construct(CountryRepository $countryRepository) {
        $this->middleware('permission:get_countries',
            ['only' => ['index']]);
        $this->countryRepository = $countryRepository;
    }

    public function index(Request $request) {
        if ($request->has('query')) {
            $countries = $this->countryRepository->searchCountries($request);
        } else {
            $countries = $this->countryRepository->getAllCountries($request);
        }
        return CountryResource::collection($countries);
    }
}