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
// Author:   Remesh Babu S <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+

namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Provider;
use Lab\Form\ProviderForm;
use Zend\View\Model\JsonModel;


class ProviderController extends AbstractActionController
{
		protected $providerTable;
    
    // Table Gateway
    public function getProviderTable()
    {
        if (!$this->providerTable) {
            $sm = $this->getServiceLocator();
            $this->providerTable = $sm->get('Provider\Model\ProviderTable');
        }
        return $this->providerTable;
    }
    
		// Index page
    public function indexAction()
    {
				$form 		= new ProviderForm();
				$index = new ViewModel(array(
						'form' 					=> $form,
				));
				return $index;
    }
		
		// List all providers
		public function getProcedureProvidersAction()
		{
				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$procProviders = $helper->getProcedureProviders();
				$data = new JsonModel($procProviders);
				//echo '<pre>'; prit_r($data); echo '</pre>';
				return $data;
		}
		
		// Save Procedure Provider (New Or Update)
		public function saveProcedureProviderAction()
		{
				$request = $this->getRequest();
				$response = $this->getResponse();
				if($request->isPost()){
						$return = $this->getLabTable()->saveProcedureProvider($request->getPost());
						if ($return) {
								$return = array('errorMsg' => 'Error while processing .... !');
						}
						$response->setContent(\Zend\Json\Json::encode($return));
				}
				return $response;
		}
		
		// Delete Procedur Provider
		public function deleteProcedureProviderAction()
		{
				$request = $this->getRequest();
				$response = $this->getResponse();
				if($request->isPost()){
						$return = $this->getLabTable()->deleteProcedureProvider($request->getPost());
						$response->setContent(\Zend\Json\Json::encode(array('success' => 'Recored Successfully Deleteed .... !')));
				}
				return $response;
		}
    
}