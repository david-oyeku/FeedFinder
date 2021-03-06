<?php

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 */
class Venue extends Model
{
        public $hasMany = array(
         'Review' => array(
             'className' => 'Review', ), );
        
    public function getVenuesWithin($data)
    {
        $from = $data['from'];
        $to = $data['to'];

        $conditions = array(
            'Venue.flag' => 0,
            'Venue.created >=' => $from,
            'Venue.created <=' => $to
            );

        return $this->find('all', array(
            'conditions' => $conditions
        ));
    }

    public function venuesWithId($id, $from, $to){
        return $this->find('count',array(
            'conditions'=> array('
                Venue.postgre_admin_one_id'=>$id,
                'Venue.created >=' => $from,
                'Venue.created <=' => $to,
                )
            ));
    }

    /**
    * Get venues created in time range 
    */
    public function getVenuesAdminOne($data){
        $conditions = array(
            'Venue.flag' => 0,
            'Venue.created >=' => $data['from'],
            'Venue.created <=' => $data['to'],
        );
        $group = array('Venue.postgre_admin_one_id');
        $fields = array(
            'Venue.name',
            'Venue.created',
            'Venue.latitude',
            'Venue.longitude',
            'Venue.postgre_admin_one_id',
            'Venue.postcode',
            'Venue.country',
            'Venue.city',    
            'COUNT(Venue.postgre_admin_one_id) as count'
        );
        return $this->find('all', array(
           'fields' => $fields,
            'conditions' => $conditions,
            'group' => $group
        ));
    }

    public function getVenuesUkAdminThree($data){
        $conditions = array('Venue.flag' => 0,
            'Venue.created >=' => $data['from'],
            'Venue.created <=' => $data['to'],
        );
        $group = array('Venue.postgre_uk_id');
         $fields = array(
            'Venue.name',
            'Venue.created',
            'Venue.latitude',
            'Venue.longitude',
            'Venue.postgre_uk_id',
            'Venue.postcode',
            'Venue.country',
            'Venue.city',    
            'COUNT(Venue.postgre_uk_id) as count'
        );
        $fields = array(
            'Venue.name',
            'Venue.created',
            'Venue.latitude',
            'Venue.longitude',
            'Venue.postgre_uk_id',
            'COUNT(Venue.postgre_uk_id) as count'
        );
        return $this->find('all', array(
            'fields' => $fields,
            'conditions' => $conditions,
            'group' => $group
        ));
    }

    public function getLatLng($data)
    {
        $results = $this->find('all', array(
            'fields' => array('Venue.latitude', 'Venue.longitude'),
            'conditions' => array('Venue.id' => $data),
        ));
        $latlng = array();
        // print_r($results);
        foreach ($results as $result => $value) {
            $latlng[] = $value['Venue'];
        }
        return $latlng;
    }


    public function findRatingsById($id)
    {
        $venue = $this->findAllById($id);
        $reviews = $venue[0]['Review'];
        $ratings = array('terrible' => 0, 'poor' => 0, 'average' => 0, 'v-good' => 0, 'excellent' => 0);
        foreach ($reviews as $review => $value) {
            switch ((int)$value['average_rating']) {
                case 1:
                    $ratings['terrible']++;
                    break;
                case 2:
                    $ratings['poor']++;
                    break;
                case 3:
                    $ratings['average']++;
                    break;
                case 4:
                    $ratings['v-good']++;
                    break;
                case 5:
                    $ratings['excellent']++;
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $ratings;

    }


}
