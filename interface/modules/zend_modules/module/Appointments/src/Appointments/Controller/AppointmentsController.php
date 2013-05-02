<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Remesh Babu  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Appointments\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Appointments\Model\Appointments;
use Zend\View\Model\JsonModel;


class AppointmentsController extends AbstractActionController
{
    protected $appointmentsTable;
    
    public function indexAction()
    {
    	
    	
    }
    
    public function getAppointmentsDataAction()
    {
      $request = $this->getRequest();
      $data = array();
      if($request->isPost()){
             $data = array(
          'criteria'	=> $request->getPost('criteria'),
          'patient' 	=> $request->getPost('patient'),
          'dos' 		=> $request->getPost('dos'),
          'dtFrom' 	=> $request->getPost('dtFrom'),
          'dtTo' 		=> $request->getPost('dtTo'),
          'page'          => $request->getPost('page'),
          'rows'          => $request->getPost('rows'),
            );
          }
      $result = $this->getAppointmentsTable()->listAppointments($data);
      $data = new JsonModel($result);
      return $data;
    }    
       
    public function getAppointmentsTable()
    {
      if (!$this->appointmentsTable) {
          $sm = $this->getServiceLocator();
          $this->appointmentsTable = $sm->get('Appointments\Model\AppointmentsTable');
      }
      return $this->appointmentsTable;
    } 
}