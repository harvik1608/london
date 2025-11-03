<?php
    namespace App\Controllers;

    use App\Models\Staff;
    use App\Models\CompanyModel;
    use App\Models\StaffTimingModel;

    class Auth extends BaseController
    {
        protected $helpers = ["custom"];
        public function index()
        {
            return view('auth/sign_in');
        }

        public function check_sign_in()
        {
            $session = session();
            $post = $this->request->getVar();
            
            $model = new Staff;
            $where = array('email' => $post['email'],'password' => md5($post['password']));
            $userdata = $model->where($where)->first();
            if($userdata) {
                if($userdata['is_active'] == 1) {
                    $companyId = 1;

                    $where = ["staffId" => $userdata["id"],"date" => date("Y-m-d")];
                    $model = new StaffTimingModel;
                    $today = $model->select("companyId")->where($where)->get()->getRowArray();
                    if($today) {
                        $companyId = $today["companyId"];
                    }
                    $session->set('companyId',$companyId);

                    $model = new CompanyModel;
                    $company = $model->where('id',$companyId)->first();
                    $session->set('company',$company);

                    $session->set('userdata',$userdata);
                    echo json_encode(array("status" => 1,"message" => "","href" => base_url("dashboard")));
                } else {
                    echo json_encode(array("status" => 0,"message" => "Your account is inactive by Admin."));
                }
            } else {
                echo json_encode(array("status" => 0,"message" => "Email or password is wrong"));
            }
        }

        public function logout()
        {
            $session = session();
            $session->destroy();
            return redirect()->route('admin');
        }
    }
