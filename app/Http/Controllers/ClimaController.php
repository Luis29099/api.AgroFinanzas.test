<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClimaController extends Controller
{
    public function getClima()
    {
        // Mocked weather data to mimic OpenWeatherMap response
        // as we likely don't have an API key configured.
        return response()->json([
            'main' => [
                'temp' => 18.5,
                'humidity' => 65
            ],
            'wind' => [
                'speed' => 4.2
            ],
            'weather' => [
                [
                    'description' => 'nubes dispersas'
                ]
            ]
        ]);
    }
}
