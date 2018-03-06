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

        $key = \Doctrine\Common\Util\Inflector::camelize($array[0]);

        if (1 === count($array)) {
            $value = true === empty($value) ? null : $value;

            return array($key => $value);
        }

        $out[$key] = $this->linearize($array[1], $value);

        return $out;
    }

    public function sqlToArray(array $res, $idName = 'id')
    {
        $temp = array();
        $out = array();

        foreach ($res as $key => $child) {
            $key = strtolower($key);

            $tld = preg_split('#/#', $key, 3, PREG_SPLIT_NO_EMPTY);

            $x = $this->linearize($tld[2], $child);

            $temp = array_merge_recursive($temp, $x);

            $out[$tld[0]] = $temp;
            $out[$tld[0]]['id'] = $tld[1];
        }

        return $out;
    }

    protected function objectFromArray($fqn, array $array)
    {
        $violations = new \Doctrine\Common\Collections\ArrayCollection();

        $reflector = new \ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                $violations->add(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return array(
            'object' => $class,
            'violations' => $violations,
        );
    }
}
