<?php
use Eris\Generator\ElementsGenerator;
use Eris\TestTrait;

class AlwaysFailsTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testFailsNoMatterWhatIsTheInput()
    {
        $this->forAll(
            ElementsGenerator::elements(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'])
        )
            ->then(function ($someChar) {
                $this->fail("This test fails by design. '$someChar' was passed in");
            });
    }
}
