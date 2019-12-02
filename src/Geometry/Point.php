<?php

namespace Geometry;

class Point
{
    /**
     * represente l'axe X
     * @var float
     */
    private $abscissa;

    /**
     * represente l'axe Y
     * @var float
     */
    private $ordinate;

    public function __construct(array $coordonnees)
    {
        $this->abscissa = $coordonnees[0];
        $this->ordinate = $coordonnees[1];
    }

    /**
     * @return float
     */
    public function getAbscissa()
    {
        return $this->abscissa;
    }

    /**
     * @return float
     */
    public function getOrdinate()
    {
        return $this->ordinate;
    }

    /**
     * @param Point $point
     */
    public function isEqual($point) : bool
    {
        return bccomp($this->abscissa, $point->abscissa, 10) == 0
            && bccomp($this->ordinate, $point->ordinate, 10) == 0
        ;
    }

    /**
     * @param Point $point
     */
    public function isStrictlyHigher($point) : bool
    {
        return $this->ordinate > $point->ordinate;
    }

    /**
     * @param Point $point
     */
    public function isLower($point) : bool
    {
        return $this->ordinate <= $point->ordinate;
    }

    public function toArray()
    {
        return [
            $this->abscissa,
            $this->ordinate
        ];
    }

    public function toJSON()
    {
        return json_encode($this->toArray());
    }
}
