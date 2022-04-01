<?php


use Phalcon\Escaper;


class Myescaper
{

    public function sanitize($value){

        $escaper = new Escaper();

        // echo "in ";
        // die();

        return $escaper->escapeHtml($value);



    }

}
