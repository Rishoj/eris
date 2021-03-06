<?php
use Eris\Generator\ChooseGenerator;
use Eris\Generator\ElementsGenerator;
use Eris\Generator\SequenceGenerator;

class WhenTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testWhenWithAnAnonymousFunctionWithGherkinSyntax()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000)
        )
            ->when(function ($n) {
                return $n > 42;
            })
            ->then(function ($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithAnAnonymousFunctionForMultipleArguments()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000),
            ChooseGenerator::choose(0, 1000)
        )
            ->when(function ($first, $second) {
                return $first > 42 && $second > 23;
            })
            ->then(function ($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testWhenWithOnePHPUnitConstraint()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000)
        )
            ->when($this->greaterThan(42))
            ->then(function ($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithMultiplePHPUnitConstraints()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000),
            ChooseGenerator::choose(0, 1000)
        )
            ->when($this->greaterThan(42), $this->greaterThan(23))
            ->then(function ($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testMultipleWhenClausesWithGherkinSyntax()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000)
        )
            ->when($this->greaterThan(42))
            ->and($this->lessThan(900))
            ->then(function ($number) {
                $this->assertTrue(
                    $number > 42 && $number < 900,
                    "\$number was filtered to be between 42 and 900, but it is $number"
                );
            });
    }

    public function testWhenWhichSkipsTooManyValues()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000)
        )
            ->when($this->greaterThan(800))
            ->then(function ($number) {
                $this->assertTrue(
                    $number > 800
                );
            });
    }

    /**
     * The current implementation shows no problem as PHPUnit prefers to show
     * the exception from the test method than the one from teardown
     * when both fail.
     */
    public function testWhenFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne()
    {
        $this->forAll(
            ChooseGenerator::choose(0, 1000)
        )
            ->when($this->greaterThan(100))
            ->then(function ($number) {
                $this->assertTrue(
                    $number <= 100,
                    "\$number should be less or equal to 100, but it is $number"
                );
            });
    }

    public function testSizeIncreasesEvenIfEvaluationsAreSkippedDueToAntecedentsNotBeingSatisfied()
    {
        $this->forAll(
            SequenceGenerator::seq(ElementsGenerator::elements(1, 2, 3))
        )
            ->when(function ($seq) {
                return count($seq) > 0;
            })
            ->then(function ($seq) {
                $this->assertGreaterThan(0, count($seq));
            });
    }
}
