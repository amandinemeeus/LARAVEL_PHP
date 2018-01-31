<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LigneController extends Controller
{
    function index() {
        $routes = $this->getRoutes();
        $position = $this->getPosition(); //call the function
        dump($position);
        return view ('Lignes') -> with ('routes', $routes);
    }

    function getRoutes() {
        $path = storage_path('stib/routes.json');
        $json = file_get_contents($path);
        $response = json_decode($json);
        return $response->routes;
    }

    function getPosition() {
        $token = $this->getToken();
        // initialisation de la session
        $curl = curl_init();

        // configuration des options
        curl_setopt_array($curl, array(
            CURLOPT_URL                 => "https://opendata-api.stib-mivb.be/OperationMonitoring/1.0/VehiclePositionByLine/93",
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
}
