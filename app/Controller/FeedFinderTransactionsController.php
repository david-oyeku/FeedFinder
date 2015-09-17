<?php

App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::import('vendor', 'geoPHP/geoPHP.inc');

/**
 * Static content controller.
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class FeedFinderTransactionsController extends AppController
{
    public $components = array('Session','RequestHandler');
    public $helpers = array('Session', 'Html', 'Form','Js' => array('jquery'));
    public $uses = array('Venue','Review','FeedFinderTransaction','UserLookupTable',
                         'World','AdminOne','UkAdminThree','User', );

    public function index()
    {
        // ini_set('memory_limit', '2048M');
        // ini_set('max_execution_time', 300);
    }

    public function stats()
    {
    }

    public function review_interq_ukadminthree()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->Review->getReviewByAddress($this->request->query);
            if (count($results) > 0) {
                $quartiles = $this->UkAdminThree->updateReview($results);
                echo json_encode($quartiles);
            } else {
                echo 'no result brah ...';
            }
        }
    }

    public function review_interq_adminone()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->Review->getReviewByCity($this->request->query);
            if (count($results) > 0) {
                $quartiles = $this->AdminOne->updateReview($results);
                echo json_encode($quartiles);
            } else {
                echo 'no result brah ...';
            }
        }
    }

    public function review_interq_world()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->Review->getReviewByCountry($this->request->query);
          //$this->_print_array($results);
          if (count($results) > 0) {
              $quartiles = $this->World->updateReview($results);
              echo json_encode($quartiles);
          } else {
              echo 'no result brah ...';
          }
        }
    }

    public function get_stats_venues()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->Venue->getVenuesWithin($this->request->query);
            echo json_encode($results);
        }
    }

    public function average_rating()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            // print_r($this->request->query);
            $data = $this->request->query;
            $results = $this->Review->getVenueRating($data);
            $arrayName = array();
            switch ($data['model']) {
            case 'World':
              $inter_q_world = $this->World->updateVenueRating($results);
              echo json_encode($inter_q_world);

              break;
            case 'AdminOne':
              $inter_q_adminone = $this->AdminOne->updateVenueRating($results);
              echo json_encode($inter_q_adminone);
              break;
            case 'UkAdminThree':
              $inter_q_uk = $this->UkAdminThree->updateVenueRating($results);
              echo json_encode($inter_q_uk);
              break;
            default:
              # code...
              break;
          }
        }
    }

    public function users_interq_world()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->User->getUserBaseLocation($this->request->query);
            if(!empty($results)){
            $latlng = $this->Venue->getLatLng($results);
            $quartiles = $this->World->updateUserCount($latlng);
            echo json_encode($quartiles);
          }else{
            $quartiles = array('first_q'=>0,'second_q'=>0,'third_q'=>0, 'geo_layer_name'=>'admin_ones','results'=>0);
            echo json_encode($quartiles);

          }
        }
    }
    public function users_interq_adminone()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->User->getUserBaseLocation($this->request->query);
            if(!empty($results)){
              $latlng = $this->Venue->getLatLng($results);
              $quartiles = $this->AdminOne->updateUserCount($latlng);
              echo json_encode($quartiles);
            }else{
              $quartiles = array('first_q'=>0,'second_q'=>0,'third_q'=>0, 'geo_layer_name'=>'admin_ones','results'=>0);
              echo json_encode($quartiles);

            }

        }
    }
    public function users_interq_ukadminone()
    {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $results = $this->User->getUserBaseLocation($this->request->query);
            if(!empty($results)){
            $latlng = $this->Venue->getLatLng($results);
            $quartiles = $this->UkAdminThree->updateUserCount($latlng);
            echo json_encode($quartiles);
          }else{
            $quartiles = array('first_q'=>0,'second_q'=>0,'third_q'=>0, 'geo_layer_name'=>'admin_ones','results'=>0);
            echo json_encode($quartiles);

          }
        }
    }

    public function getVenueByCountry()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $results = $this->Venue->getVenueByIso($this->request->data);

            echo json_encode($results);
        }
    }

    public function totalUsers()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $results = array();
            $results['total_users'] = $this->User->getTotalUsers($this->request->query);
            $ans = $this->User->getActiveUsers($this->request->query);
            $results['active_users'] = intval($ans[0][0]['activeUsers']);

            echo json_encode($results);
        }
    }

    public function _print_array($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}
