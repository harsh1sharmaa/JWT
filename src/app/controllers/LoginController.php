<?php

// 
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Escaper;

class LoginController extends Controller
{
    public function indexAction()
    {
        return '<h1>Hello!!!</h1>';
    }

    public function loginuserAction()
    {

        // echo "hello";
        // die();

        $data = $this->request->getPost();

        if (isset($data['submit'])) {

            // echo "hello";

            // $adapter = new Stream(APP_PATH .'/log/login.log');
            // $logger  = new Logger(
            //     'messages',
            //     [
            //         'main' => $adapter,
            //     ]
            // );
            $logger = $this->logger;
            $myescap = new Myescaper();
            $email = $myescap->sanitize($data['email']);
            $password = $myescap->sanitize($data['password']);

            // echo $email;
            // echo $data['password'];
            // die();
            if ($email != 'harsh@sharma') {
                $logger->error(' wrong email');
            } elseif ($password != 'har123') {
                $logger->error('wrong password');
            } else {
                $logger->error('log in');
                $response = new Response();

                $this->response->redirect('userdash/admindash');
            }


            // $logger = $this->logger;
            // $logger->error('Something went wrong');
            // die();
        }
    }


    public function registerAction()
    {



        $data = $this->request->getPost();
        if (isset($data['submit'])) {


            $logger = $this->logger2;
            $myescap = new Myescaper();
            $email = $myescap->sanitize($data['email']);
            $name = $myescap->sanitize($data['name']);
            $password = $myescap->sanitize($data['password']);


            if ($name == '') {
                $logger->error(' wrong name');
            } elseif ($email == '') {
                $logger->error(' wrong email');
            } elseif ($password == '') {
                $logger->error('wrong password');
            } else {
                $logger->error('register');
            }


            // $this->view->message = $data;
            // print_r($input);
            // die();

            // $user = new Users();

            // $user->username = $input['username'];
            // $user->password = $input['password'];
            // $user->email = $input['email'];
            // $user->save();
        }
    }

    public function productaddAction()
    {

        $data = $this->request->getPost();
        // print_r($data);

        if ($data['submit'] == 'add') {
            // print_r($data);
            // die();
            $name = $data['name'];
            $price = $data['price'] == '' ? null : $data['price'];
            $stock = $data['stock'] == '' ? null : $data['stock'];
            $description = $data['description'];
            $tags = $data['tags'];


            $product = new Products();

            $product->name = $name;
            $product->price = $price;
            $product->stock = $stock;
            $product->description = $description;
            $product->tag = $tags;




            $product->save();
            $eventsManager = $this->di->get('EventsManager');
            $settingobj = new Settings();
            $setting = $settingobj::findFirst(1);
            $updateProduct = $eventsManager->fire('notification:afterAdd', $product, $setting);
            $updateProduct->save();
            print_r($product->getMessages());
            // die();
        }
    }
    public function orderaddAction()
    {
        $product = Products::find();

        $this->view->message = $product;

        $data = $this->request->getPost();
        // print_r($data);

        if ($data['submit'] == 'add') {
            // print_r($data);
            // die();
            $name = $data['name'];
            $address = $data['address'];
            $Zipcode = $data['Zipcode'] == '' ? null : $data['Zipcode'];
            $product = $data['product'];
            $stock = $data['stock'];


            $order = new Orders();

            $order->name = $name;
            $order->address = $address;
            $order->Zipcode = $Zipcode;
            $order->product = $product;
            $order->stock = $stock;




            $order->save();
            $eventsManager = $this->di->get('EventsManager');
            $settingobj = new Settings();
            $setting = $settingobj::findFirst(1);
            $updateorder = $eventsManager->fire('notification:afterorderAdd', $order, $setting);
            $updateorder->save();
            // print_r($product->getMessages());
            // die();
        }
    }
    public function settingAction()
    {

        $data = $this->request->getPost();
        if ($data['submit'] == 'save') {
            print_r($data);
            // echo "Welcome";
            // echo $data['optimization'];
            // die();          
            $settingobj = new Settings();
            $setting = $settingobj::findFirst(1);
            // print_r($obj->title_optimization);
            // die();
            $setting->id = 1;
            $setting->title_optimization = $data['optimization'];
            $setting->Default_Price = $data['Default_Price'];
            $setting->default_zip = $data['default_zip'];
            $setting->default_stock = $data['default_stock'];
            // $eventsManager = $this->di->get('EventsManager');

            // $this->logger->info($event);
            $setting->save();

            // echo $success;
            // die();
            // $event =   $eventsManager->fire('notification:aftersend',$settingobj,$);

        }
    }
    public function orderlistAction()
    {

        $order = Orders::find();

        $this->view->message = $order;
    }
    public function productlistAction()
    {

        $product = Products::find();

        $this->view->message = $product;
    }
}
