<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @implements ConfigurableFixerInterface<_AutogeneratedInputConfiguration, _AutogeneratedComputedConfiguration>
 *
 * @phpstan-type _AutogeneratedInputConfiguration array{
 *  call_type?: 'self'|'static'|'this',
 *  methods?: array<string, string>
 * }
 * @phpstan-type _AutogeneratedComputedConfiguration array{
 *  call_type: 'self'|'static'|'this',
 *  methods: array<string, string>
 * }
 */
final class PhpUnitTestCaseStaticMethodCallsFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface
{
    /** @use ConfigurableFixerTrait<_AutogeneratedInputConfiguration, _AutogeneratedComputedConfiguration> */
    use ConfigurableFixerTrait;

    /**
     * @internal
     */
    public const CALL_TYPE_THIS = 'this';

    /**
     * @internal
     */
    public const CALL_TYPE_SELF = 'self';

    /**
     * @internal
     */
    public const CALL_TYPE_STATIC = 'static';

    /**
     * @var array<string, true>
     */
    private const STATIC_METHODS = [
        // Assert methods
        'anything' => true,
        'arrayHasKey' => true,
        'assertArrayHasKey' => true,
        'assertArrayIsEqualToArrayIgnoringListOfKeys' => true,
        'assertArrayIsEqualToArrayOnlyConsideringListOfKeys' => true,
        'assertArrayIsIdenticalToArrayIgnoringListOfKeys' => true,
        'assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys' => true,
        'assertArrayNotHasKey' => true,
        'assertArraySubset' => true,
        'assertAttributeContains' => true,
        'assertAttributeContainsOnly' => true,
        'assertAttributeCount' => true,
        'assertAttributeEmpty' => true,
        'assertAttributeEquals' => true,
        'assertAttributeGreaterThan' => true,
        'assertAttributeGreaterThanOrEqual' => true,
        'assertAttributeInstanceOf' => true,
        'assertAttributeInternalType' => true,
        'assertAttributeLessThan' => true,
        'assertAttributeLessThanOrEqual' => true,
        'assertAttributeNotContains' => true,
        'assertAttributeNotContainsOnly' => true,
        'assertAttributeNotCount' => true,
        'assertAttributeNotEmpty' => true,
        'assertAttributeNotEquals' => true,
        'assertAttributeNotInstanceOf' => true,
        'assertAttributeNotInternalType' => true,
        'assertAttributeNotSame' => true,
        'assertAttributeSame' => true,
        'assertClassHasAttribute' => true,
        'assertClassHasStaticAttribute' => true,
        'assertClassNotHasAttribute' => true,
        'assertClassNotHasStaticAttribute' => true,
        'assertContains' => true,
        'assertContainsEquals' => true,
        'assertContainsNotOnlyArray' => true,
        'assertContainsNotOnlyBool' => true,
        'assertContainsNotOnlyCallable' => true,
        'assertContainsNotOnlyClosedResource' => true,
        'assertContainsNotOnlyFloat' => true,
        'assertContainsNotOnlyInstancesOf' => true,
        'assertContainsNotOnlyInt' => true,
        'assertContainsNotOnlyIterable' => true,
        'assertContainsNotOnlyNull' => true,
        'assertContainsNotOnlyNumeric' => true,
        'assertContainsNotOnlyObject' => true,
        'assertContainsNotOnlyResource' => true,
        'assertContainsNotOnlyScalar' => true,
        'assertContainsNotOnlyString' => true,
        'assertContainsOnly' => true,
        'assertContainsOnlyArray' => true,
        'assertContainsOnlyBool' => true,
        'assertContainsOnlyCallable' => true,
        'assertContainsOnlyClosedResource' => true,
        'assertContainsOnlyFloat' => true,
        'assertContainsOnlyInstancesOf' => true,
        'assertContainsOnlyInt' => true,
        'assertContainsOnlyIterable' => true,
        'assertContainsOnlyNull' => true,
        'assertContainsOnlyNumeric' => true,
        'assertContainsOnlyObject' => true,
        'assertContainsOnlyResource' => true,
        'assertContainsOnlyScalar' => true,
        'assertContainsOnlyString' => true,
        'assertCount' => true,
        'assertDirectoryDoesNotExist' => true,
        'assertDirectoryExists' => true,
        'assertDirectoryIsNotReadable' => true,
        'assertDirectoryIsNotWritable' => true,
        'assertDirectoryIsReadable' => true,
        'assertDirectoryIsWritable' => true,
        'assertDirectoryNotExists' => true,
        'assertDirectoryNotIsReadable' => true,
        'assertDirectoryNotIsWritable' => true,
        'assertDoesNotMatchRegularExpression' => true,
        'assertEmpty' => true,
        'assertEquals' => true,
        'assertEqualsCanonicalizing' => true,
        'assertEqualsIgnoringCase' => true,
        'assertEqualsWithDelta' => true,
        'assertEqualXMLStructure' => true,
        'assertFalse' => true,
        'assertFileDoesNotExist' => true,
        'assertFileEquals' => true,
        'assertFileEqualsCanonicalizing' => true,
        'assertFileEqualsIgnoringCase' => true,
        'assertFileExists' => true,
        'assertFileIsNotReadable' => true,
        'assertFileIsNotWritable' => true,
        'assertFileIsReadable' => true,
        'assertFileIsWritable' => true,
        'assertFileMatchesFormat' => true,
        'assertFileMatchesFormatFile' => true,
        'assertFileNotEquals' => true,
        'assertFileNotEqualsCanonicalizing' => true,
        'assertFileNotEqualsIgnoringCase' => true,
        'assertFileNotExists' => true,
        'assertFileNotIsReadable' => true,
        'assertFileNotIsWritable' => true,
        'assertFinite' => true,
        'assertGreaterThan' => true,
        'assertGreaterThanOrEqual' => true,
        'assertInfinite' => true,
        'assertInstanceOf' => true,
        'assertInternalType' => true,
        'assertIsArray' => true,
        'assertIsBool' => true,
        'assertIsCallable' => true,
        'assertIsClosedResource' => true,
        'assertIsFloat' => true,
        'assertIsInt' => true,
        'assertIsIterable' => true,
        'assertIsList' => true,
        'assertIsNotArray' => true,
        'assertIsNotBool' => true,
        'assertIsNotCallable' => true,
        'assertIsNotClosedResource' => true,
        'assertIsNotFloat' => true,
        'assertIsNotInt' => true,
        'assertIsNotIterable' => true,
        'assertIsNotNumeric' => true,
        'assertIsNotObject' => true,
        'assertIsNotReadable' => true,
        'assertIsNotResource' => true,
        'assertIsNotScalar' => true,
        'assertIsNotString' => true,
        'assertIsNotWritable' => true,
        'assertIsNumeric' => true,
        'assertIsObject' => true,
        'assertIsReadable' => true,
        'assertIsResource' => true,
        'assertIsScalar' => true,
        'assertIsString' => true,
        'assertIsWritable' => true,
        'assertJson' => true,
        'assertJsonFileEqualsJsonFile' => true,
        'assertJsonFileNotEqualsJsonFile' => true,
        'assertJsonStringEqualsJsonFile' => true,
        'assertJsonStringEqualsJsonString' => true,
        'assertJsonStringNotEqualsJsonFile' => true,
        'assertJsonStringNotEqualsJsonString' => true,
        'assertLessThan' => true,
        'assertLessThanOrEqual' => true,
        'assertMatchesRegularExpression' => true,
        'assertNan' => true,
        'assertNotContains' => true,
        'assertNotContainsEquals' => true,
        'assertNotContainsOnly' => true,
        'assertNotCount' => true,
        'assertNotEmpty' => true,
        'assertNotEquals' => true,
        'assertNotEqualsCanonicalizing' => true,
        'assertNotEqualsIgnoringCase' => true,
        'assertNotEqualsWithDelta' => true,
        'assertNotFalse' => true,
        'assertNotInstanceOf' => true,
        'assertNotInternalType' => true,
        'assertNotIsReadable' => true,
        'assertNotIsWritable' => true,
        'assertNotNull' => true,
        'assertNotRegExp' => true,
        'assertNotSame' => true,
        'assertNotSameSize' => true,
        'assertNotTrue' => true,
        'assertNull' => true,
        'assertObjectEquals' => true,
        'assertObjectHasAttribute' => true,
        'assertObjectHasProperty' => true,
        'assertObjectNotEquals' => true,
        'assertObjectNotHasAttribute' => true,
        'assertObjectNotHasProperty' => true,
        'assertRegExp' => true,
        'assertSame' => true,
        'assertSameSize' => true,
        'assertStringContainsString' => true,
        'assertStringContainsStringIgnoringCase' => true,
        'assertStringContainsStringIgnoringLineEndings' => true,
        'assertStringEndsNotWith' => true,
        'assertStringEndsWith' => true,
        'assertStringEqualsFile' => true,
        'assertStringEqualsFileCanonicalizing' => true,
        'assertStringEqualsFileIgnoringCase' => true,
        'assertStringEqualsStringIgnoringLineEndings' => true,
        'assertStringMatchesFormat' => true,
        'assertStringMatchesFormatFile' => true,
        'assertStringNotContainsString' => true,
        'assertStringNotContainsStringIgnoringCase' => true,
        'assertStringNotEqualsFile' => true,
        'assertStringNotEqualsFileCanonicalizing' => true,
        'assertStringNotEqualsFileIgnoringCase' => true,
        'assertStringNotMatchesFormat' => true,
        'assertStringNotMatchesFormatFile' => true,
        'assertStringStartsNotWith' => true,
        'assertStringStartsWith' => true,
        'assertThat' => true,
        'assertTrue' => true,
        'assertXmlFileEqualsXmlFile' => true,
        'assertXmlFileNotEqualsXmlFile' => true,
        'assertXmlStringEqualsXmlFile' => true,
        'assertXmlStringEqualsXmlString' => true,
        'assertXmlStringNotEqualsXmlFile' => true,
        'assertXmlStringNotEqualsXmlString' => true,
        'attribute' => true,
        'attributeEqualTo' => true,
        'callback' => true,
        'classHasAttribute' => true,
        'classHasStaticAttribute' => true,
        'contains' => true,
        'containsEqual' => true,
        'containsIdentical' => true,
        'containsOnly' => true,
        'containsOnlyArray' => true,
        'containsOnlyBool' => true,
        'containsOnlyCallable' => true,
        'containsOnlyClosedResource' => true,
        'containsOnlyFloat' => true,
        'containsOnlyInstancesOf' => true,
        'containsOnlyInt' => true,
        'containsOnlyIterable' => true,
        'containsOnlyNull' => true,
        'containsOnlyNumeric' => true,
        'containsOnlyObject' => true,
        'containsOnlyResource' => true,
        'containsOnlyScalar' => true,
        'containsOnlyString' => true,
        'countOf' => true,
        'directoryExists' => true,
        'equalTo' => true,
        'equalToCanonicalizing' => true,
        'equalToIgnoringCase' => true,
        'equalToWithDelta' => true,
        'fail' => true,
        'fileExists' => true,
        'getCount' => true,
        'getObjectAttribute' => true,
        'getStaticAttribute' => true,
        'greaterThan' => true,
        'greaterThanOrEqual' => true,
        'identicalTo' => true,
        'isArray' => true,
        'isBool' => true,
        'isCallable' => true,
        'isClosedResource' => true,
        'isEmpty' => true,
        'isFalse' => true,
        'isFinite' => true,
        'isFloat' => true,
        'isInfinite' => true,
        'isInstanceOf' => true,
        'isInt' => true,
        'isIterable' => true,
        'isJson' => true,
        'isList' => true,
        'isNan' => true,
        'isNull' => true,
        'isNumeric' => true,
        'isObject' => true,
        'isReadable' => true,
        'isResource' => true,
        'isScalar' => true,
        'isString' => true,
        'isTrue' => true,
        'isType' => true,
        'isWritable' => true,
        'lessThan' => true,
        'lessThanOrEqual' => true,
        'logicalAnd' => true,
        'logicalNot' => true,
        'logicalOr' => true,
        'logicalXor' => true,
        'markTestIncomplete' => true,
        'markTestSkipped' => true,
        'matches' => true,
        'matchesRegularExpression' => true,
        'objectEquals' => true,
        'objectHasAttribute' => true,
        'readAttribute' => true,
        'resetCount' => true,
        'stringContains' => true,
        'stringEndsWith' => true,
        'stringEqualsStringIgnoringLineEndings' => true,
        'stringStartsWith' => true,

        // TestCase methods
        'any' => true,
        'at' => true,
        'atLeast' => true,
        'atLeastOnce' => true,
        'atMost' => true,
        'createStub' => true,
        'createConfiguredStub' => true,
        'createStubForIntersectionOfInterfaces' => true,
        'exactly' => true,
        'never' => true,
        'once' => true,
        'onConsecutiveCalls' => true,
        'returnArgument' => true,
        'returnCallback' => true,
        'returnSelf' => true,
        'returnValue' => true,
        'returnValueMap' => true,
        'setUpBeforeClass' => true,
        'tearDownAfterClass' => true,
        'throwException' => true,
    ];

    /**
     * @var array<string, bool>
     */
    private const ALLOWED_VALUES = [
        self::CALL_TYPE_THIS => true,
        self::CALL_TYPE_SELF => true,
        self::CALL_TYPE_STATIC => true,
    ];

    /**
     * @var array<string, list<array{int, string}>>
     */
    private array $conversionMap = [
        self::CALL_TYPE_THIS => [[T_OBJECT_OPERATOR, '->'], [T_VARIABLE, '$this']],
        self::CALL_TYPE_SELF => [[T_DOUBLE_COLON, '::'], [T_STRING, 'self']],
        self::CALL_TYPE_STATIC => [[T_DOUBLE_COLON, '::'], [T_STATIC, 'static']],
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        $codeSample = '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testMe()
    {
        $this->assertSame(1, 2);
        self::assertSame(1, 2);
        static::assertSame(1, 2);
        static::assertTrue(false);
    }
}
';

        return new FixerDefinition(
            'Calls to `PHPUnit\Framework\TestCase` static methods must all be of the same type, either `$this->`, `self::` or `static::`.',
            [
                new CodeSample($codeSample),
                new CodeSample($codeSample, ['call_type' => self::CALL_TYPE_THIS]),
                new CodeSample($codeSample, ['methods' => ['assertTrue' => self::CALL_TYPE_THIS]]),
            ],
            null,
            'Risky when PHPUnit methods are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before SelfStaticAccessorFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('call_type', 'The call type to use for referring to PHPUnit methods.'))
                ->setAllowedTypes(['string'])
                ->setAllowedValues(array_keys(self::ALLOWED_VALUES))
                ->setDefault(self::CALL_TYPE_STATIC)
                ->getOption(),
            (new FixerOptionBuilder('methods', 'Dictionary of `method` => `call_type` values that differ from the default strategy.'))
                ->setAllowedTypes(['array<string, string>'])
                ->setAllowedValues([static function (array $option): bool {
                    foreach ($option as $method => $value) {
                        if (!isset(self::STATIC_METHODS[$method])) {
                            throw new InvalidOptionsException(
                                \sprintf(
                                    'Unexpected "methods" key, expected any of %s, got "%s".',
                                    Utils::naturalLanguageJoin(array_keys(self::STATIC_METHODS)),
                                    \gettype($method).'#'.$method
                                )
                            );
                        }

                        if (!isset(self::ALLOWED_VALUES[$value])) {
                            throw new InvalidOptionsException(
                                \sprintf(
                                    'Unexpected value for method "%s", expected any of %s, got "%s".',
                                    $method,
                                    Utils::naturalLanguageJoin(array_keys(self::ALLOWED_VALUES)),
                                    \is_object($value) ? \get_class($value) : (null === $value ? 'null' : \gettype($value).'#'.$value)
                                )
                            );
                        }
                    }

                    return true;
                }])
                ->setDefault([])
                ->getOption(),
        ]);
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $analyzer = new TokensAnalyzer($tokens);

        for ($index = $startIndex; $index < $endIndex; ++$index) {
            // skip anonymous classes
            if ($tokens[$index]->isGivenKind(T_CLASS)) {
                $index = $this->findEndOfNextBlock($tokens, $index);

                continue;
            }

            $callType = $this->configuration['call_type'];

            if ($tokens[$index]->isGivenKind(T_FUNCTION)) {
                // skip lambda
                if ($analyzer->isLambda($index)) {
                    $index = $this->findEndOfNextBlock($tokens, $index);

                    continue;
                }

                // do not change `self` to `this` in static methods
                if (self::CALL_TYPE_THIS === $callType) {
                    $attributes = $analyzer->getMethodAttributes($index);

                    if (false !== $attributes['static']) {
                        $index = $this->findEndOfNextBlock($tokens, $index);

                        continue;
                    }
                }
            }

            if (!$tokens[$index]->isGivenKind(T_STRING) || !isset(self::STATIC_METHODS[$tokens[$index]->getContent()])) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);

            if (!$tokens[$nextIndex]->equals('(')) {
                $index = $nextIndex;

                continue;
            }

            if ($tokens[$tokens->getNextMeaningfulToken($nextIndex)]->isGivenKind(CT::T_FIRST_CLASS_CALLABLE)) {
                continue;
            }

            $methodName = $tokens[$index]->getContent();

            if (isset($this->configuration['methods'][$methodName])) {
                $callType = $this->configuration['methods'][$methodName];
            }

            $operatorIndex = $tokens->getPrevMeaningfulToken($index);
            $referenceIndex = $tokens->getPrevMeaningfulToken($operatorIndex);

            if (!$this->needsConversion($tokens, $index, $referenceIndex, $callType)) {
                continue;
            }

            $tokens[$operatorIndex] = new Token($this->conversionMap[$callType][0]);
            $tokens[$referenceIndex] = new Token($this->conversionMap[$callType][1]);
        }
    }

    private function needsConversion(Tokens $tokens, int $index, int $referenceIndex, string $callType): bool
    {
        $functionsAnalyzer = new FunctionsAnalyzer();

        return $functionsAnalyzer->isTheSameClassCall($tokens, $index)
            && !$tokens[$referenceIndex]->equals($this->conversionMap[$callType][1], false);
    }

    private function findEndOfNextBlock(Tokens $tokens, int $index): int
    {
        $nextIndex = $tokens->getNextTokenOfKind($index, [';', '{']);

        return $tokens[$nextIndex]->equals('{')
            ? $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nextIndex)
            : $nextIndex;
    }
}
