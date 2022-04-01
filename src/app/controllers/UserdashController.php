<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UserdashController extends Controller
{
    public function indexAction()
    {
        return '<h1>Hello!!!</h1>';
    }

    public function admindashAction()
    {
        // echo "Welcome";
        // die();



        $productobj = Products::find();

        // print_r($productobj[0]->name);
        // die();

        $this->view->message = $productobj;
    }

    public function updateAction()
    {

        echo "Welcome";

        $data = $this->request->getPost();
        // print_r($data);

        if ($data['submit'] == 'Submit') {

            

            $productobj = Products::findFirst($data['id']);


            $productobj->price = $data['price'];

            $productobj->save();
        }
        // die();
        $response = new Response();
        $this->response->redirect('userdash/admindash');
    }
}
