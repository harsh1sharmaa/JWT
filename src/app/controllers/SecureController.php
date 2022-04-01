<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;



class SecureController extends Controller
{


    public function BuildaclAction($id = 0)
    {

        // $aclFile = APP_PATH . '/security/acl.cache';

        // //check aclfile present or not
        // if (is_file($aclFile) != true) {

        //     //file does not exist now we build a new file
        //     $acl = new Memory();
        //     $acl->addrole('manager');
        //     $acl->addrole('accounting');
        //     $acl->addrole('guest');

        //     $acl->addComponent(
        //         'test', //this is controller
        //         [
        //             'eventtest'  //this is action
        //         ]
        //     );

        //     $acl->allow('manager', 'test', 'eventtest');
        //     $acl->deny('guest', '*', '*');


        //     //store serialize list into plane file
        //     file_put_contents(
        //         $aclFile,  //why $aclFile is here????????????????????????????????????????????????/
        //         serialize($acl)
        //     );
        // } else {

        //     $acl = unserialize(
        //         file_get_contents($aclFile)
        //     );
        // }

        // if (true === $acl->allow('accounting', 'test', 'eventtest')) {

        //     echo 'access granted';
        // } else {
        //     echo 'access denied ';
        // }



        $aclFile = APP_PATH . '/security/acl.cache';

        if (true !== is_file($aclFile)) {
            $acl = new Memory();

            $acl->addRole('admin');


            $acl->addComponent(
                'acl',
                [
                    'index',
                ]
            );

            $acl->allow('admin', '*', '*');


            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(file_get_contents($aclFile));
        }


        if (true == $acl->isallowed('manager', 'index', 'index')) {
            echo "Access granted";
        } else {
            echo "Access denied";
        }

        if ($id == 1) {
            $this->response->redirect('acl/index');
        }

        return $acl;
    }
}
