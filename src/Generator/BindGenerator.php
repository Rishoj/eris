<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;

/**
 * @param Generator $innerGenerator
 * @param callable $outerGeneratorFactory
 * @return BindGenerator
 */
function bind(Generator $innerGenerator, callable $outerGeneratorFactory)
{
    return BindGenerator::bind($innerGenerator, $outerGeneratorFactory);
}

class BindGenerator implements Generator
{
    private $innerGenerator;
    private $outerGeneratorFactory;
    
    public function __construct($innerGenerator, $outerGeneratorFactory)
    {
        $this->innerGenerator = $innerGenerator;
        $this->outerGeneratorFactory = $outerGeneratorFactory;
    }

    public function __invoke($size, RandomRange $rand)
    {
        $innerGeneratorValue = $this->innerGenerator->__invoke($size, $rand);
        $outerGenerator = call_user_func($this->outerGeneratorFactory, $innerGeneratorValue->unbox());
        $outerGeneratorValue = $outerGenerator->__invoke($size, $rand);
        return $this->packageGeneratedValueSingle(
            $outerGeneratorValue,
            $innerGeneratorValue
        );
    }

    public function shrink(GeneratedValue $element)
    {
        list($outerGeneratorValue, $innerGeneratorValue) = $element->input();
        // TODO: shrink also the second generator
        $outerGenerator = call_user_func($this->outerGeneratorFactory, $innerGeneratorValue->unbox());
        $shrinkedOuterGeneratorValue = $outerGenerator->shrink($outerGeneratorValue);
        return $this->packageGeneratedValueSingle(
            $shrinkedOuterGeneratorValue,
            $innerGeneratorValue
        );
    }

    private function packageGeneratedValueSingle($outerGeneratorValue, $innerGeneratorValue)
    {
        return GeneratedValueSingle::fromValueAndInput(
            $outerGeneratorValue->unbox(),
            [
                $outerGeneratorValue,
                $innerGeneratorValue,
            ],
            'bind'
        );
    }

    /**
     * @param Generator $innerGenerator
     * @param callable $outerGeneratorFactory
     * @return BindGenerator
     */
    public static function bind(Generator $innerGenerator, callable $outerGeneratorFactory)
    {
        return new self(
            $innerGenerator,
            $outerGeneratorFactory
        );
    }
}
