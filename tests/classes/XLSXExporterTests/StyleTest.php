<?php
namespace XLSXExporterTests;

use XLSXExporter\Style;

class StyleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorGetterSetter()
    {
        $s = new Style();
        $expectedMembers = $s->getMemberNames();
        $this->assertGreaterThan(0, count($expectedMembers), "There are no members!");
        foreach ($expectedMembers as $member) {
            $this->assertInstanceOf('\XLSXExporter\Styles\\' . ucfirst($member), $s->$member, "$member is bad instanced");
            $getter = 'get' . ucfirst($member);
            $this->assertSame($s->$member, $s->$getter(), "$member property access is not the same as the getter method");
            $setter = 'set' . ucfirst($member);
            $chain = $s->$setter($s->$member);
            $this->assertInstanceOf('\XLSXExporter\Style', $chain, "The setter is not returning a Style object");
            $this->assertSame($s, $chain, "The setter return a different instance of the object");
        }
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid property name invalidpropertyname
     */
    public function testGetterThrowException()
    {
        (new Style())->invalidpropertyname;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid property name invalidpropertyname
     */
    public function testSetterThrowExceptionPropertyName()
    {
        $s = new Style();
        $s->invalidpropertyname = 0;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The value must be an instance of \XLSXExporter\Styles\Format
     */
    public function testSetterThrowExceptionType()
    {
        $s = new Style();
        $s->format = new \XLSXExporter\Styles\Font();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid method name invalidMethodName
     */
    public function testMagicCallInvalidMethodName()
    {
        $s = new Style();
        $s->invalidMethodName();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid setter/getter name someInvalid
     */
    public function testMagicCallInvalidMethodGetSet()
    {
        $s = new Style();
        $s->setSomeInvalid();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid setter argument
     */
    public function testMagicCallInvalidMethodSetNoArguments()
    {
        $s = new Style();
        $s->setFormat();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid setter argument
     */
    public function testMagicCallInvalidMethodSetMoreThanOne()
    {
        $s = new Style();
        $s->setFormat(null, null);
    }


    protected function getFormatArray()
    {
        return [
            "font" => [
                "underline" => \XLSXExporter\Styles\Font::UNDERLINE_DOUBLE,
            ],
            "format" => [
                "code" => \XLSXExporter\Styles\Format::FORMAT_COMMA_2DECS,
            ],
        ];
    }

    public function testConstructorWithArray()
    {
        $s = new Style($this->getFormatArray());
        $this->assertSame(\XLSXExporter\Styles\Format::FORMAT_COMMA_2DECS, $s->format->code, "Cannot set the style using constructor");
    }

    public function testSetFromArray()
    {
        $s = new Style();
        $x = $s->setFromArray($this->getFormatArray());
        $this->assertSame(\XLSXExporter\Styles\Font::UNDERLINE_DOUBLE, $s->font->underline, "Cannot set the style using setFromArray");
        $this->assertTrue($s->hasValues(), "It was expected that hasValues returns true");
        $this->assertSame($s, $x, "The setFromArray method is not chained");
    }

    public function testHasValues()
    {
        $s = new Style();
        $this->assertFalse($s->hasValues(), "It was expected that hasValues returns false since there is no specification of style");
        $s->setFromArray($this->getFormatArray());
        $this->assertTrue($s->hasValues(), "It was expected that hasValues returns true since it was specified using setFromArray");
    }

    public function testStyleIndexProperty()
    {
        $s = new Style();
        $this->assertNull($s->getStyleIndex(), "styleindex must be null just after style creation");
        $x = $s->setStyleIndex(5);
        $this->assertSame(5, $s->getStyleIndex(), "The StyleIndex property does not match after setStyleIndex");
        $this->assertSame($s, $x, "The setStyleIndex method is not chained");
    }

    public function testAsXML()
    {
        $s = new Style();
        $empty = '<xf applyAlignment="false" applyBorder="false" applyFill="false" applyFont="false" applyNumberFormat="false" applyProtection="false" borderId="" fillId="" fontId="" numFmtId="0" xfId="0"/>';
        $this->assertXmlStringEqualsXmlString($empty, $s->asXML(), "Style does not match with expected XML");
    }
}
