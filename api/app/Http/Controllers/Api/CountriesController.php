<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Country as CountryResource;
use App\Repositories\CountryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller {
    public $countryRepository;

    public function __construct(CountryRepository $countryRepository) {
        $this->countryRepository = $countryRepository;
    }
}