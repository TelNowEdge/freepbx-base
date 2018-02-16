<?php

namespace TelNowEdge\FreePBX\Base\Repository;

use TelNowEdge\FreePBX\Base\Exception\NoResultException;

abstract class AbstractAsteriskRepository
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected $connection;

    public function setConnection(\AGI_AsteriskManager $connection)
    {
        $this->connection = $connection;
    }

    public function show($family, $key)
    {
        $request = sprintf('%s/%d', $family, $key);

        $res = $this->connection
            ->database_show($request);

        if (true === empty($res)) {
            throw new NoResultException();
        }

        return $res;
    }

    public function linearize($keys, $value)
    {
        $out = array();

        $array = preg_split('#/#', $keys, 2);

        if (1 === count($array)) {
            return array($array[0] => $value);
        }

        $b = $this->linearize($array[1], $value);
        $out[$array[0]] = $b;

        return $out;
    }

    public function sqlToArray(array $res, $idName = 'id')
    {
        $temp = array();

        foreach ($res as $key => $child) {
            $tld = preg_split('#/#', $key, 3, PREG_SPLIT_NO_EMPTY);

            $b = $this->linearize($tld[2], $child);

            $temp = array_merge_recursive($temp, $b);

            $out[$tld[0]] = $out;
        }

        return $toto;
    }

    protected function objectFromArray($fqn, array $array)
    {
        $reflector = new \ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                throw new \Exception(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return $class;
    }
}
