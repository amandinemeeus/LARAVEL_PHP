<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LigneController extends Controller
{
    function index() {
        $routes = $this->getRoutes();
        //$position = $this->getPosition(); //call the function
        //dump($position);
        return view ('Lignes')->with ('routes', $routes);
        
    }

    function getRoutes($route_id = null) {
        $path = storage_path('stib/routes.json');
        $json = file_get_contents($path);
        $response = json_decode($json);
        $routes = $this->getRouteDirection($response->routes); 

        if ($route_id){
            $key = array_search ($route_id, array_column ($routes, "route_short_name"));
            return $routes[$key];
        }
        
        return $routes;
    }

    function getPosition($route_id) {
        $token = $this->getToken();
        // initialisation de la session
        $curl = curl_init();

        // configuration des options
        curl_setopt_array($curl, array(
            CURLOPT_URL                 => "https://opendata-api.stib-mivb.be/OperationMonitoring/1.0/VehiclePositionByLine/$route_id",
            CURLOPT_RETURNTRANSFER      => 1,
            CURLOPT_HTTPHEADER          => ["Authorization: Bearer $token"],
            CURLOPT_CAINFO              => 'C:\MAMP\htdocs\LARAVEL\STIB\cacert.pem',
        ));
        
        // exécution de la session
        $resultat = curl_exec($curl);
        
        // fermeture des ressources
        curl_close($curl);
        $decode = json_decode($resultat);
        return $decode->lines[0]->vehiclePositions;
    }

    function getToken (){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL                 => 'https://opendata-api.stib-mivb.be/token',   
            CURLOPT_RETURNTRANSFER      => 1,
            CURLOPT_POST                => 1,
            CURLOPT_CAINFO              => 'C:\MAMP\htdocs\LARAVEL\STIB\cacert.pem',
            CURLOPT_POSTFIELDS          => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER          => ['Authorization: Basic UEZWZ2xxUWNzZXVtUXRialpwRldZcjV5SkQwYTplanpLZGtUaURiajRaSEc4dk1KQkdMZUtWVE1h']
        ));
        $resultat = curl_exec($curl);
        if (FALSE === $resultat)
        curl_close($curl);  

        $data = json_decode($resultat);
        return $data->access_token;
    }

    function getRouteDirection($routes) {
        foreach ($routes as $key => $route) {
            $directions = explode(" - ", $route->route_long_name );
            //dd ($direction);
            $routes[$key]-> route_direction = array (
                $this->slugify($directions[0])=> $directions[0],
                $this->slugify($directions[1])=> $directions[1]
            );
        }
        return $routes;
    }

    function slugify($string, $replace = array(), $delimiter = '-') {
        // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
        if (!extension_loaded('iconv')) {
          throw new Exception('iconv module not loaded');
        }
        // Save the old locale and set the new locale to UTF-8
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        if (!empty($replace)) {
          $clean = str_replace((array) $replace, ' ', $clean);
        }
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        // Revert back to the old locale
        setlocale(LC_ALL, $oldLocale);
        return $clean;
    }

    function show ($route_id, $direction){
        $route = $this->getRoutes($route_id); 
        $stops = $this->getRoutesStops($route, $direction);
        $position = $this->getPosition($route_id);
        $routeStopsRefletPosition = $this->makeItAppened($stops, $position);
        
        return view ('Ligne')->with ([
            'stops' => $routeStopsRefletPosition,
            'route' => $route
            ]);
    }

    function getRoutesStops ($route, $direction) {
        $path = storage_path("stib/routes_stops/$route->route_short_name.$route->route_long_name.json");
        $json = file_get_contents($path);
        $response = json_decode($json);
        $direction_name = $route->route_direction[$direction]; 
        $key = array_search ($direction_name, $response->directions);
        
        return $response->stops[$key];
    }

    function makeItAppened($stops, $positions){
        $result = $stops;
        $lastStop = end ($stops)->stop_id;
        
        foreach ($stops as $key => $stop){
            foreach ($positions as $position){
                if ($position->directionId != $lastStop){
                    continue;
                }
                if ($position->pointId == $stop->stop_id){
                    $stops[$key]->here = true;
                }
            }
        }
        return $stops;     
    }

}