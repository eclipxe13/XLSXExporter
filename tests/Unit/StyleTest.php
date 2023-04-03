<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Tests\Unit;

use Eclipxe\XlsxExporter\Style;
use Eclipxe\XlsxExporter\Styles\Font;
use Eclipxe\XlsxExporter\Styles\Format;
use Eclipxe\XlsxExporter\Tests\TestCase;
use LogicException;
use stdClass;

final class StyleTest extends TestCase
{
    public function testConstructorGetterSetter(): void
    {
        $s = new Style();
        $expectedMembers = $s->getMemberNames();
        $this->assertGreaterThan(0, count($expectedMembers), 'There are no members!');
        foreach ($expectedMembers as $member) {
            /** @phpstan-var class-string $memberClass */
            $memberClass = '\Eclipxe\XlsxExporter\Styles\\' . ucfirst($member);
            $this->assertInstanceOf($memberClass, $s->$member, "$member is bad instanced");
            $getter = 'get' . ucfirst($member);
            $this->assertSame(
                $s->{$member},
                $s->{$getter}(),
                "$member property access is not the same as the getter method"
            );
            $setter = 'set' . ucfirst($member);
            $chain = $s->$setter($s->$member);
            $this->assertInstanceOf(Style::class, $chain, 'The setter is not returning a Style object');
            $this->assertSame($s, $chain, 'The setter return a different instance of the object');
        }
    }

    public function testGetterThrowException(): void
    {
        /** @var stdClass $style */
        $style = new Style();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid property name invalidpropertyname');
        echo $style->{'invalidpropertyname'};
    }

    public function testSetterThrowExceptionPropertyName(): void
    {
        /**
         * @noinspection PhpObjectFieldsAreOnlyWrittenInspection
         * @var stdClass $style
         */
        $style = new Style();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid property name invalidpropertyname');
        $style->{'invalidpropertyname'} = 0;
    }

    public function testSetterThrowExceptionType(): void
    {
        $style = new Style();
        /** @var Format $notFormatObject */
        $notFormatObject = new Font();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The value must be an instance of \Eclipxe\XlsxExporter\Styles\Format');
        $style->format = $notFormatObject;
    }

    public function testMagicCallInvalidMethodName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid method name invalidMethodName');
        $style = new Style();
        $style->{'invalidMethodName'}();
    }

    public function testMagicCallInvalidMethodGetSet(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid setter/getter name someInvalid');
        $style = new Style();
        $style->{'setSomeInvalid'}();
    }

    public function testMagicCallInvalidMethodSetNoArguments(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid setter argument');
        $style = new Style();
        $style->{'setFormat'}();
    }

    public function testMagicCallInvalidMethodSetMoreThanOne(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid setter argument');
        $style = new Style();
        $style->{'setFormat'}(null, null);
    }

    /**
     * @return array{font: array{underline: string}, format: array{code: string}}
     */
    protected function getFormatArray(): array
    {
        return [
            'font' => [
                'underline' => Font::UNDERLINE_DOUBLE,
            ],
            'format' => [
                'code' => Format::FORMAT_COMMA_2DECS,
            ],
        ];
    }

    public function testConstructorWithArray(): void
    {
        $style = new Style($this->getFormatArray());
        $this->assertSame(Format::FORMAT_COMMA_2DECS, $style->format->code, 'Cannot set the style using constructor');
    }

    public function testSetFromArray(): void
    {
        $style = new Style();
        $array = $style->setFromArray($this->getFormatArray());
        $this->assertSame(Font::UNDERLINE_DOUBLE, $style->font->underline, 'Cannot set the style using setFromArray');
        $this->assertTrue($style->hasValues(), 'It was expected that hasValues returns true');
        $this->assertSame($style, $array, 'The setFromArray method is not chained');
    }

    public function testHasValues(): void
    {
        $style = new Style();
        $this->assertFalse(
            $style->hasValues(),
            'It was expected that hasValues returns false since there is no specification of style'
        );
        $style->setFromArray($this->getFormatArray());
        $this->assertTrue(
            $style->hasValues(),
            'It was expected that hasValues returns true since it was specified using setFromArray'
        );
    }

    public function testStyleIndexProperty(): void
    {
        $style = new Style();
        $this->assertNull($style->getStyleIndex(), 'styleindex must be null just after style creation');
        $fluent = $style->setStyleIndex(5);
        $this->assertSame(5, $style->getStyleIndex(), 'The StyleIndex property does not match after setStyleIndex');
        $this->assertSame($style, $fluent, 'The setStyleIndex method is not chained');
    }

    public function testAsXml(): void
    {
        $style = new Style();
        $empty = '<' . 'xf'
            . ' applyAlignment="false"'
            . ' applyBorder="false"'
            . ' applyFill="false"'
            . ' applyFont="false"'
            . ' applyNumberFormat="false"'
            . ' applyProtection="false"'
            . ' borderId=""'
            . ' fillId=""'
            . ' fontId=""'
            . ' numFmtId="0"'
            . ' xfId="0"/>'
        ;
        $this->assertXmlStringEqualsXmlString($empty, $style->asXML(), 'Style does not match with expected XML');
    }
}
