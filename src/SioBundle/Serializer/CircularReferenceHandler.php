<?php

namespace SioBundle\Serializer;

class CircularReferenceHandler
{

    public function __invoke($object)
    {

        var_dump($object);

    }

}