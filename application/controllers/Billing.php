<?php

defined('BASEPATH') OR exit('No direct script access allowed');
include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class Billing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Calcutta");
        $this->load->library(array('form_validation', 'session', 'email', 'encryption'));
        $this->load->helper(array('url', 'html', 'form', 'email'));
        $this->load->model("Billmodel");
        $this->gst = 18;
        if (!isset($this->session->userdata['client_logged_in']['email'])) {
            redirect('login');
        }
		$this->user_id = $this->session->userdata['client_logged_in']['user_id'];
    }

    public function index()
	{
        $string = 'ABCDEFGHLKMNPQRSTUVWXYZ' . time() . mt_rand();
        $contractnumber 		 = substr(str_shuffle($string), 0, 8);
		$data['user_id']   		 = $userId = $this->user_id;
		$data['taxcodes'] 		 = $data['services'] = $data['companies'] = array();
		$integrationDetail 		 = getIntegrationDetails('both',$userId);
		if(!empty($integrationDetail) && $integrationDetail->type == 'Quickbooks')
		{
			$response = updateAccessToken('Quickbooks',$userId);
			if( !$response['success'] )
			{
				$this->session->set_flashdata('error', $response['error_message']);
				redirect('billing');
			}
			else
			{
				$dataService  			 = $response['dataService'];
				$data['taxcodes']        = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/getApis/example_tax_code_query.php');
				$data['services']        = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/getApis/getinvoiceitem.php');;
				$data['companies'] 		 = $this->Billmodel->companieslist($this->user_id,'Quickbooks',$integrationDetail->realmId);
			}
		}
		else if(!empty($integrationDetail) && $integrationDetail->type == 'Xero')
		{
			require_once(APPPATH . 'third_party/xerophp/public.php');
			$data['companies'] 		 = $this->Billmodel->companieslist($this->user_id,'Xero',$integrationDetail->realmId);
			#Fetch Items (Product/Service)
			/*$response = $XeroOAuth->request('GET', $XeroOAuth->url('Taxrates', 'core'), array(), 'xml', '');
	        //echo "<prE>"; print_r($response); die('======');
			if ($XeroOAuth->response['code'] == 200)
			{
	            $taxRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
		        $taxRes = json_decode(json_encode($taxRes), true);
				//echo "<pre>";print_r($taxRes);die;
		        if(!empty($taxRes['TaxRates']['TaxRate']))
				{
		        	if(isset($taxRes['TaxRates']['TaxRate'][0]))
					{
						$taxData = array();
		        		foreach ($taxRes['TaxRates']['TaxRate'] as $key => $tax) {
							$taxData[$key]['taxCodeId'] = isset($tax['Name']) ? $tax['Name'] : '';
							$taxData[$key]['taxName'] = isset($tax['Name']) ? $tax['Name'] : '';
						}
					}else{
						$taxData = array();
						$tax = $taxRes['TaxRates']['TaxRate'];
						$taxData[0]['taxCodeId'] = isset($tax['taxCodeId']) ? $tax['Id'] : '';
						$taxData[0]['taxName'] = isset($tax['Name']) ? $tax['Name'] : '';
					}
					$data['taxcodes']        = $taxData;
				}
			}
			else if ($XeroOAuth->response['code'] == 401)
			{
				$this->session->set_flashdata('error_message', 'Token expired. Please authorize again.');
				redirect('billing/integrations');
			}
			else
			{
				$this->session->set_flashdata('error_message', $response['response']);
				redirect('billing/integrations');
			}
			#Fetch Items (Product/Service)
			$response = $XeroOAuth->request('GET', $XeroOAuth->url('Items', 'core'), array(), 'xml', '');
	        //echo "<prE>"; print_r($response); die('======');
			if ($XeroOAuth->response['code'] == 200)
			{
	            $inventoryRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
		        $inventoryRes = json_decode(json_encode($inventoryRes), true);
		        //echo "<prE>"; print_r($inventoryRes); die;
				$inventoryData = array();
		        if(!empty($inventoryRes['Items']['Item'])){
		        	if(isset($inventoryRes['Items']['Item'][0]))
					{
		        		foreach ($inventoryRes['Items']['Item'] as $key => $inventory)
						{
			             	$inventoryData[$key]['itemid'] = isset($inventory['ItemID']) ? $inventory['ItemID'] : '';
			             	$inventoryData[$key]['itemcode'] = isset($inventory['Code']) ? $inventory['Code'] : '';
							$inventoryData[$key]['FullyQualifiedName'] = isset($inventory['Name']) ? $inventory['Name'] : '';
			            }
		        	}
					else
					{
		        		$inventory = $inventoryRes['Items']['Item'];
		        		$inventoryData = array();
		             	$inventoryData[0]['itemid'] = isset($inventory['ItemID']) ? $inventory['ItemID'] : '';
		             	$inventoryData[0]['itemcode'] = isset($inventory['Code']) ? $inventory['Code'] : '';
		                $inventoryData[0]['FullyQualifiedName'] = isset($inventory['Name']) ? $inventory['Name'] : '';
		        	}
				}
				$data['services']        = $inventoryData;
			}
			else if ($XeroOAuth->response['code'] == 401)
			{
				$this->session->set_flashdata('error_message', 'Token expired. Please authorize again.');
				redirect('billing/integrations');
			}
			else
			{
				$this->session->set_flashdata('error_message', $response['response']);
				redirect('billing/integrations');
			}*/
		}
		else
		{
			$data['companies'] 		 = $this->Billmodel->companieslist($this->user_id,null,null);
		}
		$data['plans'] 			 = $this->Billmodel->planslist();
		$data['contract_number'] = $contractnumber;
		//echo "<pre>";print_r($data);die;
		$this->load->view('contract', $data);
    }

    public function createcontract()
	{
		set_time_limit(0);
		$userId   = $this->user_id;
		$serviceContactId   = $service_organisation_id = $uniqueId = '';
		$string 		 = 'ABCDEFGHLKMNPQRSTUVWXYZ' . time() . mt_rand();
		$invoice_code 	 = substr(str_shuffle($string), 0, 6);
		$contract_number = $this->input->post('contract_number');
		$company_id 	 = $this->input->post('company_id');
		$plan 			 = $this->input->post('plan');
		$startdate 		 = $this->input->post('plan_start_date');
		$enddate 		 = $this->input->post('plan_end_date');
		if ($plan == 0 || empty($startdate) || empty($enddate) || empty($company_id)) {
			$this->session->set_flashdata('error', 'data error');
			redirect('/billing');
		}
		else
		{
			$serviceInvoiceId = $customer_id = '';
			$invoiceType 	  = 'local';
			$companyData = getSomeFields( 'customer_id', array('company_code'=>$company_id),'companies' );
			if( empty($companyData) )
			{
				$this->session->set_flashdata('error', 'Something went wrong. Please try again.');
				redirect('/billing');
			}
			else
			{
				$customer_id = $companyData->customer_id;
			}
			$planprice = $plan_name = '';
			$planData  = getSomeFields( 'plan_price,plan_name', array('id'=>$plan),'plans' );
			if(!empty($planData))
			{
				$planprice = $planData->plan_price;
				$plan_name = $planData->plan_name;
			}
			$_POST['customer_id'] = $customer_id;
			$_POST['invoiceNo']   = $invoice_code;
			$_POST['invoiceDate'] = date('Y-m-d');
			$_POST['qty'] 		  = 1;
			$_POST['rate'] 		  = $planprice;
			$_POST['amount']	  = $planprice * 1;
			$integrationDetail 		 = getIntegrationDetails('both',$userId);
			if(!empty($integrationDetail) && $integrationDetail->type == 'Quickbooks')
			{
				$response = updateAccessToken('Quickbooks',$userId);
				if( !$response['success'] )
				{
					$this->session->set_flashdata('error', $response['error_message']);
					redirect('billing');
				}
				else
				{
					$dataService  		  = $response['dataService'];
					$invoiceResponse      = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/getApis/invoice_add.php');
					if( !$invoiceResponse['success'] )
					{
						$this->session->set_flashdata('error', $response['error_msg']);
						redirect('billing');
					}
					else
					{
						$serviceInvoiceId = $invoiceResponse['qbInvoiceId'];
						$invoiceType 	  = 'Quickbooks';
						$uniqueId  		  = $userId.'-'.$serviceInvoiceId.'-qb';
						$service_organisation_id = $integrationDetail->realmId;
					}
				}
			}
			else if(!empty($integrationDetail) && $integrationDetail->type == 'Xero')
			{
				$tax_percentage 	 = $this->gst;
				$tax_amount  		 = $planprice * ($this->gst / 100);
				$total_amount 		 = $planprice + $planprice * ($this->gst / 100);
				$responsee = updateAccessToken('Xero',$userId);
        		if( !$responsee['success'] )
        		{
        			$this->session->set_flashdata('error', $responsee['error_message']);
				    redirect('/billing');
        		}
        		else
        		{ 	
        		    $XeroOAuth = $responsee['XeroOAuth'];
    				require_once(APPPATH . 'third_party/xerophp/public.php');
    				$invoiceXml = '<Invoices>
    								  <Invoice>
    									<Type>ACCREC</Type>
    									<Contact>
    									  <ContactID>'.$customer_id.'</ContactID>
    									</Contact>
    									<Date>'.$_POST['invoiceDate'].'T00:00:00</Date>
    									<DueDate>'.$_POST['invoiceDate'].'T00:00:00</DueDate>
    									<LineAmountTypes>Exclusive</LineAmountTypes>
    									<LineItems>
    									  <LineItem>
    										<Description>Test</Description>
    										<Quantity>'.$_POST['qty'].'</Quantity>
    										<UnitAmount>'.$_POST['amount'].'</UnitAmount>
    										<AccountCode>200</AccountCode>
    										<TaxAmount>'.$tax_amount.'</TaxAmount>
    									  </LineItem>
    									</LineItems>
    								  </Invoice>
    								</Invoices>';

    				$response = $XeroOAuth->request('PUT', $XeroOAuth->url('Invoices', 'core'), array(), $invoiceXml);
    				if ($response['code'] == 200)
    				{
    					$invoiceResponse = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
    					$invoiceResponse = json_decode(json_encode($invoiceResponse), true);
    					$invoiceType 	  = 'Xero';
    					if(!empty($invoiceResponse['Invoices']['Invoice']))
    					{
    						$serviceInvoiceId = $invoiceResponse['Invoices']['Invoice']['InvoiceID'];
    						$uniqueId  		  = $userId.'-'.$serviceInvoiceId.'-xero';
    					}
    					$service_organisation_id = $integrationDetail->realmId;
    				}
    				else
    				{
    					$this->session->set_flashdata('error', $response['response']);
    					redirect('billing');
    				}
        		}
			}
			$this->db->trans_begin();
			$contracts = array(
				'contract_code' 	=> $contract_number,
				'company_code' 		=> $company_id,
				'contract_status' 	=> 0,
			);
			$this->db->insert('contracts', $contracts);

			$contract_info = array(
				'contract_code' 	=> $contract_number,
				'plan_id' 			=> $plan,
				'plan_name' 		=> $plan_name,
				'plan_price' 		=> $planprice,
				'plan_count' 		=> '1',
				'plan_discount' 	=> '0',
				'plan_start_date' 	=> $startdate,
				'plan_end_date' 	=> $enddate
			);
			$this->db->insert('contracts_information', $contract_info);
			$tax_percentage = $tax_amount = $total_amount = 0;
			if( $invoiceType == 'local' || $invoiceType == 'Xero' )
			{
				$tax_percentage 	 = $this->gst;
				$tax_amount  		 = $planprice * ($this->gst / 100);
				$total_amount 		 = $planprice + $planprice * ($this->gst / 100);
			}
			$invoice = array(
				'user_id'	 		 => $userId,
				'invoiceId' 		 => $serviceInvoiceId,
				'type'	 	 		 => $invoiceType,
				'invoice_code' 		 => $invoice_code,
				'contract_code' 	 => $contract_number,
				'plans_total_amount' => $planprice,
				'tax_percentage' 	 => $tax_percentage,
				'tax_amount'  		 => $tax_amount,
				'total_amount' 		 => $total_amount,
				'company_code' 		 => $company_id,
				'customer_id'   	 => $customer_id,
				'createDateTime'   	 => date('Y-m-d H:i:a'),
				'uniqueId' 			 => $uniqueId,
				'service_organisation_id' => $service_organisation_id,
			);
			$this->db->insert('invoices', $invoice);
			$payments = array(
				'invoice_code' 		=> $invoice_code,
				'contract_code' 	=> $contract_number,
				'payment_status' 	=> 'pending',
			);
			$this->db->insert('payments', $payments);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Failed to add');
				redirect('/billing');
			} else {
				$this->db->trans_complete();
				$this->db->trans_commit();
				$this->session->set_flashdata('success', 'Contract Success');
				redirect('/billing');
			}
		}
	}

    public function contractlist() {
        $data['contracts'] = $this->Billmodel->contractlist();
        $this->load->view('contract_list', $data);
    }

    public function integrations() {
		$data['userId'] = $this->user_id;
        $data['integrations'] = $this->Billmodel->integrationslist();
        $this->load->view('integrations', $data);
    }

	public function company() {
		$this->load->view('company');
	}


	public function createcompany()
	{
		if( isset($_POST) )
		{
			$userId 			= $this->user_id;
			$serviceContactId   = $service_organisation_id = $uniqueId = '';
			$string 			= 'ABCDEFGHLKMNPQRSTUVWXYZ' . time() . mt_rand();
			$company_code 		= substr(str_shuffle($string), 0, 8);
			$company_name 		= $this->input->post('company_name');
			$company_email 		= $this->input->post('company_email');
			$company_contact 	= $this->input->post('company_contact');
			$company_display_as = $this->input->post('company_name_as');
			$company_gst_type 	= $this->input->post('gst_reg_type');
			$company_gstin 		= $this->input->post('gstin');
			if (empty($company_name) || empty($company_email) || empty($company_contact))
			{
				$this->session->set_flashdata('error', 'data error');
				redirect('/billing/company');
			}
			$integrationDetail 		 = getIntegrationDetails('both',$userId);
			if(!empty($integrationDetail) && $integrationDetail->type == 'Quickbooks')
			{
				$response = updateAccessToken('Quickbooks',$userId);
				if( !$response['success'] )
				{
					$this->session->set_flashdata('error', $response['error_message']);
					redirect('/billing/company');
				}
				$dataService  		  = $response['dataService'];
				$customerResponse     = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/getApis/addcustomer.php');
				if( !$customerResponse['success'] )
				{
					$this->session->set_flashdata('error', $response['error_msg']);
					redirect('billing');
				}
				else
				{
					$serviceContactId = $customerResponse['last_cus_id'];
				}
				$service_organisation_id = $integrationDetail->realmId;
				$type 	  				 = 'Quickbooks';
				$uniqueId  		  		 = $userId.'-'.$serviceContactId.'-qb';
			}
			else if(!empty($integrationDetail) && $integrationDetail->type == 'Xero')
			{
				$service_organisation_id = $integrationDetail->realmId;
				include_once(APPPATH . 'third_party/xerophp/public.php');
				$response 		= $XeroOAuth->request('GET', $XeroOAuth->url('Organisations', 'core'), array(), 'xml', '');
				if ($XeroOAuth->response['code'] == 200)
				{
					$contactXml = '<Contacts>
									  <Contact>
										<Name>'.$company_name.'</Name>
										<EmailAddress>'.$company_email.'</EmailAddress>
										<Phones>
										  <Phone>
											<PhoneType>DEFAULT</PhoneType>
											<PhoneNumber>'.$company_contact.'</PhoneNumber>
										  </Phone>
										</Phones>
									  </Contact>
									</Contacts>';
					$response = $XeroOAuth->request('PUT', $XeroOAuth->url('Contacts', 'core'), array(), $contactXml);
					if ($response['code'] == 200)
					{
						$contactResponse = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
						$contactResponse = json_decode(json_encode($contactResponse), true);
						//echo "<pre>";print_r($contactResponse);die('lol');
						$type 	  = 'Xero';
						if(!empty($contactResponse['Contacts']['Contact']))
						{
							$serviceContactId = $contactResponse['Contacts']['Contact']['ContactID'];
							$uniqueId  		  = $userId.'-'.$serviceContactId.'-xero';
						}
					}
					else
					{
						$this->session->set_flashdata('error', $response['response']);
						redirect('billing/company');
					}
				}
				else if ($XeroOAuth->response['code'] == 401)
				{
					$this->session->set_flashdata('error', 'Token expired. Please authorize again.');
					redirect('billing/company');
				}
				else
				{
					$this->session->set_flashdata('error', $response['response']);
					redirect('billing/company');
				}
			}
			$companyinfo = array(
				'user_id' 					=> $userId,
				'company_code' 				=> $company_code,
				'customer_id' 				=> $serviceContactId,
				'service_organisation_id' 	=> $service_organisation_id,
				'type' 						=> $type,
				'company_email' 			=> $company_email,
				'company_contact' 			=> $company_contact,
				'company_name' 				=> $company_name,
				//'company_name_display_as' 	=> $company_display_as,
				'company_gst' 				=> $company_gstin,
				'uniqueId' 					=> $uniqueId
			);
			$this->db->insert('companies', $companyinfo);
			if ($this->db->affected_rows() > 0)
			{
				$this->session->set_flashdata('success', 'Company added');
				redirect('/billing/company');
			}
			else
			{
				$this->session->set_flashdata('error', 'Failed to add');
				redirect('/billing/company');
			}
		}
    }


    public function invoices() {
        $data['invoices'] = $this->Billmodel->invoiceslist();
        $this->load->view('invoice_list', $data);
    }
}
