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
// Author:   Remesh Babu S  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Calendar\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Calendar\Model\Calendar;
use Calendar\Form\CalendarForm;
use Zend\View\Model\JsonModel;

class CalendarController extends AbstractActionController
{
		protected $calendarTable;
    
		// Index page
    public function indexAction()
    {
				global $pid;
				global $encounter;
				global $GLOBALS;

				$form = new CalendarForm();
				$request 			= $this->getRequest();
				$provider 	= $request->getQuery()->providerID;

				// Collect data from data base by the help of helper
				//$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');

				$result = $this->getCalendarTable()->getProviderData();
				
				$index = new ViewModel(array(
							'form' 				=> $form,
							'result'			=> $result,
							'provider'	=> $provider,
						));
				return $index;
    }
		
		// Month and Week Calendar page
		public function calendarMonthWeekAction()
    {
				global $pid;
				global $encounter;
				global $GLOBALS;

				$form = new CalendarForm();
				
				// Collect data from data base by the help of helper
				//$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$result = $this->getCalendarTable()->getProviderData();
				$request 			= $this->getRequest();
				$view 	= $request->getQuery()->view;
				$index = new ViewModel(array(
							'form' 				=> $form,
							'result'			=> $result,
							'view'				=> $view,
						));
				return $index;
    }
		
		// Calendar page (for Month and Week)
		public function calendarAction()
    {
				$request 			= $this->getRequest();
				$providerID 	= $request->getQuery()->providerID;
				$providerName	= $request->getQuery()->providerName;
				$view					= $request->getQuery()->view;
				$calendar = new ViewModel();
				$calendar = new ViewModel(array(
							'providerID'			=> $providerID,
							'providerName'		=> $providerName,
							'view'						=> $view,
						));
				return $calendar;
		}
		
		public function dataFeedAction()
		{
				$request 	= $this->getRequest();
				//$fh = fopen("D:/test.txt","a");
				//fwrite($fh,"testing ..  " . print_r($request->getQuery(),1));
				$method 	= $request->getQuery()->method;
			
				//$request->getPost()->timezone;

				switch ($method) {
						case "list":
								$date 		= $request->getPost()->showdate;
								$viewType = $request->getPost()->viewtype;
								$providerID = $request->getQuery()->providerID;
								$result = $this->getCalendarTable()->listCalendar($date, $viewType, $providerID);
								break;
						case "add":
								$startTime 			= $request->getPost()->CalendarStartTime;
								$endTime				= $request->getPost()->CalendarEndTime;
								$title					= $request->getPost()->CalendarTitle;
								$IsAllDayEvent 	= $request->getPost()->IsAllDayEvent;
								$ret = addCalendar($startTime, $endTime, $title, $IsAllDayEvent);
								break;
				}
				$data = new JsonModel($result);
				return $data;
		}
		
		// Get Providers
		public function getProvidersAction()
		{
				$request = $this->getRequest();
				$option = $request->getQuery()->opt;
				$data = array(
						'option'	=> $option,
				); 
				$result = $this->getCalendarTable()->getProviderData($data);
				$data = new JsonModel($result);
				return $data;
		}
		
		// Get Categories
		public function getCategoriesAction()
		{
				$request = $this->getRequest();
				$option = $request->getQuery()->opt;
				$id = $request->getQuery()->id;
				$data = array(
						'option'	=> $option,
						'id'			=> $id,		
				); 
				$result = $this->getCalendarTable()->getCategoriesData($data);
				$data = new JsonModel($result);
				return $data;	
		}
		
		// Get Facilities
		public function getFacilitiesAction()
		{
				$request = $this->getRequest();
				$option = $request->getQuery()->opt;
				$result = $this->getCalendarTable()->getFacilitiesData($option);
				$data = new JsonModel($result);
				return $data;
		}
		
		// Get Billing Facility
		public function getBillingFacilityAction()
		{
				$result = $this->getCalendarTable()->getBillingFacilityData();
				$data = new JsonModel($result);
				return $data;
		}
		
		// Get Status
		public function getStatusAction()
		{
				$result = $this->getCalendarTable()->getStatusData();
				$data = new JsonModel($result);
				return $data;
		}
		
		// New and Edit Calendar
		public function editAction()
		{

		}
	
		
    public function getCalendarTable()
    {
        if (!$this->calendarTable) {
            $sm = $this->getServiceLocator();
            $this->calendarTable = $sm->get('Calendar\Model\CalendarTable');
        }
        return $this->calendarTable;
    } 
}