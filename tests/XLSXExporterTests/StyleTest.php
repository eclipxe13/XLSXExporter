<?php
namespace XLSXExporterTests;

use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use XLSXExporter\Style;
use XLSXExporter\Styles\Font;
use XLSXExporter\Styles\Format;

class StyleTest extends TestCase
{
    public function testConstructorGetterSetter()
    {
        $s = new Style();
        $expectedMembers = $s->getMemberNames();
        $this->assertGreaterThan(0, count($expectedMembers), 'There are no members!');
        foreach ($expectedMembers as $member) {
            $this->assertInstanceOf(
                '\XLSXExporter\Styles\\' . ucfirst($member),
                $s->$member,
                "$member is bad instanced"
            );
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

    public function testGetterThrowException()
    {
        /** @var stdClass $style */
        $style = new Style();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Invalid property name invalidpropertyname");
        echo $style->{'invalidpropertyname'};
    }

    public function testSetterThrowExceptionPropertyName()
    {
        /** @var stdClass $style */
        $style = new Style();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Invalid property name invalidpropertyname");
        $style->{'invalidpropertyname'} = 0;
    }

    public function testSetterThrowExceptionType()
    {
        $style = new Style();
        /** @var Format $notFormatObject */
        $notFormatObject = new Font();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("The value must be an instance of \XLSXExporter\Styles\Format");
        $style->format = $notFormatObject;
    }

    public function testMagicCallInvalidMethodName()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Invalid method name invalidMethodName");
        $style = new Style();
        $style->{'invalidMethodName'}();
    }

    public function testMagicCallInvalidMethodGetSet()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Invalid setter/getter name someInvalid");
        $style = new Style();
        $style->{'setSomeInvalid'}();
    }

    public function testMagicCallInvalidMethodSetNoArguments()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Invalid setter argument");
        $style = new Style();
        $style->{'setFormat'}();
    }

    public function testMagicCallInvalidMethodSetMoreThanOne()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Invalid setter argument");
        $style = new Style();
        $style->{'setFormat'}(null, null);
    }

    protected function getFormatArray()
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

    public function testConstructorWithArray()
    {
        $style = new Style($this->getFormatArray());
        $this->assertSame(Format::FORMAT_COMMA_2DECS, $style->format->code, 'Cannot set the style using constructor');
    }

    public function testSetFromArray()
    {
        $style = new Style();
        $array = $style->setFromArray($this->getFormatArray());
        $this->assertSame(Font::UNDERLINE_DOUBLE, $style->font->underline, 'Cannot set the style using setFromArray');
        $this->assertTrue($style->hasValues(), 'It was expected that hasValues returns true');
        $this->assertSame($style, $array, 'The setFromArray method is not chained');
    }

    public function testHasValues()
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

    public function testStyleIndexProperty()
    {
        $style = new Style();
        $this->assertNull($style->getStyleIndex(), 'styleindex must be null just after style creation');
        $fluent = $style->setStyleIndex(5);
        $this->assertSame(5, $style->getStyleIndex(), 'The StyleIndex property does not match after setStyleIndex');
        $this->assertSame($style, $fluent, 'The setStyleIndex method is not chained');
    }

    public function testAsXML()
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
