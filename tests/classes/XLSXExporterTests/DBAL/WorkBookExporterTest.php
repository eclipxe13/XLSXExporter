<?php
namespace XLSXExporterTests\DBAL;

use EngineWorks\DBAL\CommonTypes;
use PHPUnit\Framework\TestCase;
use XLSXExporter\CellTypes;
use XLSXExporter\Column;
use XLSXExporter\Columns;
use XLSXExporter\DBAL\WorkBookExporter;
use XLSXExporter\Styles\Font;

class WorkBookExporterTest extends TestCase
{
    public function testCreateColumnsFromFieldsSimple()
    {
        $fields = $this->getFieldsSample();
        $columns = WorkBookExporter::createColumnsFromFields($fields);
        $this->assertInstanceOf(Columns::class, $columns);
        $this->assertCount(count($fields), $columns);
        $i = 0;
        foreach ($columns as $column) {
            $this->assertEquals($column->getId(), $fields[$i]['name']);
            $this->assertEquals($column->getTitle(), $fields[$i]['name']);
            $this->assertEquals($column->getType(), $fields[$i]['expected']);
            $i = $i + 1;
        }
    }

    public function testCreateColumnsFromFieldsWithHeaders()
    {
        $fields = $this->getFieldsSample();
        $headers = [
            'firstname' => [],
            'counter' => ['title' => 'Counter'],
            'lastrecord' => ['style' => ['font' => ['underline' => Font::UNDERLINE_DOUBLE]]],
            'atime' => ['title' => 'The Time', 'style' => ['font' => ['underline' => Font::UNDERLINE_DOUBLE]]],
        ];
        $expectedProperties = [
            'firstname' => [
                'title' => 'firstname',
                'underline' => null,
            ],
            'counter' => [
                'title' => 'Counter',
                'underline' => null,
            ],
            'lastrecord' => [
                'title' => 'lastrecord',
                'underline' => Font::UNDERLINE_DOUBLE,
            ],
            'atime' => [
                'title' => 'The Time',
                'underline' => Font::UNDERLINE_DOUBLE,
            ],
            'invalid' => [],
        ];
        $columns = WorkBookExporter::createColumnsFromFields($fields, $headers);
        $this->assertCount(4, $columns);
        $this->assertFalse($columns->existsById('invalid'));
        unset($expectedProperties['invalid']);
        foreach ($expectedProperties as $fieldname => $properties) {
            $this->assertTrue($columns->existsById($fieldname));
            /* @var $column Column */
            $column = $columns->getById($fieldname);
            $this->assertEquals($fieldname, $column->getId());
            $this->assertEquals($properties['title'], $column->getTitle());
            $this->assertEquals(
                $properties['underline'],
                $column->getStyle()->font->underline,
                "Missmatch at $fieldname"
            );
        }
    }

    /**
     * @return array
     */
    private function getFieldsSample()
    {
        return [
            [
                'name' => 'firstname',
                'commontype' => CommonTypes::TTEXT,
                'expected' => CellTypes::TEXT,
            ],
            [
                'name' => 'counter',
                'commontype' => CommonTypes::TINT,
                'expected' => CellTypes::NUMBER,
            ],
            [
                'name' => 'active',
                'commontype' => CommonTypes::TBOOL,
                'expected' => CellTypes::BOOLEAN,
            ],
            [
                'name' => 'amount',
                'commontype' => CommonTypes::TNUMBER,
                'expected' => CellTypes::NUMBER,
            ],
            [
                'name' => 'lastrecord',
                'commontype' => CommonTypes::TDATETIME,
                'expected' => CellTypes::DATETIME,
            ],
            [
                'name' => 'birth',
                'commontype' => CommonTypes::TDATE,
                'expected' => CellTypes::DATE,
            ],
            [
                'name' => 'atime',
                'commontype' => CommonTypes::TTIME,
                'expected' => CellTypes::TIME,
            ],
        ];
    }
}
