<?php
/**
 * Created by PhpStorm.
 * User: samirmarin
 * Date: 2016-06-09
 * Time: 2:57 PM
 */


$app->get('/', ['Trippi\Controllers\HomeController', 'index'])->setName('home');

$app->post('/profile', ['Trippi\Controllers\HomeController', 'signIn'])->setName('signIn');

$app->post('/new_account', ['Trippi\Controllers\HomeController', 'signUp'])->setName('signUp');

$app->post('/new_profile', ['Trippi\Controllers\NewProfileController', 'create_profile'])->setName('getInfo');

$app->get('/search', ['Trippi\Controllers\SearchController', 'index'])->setName('goToSearch');

$app->get('/search/users', ['Trippi\Controllers\SearchController', 'searchByUser'])->setName('searchByUser');

//TODO: SM: this rout naming a a bit confusing it goes to the home buts its route with trips time permiting we can refactor the name.
$app->get('/profile', ['Trippi\Controllers\HomeController', 'signIn'])->setName('Trips.signIn');

$app->get('/profile/{tripId}', ['Trippi\Controllers\ProfileController', 'getTrip'])->setName('trip.getTrip');
$app->post('/createProfile', ['Trippi\Controllers\CreateTripController', 'createTrip'])->setName('trip.createTrip');
$app->get('/createProfile', ['Trippi\Controllers\CreateTripController', 'createTrip'])->setName('trip.getCreateTrip');

$app->post('/createProfile/addLocations', ['Trippi\Controllers\CreateTripController', 'addLocationDetails'])->setName('addLocationDetails');


$app->get('/deleteProfile/{tripId}', ['Trippi\Controllers\ProfileController', 'deleteTrip'])->setName('trip.deleteTrip');


$app->get('/getTrips', ['Trippi\Controllers\ProfileController', 'getAllTrips'])->setName('trip.getAllTrips');

$app->get('/otherProfile/{email}', ['Trippi\Controllers\ProfileController', 'getOtherUser'])->setName('getProfile');

$app->post('/otherProfile/', ['Trippi\Controllers\ProfileController', 'getOtherUser'])->setName('viewProfile');

$app->post('/addedRating/{remail}', ['Trippi\Controllers\RatingsController', 'add_rating'])->setName('addRating');







