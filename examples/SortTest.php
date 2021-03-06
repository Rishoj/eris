<?php
use Eris\Generator\IntegerGenerator;
use Eris\Generator\SequenceGenerator;

class SortTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testArraySorting()
    {
        $this
            ->forAll(
                SequenceGenerator::seq(IntegerGenerator::nat())
            )
            ->then(function ($array) {
                sort($array);
                for ($i = 0; $i < count($array) - 1; $i++) {
                    $this->assertTrue(
                        $array[$i] <= $array[$i+1],
                        "Array is not sorted: " . var_export($array, true)
                    );
                }
            });
    }
}
