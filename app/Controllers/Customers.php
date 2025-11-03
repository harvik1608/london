<?php
    namespace App\Controllers;

    require APPPATH.'Views/vendor/vendor/autoload.php';

    use App\Models\CustomerModel;
    use App\Models\CompanyModel;

    class Customers extends BaseController
    {
        protected $helpers = ["custom"];
        
        public function __construct()
        {
            $session = session();
            if($session->get('userdata')) {
                $this->userdata = $session->get('userdata');
            }
            $this->path = "public/uploads/service_group";
        }

        public function index()
        {
            // $res = update_google_contact("people/c747308716880456350");
            // preview($res);

            if(isset($this->userdata["id"])) {
                if(check_permission("customers")) {
                    $model = new CustomerModel;
                    $data["customers"] = $model->where("companyId",static_company_id())->where("is_deleted",0)->orderBy("id","desc")->get()->getResultArray();
                    return view('admin/customer/list',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }
        
        public function load()
        {
            $session = session();
            $userdata = $session->get('userdata');
            $current_staff_id = $userdata['id'];
    
            $post = $this->request->getVar();
            $result = array("data" => array());
    
            // Extract DataTable parameters
            $draw = $post['draw'];
            $start = (int) $post['start'];
            $length = (int) $post['length'];
            $searchValue = $post['search']['value'];
            $orderColumn = $post['order'][0]['column'];
            $orderDir = $post['order'][0]['dir'];
            
            $columns = ['id', 'name', 'email','phone'];
            $orderBy = $columns[$orderColumn] ?? 'id';

            $model = db_connect();
            $query = $model->table("customers c");
            $query = $query->select("c.id,c.name,c.email,c.phone");
            $query = $query->where("c.companyId",static_company_id());
            $query = $query->where("c.is_deleted",0);
            if($searchValue != "") {
                $query = $query->where("(c.name LIKE '%".$searchValue."%' OR c.email LIKE '%".$searchValue."%' OR c.phone LIKE '%".$searchValue."%')");
            }
            $totalRecords = $query->countAllResults(false);
            $query = $query->orderBy($orderBy, $orderDir)->limit($length, $start);
            $entries = $query->get()->getResultArray();
            foreach ($entries as $key => $val) {
                $_editUrl = base_url("customers/" . $val["id"] . "/edit");
                $trashUrl = base_url("customers/" . $val["id"]);
    
                $buttons = "";
                // if($userdata['role'] == 1) {
                    // $buttons .= '<a href="' . $_editUrl . '"><i class="bx bx-eye icon-sm"></i></a>&nbsp;';
                    $buttons .= '<a href="' . $_editUrl . '" class="btn btn-sm btn-success"><i class="fa fa-edit text-white"></i></a>&nbsp;';
                    $buttons .= '<a href="javascript:;" class="btn btn-sm btn-danger" onclick=remove_row("' . $trashUrl . '",0)><i class="fa fa-trash text-white"></i></a>';
                // }
                $result['data'][$key] = [
                    "<small>".($key + 1)."</small>",
                    $val['name'],
                    $val['email'],
                    $val['phone'],
                    $buttons
                ];
            }

            // Add response metadata
            $result["draw"] = intval($draw);
            $result["recordsTotal"] = $totalRecords;
            $result["recordsFiltered"] = $totalRecords;
    
            // Output JSON
            echo json_encode($result);
            exit;
        }

        public function new()
        {
            if(isset($this->userdata["id"])) {
                if(check_permission("customers")) {
                    $data["customer"] = [];
                    return view('admin/customer/add_edit',$data);
                } else {
                    return redirect("dashboard");
                }
            }
        }

        public function create()
        {
            $session = session();
            if($session->get('userdata'))
            {
                $createdBy = $session->get('userdata');
                $post = $this->request->getVar();

                $post['companyId'] = static_company_id();
                $post['addedBy'] = $createdBy["id"];
                $post['updatedBy'] = $createdBy["id"];
                $post['createdAt'] = format_date(5);
                $post['updatedAt'] = format_date(5);

                $model = new CustomerModel();
                $model->insert($post);

                $customer_id = $model->getInsertID();
                if($customer_id > 0)
                {
                    $resource_id = add_google_contact($post);
                    if($resource_id != "") {
                        $model = new CustomerModel();
                        $model->update($customer_id,array("resource_id" => $resource_id));
                    }

                    $session->setFlashData('success','Customer added successfully');
                    $ret_arr = array("status" => 1);
                } else {
                    $ret_arr = array("status" => 0,"message" => "Error");
                }
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function edit($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                if(check_permission("customers")) {
                    $model = new CustomerModel();
                    $data['customer'] = $model->where('id',$id)->first();
                    if($data['customer']) {
                        return view('admin/customer/add_edit',$data);
                    } else { 
                        return redirect()->route('customers');
                    }
                } else {
                    return redirect()->route('dashboard');
                }
            } else {
                return redirect()->route('admin');
            } 
        }

        public function update($id)
        {
            $session = session();
            if($session->get('userdata'))
            {
                $createdBy = $session->get('userdata');
                $post = $this->request->getVar();

                $post['companyId'] = static_company_id();
                $post['updatedBy'] = $createdBy["id"];
                $post['updatedAt'] = format_date(5);

                $model = new CustomerModel();
                $data = $model->update($id,$post);
                if($data)
                {
                    $session->setFlashData('success','Customer edited successfully');
                    $ret_arr = array("status" => 1);
                } else
                    $ret_arr = array("status" => 0,"message" => "Error");
                
                echo json_encode($ret_arr);
                exit;
            }
        }

        public function delete($id)
        {
            $model = New CustomerModel;
            $custo = $model->select("resource_id")->where("id",$id)->first();
            $resource_id = $custo["resource_id"];

            if($model->update($id,["is_deleted" => 1])) {
                if(!is_null($resource_id) && $resource_id != "") {
                    delete_google_contact($resource_id);
                }
            }
            echo json_encode(array("status" => 200));
            exit;
        }
    }
