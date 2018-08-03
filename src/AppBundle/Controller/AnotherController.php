<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AnotherController
 * @package AppBundle\Controller
 *
 * @RouteResource("Another")
 */
class AnotherController extends FOSRestController implements ClassResourceInterface
{

    public function getAction($Ip_address, $Mac_address)
    {
        $arr = ['Ip_address' => $Ip_address, 'Mac_address' => $Mac_address];
        $array =
            ['Ip_address' => '111.111.111.111', 'Mac_address' => 'e4:R4:u7:6t:5r'];

        if (!empty(array_intersect($arr, $array)))
        return new JsonResponse(array('success' => 'true'));
        else return new JsonResponse(array('success' => 'false'));
    }
}
