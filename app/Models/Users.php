<?php
/**
 * Created by PhpStorm.
 * User: samirmarin
 * Date: 2016-06-10
 * Time: 5:30 PM
 */
namespace Trippi\Models;

use mysqli;
use Trippi\Models\Db;

class Users {

  /*
   * VANILLA USER SEARCH QUERIES
   */ 

  public function searchUserByUserName($userName) {
      $query = "SELECT `email`,`username`,`name`,`hometown`,`country`,`dateOfBirth`,`aboutMe`,`rating` 
                FROM `user` 
                WHERE username = " . ModelsUtils::mysqlstring($userName);

      return $this->returnResult( $this->submitQuery($query));
  }

  public function searchByUserEmail($email) {
      $query = "SELECT `email`,`username`,`name`,`hometown`,`country`,`dateOfBirth`,`aboutMe`,`rating` 
                FROM `user` 
                WHERE email = " . ModelsUtils::mysqlstring($email);

      return $this->returnResult( $this->submitQuery($query));
  }

  public function searchByUserRating($rating) {
      $query = "SELECT `email`,`username`,`name`,`hometown`,`country`,`dateOfBirth`,`aboutMe`,`rating` 
                FROM `user` 
                WHERE rating = " . $rating;

      return $this->returnResult( $this->submitQuery($query));
  }

  public function searchByUserDOB($dob) {
      // TODO: Determine how DOB's are searched by day, year,
      // and look for members born in the same year

      $query = "SELECT `email`,`username`,`name`,`hometown`,`country`,`dateOfBirth`,`aboutMe`,`rating` 
                FROM `user` 
                WHERE dateOfBirth = " . $dob;

      return $this->returnResult( $this->submitQuery($query));
  }

  public function searchByUserLocation($country, $homeTown) {
      $query = "SELECT `email`,`username`,`name`,`hometown`,`country`,`dateOfBirth`,`aboutMe`,`rating` 
                FROM `user` 
                WHERE country = " . ModelsUtils::mysqlstring($country);

      if( $homeTown ) {
        $query .= " AND hometown='$homeTown'";
      }

      return $this->returnResult( $this->submitQuery($query));
  }

  public function searchByUserName($name) {
      $query = "SELECT `email`,`username`,`name`,`hometown`,`country`,`dateOfBirth`,`aboutMe`,`rating` 
                FROM `user` 
                WHERE `name` LIKE " . ModelsUtils::mysqlstring($name);

      return $this->returnResult( $this->submitQuery($query));
  }

  

  // Return all members participating in a particular trip
  public function returnMembersOfTrip($tripId) {
      $query = "SELECT * 
                FROM `joins` j, `user` u 
                WHERE u.email = j.email AND 
                      tripId = " . ModelsUtils::mysqlstring($tripId);

      return $this->returnResult( $this->submitQuery($query));
  }
    

  // Return all members who have travelled to a location
  public function returnUsersTravelledTO($city) {
    // trip status == complete
    // trip location == location
    $query = "SELECT * 
              FROM `joins` j, `user` u 
              WHERE j.email = u.email AND 
                    j.tripID IN (SELECT T1.tripID 
                                 FROM `location` L1, `travelling_transportation` T1 
                                 WHERE L1.locationID = T1.to_locationID AND 
                                       L1.city = " . ModelsUtils::mysqlstring($city) . ")";

    return $this->returnResult( $this->submitQuery($query));
  }
    
  // Return all trips a user is involved in
    public function returnAllUsersTrips($email) {
        $query = "SELECT `email`, `tripId` 
                  FROM `plan` p 
                  WHERE p.email = " . ModelsUtils::mysqlstring($email) . " 
                  UNION 
                  SELECT `email`, `tripId` 
                  FROM `joins` j 
                  WHERE j.email = " . ModelsUtils::mysqlstring($email);
  
    return $this->returnResult( $this->submitQuery($query));
  }

    private function submitQuery($query){
        $db = new Db();
    
        return $db->query($query);
    
    }

  // Helper function for returning results into an
  // array
  private function returnResult( $result ) {
    if( $result ) {
      // Successful Match
      $rows = array();
      while($row = mysqli_fetch_array($result)) {
        $rows[] = $row;
      }
      return $rows;
    } else {
      // No match found
      return false;
    }  
  }

    // TODO:
    // all users travelling to specific location
        // travelling to by date

    // participating in activity
      // by date


    // all users who have done acivity
    // all users who have been to hotel

}