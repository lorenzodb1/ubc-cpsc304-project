<?php
/**
 * Created by PhpStorm.
 * User: giuliamattia
 * Date: 2016-06-13
 * Time: 3:34 PM
 */

namespace Trippi\Controllers;

use Slim\Router;
use Trippi\Models\Trip;


use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Trippi\Models\IdGenerator;
use Trippi\Models\CreateTrip;
use Slim\Router as router2;

class CreateTripController  {

    public function createTrip($email, Request $request, Response $response, Twig $view, router2 $router){
        $data = $request->getParsedBody();
        $tripName =  filter_var($data['tripName'],FILTER_SANITIZE_STRING);
        $startDate = filter_var($data['startDate'],FILTER_SANITIZE_STRING);
        $endDate = filter_var($data['endDate'],FILTER_SANITIZE_STRING);

        $generator = new IdGenerator();
        $tripID = $generator->newTripID();
        
        $create = new CreateTrip();
        $createdTrip = $create->createNewTrip($tripID, $startDate, $endDate, $tripName);
        $linkTripToUser = $create->linkTripPlanner($email, $tripID);

        if($createdTrip and $linkTripToUser) {
            return $view->render($response, 'trip/trip_segment.twig', [
                'tripName' => $tripName,
                'tripId' => $tripID,
                'email'=> $email
            ]);
        }

        else {
            return $view->render($response, 'trip/create_trip.twig', [
                'tripName' => $tripName,
                'tripId' => $tripID,
                'email'=> $email

            ]);
        }


    }
    public function addLocationDetails($email, $tripName, $tripId, Request $request, Response $response, Twig $view){
        $data = $request->getParsedBody();
        $fromCity =filter_var($data['fromCity'], FILTER_SANITIZE_STRING);
        $fromCountry =filter_var($data['fromCountry'], FILTER_SANITIZE_STRING);
        $toCity = filter_var($data['toCity'],FILTER_SANITIZE_STRING);
        $toCountry = filter_var($data['toCountry'],FILTER_SANITIZE_STRING);

        $addTripDetails = new CreateTrip();
        $startingLocationId = $addTripDetails->addLocationDetails($fromCity, $fromCountry);
        $endingLocationId = $addTripDetails->addLocationDetails($toCity, $toCountry);

        if($startingLocationId and $endingLocationId){
            return $view->render($response, 'trip/travelling_transportation.twig', [
                'tripName' => $tripName,
                'tripId' => $tripId,
                'tripDetails' => $addTripDetails,
                'startLocationId'=> $startingLocationId,
                'endLocationId'=> $endingLocationId,
                'startLocation'=> $fromCity . ", " . $fromCountry,
                'endLocation'=> $toCity . ", " . $toCountry,
                'email'=> $email

            ]);
        }
        else{
            return $view->render($response, 'trip/create_trip.twig', [
                'tripName' => $tripName,
                'tripId' => $tripId,
                'tripDetails' => $addTripDetails,
                'startLocationId'=> $startingLocationId,
                'endLocationId'=> $endingLocationId,
                'email'=>$email

            ]); 
        }
    }

    public function addTransportationDetails(
        $email,
        $tripId,
        $tripName,
        $startLocationId,
        $endLocationId,
        Request $request,
        Response $response,
        Twig $view){


        $data = $request->getParsedBody();
        $startDate =  filter_var($data['startDate'],FILTER_SANITIZE_STRING);
        $endDate =  filter_var($data['endDate'],FILTER_SANITIZE_STRING);
        $type =  filter_var($data["type"],FILTER_SANITIZE_STRING);

        $addTransportationDetails = new CreateTrip();

        $generateID = new IdGenerator();
        $transportationID = $generateID->newTransportationId();
        $addTransportationDetailsQuery = $addTransportationDetails->insertNewTravelTransportation(
            $transportationID,
            $startLocationId,
            $endLocationId,
            $tripId,
            $startDate,
            $endDate,
            $type);
        $tripModel = new Trip();
        
        $fromCity = $tripModel->getLocationCityById($startLocationId);
        $fromCountry = $tripModel->getLocationCountryById($startLocationId);

        $toCity = $tripModel->getLocationCityById($endLocationId);

        $toCountry = $tripModel->getLocationCountryById($endLocationId);





        if($addTransportationDetailsQuery){
            return $view->render($response, 'trip/trip_segment_activity.twig', [
                'tripName' => $tripName,
                'tripId' => $tripId,
                'startLocationId'=> $startLocationId,
                'endLocationId'=> $endLocationId,
                'startLocation'=> $fromCity . ", " . $fromCountry,
                'endLocation'=> $toCity . ", " . $toCountry,
                'email'=> $email
            ]);

        }
        else{

            return $view->render($response, 'trip/create_trip.twig', [
                'tripName' => $tripName,
                'tripId' => $tripId,
                'startLocationId'=> $startLocationId,
                'endLocationId'=> $endLocationId,
                'startLocation'=> $fromCity . ", " . $fromCountry,
                'endLocation'=> $toCity . ", " . $toCountry,
                'email'=> $email
            ]);
        }
    }
    public function addLocationActivityDetails($email, $tripName, $locationId1, $locationId2, $tripId, Request $request, Response $response, Twig $view){

        $data = $request->getParsedBody();
        $nameActivityStart =  filter_var($data['nameActivityStart'],FILTER_SANITIZE_STRING);
        $placeActivityStart =  filter_var($data['placeActivityStart'],FILTER_SANITIZE_STRING);
        $dateActivityStart =  filter_var($data['dateActivityStart'],FILTER_SANITIZE_STRING);
        $nameActivityEnd =  filter_var($data['nameActivityEnd'],FILTER_SANITIZE_STRING);
        $placeActivityEnd =  filter_var($data['placeActivityEnd'],FILTER_SANITIZE_STRING);
        $dateActivityEnd =  filter_var($data['dateActivityEnd'],FILTER_SANITIZE_STRING);

        $addActivityDetails = new CreateTrip();
        
        $newActivity1 = $addActivityDetails->insertNewActivity(
            $nameActivityStart, 
            $placeActivityStart, 
            $dateActivityStart, 
            $locationId1);

        $newActivity2 = $addActivityDetails->insertNewActivity(
            $nameActivityEnd,
            $placeActivityEnd,
            $dateActivityEnd,
            $locationId2);

        $tripModel = new Trip();

        $fromCity = $tripModel->getLocationCityById($locationId1);
        $fromCountry = $tripModel->getLocationCountryById($locationId1);

        $toCity = $tripModel->getLocationCityById($locationId2);

        $toCountry = $tripModel->getLocationCountryById($locationId2);
        
        
        
        if($newActivity1 and $newActivity2){
            return $view->render($response, 'trip/accommodation_segment.twig', [
                'tripName' => $tripName,
                'tripId' => $tripId,
                'startLocationId'=> $locationId1,
                'endLocationId'=> $locationId2,
                'startLocation'=> $fromCity . ", " . $fromCountry,
                'endLocation'=> $toCity . ", " . $toCountry,
                'email'=> $email,
                'tripName' => $tripName

            ]);
            
        }
        else{
            return $view->render($response, 'trip/create_trip.twig', [
                'userEmail' => $email
            ]);
        }

    }
    public function addAccommodationDetails($email, $locationId1, $locationId2, $tripId, $tripName, Request $request, Response $response, Twig $view){


        $data = $request->getParsedBody();
        $nameHotelStart =  filter_var($data['nameHotelStart'],FILTER_SANITIZE_STRING);
        $typeHotelStart =  filter_var($data['typeHotelStart'],FILTER_SANITIZE_STRING);
        $dateCheckInStart =  filter_var($data['dateCheckInStart'],FILTER_SANITIZE_STRING);
        $dateCheckoutStart =  filter_var($data['dateCheckoutStart'],FILTER_SANITIZE_STRING);
        $nameHotelEnd =  filter_var($data['nameHotelEnd'],FILTER_SANITIZE_STRING);
        $typeHotelEnd =  filter_var($data['typeHotelEnd'],FILTER_SANITIZE_STRING);
        $dateCheckInEnd =  filter_var($data['dateCheckInEnd'],FILTER_SANITIZE_STRING);
        $dateCheckoutEnd =  filter_var($data['dateCheckoutEnd'],FILTER_SANITIZE_STRING);

        $addAccommodationDetails = new CreateTrip();
        
        $accommodation1 = $addAccommodationDetails->insertNewAccommodation(
            $nameHotelStart, 
            $typeHotelStart, 
            $dateCheckInStart, 
            $dateCheckoutStart,
            $locationId1);

        $accommodation2 = $addAccommodationDetails->insertNewAccommodation(
            $nameHotelEnd,
            $typeHotelEnd,
            $dateCheckInEnd,
            $dateCheckoutEnd,
            $locationId2);
        
        if($accommodation1 and $accommodation2){

            return $view->render($response, 'trip/tripDetailsAddedSuccess.twig', [
                'userEmail' => $email
            ]);
            
        }
        
        else{
            return $view->render($response, 'trip/create_trip.twig', [
                'userEmail' => $email
            ]);
        }





    }





    }
