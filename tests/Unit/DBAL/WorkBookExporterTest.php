<?php

declare(strict_types=1);

namespace Eclipxe\XLSXExporter\Tests\Unit\DBAL;

use DateTime;
use Eclipxe\XLSXExporter\CellTypes;
use Eclipxe\XLSXExporter\Columns;
use Eclipxe\XLSXExporter\DBAL\WorkBookExporter;
use Eclipxe\XLSXExporter\Styles\Font;
use Eclipxe\XLSXExporter\Tests\TestCase;
use EngineWorks\DBAL\CommonTypes;

final class WorkBookExporterTest extends TestCase
{
    public function testCreateColumnsFromFieldsSimple(): void
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

    public function testCreateColumnsFromFieldsWithHeaders(): void
    {
        $fields = $this->getFieldsSample();
        $headers = [
            'counter' => ['title' => 'Counter'],
            'firstname' => [],
            'lastrecord' => ['style' => ['font' => ['underline' => Font::UNDERLINE_DOUBLE]]],
            'atime' => ['title' => 'The Time', 'style' => ['font' => ['underline' => Font::UNDERLINE_DOUBLE]]],
            'invalid' => [],
        ];
        // create columns
        $columns = WorkBookExporter::createColumnsFromFields($fields, $headers);
        $this->assertCount(4, $columns);
        // 'invalid' does not exists
        $this->assertFalse($columns->existsById('invalid'));
        unset($headers['invalid']);

        // the order must be the same
        $columnsOrder = [];
        foreach ($columns as $column) {
            $columnsOrder[] = $column->getId();
        }
        $this->assertEquals(array_keys($headers), $columnsOrder);

        // check that title and underline was set
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
        ];
        foreach ($expectedProperties as $fieldname => $properties) {
            $this->assertTrue($columns->existsById($fieldname));
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
     * @return array<int, array{name: string, commontype: class-string<DateTime>|string, expected: class-string<DateTime>|string}>
     */
    private function getFieldsSample(): array
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
